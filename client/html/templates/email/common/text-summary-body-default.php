<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$addresses = $this->summaryBasket->getAddresses();
$services = $this->summaryBasket->getServices();


?>
<?php $this->block()->start( 'email/common/text/summary' ); ?>

<?php
	echo strip_tags( $this->translate( 'client', 'Billing address' ) ) . ":\n";

	if( isset( $addresses['payment'] ) )
	{
		echo $this->partial(
			/** client/html/email/common/summary/address/text
			 * Location of the address partial template for the text e-mails
			 *
			 * To configure an alternative template for the address partial, you
			 * have to configure its path relative to the template directory
			 * (usually client/html/templates/). It's then used to display the
			 * payment or delivery address block in the text e-mails.
			 *
			 * @param string Relative path to the address partial
			 * @since 2017.01
			 * @category Developer
			 * @see client/html/email/common/summary/detail/text
			 * @see client/html/email/common/summary/service/text
			 */
			$this->config( 'client/html/email/common/summary/address/text', 'common/summary/address-default.php' ),
			array( 'address' => $addresses['payment'], 'type' => 'payment' )
		);
	}
?>


<?php
	echo strip_tags( $this->translate( 'client', 'Delivery address' ) ) . ":\n";

	if( isset( $addresses['delivery'] ) )
	{
		echo $this->partial(
			$this->config( 'client/html/email/common/summary/address/text', 'common/summary/address-default.php' ),
			array( 'address' => $addresses['delivery'], 'type' => 'delivery' )
		);
	}
	else
	{
		echo $this->translate( 'client', 'like billing address' );
	}
?>



<?php
	echo strip_tags( $this->translate( 'client', 'delivery' ) ) . ': ';

	if( isset( $services['delivery'] ) )
	{
		echo $this->partial(
			/** client/html/email/common/summary/service/text
			 * Location of the service partial template for the text e-mails
			 *
			 * To configure an alternative template for the service partial, you
			 * have to configure its path relative to the template directory
			 * (usually client/html/templates/). It's then used to display the
			 * payment or delivery service block in the text e-mails.
			 *
			 * @param string Relative path to the service partial
			 * @since 2017.01
			 * @category Developer
			 * @see client/html/email/common/summary/address/text
			 * @see client/html/email/common/summary/detail/text
			 */
			$this->config( 'client/html/email/common/summary/service/text', 'email/common/text-summary-service-partial-default.php' ),
			array( 'service' => $services['delivery'], 'type' => 'delivery' )
		);
	}
?>

<?php
	echo strip_tags( $this->translate( 'client', 'payment' ) ) . ': ';

	if( isset( $services['payment'] ) )
	{
		echo $this->partial(
			$this->config( 'client/html/email/common/summary/service/text', 'email/common/text-summary-service-partial-default.php' ),
			array( 'service' => $services['payment'], 'type' => 'payment' )
		);
	}
?>


<?php
	echo strip_tags( $this->translate( 'client', 'Coupons' ) ) . ":\n";

	foreach( $this->extOrderBaseItem->getCoupons() as $code => $products ) {
		echo '- ' . $code . "\n";
	}
?>

<?php
	echo strip_tags( $this->translate( 'client', 'Your comment' ) ) . ":\n";
	echo strip_tags( $this->summaryBasket->getComment() ) . "\n";
?>


<?php
	echo $this->partial(
		/** client/html/email/common/summary/detail/text
		 * Location of the product detail partial template for the text e-mails
		 *
		 * To configure an alternative template for the product detail partial,
		 * you have to configure its path relative to the template directory
		 * (usually client/html/templates/). It's then used to display the
		 * product details block within the text part of the e-mails sent to the
		 * customers
		 *
		 * @param string Relative path to the product details partial for text e-mails
		 * @since 2017.01
		 * @category Developer
		 * @see client/html/email/common/summary/address/text
		 * @see client/html/email/common/summary/service/text
		 */
		$this->config( 'client/html/email/common/summary/detail/text', 'email/common/text-summary-detail-partial-default.php' ),
		array(
			'summaryBasket' => $this->summaryBasket,
			'summaryTaxRates' => $this->get( 'summaryTaxRates', [] ),
			'summaryShowDownloadAttributes' => $this->get( 'summaryShowDownloadAttributes', false ),
		)
	);
?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/common/text/summary' ); ?>
