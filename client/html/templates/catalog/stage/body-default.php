<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();
$catPath = (array) $this->get( 'stageCatPath', array() );

$classes = '';
foreach( $catPath as $cat )
{
	$config = $cat->getConfig();
	if( isset( $config['css-class'] ) ) {
		$classes .= ' ' . $config['css-class'];
	}
}

$mediaItems = array();
foreach( array_reverse( $catPath ) as $catItem )
{
	if( ( $mediaItems = $catItem->getRefItems( 'media', 'default', 'stage' ) ) !== array() ) {
		break;
	}
}


?>
<section class="aimeos catalog-stage<?php echo $enc->attr( $classes ); ?>">

	<?php if( isset( $this->stageErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->stageErrorList as $errmsg ) : ?>
				<li class="error-item"><?php echo $enc->html( $errmsg ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<div class="catalog-stage-image">
		<?php foreach( $mediaItems as $media ) : ?>
			<img src="<?php echo $this->content( $media->getUrl() ); ?>" alt="<?php echo $enc->attr( $media->getName() ); ?>" />
		<?php endforeach; ?>
	</div>

	<?php echo $this->block()->get( 'catalog/stage/breadcrumb' ); ?>
	<?php echo $this->block()->get( 'catalog/stage/navigator' ); ?>

</section>
