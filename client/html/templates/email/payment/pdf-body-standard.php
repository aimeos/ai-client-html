<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */

/** Available data
 * - summaryBasket : Order base item (basket) with addresses, services, products, etc.
 * - summaryTaxRates : List of tax values grouped by tax rates
 * - summaryNamedTaxes : Calculated taxes grouped by the tax names
 * - summaryShowDownloadAttributes : True if product download links should be shown, false if not
 * - summaryCostsDelivery : Sum of all shipping costs
 * - summaryCostsPayment : Sum of all payment costs
 * - priceFormat : Format of the price incl. currency placeholder
 */


$enc = $this->encoder();

$this->pdf->setMargins( 15, 30, 15 );
$this->pdf->setAutoPageBreak( true, 30 );
$this->pdf->setTitle( sprintf( $this->translate( 'client', 'Order %1$s' ), $this->extOrderItem->getOrderNumber() ) );
$this->pdf->setFont( 'dejavusans', '', 10 );

$vmargin = [
	'h1' => [ // HTML tag
		0 => ['h' => 1.5, 'n' => 0], // space before = h * n
		1 => ['h' => 1.5, 'n' => 3] // space after = h * n
	],
	'h2' => [
		0 => ['h' => 1.5, 'n' => 10],
		1 => ['h' => 1.5, 'n' => 5]
	],
	'ul' => [
		0 => ['h' => 0, 'n' => 0],
		1 => ['h' => 0, 'n' => 0]
	],
];

$this->pdf->setHtmlVSpace( $vmargin );
$this->pdf->setListIndentWidth( 4 );

