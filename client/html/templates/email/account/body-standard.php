<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

?>
<?php $this->block()->start( 'email/account' ); ?>
<?= $this->get( 'accountBody' ); ?>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'email/account' ); ?>
