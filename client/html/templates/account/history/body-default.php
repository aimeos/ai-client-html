<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

$optTarget = $this->config( 'client/jsonapi/url/options/target' );
$optController = $this->config( 'client/jsonapi/url/options/controller', 'jsonapi' );
$optAction = $this->config( 'client/jsonapi/url/options/action', 'index' );
$optConfig = $this->config( 'client/jsonapi/url/options/config', [] );


?>
<section class="aimeos account-history" data-jsonurl="<?= $enc->attr( $this->url( $optTarget, $optController, $optAction, [], [], $optConfig ) ); ?>">

	<?php if( ( $errors = $this->get( 'historyErrorList', [] ) ) !== [] ) : ?>
		<ul class="error-list">
			<?php foreach( $errors as $error ) : ?>
				<li class="error-item"><?= $enc->html( $error ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?= $this->get( 'historyBody' ); ?>

</section>