$this->pdf->setHeaderFunction( function( $pdf ) {
	/* Add background image
	$margin = $pdf->getBreakMargin();
	$pdf->setAutoPageBreak( false, 0 );
	$pdf->image( __DIR__ . '/pdf-background.png', 0, 0, $pdf->getPageWidth(), $pdf->getPageHeight(), 'PNG' );
	$pdf->setAutoPageBreak( true, $margin );
	$pdf->setPageMark();
	*/

	$pdf->writeHtmlCell( 210, 20, 0, 0, '
		<div style="background-color: #103050; color: #ffffff; text-align: center; font-weight: bold">
			<div style="font-size: 0px"> </div>
			<!-- img src="https://aimeos.org/fileadmin/logos/logo-aimeos-white.png" height="30" -->
			Example company
			<div style="font-size: 0px"> </div>
		</div>
	' );
} );

$this->pdf->setFooterFunction( function( $pdf ) {
	$pdf->writeHtmlCell( 180, 22.5, 15, -22.5, '
		<table cellpadding="0.5" style="font-size: 8px">
			<tr>
				<td style="font-weight: bold">Example company</td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>Example address, 12345 Example city</td>
				<td>District court: </td>
				<td>Bank: </td>
			</tr>
			<tr>
				<td>Telephone: </td>
				<td>Managing director: </td>
				<td>IBAN: </td>
			</tr>
			<tr>
				<td>E-Mail: </td>
				<td>VAT ID: </td>
				<td>BIC: </td>
			</tr>
		</table>
	' );
	$pdf->writeHtmlCell( 210, 5, 0, -5, '
		<div style="background-color: #103050; color: #ffffff; text-align: center; font-weight: bold; font-size: 10px">
			example.com
		</div>
	' );
} );

// SEPA payment QR-Code
$total = $this->summaryBasket->getPrice()->getValue() + $this->summaryBasket->getPrice()->getCosts();
$data = [
	'BCD', // required
	'002', // Version 2 (required, 1=UTF-8, 2=ISO 8859-1, 3=ISO 8859-2, 4=ISO 8859-4, 5=ISO 8859-5, 6=ISO 8859-7, 7=ISO 8859-10, 8=ISO 8859-15)
	1,     // UTF-8 (required)
	'SCT', // SEPA Credit Transfer (required)
	'',    // BIC (optional)
	'',    // Name of recipient (required, name of your company)
	'',    // IBAN (required)
	$this->summaryBasket->getPrice()->getCurrencyId() . $total,    // Currency and value (required)
	'',    // Purpose (optional, 4 char code, https://wiki.windata.de/index.php?title=Purpose-SEPA-Codes)
	'',    // ISO 11649 RF Creditor Reference (optional, 35 characters structured code)
	$this->translate( 'client', 'Order' ) . ' ' . $this->extOrderItem->getOrderNumber(), // Reference of order and other data (optional, max. 140 characters)
	$this->extOrderBaseItem->getCustomerReference(), // Notice to the customer (optional, max. 70 characters)
];

$barcode = new TCPDF2DBarcode( join( "\n", $data ), 'QRCODE,M' );


?>
<?php $this->block()->start( 'email/payment/pdf' ) ?>
	<style>
		.address-self { font-size: 7px; font-weight: normal }
		.address-self .company { font-weight: bold }
		.meta { font-size: 8.5px }
	</style>
	<h1 class="address-self">
		<span class="company">Example company</span> · Example address · 12345 Example city
	</h1>
	<table>
		<tr>
			<td class="address" style="width: 66%">
				<?php if( $addr = current( $this->summaryBasket->getAddress( 'payment' ) ) ) : ?>
					<?= preg_replace( ['/^[ ]+/', '/\n+/m', '/ +/'], ['', '<br/>', ' '], trim( $enc->html( sprintf(
						/// Address format with company (%1$s), salutation (%2$s), title (%3$s), first name (%4$s), last name (%5$s),
						/// address part one (%6$s, e.g street), address part two (%7$s, e.g house number), address part three (%8$s, e.g additional information),
						/// postal/zip code (%9$s), city (%10$s), state (%11$s), country (%12$s), language (%13$s),
						/// e-mail (%14$s), phone (%15$s), facsimile/telefax (%16$s), web site (%17$s), vatid (%18$s)
						$this->translate( 'client', '%1$s
%2$s %3$s %4$s %5$s
%6$s %7$s
%8$s
%9$s %10$s
%11$s
%12$s
%13$s
%14$s
%15$s
%16$s
%17$s
%18$s
'
						),
						$addr->getCompany(),
						$this->translate( 'mshop/code', $addr->getSalutation() ),
						$addr->getTitle(),
						$addr->getFirstName(),
						$addr->getLastName(),
						$addr->getAddress1(),
						$addr->getAddress2(),
						$addr->getAddress3(),
						$addr->getPostal(),
						$addr->getCity(),
						$addr->getState(),
						$this->translate( 'country', $addr->getCountryId() ),
						$this->translate( 'language', $addr->getLanguageId() ),
						$addr->getEmail(),
						$addr->getTelephone(),
						$addr->getTelefax(),
						$addr->getWebsite(),
						$addr->getVatID()
					) ) ) ) ?>
				<?php endif ?>
			</td>
			<td style="width: 33%">
				<table class="meta">
					<tr>
						<td><?= $enc->html( $this->translate( 'client', 'Order date' ) ) ?>:</td>
						<td><?= $enc->html( date_create( $this->extOrderItem->getTimeCreated() )->format( $this->translate( 'client', 'Y-m-d' ) ) ) ?></td>
					</tr>
					<?php if( $this->extOrderBaseItem->getCustomerReference() ) : ?>
						<tr>
							<td><?= $enc->html( $this->translate( 'client', 'Reference' ) ) ?>:</td>
							<td><?= $enc->html( $this->extOrderBaseItem->getCustomerReference() ) ?></td>
						</tr>
					<?php endif ?>
					<tr>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td colspan="2">
							<img style="padding: 5px" src="@<?= base64_encode( $barcode->getBarcodePngData( 3, 3, [0, 0, 0] ) ) ?>" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<h2><?= $enc->html( $this->translate( 'client', 'Order' ) ) ?>: <?= $enc->html( $this->extOrderItem->getOrderNumber() ) ?></h2>
	<?= $this->partial(
		/** client/html/email/common/summary/pdf
		 * Location of the address partial template for the text e-mails
		 *
		 * To configure an alternative template for the address partial, you
		 * have to configure its path relative to the template directory
		 * (usually client/html/templates/). It's then used to display the
		 * payment or delivery address block in the text e-mails.
		 *
		 * @param string Relative path to the address partial
		 * @since 2020.07
		 */
		$this->config( 'client/html/email/common/summary/pdf', 'email/common/pdf-summary-partial' ),
		array(
			'summaryBasket' => $this->summaryBasket,
			'summaryTaxRates' => $this->get( 'summaryTaxRates', [] ),
			'summaryNamedTaxes' => $this->get( 'summaryNamedTaxes', [] ),
			'summaryShowDownloadAttributes' => $this->get( 'summaryShowDownloadAttributes', false ),
			'summaryCostsDelivery' => $this->get( 'summaryCostsDelivery', 0 ),
			'summaryCostsPayment' => $this->get( 'summaryCostsPayment', 0 ),
			'priceFormat' => $this->get( 'priceFormat' )
		)
	) ?>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'email/payment/pdf' ) ?>
