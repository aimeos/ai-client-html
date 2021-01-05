<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

@header( 'HTTP/1.1 ' . $this->response()->getStatusCode() . ' ' . $this->response()->getReasonPhrase() );

foreach( $this->response()->getHeaders() as $key => $value )
{
	foreach( (array) $value as $val ) {
		@header( $key . ': ' . $val );
	}
}

echo $this->get( 'updateHeader' );
