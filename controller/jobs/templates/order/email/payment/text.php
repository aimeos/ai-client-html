<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

/** Available data
 * - orderItem: Order item
 * - summaryBasket : Order base item (basket) with addresses, services, products, etc.
 */


/** client/html/email/common/summary/text
 * Template partial used for redering the order summary details for text e-mails
 *
 * The setting must be the path to the partial relative to the template directory
 * in your own extension and must include the file name without the file extension.
 *
 * @param string Relative path to the partial file without file extension
 * @since 2019.10
 */

$key = 'pay:' . $this->orderItem->getStatusPayment();
$orderStatus = $this->translate( 'mshop/code', $key );
$orderDate = date_create( $this->orderItem->getTimeCreated() )->format( $this->translate( 'client', 'Y-m-d' ) );


?>
<?php switch( $this->addressItem->getSalutation() ) : case 'mr': ?>
<?= 	sprintf( $this->translate( 'client', 'Dear Mr %1$s %2$s' ), $this->addressItem->getFirstName(), $this->addressItem->getLastName() ) ?>
<?php break; case 'ms': ?>
<?= 	sprintf( $this->translate( 'client', 'Dear Ms %1$s %2$s' ), $this->addressItem->getFirstName(), $this->addressItem->getLastName() ) ?>
<?php break; default: ?>
<?= 	sprintf( $this->translate( 'client', 'Dear %1$s %2$s' ), $this->addressItem->getFirstName(), $this->addressItem->getLastName() ) ?>
<?php endswitch ?>


<?php switch( $this->orderItem->getStatusPayment() ) : case 3: /// Payment e-mail intro with order ID (%1$s) and order date (%2$s) ?>
<?= 	sprintf( $this->translate( 'client', 'The payment for your order %1$s from %2$s has been refunded.' ), $this->orderItem->getOrderNumber(), $orderDate, $orderStatus ) ?>
<?php break; case 4: /// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3$s) ?>
<?= 	sprintf( $this->translate( 'client', 'The order is pending until we receive the final payment. If you\'ve chosen to pay in advance, please transfer the money to our bank account with the order ID %1$s as reference.' ), $this->orderItem->getOrderNumber(), $orderDate, $orderStatus ) ?>
<?php break; case 5: /// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3$s) ?>
<?= 	sprintf( $this->translate( 'client', 'Thank you for your order %1$s from %2$s.' ), $this->orderItem->getOrderNumber(), $orderDate, $orderStatus ) ?>
<?php break; case 6: /// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3$s) ?>
<?= 	sprintf( $this->translate( 'client', 'We have received your payment, and will take care of your order immediately.' ), $this->orderItem->getOrderNumber(), $orderDate, $orderStatus ) ?>
<?php break; default: /// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3$s) ?>
<?= 	sprintf( $this->translate( 'client', 'The payment status of your order %1$s from %2$s has been changed to "%3$s".' ), $this->orderItem->getOrderNumber(), $orderDate, $orderStatus ) ?>
<?php endswitch ?>


<?= $this->partial( 'order/email/summary-text', ['summaryBasket' => $this->summaryBasket] ) ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ) ) ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'All orders are subject to our terms and conditions.' ) ) ) ?>
