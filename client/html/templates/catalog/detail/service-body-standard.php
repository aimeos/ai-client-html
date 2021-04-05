<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */

$enc = $this->encoder();


?>
<?php $this->block()->start( 'catalog/detail/service' ) ?>
<div class="catalog-detail-service">

	<?php if( !$this->get( 'serviceItems', map() )->isEmpty() ) : ?>
		<span class="service-intro"><?= $enc->html( $this->translate( 'client', '+ shipping costs' ) ) ?></span>
		<ul class="service-list">

			<?php foreach( $this->get( 'serviceItems', [] ) as $item ) : ?>
				<li class="service-item">
					<span class="service-name"><?= $enc->html( $item->getName() ) ?></span>

					<?= $this->partial(
						$this->config( 'client/html/common/partials/price', 'common/partials/price-standard' ),
						array( 'prices' => $item->getRefItems( 'price', null, 'default' ), 'costsItem' => false, 'all' => true )
					) ?>

					<?php foreach( $item->getRefItems( 'text', 'short', 'default' ) as $textItem ) : ?>
						<span class="service-short"><?= $enc->html( $textItem->getContent() ) ?></span>
					<?php endforeach ?>
				</li>

			<?php endforeach ?>

		</ul>
	<?php endif ?>

</div>
<?php $this->block()->stop() ?>
