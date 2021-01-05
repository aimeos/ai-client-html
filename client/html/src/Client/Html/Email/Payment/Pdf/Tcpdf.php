<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Email\Payment\Pdf;


/**
 * Extended TCPDF class
 *
 * @package Client
 * @subpackage Html
 */
class Tcpdf extends \TCPDF
{
	private $headerFcn;
	private $footerFcn;


	/**
	 * Adds the footer to each page
	 */
	public function Footer()
	{
		if( $fcn = $this->footerFcn ) {
			$fcn( $this );
		}
	}


	/**
	 * Adds the header to each page
	 */
	public function Header()
	{
		if( $fcn = $this->headerFcn ) {
			$fcn( $this );
		}
	}


	/**
	 * Sets the anonymous function which adds the page footer
	 *
	 * @param \Closure $fcn Function that adds the page footer
	 * @return \Aimeos\Client\Html\Email\Payment\Pdf\Tcpdf Same object for method chaining
	 */
	public function setFooterFunction( \Closure $fcn )
	{
		$this->footerFcn = $fcn;
		return $this;
	}


	/**
	 * Sets the anonymous function which adds the page header
	 *
	 * @param \Closure $fcn Function that adds the page header
	 * @return \Aimeos\Client\Html\Email\Payment\Pdf\Tcpdf Same object for method chaining
	 */
	public function setHeaderFunction( \Closure $fcn )
	{
		$this->headerFcn = $fcn;
		return $this;
	}
}
