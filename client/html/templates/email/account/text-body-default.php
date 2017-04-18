<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


?>
<?php $this->block()->start( 'email/account/text' ); ?>
<?= wordwrap( strip_tags( $this->get( 'emailIntro' ) ) ); ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'An account has been created for you.' ) ) ); ?>


<?= strip_tags( $this->translate( 'client', 'Your account' ) ); ?>

<?= $this->translate( 'client', 'Account' ); ?>: <?php	echo $this->extAccountCode; ?>

<?= $this->translate( 'client', 'Password' ); ?>: <?php	echo $this->extAccountPassword; ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ) ); ?>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'email/account/text' ); ?>
