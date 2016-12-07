<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
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

$listTarget = $this->config( 'client/html/catalog/lists/url/target' );
$listController = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
$listAction = $this->config( 'client/html/catalog/lists/url/action', 'list' );
$listConfig = $this->config( 'client/html/catalog/lists/url/config', array() );

$params = $this->get( 'stageParams', array() );


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


	<div class="catalog-stage-breadcrumb">
		<nav class="breadcrumb">
			<span class="title"><?php echo $enc->html( $this->translate( 'client', 'You are here:' ), $enc::TRUST ); ?></span>
			<ol>

				<?php if( isset( $this->stageCatPath ) ) : ?>
					<?php foreach( (array) $this->stageCatPath as $cat ) : $params['f_catid'] = $cat->getId(); ?>
						<li>
							<a href="<?php echo $enc->attr( $this->url( $listTarget, $listController, $listAction, $params, array( $cat->getName() ), $listConfig ) ); ?>">
								<?php echo $enc->html( $cat->getName() ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				<?php else : ?>
					<li>
						<a href="<?php echo $enc->attr( $this->url( $listTarget, $listController, $listAction, $params, array(), $listConfig ) ); ?>">
							<?php echo $enc->html( $this->translate( 'client', 'Your search result' ), $enc::TRUST ); ?>
						</a>
					</li>
				<?php endif; ?>

			</ol>
		</nav>
	</div>


	<?php echo $this->block()->get( 'catalog/stage/navigator' ); ?>

</section>
