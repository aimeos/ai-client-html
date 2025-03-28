<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2025
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Common\Decorator;


/**
 * Provides context data for html client decorators.
 *
 * @package Client
 * @subpackage Html
 */
class Context extends Base implements Iface
{
	/**
	 * Adds the data to the view object required by the templates
	 *
	 * @param \Aimeos\Base\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\Base\View\Iface The view object with the data required by the templates
	 * @since 2020.07
	 */
	public function data( \Aimeos\Base\View\Iface $view, array &$tags = [], ?string &$expire = null ) : \Aimeos\Base\View\Iface
	{
		$context = $this->context();
		$locale = $context->locale();

		$view->contextLanguage = $locale->getLanguageId();
		$view->contextCurrency = $locale->getCurrencyId();
		$view->contextSiteIcon = $locale->getSiteItem()->getIcon();
		$view->contextSiteLogo = $locale->getSiteItem()->getLogo();
		$view->contextSiteLogos = $locale->getSiteItem()->getLogos();
		$view->contextSite = $locale->getSiteItem()->getCode();
		$view->contextSiteLabel = $locale->getSiteItem()->getLabel();
		$view->contextSiteTheme = $locale->getSiteItem()->getTheme();
		$view->contextSiteId = $locale->getSiteId();
		$view->contextNonce = $context->nonce();

		return $this->client()->data( $view, $tags, $expire );
	}
}
