<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


?>
<?php $this->block()->start( 'email/delivery/text' ); ?>
<?= wordwrap( strip_tags( $this->get( 'emailIntro' ) ) ); ?>


<?= $this->block()->get( 'email/delivery/text/intro' ); ?>


<?= $this->block()->get( 'email/common/text/summary' ); ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ) ); ?>


<?= wordwrap( strip_tags( $this->translate( 'client',  'All orders are subject to our terms and conditions.' ) ) ); ?>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'email/delivery/text' ); ?>
