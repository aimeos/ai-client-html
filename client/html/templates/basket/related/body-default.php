<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

$optTarget = $this->config( 'client/jsonapi/url/options/target' );
$optController = $this->config( 'client/jsonapi/url/options/controller', 'jsonapi' );
$optAction = $this->config( 'client/jsonapi/url/options/action', 'index' );
$optConfig = $this->config( 'client/jsonapi/url/options/config', [] );


?>
<section class="aimeos basket-related" data-jsonurl="<?= $enc->attr( $this->url( $optTarget, $optController, $optAction, [], [], $optConfig ) ); ?>">

	<?php if( isset( $this->relatedErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->relatedErrorList as $errmsg ) : ?>
				<li class="error-item"><?= $enc->html( $errmsg ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>


	<h1><?= $enc->html( $this->translate( 'client', 'Related' ), $enc::TRUST ); ?></h1>

	<?= $this->block()->get( 'basket/related/bought' ); ?>

</section>
