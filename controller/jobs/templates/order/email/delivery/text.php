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

$key = 'stat:' . $this->orderItem->getStatusDelivery();
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


<?php switch( $this->orderItem->getStatusDelivery() ) : case 3: /// Delivery e-mail intro with order ID (%1$s), order date (%2$s) and delivery status (%3%s) ?>
<?= 	sprintf( $this->translate( 'client', 'Your order %1$s from %2$s has been dispatched.' ), $this->orderItem->getOrderNumber(), $orderDate, $orderStatus ) ?>
<?php break; case 6: /// Delivery e-mail intro with order ID (%1$s), order date (%2$s) and delivery status (%3%s) ?>
<?= 	sprintf( $this->translate( 'client', 'The parcel for your order %1$s from %2$s could not be delivered.' ), $this->orderItem->getOrderNumber(), $orderDate, $orderStatus ) ?>
<?php break; case 7: /// Delivery e-mail intro with order ID (%1$s), order date (%2$s) and delivery status (%3%s) ?>
<?= 	sprintf( $this->translate( 'client', 'We received the returned parcel for your order %1$s from %2$s.' ), $this->orderItem->getOrderNumber(), $orderDate, $orderStatus ) ?>
<?php break; default: /// Delivery e-mail intro with order ID (%1$s), order date (%2$s) and delivery status (%3%s) ?>
<?= 	sprintf( $this->translate( 'client', 'The delivery status of your order %1$s from %2$s has been changed to "%3$s".' ), $this->orderItem->getOrderNumber(), $orderDate, $orderStatus ) ?>
<?php endswitch ?>


<?= $this->partial( 'order/email/summary-text', ['summaryBasket' => $this->summaryBasket] ) ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ) ) ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'All orders are subject to our terms and conditions.' ) ) ) ?>
