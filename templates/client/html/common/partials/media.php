<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */

/* Available data:
 * - item : Media item object implementing \MShop\Media\Item\Iface
 */


if( ( $item = $this->get( 'item' ) ) === null ) {
	return;
}

$enc = $this->encoder();

$boxattr = '';
foreach( $this->get( 'boxAttributes', [] ) as $name => $value ) {
	$boxattr .= $name . ( $value != null ? '="' . $enc->attr( $value ) . '" ' : '' );
}

$itemattr = '';
foreach( $this->get( 'itemAttributes', [] ) as $name => $value ) {
	$itemattr .= $name . ( $value != null ? '="' . $enc->attr( $value ) . '" ' : '' );
}

?>
<?php switch( current( explode( '/', $item->getMimetype() ) ) ) : case 'audio': ?>

	<audio <?= $boxattr ?>>
		<source src="<?= $enc->attr( $this->content( $item->getUrl( true ), $item->getFileSystem() ) ) ?>"
			title="<?= $enc->attr( $item->getProperties( 'title' )->first( $item->getName() ) ) ?>"
			type="<?= $enc->attr( $item->getMimetype() ) ?>" <?= $itemattr ?>>
		<?php foreach( $item->getRefItems( 'media' ) as $subItem ) : ?>
			<source src="<?= $enc->attr( $this->content( $subItem->getUrl( true ), $subItem->getFileSystem() ) ) ?>"
				title="<?= $enc->attr( $subItem->getProperties( 'title' )->first( $item->getName() ) ) ?>"
				type="<?= $enc->attr( $subItem->getMimetype() ) ?>" <?= $itemattr ?>>
		<?php endforeach ?>
		<?= $enc->html( $item->getName() ) ?>
	</audio>

	<?php break; case 'video': ?>

		<video <?= $boxattr ?>>
			<source src="<?= $enc->attr( $this->content( $item->getUrl( true ), $item->getFileSystem() ) ) ?>"
				title="<?= $enc->attr( $item->getProperties( 'title' )->first( $item->getName() ) ) ?>"
				type="<?= $enc->attr( $item->getMimetype() ) ?>" <?= $itemattr ?>>
		<?php foreach( $item->getRefItems( 'media' ) as $subItem ) : ?>
			<source src="<?= $enc->attr( $this->content( $subItem->getUrl( true ), $subItem->getFileSystem() ) ) ?>"
				title="<?= $enc->attr( $subItem->getProperties( 'title' )->first( $item->getName() ) ) ?>"
				type="<?= $enc->attr( $subItem->getMimetype() ) ?>" <?= $itemattr ?>>
		<?php endforeach ?>
		<?= $enc->html( $item->getName() ) ?>
	</video>

	<?php break; case 'image': ?>

		<span <?= $boxattr ?>><!--
			--><img sizes="100vw"
				alt="<?= $enc->attr( $item->getProperties( 'title' )->first( $item->getName() ) ) ?>" <?= $itemattr ?>
				src="<?= $enc->attr( $this->content( $item->getPreview(), $item->getFileSystem() ) ) ?>"
				<?= !empty( $item->getPreviews() ) ? 'srcset="' . $enc->attr( $this->imageset( $item->getPreviews( true ), $item->getFileSystem() ) ) . '"' : '' ?>
			><!--
		<?php foreach( $item->getRefItems( 'media' ) as $subItem ) : ?>
			--><img sizes="100vw"
				alt="<?= $enc->attr( $subItem->getProperties( 'title' )->first( $item->getName() ) ) ?>" <?= $itemattr ?>
				src="<?= $enc->attr( $this->content( $subItem->getPreview(), $subItem->getFileSystem() ) ) ?>"
				<?= !empty( $subItem->getPreviews() ) ? 'srcset="' . $enc->attr( $this->imageset( $subItem->getPreviews( true ), $subItem->getFileSystem() ) ) . '"' : '' ?>
			><!--
		<?php endforeach ?>
		--></span>

	<?php break; default: ?>

		<a href="<?= $enc->attr( $this->content( $item->getUrl( true ), $item->getFileSystem() ) ) ?>" <?= $boxattr ?>><!--
			--><img src="<?= $enc->attr( $this->content( $item->getPreview(), $item->getFileSystem() ) ) ?>"
				title="<?= $enc->attr( $item->getProperties( 'title' )->first( $item->getName() ) ) ?>" <?= $itemattr ?>
			><?= $enc->html( $item->getName() ) ?><!--
		--></a>

<?php endswitch ?>
