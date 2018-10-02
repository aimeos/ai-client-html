<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

$enc = $this->encoder();
$level = $this->get( 'level', 0 );
$path = $this->get( 'path', [] );
$params = $this->get( 'params', [] );

$target = $this->config( 'client/html/catalog/lists/url/target' );
$controller = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
$action = $this->config( 'client/html/catalog/lists/url/action', 'list' );
$config = $this->config( 'client/html/catalog/lists/url/config', [] );

/** client/html/common/partials/media
 * Relative path to the media partial template file
 *
 * Partials are templates which are reused in other templates and generate
 * reoccuring blocks filled with data from the assigned values. The media
 * partial creates an HTML block of for images, video, audio or other documents.
 *
 * The partial template files are usually stored in the templates/partials/ folder
 * of the core or the extensions. The configured path to the partial file must
 * be relative to the templates/ folder, e.g. "common/partials/media-standard.php".
 *
 * @param string Relative path to the template file
 * @since 2015.08
 * @category Developer
 */


?>
<ul class="level-<?= $enc->attr( $level ); ?>">
	<?php foreach( $this->get( 'nodes', [] ) as $item ) : ?>
		<?php if( $item->getStatus() > 0 ) : ?>

			<?php $id = $item->getId(); $config = $item->getConfig(); ?>
			<?php $params['f_name'] = $item->getName( 'url' ); $params['f_catid'] = $id; ?>
			<?php $class = ( $item->hasChildren() ? ' withchild' : ' nochild' ) . ( isset( $path[$id] ) ? ' active' : '' ); ?>
			<?php $class .= ' catcode-' . $item->getCode() . ( isset( $config['css-class'] ) ? ' ' . $config['css-class'] : '' ); ?>

			<li class="cat-item catid-<?= $enc->attr( $id . $class ); ?>" data-id="<?= $id; ?>" >

				<a class="cat-item" href="<?= $enc->attr( $this->url( ( $item->getTarget() ?: $target ), $controller, $action, $params, [], $config ) ); ?>"><!--
					--><div class="media-list"><!--

						<?php foreach( $item->getListItems( 'media', 'icon' ) as $listItem ) : ?>
							<?php if( ( $mediaItem = $listItem->getRefItem() ) !== null ) : ?>
								<?= '-->' . $this->partial(
									$this->config( 'client/html/common/partials/media', 'common/partials/media-standard.php' ),
									array( 'item' => $mediaItem, 'boxAttributes' => array( 'class' => 'media-item' ) )
								) . '<!--'; ?>
							<?php endif; ?>
						<?php endforeach; ?>

					--></div><!--
					--><span class="cat-name"><?= $enc->html( $item->getName(), $enc::TRUST ); ?></span><!--
				--></a>

				<?php if( count( $item->getChildren() ) > 0 ) : ?>
					<?php $values = array( 'nodes' => $item->getChildren(), 'path' => $path, 'params' => $params, 'level' => $level + 1 ); ?>
					<?= $this->partial( $this->config( 'client/html/catalog/filter/partials/tree', 'catalog/filter/tree-standard.php' ), $values ); ?>
				<?php endif; ?>

			</li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>
