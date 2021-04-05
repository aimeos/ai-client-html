<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */

?>
<?php $this->block()->start( 'email/voucher' ) ?>
<?= $this->get( 'voucherBody' ) ?>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'email/voucher' );
