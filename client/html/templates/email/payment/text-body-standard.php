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

$msg = $msg2 = '';
$key = 'pay:' . $order->getPaymentStatus();
$status = $this->translate( 'mshop/code', $key );
$format = $this->translate( 'client', 'Y-m-d' );

switch( $order->getPaymentStatus() )
{
	case 3:
		/// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3%s)
		$msg = $this->translate( 'client', 'The payment for your order %1$s from %2$s has been refunded.' );
		break;
	case 4:
		/// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3%s)
		$msg = $this->translate( 'client', 'Thank you for your order %1$s from %2$s.' );
		$msg2 = $this->translate( 'client', 'The order is pending until we receive the final payment. If you\'ve chosen to pay in advance, please transfer the money to our bank account with the order ID %1$s as reference.' );
		break;
	case 6:
		/// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3%s)
		$msg = $this->translate( 'client', 'Thank you for your order %1$s from %2$s.' );
		$msg2 = $this->translate( 'client', 'We have received your payment, and will take care of your order immediately.' );
		break;
	default:
		/// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3%s)
		$msg = $this->translate( 'client', 'Thank you for your order %1$s from %2$s.' );
}

$message = sprintf( $msg, $order->getId(), date_create( $order->getTimeCreated() )->format( $format ), $status );
$message .= "\n" . sprintf( $msg2, $order->getId(), date_create( $order->getTimeCreated() )->format( $format ), $status );

/** client/html/email/common/summary/text
 * Template partial used for redering the order summary details for text e-mails
 *
 * The setting must be the path to the partial relative to the template directory
 * in your own extension and must include the file name without the file extension.
 *
 * @param string Relative path to the partial file without file extension
 * @category Developer
 * @since 2019.10
 */

?>
<?php $this->block()->start( 'email/payment/text' ); ?>
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
<?= $this->block()->get( 'email/payment/text' );
