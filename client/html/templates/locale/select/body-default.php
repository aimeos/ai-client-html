<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

?>
<?php $this->block()->start( 'locale/select' ); ?>
<section class="aimeos locale-select">
<?php if( ( $errors = $this->get( 'selectErrorList', [] ) ) !== [] ) : ?>
	<ul class="error-list">
<?php foreach( $errors as $error ) : ?>
		<li class="error-item"><?= $enc->html( $error ); ?></li>
<?php endforeach; ?>
	</ul>
<?php endif; ?>
<?= $this->get( 'selectBody' ); ?>
</section>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'locale/select' ); ?>
