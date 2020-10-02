<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 */

$enc = $this->encoder();


?>
<section class="aimeos account-review" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url', [], [] ) ); ?>">

	<?php if( ( $errors = $this->get( 'reviewErrorList', [] ) ) !== [] ) : ?>
		<ul class="error-list">
			<?php foreach( $errors as $error ) : ?>
				<li class="error-item"><?= $enc->html( $error ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php if( ( $infos = $this->get( 'reviewInfoList', [] ) ) !== [] ) : ?>
		<ul class="info-list">
			<?php foreach( $infos as $info ) : ?>
				<li class="info-item"><?= $enc->html( $info ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<h1 class="header"><?= $this->translate( 'client', 'Reviews' ) ?></h1>

	<?= $this->get( 'reviewBody' ); ?>

</section>
