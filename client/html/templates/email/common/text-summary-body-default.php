<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$addresses = $this->summaryBasket->getAddresses();
$services = $this->summaryBasket->getServices();


?>
<?php $this->block()->start( 'email/common/text/summary' ); ?>

<?php
	echo strip_tags( $this->translate( 'client', 'Billing address' ) ) . ":\n";

	if( isset( $addresses['payment'] ) )
	{
		echo $this->partial(
			$this->config( 'client/html/common/summary/address', 'common/summary/address-default.php' ),
			array( 'address' => $addresses['payment'], 'type' => 'payment' )
		);
	}
?>


<?php
	echo strip_tags( $this->translate( 'client', 'Delivery address' ) ) . ":\n";

	if( isset( $addresses['delivery'] ) )
	{
		echo $this->partial(
			$this->config( 'client/html/common/summary/address', 'common/summary/address-default.php' ),
			array( 'address' => $addresses['delivery'], 'type' => 'delivery' )
		);
	}
	else
	{
		echo $this->translate( 'client', 'like billing address' );
	}
?>



<?php
	echo strip_tags( $this->translate( 'client', 'delivery' ) ) . ': ';

	if( isset( $services['delivery'] ) )
	{
		$service = $services['delivery'];
		echo strip_tags( $service->getName() ) . "\n";

		foreach( $service->getAttributes() as $attribute )
		{
			if( $attribute->getType() === 'delivery' )
			{
				$name = ( $attribute->getName() != '' ? $attribute->getName() : $this->translate( 'client/code', $attribute->getCode() ) );

				switch( $attribute->getValue() )
				{
					case 'array':
					case 'object':
						$value = join( ', ', (array) $attribute->getValue() );
						break;
					default:
						$value = $attribute->getValue();
				}

				echo '- ' . strip_tags( $name ) . ': ' . strip_tags( $value ) . "\n";
			}
		}
	}
?>

<?php
	echo strip_tags( $this->translate( 'client', 'payment' ) ) . ': ';

	if( isset( $services['payment'] ) )
	{
		$service = $services['payment'];
		echo strip_tags( $service->getName() ) . "\n";

		foreach( $service->getAttributes() as $attribute )
		{
			if( $attribute->getType() === 'payment' )
			{
				$name = ( $attribute->getName() != '' ? $attribute->getName() : $this->translate( 'client/code', $attribute->getCode() ) );

				switch( $attribute->getValue() )
				{
					case 'array':
					case 'object':
						$value = join( ', ', (array) $attribute->getValue() );
						break;
					default:
						$value = $attribute->getValue();
				}

				echo '- ' . strip_tags( $name ) . ': ' . strip_tags( $value ) . "\n";
			}
		}
	}
?>


<?php
	echo strip_tags( $this->translate( 'client', 'Coupons' ) ) . ":\n";

	foreach( $this->extOrderBaseItem->getCoupons() as $code => $products ) {
		echo '- ' . $code . "\n";
	}
?>

<?php
	echo strip_tags( $this->translate( 'client', 'Your comment' ) ) . ":\n";
	echo strip_tags( $this->summaryBasket->getComment() ) . "\n";
?>


<?php
	echo $this->partial(
		$this->config( 'client/html/email/common/summary/text/detail', 'email/common/text-summary-detail-partial-default.php' ),
		array(
			'summaryBasket' => $this->summaryBasket,
			'summaryTaxRates' => $this->get( 'summaryTaxRates', array() ),
			'summaryShowDownloadAttributes' => $this->get( 'summaryShowDownloadAttributes', false ),
		)
	);
?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/common/text/summary' ); ?>
