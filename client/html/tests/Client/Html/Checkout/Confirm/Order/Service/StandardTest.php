<?php

namespace Aimeos\Client\Html\Checkout\Confirm\Order\Service;


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

		$this->object = new \Aimeos\Client\Html\Checkout\Confirm\Order\Service\Standard( $this->context, $paths );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<div class="common-summary-service', $output );
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
