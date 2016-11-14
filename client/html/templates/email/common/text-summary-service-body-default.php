<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


?>
<?php $this->block()->start( 'email/common/text/summary/service' ); ?>



<?php
	try
	{
		$service = $this->extOrderBaseItem->getService( 'delivery' );
		echo strip_tags( $this->translate( 'client', 'delivery' ) ) . ': ' . strip_tags( $service->getName() ) . "\n";

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
	} catch( \Exception $e ) { ; }
?>

<?php
	try
	{
		$service = $this->extOrderBaseItem->getService( 'payment' );
		echo strip_tags( $this->translate( 'client', 'payment' ) ) . ': ' . strip_tags( $service->getName() ) . "\n";

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
	} catch( \Exception $e ) { ; }
?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/common/text/summary/service' ); ?>
