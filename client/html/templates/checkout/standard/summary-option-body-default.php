<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

?>
<?php $this->block()->start( 'checkout/standard/summary/option' ); ?>
<div class="checkout-standard-summary-option container">
	<h2 class="header"><?php echo $enc->html( $this->translate( 'client', 'Options' ), $enc::TRUST ); ?></h2>
<?php if( !isset( $this->optionCustomerId ) ) : ?>
	<div class="checkout-standard-summary-option-account">
		<h3><?php echo $enc->html( $this->translate( 'client', 'Create account' ), $enc::TRUST ); ?></h3>
		<div class="single">
			<input id="option-account" type="checkbox" name="<?php echo $enc->attr( $this->formparam( array( 'cs_option_account' ) ) ); ?>" value="1" <?php echo ( $this->param( 'cs_option_account', 1 ) == 1 ? 'checked="checked"' : '' ); ?> />
			<p><label for="option-account"><?php echo $enc->html( $this->translate( 'client', 'Create customer account' ), $enc::TRUST ); ?></label></p>
		</div>
	</div>
<?php endif; ?>
<?php echo $this->get( 'optionBody' ); ?>
</div>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'checkout/standard/summary/option' ); ?>
