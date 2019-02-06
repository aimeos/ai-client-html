<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

$enc = $this->encoder();

?>
<?php $this->block()->start( 'checkout/confirm/intro' ); ?>
<div class="checkout-confirm-intro">
	<p class="note"><?= nl2br( $enc->html( $this->translate( 'client', 'The payment confirmation for your order is still pending.
You will get an e-mail as soon as the payment is authorized.' ), $enc::TRUST ) ); ?></p>
<?= $this->get( 'introBody' ); ?>
</div>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'checkout/confirm/intro' ); ?>
