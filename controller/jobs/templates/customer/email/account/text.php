<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

?>
<?= wordwrap( strip_tags( $this->get( 'intro', '' ) ) ) ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'An account has been created for you.' ) ) ) ?>


<?= strip_tags( $this->translate( 'client', 'Your account' ) ) ?>

<?= $this->translate( 'client', 'Account' ) ?>: <?= $this->get( 'account' ) ?>

<?= $this->translate( 'client', 'Password' ) ?>: <?= $this->get( 'password' ) ?: $this->translate( 'client', 'Like entered by you' ) ?>


<?= $this->translate( 'client', 'Login' ) ?>: <?= $this->link( 'client/html/account/index/url', ['locale' => $this->addressItem->getLanguageId()], ['absoluteUri' => 1] ) ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ) ) ?>
