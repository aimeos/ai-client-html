<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


$target = $this->config( 'client/html/account/index/url/target' );
$controller = $this->config( 'client/html/account/index/url/controller', 'account' );
$action = $this->config( 'client/html/account/index/url/action', 'index' );
$config = $this->config( 'client/html/account/index/url/config', ['absoluteUri' => 1] );


?>
<?php $this->block()->start( 'email/account/text' ) ?>
<?= wordwrap( strip_tags( $this->get( 'emailIntro' ) ) ) ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'An account has been created for you.' ) ) ) ?>


<?= strip_tags( $this->translate( 'client', 'Your account' ) ) ?>

<?= $this->translate( 'client', 'Account' ) ?>: <?= $this->extAccountCode ?>

<?php if( ( $pass = $this->get( 'extAccountPassword' ) ) !== null ) : ?>
	<?= $this->translate( 'client', 'Password' ) ?>: <?= $pass ?>
<?php else : ?>
	<?= $this->translate( 'client', 'Password' ) ?>: <?= $this->translate( 'client', 'Like entered by you' ) ?>
<?php endif ?>


<?= $this->translate( 'client', 'Login' ) ?>: <?= $this->url( $target, $controller, $action, [], [], $config ) ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ) ) ?>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'email/account/text' ) ?>
