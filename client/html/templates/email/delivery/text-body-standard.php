<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

/** Available data
 * - summaryBasket : Order base item (basket) with addresses, services, products, etc.
 * - summaryTaxRates : List of tax values grouped by tax rates
 * - summaryNamedTaxes : Calculated taxes grouped by the tax names
 * - summaryShowDownloadAttributes : True if product download links should be shown, false if not
 * - summaryCostsDelivery : Sum of all shipping costs
 * - summaryCostsPayment : Sum of all payment costs
 */


?>
<?php $this->block()->start( 'email/delivery/text' ) ?>
<?= wordwrap( strip_tags( $this->get( 'emailIntro' ) ) ) ?>


<?= wordwrap( strip_tags( $this->get( 'message' ) ) ) ?>


<?= $this->partial(
	$this->config( 'client/html/email/common/summary/text', 'email/common/text-summary-partial' ),
	array(
		'summaryBasket' => $this->summaryBasket,
		'summaryTaxRates' => $this->get( 'summaryTaxRates', [] ),
		'summaryNamedTaxes' => $this->get( 'summaryNamedTaxes', [] ),
		'summaryShowDownloadAttributes' => $this->get( 'summaryShowDownloadAttributes', false ),
		'summaryCostsDelivery' => $this->get( 'summaryCostsDelivery', 0 ),
		'summaryCostsPayment' => $this->get( 'summaryCostsPayment', 0 )
	)
) ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ) ) ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'All orders are subject to our terms and conditions.' ) ) ) ?>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'email/delivery/text' );
