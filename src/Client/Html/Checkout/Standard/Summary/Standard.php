<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Checkout\Standard\Summary;


// Strings for translation
sprintf( 'summary' );


/**
 * Default implementation of checkout summary HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string
	{
		$view = $this->view();
		$step = $view->get( 'standardStepActive' );
		$onepage = $view->config( 'client/html/checkout/standard/onepage', [] );

		if( $step != 'summary' && !( in_array( 'summary', $onepage ) && in_array( $step, $onepage ) ) ) {
			return '';
		}

		return parent::body( $uid );
	}


	/**
	 * Processes the input, e.g. store given values.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function init()
	{
		$result = true;
		$view = $this->view();

		try
		{
			if( $view->param( 'cs_order', null ) === null ) {
				return $result;
			}


			$context = $this->context();
			$controller = \Aimeos\Controller\Frontend::create( $context, 'basket' );
			$customerref = strip_tags( $view->param( 'cs_customerref', '', false ) );
			$comment = strip_tags( $view->param( 'cs_comment', '', false ) );

			if( $customerref || $comment )
			{
				$controller->get()->setCustomerReference( $customerref )->setComment( $comment );
				$controller->save();
			}


			// only start if there's something to do
			if( $view->param( 'cs_option_terms', null ) !== null
				&& ( $option = $view->param( 'cs_option_terms_value', 0 ) ) != 1
			) {
				$error = $view->translate( 'client', 'Please accept the terms and conditions' );
				$errors = $view->get( 'summaryErrorCodes', [] );
				$errors['option']['terms'] = $error;

				$view->summaryErrorCodes = $errors;
				$view->standardStepActive = 'summary';
				$view->errors = array_merge( $view->get( 'errors', [] ), array( $error ) );
			}


			parent::init();

			$controller->get()->check( $context->config()->get( 'mshop/order/manager/subdomains', [] ) );
		}
		catch( \Exception $e )
		{
			$view->standardStepActive = 'summary';
			throw $e;
		}
	}


	/** client/html/checkout/standard/summary/template-body
	 * Relative path to the HTML body template of the checkout standard summary client.
	 *
	 * The template file contains the HTML code and processing instructions
	 * to generate the result shown in the body of the frontend. The
	 * configuration string is the path to the template file relative
	 * to the templates directory (usually in templates/client/html).
	 *
	 * You can overwrite the template file configuration in extensions and
	 * provide alternative templates. These alternative templates should be
	 * named like the default one but suffixed by
	 * an unique name. You may use the name of your project for this. If
	 * you've implemented an alternative client class as well, it
	 * should be suffixed by the name of the new class.
	 *
	 * @param string Relative path to the template creating code for the HTML page body
	 * @since 2014.03
	 * @see client/html/checkout/standard/summary/template-header
	 */
}
