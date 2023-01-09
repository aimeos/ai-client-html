<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Common\Decorator;


/**
 * Provides exception handling for HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Exceptions extends Base implements Iface
{
	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string
	{
		$output = '';
		$view = $this->view();
		$context = $this->context();

		try
		{
			$output = $this->client()->body( $uid );
		}
		catch( \Aimeos\Client\Html\Exception $e )
		{
			$error = [$context->translate( 'client', $e->getMessage() )];
			$view->errors = array_merge( $view->get( 'errors', [] ), $error );
		}
		catch( \Aimeos\Controller\Frontend\Exception $e )
		{
			if( $e->getCode() >= 400 ) {
				throw $e;
			}

			$error = [$context->translate( 'controller/frontend', $e->getMessage() )];
			$view->errors = array_merge( $view->get( 'errors', [] ), $error );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			if( $e->getCode() >= 400 ) {
				throw $e;
			}

			$error = [$context->translate( 'mshop', $e->getMessage() )];
			$view->errors = array_merge( $view->get( 'errors', [] ), $error );
		}
		catch( \Exception $e )
		{
			$error = [$context->translate( 'client', 'A non-recoverable error occured' )];
			$view->errors = array_merge( $view->get( 'errors', [] ), $error );
			$this->logException( $e );
		}

		return $view->render( 'error' ) . $output;
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function header( string $uid = '' ) : ?string
	{
		try
		{
			return $this->client()->header( $uid );
		}
		catch( \Exception $e )
		{
			if( $e->getCode() >= 400 ) {
				throw $e;
			}

			$this->logException( $e, \Aimeos\Base\Logger\Iface::NOTICE );
		}

		return null;
	}


	/**
	 * Processes the input, e.g. store given values.
	 */
	public function init()
	{
		$view = $this->view();
		$context = $this->context();

		try
		{
			$this->client()->init();
		}
		catch( \Aimeos\Client\Html\Exception $e )
		{
			$error = array( $context->translate( 'client', $e->getMessage() ) );
			$view->errors = array_merge( $view->get( 'errors', [] ), $error );
		}
		catch( \Aimeos\Controller\Frontend\Exception $e )
		{
			if( $e->getCode() >= 400 ) {
				throw $e;
			}

			$error = array( $context->translate( 'controller/frontend', $e->getMessage() ) );
			$view->errors = array_merge( $view->get( 'errors', [] ), $error );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			if( $e->getCode() >= 400 ) {
				throw $e;
			}

			$error = array( $context->translate( 'mshop', $e->getMessage() ) );
			$view->errors = array_merge( $view->get( 'errors', [] ), $error );
		}
		catch( \Exception $e )
		{
			$error = array( $context->translate( 'client', 'A non-recoverable error occured' ) );
			$view->errors = array_merge( $view->get( 'errors', [] ), $error );
			$this->logException( $e );
		}
	}
}
