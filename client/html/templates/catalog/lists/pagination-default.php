<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

$listTarget = $this->config( 'client/html/catalog/lists/url/target' );
$listController = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
$listAction = $this->config( 'client/html/catalog/lists/url/action', 'list' );
$listConfig = $this->config( 'client/html/catalog/lists/url/config', [] );


$params = $this->get( 'params', [] );
$sort = ( isset( $params['f_sort'] ) ? $params['f_sort'] : 'relevance' );
$sortname = ltrim( $sort, '-' );
$nameDir = $priceDir = '';

if( $sort === 'name' ) {
	$nameSort = $this->translate( 'client', '▼ Name' ); $nameDir = '-';
} else if( $sort === '-name' ) {
	$nameSort = $this->translate( 'client', '▲ Name' );
} else {
	$nameSort = $this->translate( 'client', 'Name' );
}

if( $sort === 'price' ) {
	$priceSort = $this->translate( 'client', '▼ Price' ); $priceDir = '-';
} else if( $sort === '-price' ) {
	$priceSort = $this->translate( 'client', '▲ Price' );
} else {
	$priceSort = $this->translate( 'client', 'Price' );
}


?>
<nav class="pagination">

	<div class="sort">
		<span><?php echo $enc->html( $this->translate( 'client', 'Sort by:' ), $enc::TRUST ); ?></span>
		<ul>
			<li>
				<?php $url = $this->url( $listTarget, $listController, $listAction, array( 'f_sort' => 'relevance' ) + $params, [], $listConfig ); ?>
				<a class="option-relevance <?php echo ( $sort === 'relevance' ? 'active' : '' ); ?>" href="<?php echo $enc->attr( $url ); ?>" >
					<?php echo $enc->html( $this->translate( 'client', 'Relevance' ), $enc::TRUST ); ?>
				</a>
			</li>
			<li>
				<?php $url = $this->url( $listTarget, $listController, $listAction, array( 'f_sort' => $nameDir . 'name' ) + $params, [], $listConfig ); ?>
				<a class="option-name <?php echo ( $sortname === 'name' ? 'active' : '' ); ?>" href="<?php echo $enc->attr( $url ); ?>">
					<?php echo $enc->html( $nameSort, $enc::TRUST ); ?>
				</a>
			</li>
			<li>
				<?php $url = $this->url( $listTarget, $listController, $listAction, array( 'f_sort' => $priceDir . 'price' ) + $params, [], $listConfig ); ?>
				<a class="option-price <?php echo ( $sortname === 'price' ? 'active' : '' ); ?>" href="<?php echo $enc->attr( $url ); ?>">
					<?php echo $enc->html( $priceSort, $enc::TRUST ); ?>
				</a>
			</li>
		</ul>
	</div>

	<?php if( $this->last > 1 ) : ?>
		<div class="browser">

			<?php $url = $this->url( $listTarget, $listController, $listAction, array( 'l_page' => 1 ) + $params, [], $listConfig ); ?>
			<a class="first" href="<?php echo $enc->attr( $url ); ?>">
				<?php echo $enc->html( $this->translate( 'client', '◀◀' ), $enc::TRUST ); ?>
			</a>

			<?php $url = $this->url( $listTarget, $listController, $listAction, array( 'l_page' => $this->prev ) + $params, [], $listConfig ); ?>
			<a class="prev" href="<?php echo $enc->attr( $url ); ?>" rel="prev">
				<?php echo $enc->html( $this->translate( 'client', '◀' ), $enc::TRUST ); ?>
			</a>

			<span><?php echo $enc->html( sprintf( $this->translate( 'client', 'Page %1$d of %2$d' ), $this->current, $this->last ) ); ?></span>

			<?php $url = $this->url( $listTarget, $listController, $listAction, array( 'l_page' => $this->next ) + $params, [], $listConfig ); ?>
			<a class="next" href="<?php echo $enc->attr( $url ); ?>" rel="next">
				<?php echo $enc->html( $this->translate( 'client', '▶' ), $enc::TRUST ); ?>
			</a>

			<?php $url = $this->url( $listTarget, $listController, $listAction, array( 'l_page' => $this->last ) + $params, [], $listConfig ); ?>
			<a class="last" href="<?php echo $enc->attr( $url ); ?>">
				<?php echo $enc->html( $this->translate( 'client', '▶▶' ), $enc::TRUST ); ?>
			</a>

		</div>
	<?php endif; ?>

</nav>
