<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */

$enc = $this->encoder();


?>
<?php if( !empty( $errors = $this->get( 'errors', [] ) ) ) : ?>
	<div class="aimeos">
		<ul class="error-list">
			<?php foreach( $errors as $error ) : ?>
				<li class="error-item"><?= $enc->html( $error ) ?></li>
			<?php endforeach ?>
		</ul>
	</div>
<?php endif ?>
<?php if( !empty( $infos = $this->get( 'infos', [] ) ) ) : ?>
	<div class="aimeos">
		<ul class="info-list">
			<?php foreach( $infos as $error ) : ?>
				<li class="info-item"><?= $enc->html( $error ) ?></li>
			<?php endforeach ?>
		</ul>
	</div>
<?php endif ?>
