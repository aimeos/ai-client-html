<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */

?>
<?php $this->block()->start( 'email/subscription' ); ?>
<?= $this->get( 'subscriptionBody' ); ?>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'email/subscription' ); ?>
