<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

@header( 'HTTP/1.1 ' . $this->response()->getStatusCode() . ' ' . $this->response()->getReasonPhrase() );
echo $this->get( 'updateHeader' );

?>