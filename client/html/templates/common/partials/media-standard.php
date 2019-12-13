<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

if( !isset( $this->item ) ) {
	return;
}

$enc = $this->encoder();
$boxAttributes = $this->get( 'boxAttributes', [] );
$itemAttributes = $this->get( 'itemAttributes', [] );

$item = $this->item;
$url = $item->getUrl();
$previewUrl = $item->getPreview();
$parts = explode( '/', $item->getMimetype() );

$boxattr = '';
foreach( $boxAttributes as $name => $value ) {
	$boxattr .= $name . ( $value != null ? '="' . $enc->attr( $value ) . '"' : '' ) . ' ';
}

$itemattr = '';
foreach( $itemAttributes as $name => $value ) {
	$itemattr .= $name . ( $value != null ? '="' . $enc->attr( $value ) . '"' : '' ) . ' ';
}

?>
<?php switch( $parts[0] ) : case 'audio': ?>

	<audio <?= $boxattr; ?> >
		<source src="<?= $enc->attr( $this->content( $url ) ); ?>" title="<?= $enc->attr( $item->getName() ); ?>" type="<?= $enc->attr( $item->getMimetype() ); ?>" <?= $itemattr; ?> />
		<?php foreach( $item->getRefItems( 'media' ) as $item ) : ?>
			<source src="<?= $enc->attr( $this->content( $url ) ); ?>" title="<?= $enc->attr( $item->getName() ); ?>" type="<?= $enc->attr( $item->getMimetype() ); ?>" <?= $itemattr; ?> />
		<?php endforeach; ?>
		<?= $enc->html( $item->getName() ); ?>
	</audio>

	<?php break; case 'video': ?>

		<video <?= $boxattr; ?> >
			<source src="<?= $enc->attr( $this->content( $url ) ); ?>" title="<?= $enc->attr( $item->getName() ); ?>" type="<?= $enc->attr( $item->getMimetype() ); ?>" <?= $itemattr; ?> />
		<?php foreach( $item->getRefItems( 'media' ) as $item ) : ?>
			<source src="<?= $enc->attr( $this->content( $url ) ); ?>" title="<?= $enc->attr( $item->getName() ); ?>" type="<?= $enc->attr( $item->getMimetype() ); ?>" <?= $itemattr; ?> />
		<?php endforeach; ?>
		<?= $enc->html( $item->getName() ); ?>
	</video>

	<?php break; case 'image': ?>

		<div <?= $boxattr; ?> ><!--
			--><img src="<?= $enc->attr( $this->content( $previewUrl ) ); ?>" title="<?= $enc->attr( $item->getName() ); ?>" <?= $itemattr; ?> /><!--
		<?php foreach( $item->getRefItems( 'media' ) as $item ) : ?>
			--><img src="<?= $enc->attr( $this->content( $previewUrl ) ); ?>" title="<?= $enc->attr( $item->getName() ); ?>" <?= $itemattr; ?> /><!--
		<?php endforeach; ?>
		--></div>

	<?php break; default: ?>

		<a href="<?= $enc->attr( $this->content( $url ) ); ?>" <?= $boxattr ?> ><!--
			--><img src="<?= $enc->attr( $this->content( $previewUrl ) ); ?>" title="<?= $enc->attr( $item->getName() ); ?>" <?= $itemattr ?> /><!--
			<?= $enc->html( $item->getName() ); ?>
		--></a>

<?php endswitch; ?>
