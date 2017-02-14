<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

$map = $this->get( 'selectMap', array() );
$params = $this->get( 'selectParams', array() );
$langId = $this->get( 'selectLanguageId', 'en' );
$currencyId = $this->get( 'selectCurrencyId', 'EUR' );
$currencies = ( isset( $map[$langId] ) ? (array) $map[$langId] : array() );

/** client/html/locale/select/currency/url/config
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
 * @since 2014.09
 * @category Developer
 */
$config = $this->config( 'client/html/locale/select/currency/url/config', array() );


?>
<?php $this->block()->start( 'locale/select/currency' ); ?>
<div class="locale-select-currency">
	<h2 class="header"><?php echo $this->translate( 'client', 'Select currency' ); ?></h2>

	<ul class="select-menu">
		<li class="select-dropdown select-current"><a href="#"><?php echo $this->translate( 'client/currency', $currencyId ); ?></a>
			<ul class="select-dropdown">

				<?php foreach( $currencies as $currency => $locParam ) : ?>
					<li class="select-item <?php echo ( $currency === $currencyId ? 'active' : '' ); ?>">
						<?php $target = $this->request()->getTarget(); ?>
						<?php $url = $this->url( $target, $this->param( 'controller' ), $this->param( 'action' ), array_merge( $params, $locParam ), array(), $config ); ?>
						<a href="<?php echo $enc->attr( $url ); ?>">
							<?php echo $enc->html( $this->translate( 'client/currency', $currency ), $enc::TRUST ); ?>
						</a>
					</li>
				<?php endforeach; ?>

			</ul>
		</li>
	</ul>

</div>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'locale/select/currency' ); ?>
