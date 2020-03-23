<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
 */

/* Available data:
 * - item : Media item object implemnting \MShop\Media\Item\Iface
 */


if( ( $item = $this->get( 'item' ) ) === null ) {
	return;
}

$enc = $this->encoder();

$boxattr = '';
foreach( $this->get( 'boxAttributes', [] ) as $name => $value ) {
	$boxattr .= $name . ( $value != null ? '="' . $enc->attr( $value ) . '"' : '' ) . ' ';
}

$itemattr = '';
foreach( $this->get( 'itemAttributes', [] ) as $name => $value ) {
	$itemattr .= $name . ( $value != null ? '="' . $enc->attr( $value ) . '"' : '' ) . ' ';
}

?>
<?php switch( current( explode( '/', $item->getMimetype() ) ) ) : case 'audio': ?>

	<audio <?= $boxattr; ?> >
		<source src="<?= $enc->attr( $this->content( $item->getUrl() ) ); ?>" title="<?= $enc->attr( $item->getName() ); ?>" type="<?= $enc->attr( $item->getMimetype() ); ?>" <?= $itemattr; ?> />
		<?php foreach( $item->getRefItems( 'media' ) as $item ) : ?>
			<source src="<?= $enc->attr( $this->content( $item->getUrl() ) ); ?>" title="<?= $enc->attr( $item->getName() ); ?>" type="<?= $enc->attr( $item->getMimetype() ); ?>" <?= $itemattr; ?> />
		<?php endforeach; ?>
		<?= $enc->html( $item->getName() ); ?>
	</audio>

	<?php break; case 'video': ?>

		<video <?= $boxattr; ?> >
			<source src="<?= $enc->attr( $this->content( $item->getUrl() ) ); ?>" title="<?= $enc->attr( $item->getName() ); ?>" type="<?= $enc->attr( $item->getMimetype() ); ?>" <?= $itemattr; ?> />
		<?php foreach( $item->getRefItems( 'media' ) as $item ) : ?>
			<source src="<?= $enc->attr( $this->content( $item->getUrl() ) ); ?>" title="<?= $enc->attr( $item->getName() ); ?>" type="<?= $enc->attr( $item->getMimetype() ); ?>" <?= $itemattr; ?> />
		<?php endforeach; ?>
		<?= $enc->html( $item->getName() ); ?>
	</video>

	<?php break; case 'image': ?>

		<div <?= $boxattr; ?> ><!--
			--><img src="<?= $enc->attr( $this->content( $item->getPreview() ) ); ?>" title="<?= $enc->attr( $item->getName() ); ?>" <?= $itemattr; ?> /><!--
		<?php foreach( $item->getRefItems( 'media' ) as $item ) : ?>
			--><img src="<?= $enc->attr( $this->content( $item->getPreview() ) ); ?>" title="<?= $enc->attr( $item->getName() ); ?>" <?= $itemattr; ?> /><!--
		<?php endforeach; ?>
		--></div>

	<?php break; default: ?>

		<a href="<?= $enc->attr( $this->content( $item->getUrl() ) ); ?>" <?= $boxattr ?> ><!--
			--><img src="<?= $enc->attr( $this->content( $item->getPreview() ) ); ?>" title="<?= $enc->attr( $item->getName() ); ?>" <?= $itemattr ?> /><!--
			<?= $enc->html( $item->getName() ); ?>
		--></a>

<?php endswitch; ?>
