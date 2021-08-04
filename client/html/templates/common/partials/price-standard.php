<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

/* Available data:
 * - prices : List of price items
 * - costsItems : Show "per item" for costs
 * - all: Show all properties including costs
 */


$prices = map();
$enc = $this->encoder();
$priceItems = map( $this->get( 'prices', [] ) );

foreach( $priceItems as $priceItem )
{
	$qty = (string) $priceItem->getQuantity();
	if( !( $p = $prices->get( $qty ) ) || $p->getValue() > $priceItem->getValue() ) {
		$prices[$qty] = $priceItem;
	}
}

$prices->ksort();
$price = $prices->getValue()->first();

$format = array(
	/// Price quantity format with quantity (%1$s)
	'quantity' => $this->translate( 'client', 'from %1$s' ),
	/// Price shipping format with shipping / payment cost value (%1$s) and currency (%2$s)
	'costs' => ( $this->get( 'costsItem', true ) ? $this->translate( 'client', '+ %1$s %2$s/item' ) : $this->translate( 'client', '%1$s %2$s' ) ),
	/// Rebate format with rebate value (%1$s) and currency (%2$s)
	'rebate' => $this->translate( 'client', '%1$s %2$s off' ),
	/// Rebate percent format with rebate percent value (%1$s)
	'rebate%' => $this->translate( 'client', '-%1$s%%' ),
);

/// Tax rate format with tax rate in percent (%1$s)
$withtax = $this->translate( 'client', 'Incl. %1$s%% VAT' );
/// Tax rate format with tax rate in percent (%1$s)
$notax = $this->translate( 'client', '+ %1$s%% VAT' );


?>
<meta itemprop="price" content="<?= $price ?>">

<?php foreach( $prices as $priceItem ) : ?>
	<?php
		if( $priceItem->getValue() > $price ) {
			continue; // Only show prices for higher quantities if they are lower then the first price
		}

		/// Price format with price value (%1$s) and currency (%2$s)
		$format['value'] = $this->translate( 'client/code', 'price:' . ( $priceItem->getType() ?: 'default' ), null, 0, false ) ?: $this->translate( 'client', '%1$s %2$s' );
		$currency = $this->translate( 'currency', $priceItem->getCurrencyId() );
	?>

	<div class="price-item <?= $enc->attr( $priceItem->getType() ) ?>" itemscope itemprop="priceSpecification" itemtype="http://schema.org/PriceSpecification">

		<meta itemprop="valueAddedTaxIncluded" content="<?= ( $priceItem->getTaxFlag() ? 'true' : 'false' ) ?>">
		<meta itemprop="priceCurrency" content="<?= $priceItem->getCurrencyId() ?>">
		<meta itemprop="price" content="<?= $priceItem->getValue() ?>">

		<span class="quantity" itemscope itemtype="http://schema.org/QuantitativeValue">
			<meta itemprop="minValue" content="<?= $priceItem->getQuantity() ?>">
			<?= $enc->html( sprintf( $format['quantity'], $priceItem->getQuantity() ), $enc::TRUST ) ?>
		</span>

		<span class="value">
			<?php if( $priceItem->getValue() !== null ) : ?>
				<?= $enc->html( sprintf( $format['value'], $this->number( $priceItem->getValue(), $priceItem->getPrecision() ), $currency ), $enc::TRUST ) ?>
			<?php else : ?>
				<?= $enc->html( $this->translate( 'client', 'on request' ) ) ?>
			<?php endif ?>
		</span>

		<?php if( $priceItem->getValue() > 0 && $priceItem->getRebate() > 0 ) : ?>
			<span class="rebate">
				<?= $enc->html( sprintf( $format['rebate'], $this->number( $priceItem->getRebate() ), $currency ), $enc::TRUST ) ?>
			</span>
			<span class="rebatepercent">
				<?= $enc->html( sprintf( $format['rebate%'], $this->number( round( $priceItem->getRebate() * 100 / ( $priceItem->getValue() + $priceItem->getRebate() ) ), 0 ) ), $enc::TRUST ) ?>
			</span>
		<?php endif ?>

		<?php if( $this->get( 'all' ) || $priceItem->getCosts() > 0 ) : ?>
			<span class="costs">
				<?= $enc->html( sprintf( $format['costs'], $this->number( $priceItem->getCosts(), $priceItem->getPrecision() ), $currency ), $enc::TRUST ) ?>
			</span>
		<?php endif ?>

		<?php if( $priceItem->getTaxrate() > 0 ) : ?>
			<span class="taxrate">
				<?= $enc->html( sprintf( ( $priceItem->getTaxFlag() ? $withtax : $notax ), $this->number( $priceItem->getTaxrate() ) ), $enc::TRUST ) ?>
			</span>
		<?php endif ?>
	</div>

<?php endforeach ?>
