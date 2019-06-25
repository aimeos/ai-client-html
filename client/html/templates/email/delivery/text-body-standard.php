<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

/** Available data
 * - summaryBasket : Order base item (basket) with addresses, services, products, etc.
 * - summaryTaxRates : List of tax values grouped by tax rates
 * - summaryNamedTaxes : Calculated taxes grouped by the tax names
 * - summaryShowDownloadAttributes : True if product download links should be shown, false if not
 * - summaryCostsDelivery : Sum of all shipping costs
 * - summaryCostsPayment : Sum of all payment costs
 */


$order = $this->extOrderItem;

$key = 'stat:' . $order->getDeliveryStatus();
$status = $this->translate( 'mshop/code', $key );
$format = $this->translate( 'client', 'Y-m-d' );

switch( $order->getDeliveryStatus() )
{
	case 3:
		/// Delivery e-mail intro with order ID (%1$s), order date (%2$s) and delivery status (%3%s)
		$msg = $this->translate( 'client', 'Your order %1$s from %2$s has been dispatched.' );
		break;
	case 6:
		/// Delivery e-mail intro with order ID (%1$s), order date (%2$s) and delivery status (%3%s)
		$msg = $this->translate( 'client', 'The parcel for your order %1$s from %2$s could not be delivered.' );
		break;
	case 7:
		/// Delivery e-mail intro with order ID (%1$s), order date (%2$s) and delivery status (%3%s)
		$msg = $this->translate( 'client', 'We received the returned parcel for your order %1$s from %2$s.' );
		break;
	default:
		/// Delivery e-mail intro with order ID (%1$s), order date (%2$s) and delivery status (%3%s)
		$msg = $this->translate( 'client', 'The delivery status of your order %1$s from %2$s has been changed to "%3$s".' );
}

$message = sprintf( $msg, $order->getId(), date_create( $order->getTimeCreated() )->format( $format ), $status );


?>
<?php $this->block()->start( 'email/delivery/text' ); ?>
<?= wordwrap( strip_tags( $this->get( 'emailIntro' ) ) ); ?>


<?= wordwrap( strip_tags( $message ) ); ?>


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
); ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ) ); ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'All orders are subject to our terms and conditions.' ) ) ); ?>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'email/delivery/text' );
