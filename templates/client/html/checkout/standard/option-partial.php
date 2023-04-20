<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


 $enc = $this->encoder();


/** client/html/checkout/standard/summary/option/terms/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2014.03
 * @see client/html/checkout/standard/summary/option/terms/url/controller
 * @see client/html/checkout/standard/summary/option/terms/url/action
 * @see client/html/checkout/standard/summary/option/terms/url/config
 * @see client/html/checkout/standard/summary/option/terms/url/filter
 */

/** client/html/checkout/standard/summary/option/terms/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2014.03
 * @see client/html/checkout/standard/summary/option/terms/url/target
 * @see client/html/checkout/standard/summary/option/terms/url/action
 * @see client/html/checkout/standard/summary/option/terms/url/config
 * @see client/html/checkout/standard/summary/option/terms/url/filter
 */

/** client/html/checkout/standard/summary/option/terms/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2014.03
 * @see client/html/checkout/standard/summary/option/terms/url/target
 * @see client/html/checkout/standard/summary/option/terms/url/controller
 * @see client/html/checkout/standard/summary/option/terms/url/config
 * @see client/html/checkout/standard/summary/option/terms/url/filter
 */

/** client/html/checkout/standard/summary/option/terms/url/config
 * Associative list of configuration options used for generating the URL
 *
 * You can specify additional options as key/value pairs used when generating
 * the URLs, like
 *
 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
 *
 * The available key/value pairs depend on the application that embeds the e-commerce
 * framework. This is because the infrastructure of the application is used for
 * generating the URLs. The full list of available config options is referenced
 * in the "see also" section of this page.
 *
 * @param string Associative list of configuration options
 * @since 2014.03
 * @see client/html/checkout/standard/summary/option/terms/url/target
 * @see client/html/checkout/standard/summary/option/terms/url/controller
 * @see client/html/checkout/standard/summary/option/terms/url/action
 * @see client/html/checkout/standard/summary/option/terms/url/filter
 */

/** client/html/checkout/standard/summary/option/terms/url/filter
 * Removes parameters for the detail page before generating the URL
 *
 * This setting removes the listed parameters from the URLs. Keep care to
 * remove no required parameters!
 *
 * @param array List of parameter names to remove
 * @since 2022.10
 * @see client/html/checkout/standard/summary/option/terms/url/target
 * @see client/html/checkout/standard/summary/option/terms/url/controller
 * @see client/html/checkout/standard/summary/option/terms/url/action
 * @see client/html/checkout/standard/summary/option/terms/url/config
 */


/** client/html/checkout/standard/summary/option/terms/privacy/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2014.03
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/controller
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/action
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/config
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/filter
 */

/** client/html/checkout/standard/summary/option/terms/privacy/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2014.03
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/target
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/action
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/config
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/filter
 */

/** client/html/checkout/standard/summary/option/terms/privacy/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2014.03
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/target
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/controller
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/config
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/filter
 */

/** client/html/checkout/standard/summary/option/terms/privacy/url/config
 * Associative list of configuration options used for generating the URL
 *
 * You can specify additional options as key/value pairs used when generating
 * the URLs, like
 *
 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
 *
 * The available key/value pairs depend on the application that embeds the e-commerce
 * framework. This is because the infrastructure of the application is used for
 * generating the URLs. The full list of available config options is referenced
 * in the "see also" section of this page.
 *
 * @param string Associative list of configuration options
 * @since 2014.03
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/target
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/controller
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/action
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/filter
 */

/** client/html/checkout/standard/summary/option/terms/privacy/url/filter
 * Removes parameters for the detail page before generating the URL
 *
 * This setting removes the listed parameters from the URLs. Keep care to
 * remove no required parameters!
 *
 * @param array List of parameter names to remove
 * @since 2022.10
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/target
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/controller
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/action
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/config
 */


/** client/html/checkout/standard/summary/option/terms/cancel/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2014.03
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/controller
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/action
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/config
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/filter
 */

