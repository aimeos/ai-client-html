<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */

$enc = $this->encoder();


$target = $this->config( 'client/html/catalog/tree/url/target' );
$controller = $this->config( 'client/html/catalog/tree/url/controller', 'catalog' );
$action = $this->config( 'client/html/catalog/tree/url/action', 'list' );
$config = $this->config( 'client/html/catalog/tree/url/config', [] );


?>
<div class="list-container level-<?= $enc->attr( $this->get( 'level', 0 ) ) ?>">
	<?php foreach( $this->get( 'nodes', [] ) as $item ) : ?>

		<?php if( $item->getStatus() > 0 ) : ?>

			<div class="cat-item catid-<?= $enc->attr( $item->getId()
				. ( $item->hasChildren() ? ' has-submenu withchild ' : ' nochild ' )
				. ( $this->get( 'path', map() )->getId()->last() == $item->getId() ? ' active' : '' )
				. ' catcode-' . $item->getCode() . ' ' . $item->getConfigValue( 'css-class' ) ) ?>"
				data-id="<?= $item->getId() ?>">

				<?php if( $item->hasChildren() ) : ?>
					<div class="row item-links">
						<a class="col-10 item-link" href="<?= $enc->attr( $this->url( $item->getTarget() ?: $target, $controller, $action, array_merge( $this->get( 'params', [] ), ['f_name' => $item->getName( 'url' ), 'f_catid' => $item->getId()] ), [], $config ) ) ?>"><?= $enc->html( $item->getName(), $enc::TRUST ) ?></a>
						<a class="col-2 data-link" data-submenu="<?= $enc->html( $item->getName(), $enc::TRUST ) ?>" href="#"></a>
					</div>
				<?php else : ?>
					<div class="item-links">
						<a class="item-link" href="<?= $enc->attr( $this->url( $item->getTarget() ?: $target, $controller, $action, array_merge( $this->get( 'params', [] ), ['f_name' => $item->getName( 'url' ), 'f_catid' => $item->getId()] ), [], $config ) ) ?>"><?= $enc->html( $item->getName(), $enc::TRUST ) ?></a>
					</div>
				<?php endif ?>

				<a class="cat-item <?= $enc->attr( ( $this->get( 'path', map() )->getId()->last() == $item->getId() ? ' active ' : '' ) ) ?>" href="<?= $enc->attr( $this->url( $item->getTarget() ?: $target, $controller, $action, array_merge( $this->get( 'params', [] ), ['f_name' => $item->getName( 'url' ), 'f_catid' => $item->getId()] ), [], $config ) ) ?>"><!--
					--><div class="media-list"><!--
						<?php foreach( $item->getRefItems( 'media', 'icon', 'default' ) as $mediaItem ) : ?>
							<?= '-->' . $this->partial(
								$this->config( 'client/html/common/partials/media', 'common/partials/media-standard' ),
								array( 'item' => $mediaItem, 'boxAttributes' => array( 'class' => 'media-item' ) )
							) . '<!--' ?>
						<?php endforeach ?>
					--></div><!--
					--><span class="cat-name"><?= $enc->html( $item->getName(), $enc::TRUST ) ?></span>
				</a>

				<?php if( count( $item->getChildren() ) > 0 ) : ?>

					<div id="<?= $enc->html( $item->getName(), $enc::TRUST ) ?>" class="submenu <?= $enc->attr(
						( $item->hasChildren() ? '' : ' nochild ' )
						. ( $this->get( 'path', map() )->getId()->last() == $item->getId() ? ' active' : '' ) ) ?>">

						<div class="submenu-header row">
							<a class="col-2" href="#" data-submenu-close="<?= $enc->html( $item->getName(), $enc::TRUST ) ?>"><span class="arrow-back"></span></a>
							<a class="col-7" href="#" data-submenu-close="<?= $enc->html( $item->getName(), $enc::TRUST ) ?>"><span><?= $enc->html( $item->getName(), $enc::TRUST ) ?></span></a>
							<div class="menu-close col-3"></div>
						</div>

						<?= $this->partial( $this->config( 'client/html/catalog/filter/partials/tree', 'catalog/filter/tree-partial-2ndlvl' ), [
							'nodes' => $item->getChildren(),
							'path' => $this->get( 'path', map() ),
							'level' => $this->get( 'level', 0 ) + 1,
							'params' => $this->get( 'params', [] )
						] ) ?>
					</div>

				<?php endif ?>

			</div>

		<?php endif ?>
	<?php endforeach ?>
</div>
