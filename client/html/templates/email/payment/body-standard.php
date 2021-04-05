<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

?>
<?php $this->block()->start( 'email/payment' ) ?>
<?= $this->get( 'paymentBody' ) ?>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'email/payment' );
