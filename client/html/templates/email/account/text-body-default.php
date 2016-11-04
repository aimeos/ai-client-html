<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

$salutations = array(
	\Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR,
	\Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MRS,
	\Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MISS,
);

try
{
	$addr = $this->extAddressItem;

	/// E-mail intro with salutation (%1$s), first name (%2$s) and last name (%3$s)
	$intro = sprintf( $this->translate( 'client', 'Dear %1$s %2$s %3$s' ),
		( in_array( $addr->getSalutation(), $salutations ) ? $this->translate( 'client/code', $addr->getSalutation() ) : '' ),
		$addr->getFirstName(),
		$addr->getLastName()
	);
}
catch( Exception $e )
{
	$intro = $this->translate( 'client/html/email', 'Dear Sir or Madam' );
}

?>
<?php $this->block()->start( 'email/account/text' ); ?>
<?php echo wordwrap( strip_tags( $intro ) ); ?>


<?php echo wordwrap( strip_tags( $this->translate( 'client', 'An account has been created for you.' ) ) ); ?>



<?php echo strip_tags( $this->translate( 'client', 'Your account' ) ); ?>

<?php echo $this->translate( 'client', 'Account' ); ?>: <?php	echo $this->extAccountCode; ?>

<?php echo $this->translate( 'client', 'Password' ); ?>: <?php	echo $this->extAccountPassword; ?>


<?php echo wordwrap( strip_tags( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ) ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/account/text' ); ?>
