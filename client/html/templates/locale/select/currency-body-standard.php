<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

$enc = $this->encoder();

$map = $this->get( 'selectMap', [] );
$params = $this->get( 'selectParams', [] );
$langId = $this->get( 'selectLanguageId', 'en' );
$currencyId = $this->get( 'selectCurrencyId', 'EUR' );
$currencies = ( isset( $map[$langId] ) ? (array) $map[$langId] : [] );

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
$config = $this->config( 'client/html/locale/select/currency/url/config', [] );


?>
<?php $this->block()->start( 'locale/select/currency' ); ?>
<div class="locale-select-currency">
	<h2 class="header"><?= $this->translate( 'client', 'Select currency' ); ?></h2>

	<ul class="select-menu">
		<li class="select-dropdown select-current"><a href="#"><?= $this->translate( 'currency', $currencyId ); ?></a>
			<ul class="select-dropdown">

				<?php foreach( $currencies as $currency => $locParam ) : ?>
					<li class="select-item <?= ( $currency === $currencyId ? 'active' : '' ); ?>">
						<?php $target = $this->request()->getTarget(); ?>
						<?php $url = $this->url( $target, $this->param( 'controller' ), $this->param( 'action' ), array_merge( $params, $locParam ), [], $config ); ?>
						<a href="<?= $enc->attr( $url ); ?>">
							<?= $enc->html( $this->translate( 'currency', $currency ), $enc::TRUST ); ?>
						</a>
					</li>
				<?php endforeach; ?>

			</ul>
		</li>
	</ul>

</div>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'locale/select/currency' ); ?>