/** client/html/checkout/standard/summary/option/terms/cancel/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2014.03
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/target
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/action
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/config
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/filter
 */

/** client/html/checkout/standard/summary/option/terms/cancel/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2014.03
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/target
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/controller
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/config
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/filter
 */

/** client/html/checkout/standard/summary/option/terms/cancel/url/config
 * Associative list of configuration options used for generating the URL
 *
 * You can specify additional options as key/value pairs used when generating
 * the URLs, like
 *
 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
 *
 * The available key/value pairs depend on the application that embeds the e-commerce
 * framework. This is because the infrastructure of the application is used for
 * generating the URLs. The full list of available config options is referenced
 * in the "see also" section of this page.
 *
 * @param string Associative list of configuration options
 * @since 2014.03
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/target
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/controller
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/action
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/filter
 */

/** client/html/checkout/standard/summary/option/terms/cancel/url/filter
 * Removes parameters for the detail page before generating the URL
 *
 * This setting removes the listed parameters from the URLs. Keep care to
 * remove no required parameters!
 *
 * @param array List of parameter names to remove
 * @since 2022.10
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/target
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/controller
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/action
 * @see client/html/checkout/standard/summary/option/terms/cancel/url/config
 */


?>
<?php if( !$this->standardBasket->getCustomerId() ) : ?>
	<div class="checkout-standard-summary-option-account col-sm-12">
		<h3><?= $enc->html( $this->translate( 'client', 'Customer account' ), $enc::TRUST ) ?></h3>

		<div class="single <?= !$this->value( $this->get( 'errors', [] ), 'option/account' ) ?: 'error' ?>">
			<input id="option-account" type="checkbox" value="1"
				name="<?= $enc->attr( $this->formparam( array( 'cs_option_account' ) ) ) ?>"
				<?= ( $this->param( 'cs_option_account', 1 ) == 1 ? 'checked="checked"' : '' ) ?>
			>
			<p>
				<label for="option-account">
					<?= $enc->html( $this->translate( 'client', 'Create a customer account for me' ), $enc::TRUST ) ?>
				</label>
			</p>
		</div>
	</div>
<?php endif ?>

<div class="checkout-standard-summary-option-terms col-sm-12">
	<h3><?= $enc->html( $this->translate( 'client', 'Terms and conditions' ), $enc::TRUST ) ?></h3>

	<div class="single <?= !$this->value( $this->get( 'errors', [] ), 'option/terms' ) ?: 'error' ?>">
		<input type="hidden" name="<?= $enc->attr( $this->formparam( array( 'cs_option_terms' ) ) ) ?>" value="1">
		<input id="option-terms-accept" type="checkbox" value="1"
			name="<?= $enc->attr( $this->formparam( array( 'cs_option_terms_value' ) ) ) ?>"
			<?= ( $this->param( 'cs_option_terms_value', null ) == 1 ? 'checked="checked"' : '' ) ?>
		>

		<p>
			<label for="option-terms-accept">
				<?= $enc->html( sprintf( $this->translate( 'client',
					'I accept the <a href="%1$s" target="_blank" title="terms and conditions" alt="terms and conditions">terms and conditions</a>, <a href="%2$s" target="_blank" title="privacy policy" alt="privacy policy">privacy policy</a> and <a href="%3$s" target="_blank" title="cancellation policy" alt="cancellation policy">cancellation policy</a>' ),
					$enc->attr( $this->link( 'client/html/checkout/standard/summary/option/terms/url', ['path' => 'terms'] ) ),
					$enc->attr( $this->link( 'client/html/checkout/standard/summary/option/terms/privacy/url', ['path' => 'privacy'] ) ),
					$enc->attr( $this->link( 'client/html/checkout/standard/summary/option/terms/cancel/url', ['path' => 'cancel'] ) )
				), $enc::TRUST ) ?>
			</label>
		</p>

	</div>
</div>
