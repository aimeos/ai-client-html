<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


?>
<?php $this->block()->start( 'email/payment/text' ); ?>
<?php echo wordwrap( strip_tags( $this->get( 'emailIntro' ) ) ); ?>


<?php echo $this->block()->get( 'email/payment/text/intro' ); ?>


<?php echo $this->block()->get( 'email/common/text/summary' ); ?>


<?php echo wordwrap( strip_tags( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ) ); ?>


<?php echo wordwrap( strip_tags( $this->translate( 'client',  'All orders are subject to our terms and conditions.' ) ) ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/payment/text' ); ?>
