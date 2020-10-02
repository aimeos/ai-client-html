<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 */

$enc = $this->encoder();


?>
<section class="aimeos account-review" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url/target/url', [], [] ) ); ?>">

	<?php if( ( $errors = $this->get( 'reviewErrorList', [] ) ) !== [] ) : ?>
		<ul class="error-list">
			<?php foreach( $errors as $error ) : ?>
				<li class="error-item"><?= $enc->html( $error ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?= $this->get( 'reviewBody' ); ?>

</section>
