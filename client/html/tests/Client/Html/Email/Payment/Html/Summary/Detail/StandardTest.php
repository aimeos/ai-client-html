<?php

namespace Aimeos\Client\Html\Email\Payment\Html\Summary\Detail;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->context = clone \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Email\Payment\Html\Summary\Detail\Standard( $this->context, $paths );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$this->setExpectedException( '\Aimeos\MW\View\Exception' );
		$this->object->getHeader();
	}


	public function testGetBody()
	{
		$this->setExpectedException( '\Aimeos\MW\View\Exception' );
		$this->object->getBody();
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}
}
