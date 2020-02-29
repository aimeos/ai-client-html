<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2020
 */

$enc = $this->encoder();

$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', [] );
$detailFilter = array_flip( $this->config( 'client/html/catalog/detail/url/filter', ['d_prodid'] ) );


?>
<?php if( isset( $this->seenProductItem ) ) : $productItem = $this->seenProductItem; ?>

	<?php $params = array_diff_key( ['d_name' => $productItem->getName( 'url' ), 'd_prodid' => $productItem->getId(), 'd_pos' => ''], $detailFilter ); ?>

	<a href="<?= $enc->attr( $this->url( $detailTarget, $detailController, $detailAction, $params, [], $detailConfig ) ); ?>">

		<?php if( ( $mediaItem = $productItem->getRefItems( 'media', 'default', 'default' )->first() ) !== null ) : ?>
			<div class="media-item" style="background-image: url('<?= $this->content( $mediaItem->getPreview() ); ?>')"></div>
		<?php else : ?>
			<div class="media-item"></div>
		<?php endif; ?>

		<h3 class="name"><?= $enc->html( $productItem->getName(), $enc::TRUST ); ?></h3>

		<div class="price-list">
			<?= $this->partial(
				$this->config( 'client/html/common/partials/price', 'common/partials/price-standard' ),
				array( 'prices' => $productItem->getRefItems( 'price', null, 'default' ) )
			); ?>
		</div>

	</a>
<?php endif; ?>
