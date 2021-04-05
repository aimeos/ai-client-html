<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

$enc = $this->encoder();

?>
<?php $this->block()->start( 'checkout/confirm/intro' ) ?>
<div class="checkout-confirm-intro">
	<p class="note"><?= nl2br( $enc->html( $this->translate( 'client', 'Thank you for your order.
We received your payment and an e-mail with the order details will be sent to you within the next few minutes.' ), $enc::TRUST ) ) ?></p>
<?= $this->get( 'introBody' ) ?>
</div>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'checkout/confirm/intro' ) ?>
