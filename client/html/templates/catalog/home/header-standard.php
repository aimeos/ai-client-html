<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 */

$enc = $this->encoder();


?>
<?php if( isset( $this->homeTree ) ) : ?>

	<?php foreach( $this->homeTree->getRefItems( 'text', 'meta-keyword', 'default' ) as $textItem ) : ?>
		<meta name="keywords" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ); ?>" />
	<?php endforeach; ?>

	<?php foreach( $this->homeTree->getRefItems( 'text', 'meta-description', 'default' ) as $textItem ) : ?>
		<meta name="description" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ); ?>" />
	<?php endforeach; ?>

<?php endif ?>

<?php if( isset( $this->homeStockUrl ) ) : ?>
	<?php foreach( $this->homeStockUrl as $url ) : ?>
		<script type="text/javascript" defer="defer" src="<?= $enc->attr( $url ); ?>"></script>
	<?php endforeach ?>
<?php endif; ?>
