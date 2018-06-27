<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */

/// Product notification e-mail subject
$this->mail()->setSubject( $this->translate( 'client', 'Your subscription product' ) );

?>
<?= $this->get( 'subscriptionHeader' ); ?>
