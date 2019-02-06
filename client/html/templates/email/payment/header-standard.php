<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

/// Payment e-mail subject with order ID
$str = $this->translate( 'client', 'Your order %1$s' );
$this->mail()->setSubject( sprintf( $str, $this->extOrderItem->getId() ) );

?>
<?= $this->get( 'paymentHeader' ); ?>
