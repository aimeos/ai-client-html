<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */

/** Available data
 * - service : Order service item (delivery or payment)
 * - type : Service type (delivery or payment)
 */

$service = $this->service;
echo strip_tags( $service->getName() ) . "\n";

foreach( $service->getAttributes() as $attribute )
{
	if( $attribute->getType() === $this->type )
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
