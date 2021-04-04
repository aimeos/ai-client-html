<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */

$enc = $this->encoder();


?>
<?php if( isset( $this->homeTree ) ) : ?>

	<title><?= $enc->html( strip_tags( $this->homeTree->getName() ) ) ?> | <?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>

	<?php foreach( $this->homeTree->getRefItems( 'text', 'meta-keyword', 'default' ) as $textItem ) : ?>
		<meta name="keywords" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ); ?>" />
	<?php endforeach; ?>

	<?php foreach( $this->homeTree->getRefItems( 'text', 'meta-description', 'default' ) as $textItem ) : ?>
		<meta name="description" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ); ?>" />
	<?php endforeach; ?>

<?php else : ?>

	<title><?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>

<?php endif ?>

<?php if( isset( $this->homeStockUrl ) ) : ?>
	<?php foreach( $this->homeStockUrl as $url ) : ?>
		<script type="text/javascript" defer="defer" src="<?= $enc->attr( $url ); ?>"></script>
	<?php endforeach ?>
<?php endif; ?>

<meta name="application-name" content="Aimeos" />
