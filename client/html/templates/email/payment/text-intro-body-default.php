<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$order = $this->extOrderItem;

/// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3%s)
$msg = $this->translate( 'client', 'Thank you for your order %1$s from %2$s.' );

$key = 'pay:' . $order->getPaymentStatus();
$status = $this->translate( 'client/code', $key );
$format = $this->translate( 'client', 'Y-m-d' );

$string = sprintf( $msg, $order->getId(), date_create( $order->getTimeCreated() )->format( $format ), $status );


?>
<?php $this->block()->start( 'email/payment/text/intro' ); ?>


<?php echo wordwrap( strip_tags( $string ) ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/payment/text/intro' ); ?>
