<?php


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */
class TestHelperJobs
{
	private static $aimeos;
	private static $context;


	public static function bootstrap()
	{
		self::getAimeos();
		\Aimeos\MShop::cache( false );
	}


	public static function getContext( $site = 'unittest' )
	{
		if( !isset( self::$context[$site] ) ) {
			self::$context[$site] = self::createContext( $site );
		}

		return clone self::$context[$site];
	}


	public static function getAimeos()
	{
		if( !isset( self::$aimeos ) )
		{
			require_once 'Bootstrap.php';
			spl_autoload_register( 'Aimeos\\Bootstrap::autoload' );

			$extdir = dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) );
			self::$aimeos = new \Aimeos\Bootstrap( array( $extdir ), true );
		}

		return self::$aimeos;
	}


	public static function getControllerPaths()
	{
		return self::getAimeos()->getCustomPaths( 'controller/jobs' );
	}


	/**
	 * @param string $site
	 */
	private static function createContext( $site )
	{
		$ctx = new \Aimeos\MShop\Context\Item\Standard();
		$aimeos = self::getAimeos();


		$paths = $aimeos->getConfigPaths( 'mysql' );
		$paths[] = __DIR__ . DIRECTORY_SEPARATOR . 'config';
		$file = __DIR__ . DIRECTORY_SEPARATOR . 'confdoc.ser';

		$conf = new \Aimeos\MW\Config\PHPArray( [], $paths );
		$conf = new \Aimeos\MW\Config\Decorator\Memory( $conf );
		$conf = new \Aimeos\MW\Config\Decorator\Documentor( $conf, $file );
		$ctx->setConfig( $conf );


		$logger = new \Aimeos\MW\Logger\File( $site . '.log', \Aimeos\MW\Logger\Base::DEBUG );
		$ctx->setLogger( $logger );


		$dbm = new \Aimeos\MW\DB\Manager\PDO( $conf );
		$ctx->setDatabaseManager( $dbm );


		$fs = new \Aimeos\MW\Filesystem\Manager\Standard( $conf );
		$ctx->setFilesystemManager( $fs );


		$mq = new \Aimeos\MW\MQueue\Manager\Standard( $conf );
		$ctx->setMessageQueueManager( $mq );


		$session = new \Aimeos\MW\Session\None();
		$ctx->setSession( $session );


		$i18n = new \Aimeos\MW\Translation\None( 'de' );
		$ctx->setI18n( array( 'de' => $i18n ) );


		$localeManager = \Aimeos\MShop\Locale\Manager\Factory::create( $ctx );
		$locale = $localeManager->bootstrap( $site, 'de', '', false );
		$ctx->setLocale( $locale );


		$view = self::createView( $conf );
		$ctx->setView( $view );


		$ctx->setEditor( 'core:controller/jobs' );

		return $ctx;
	}


	protected static function createView( \Aimeos\MW\Config\Iface $config )
	{
		$tmplpaths = array_merge_recursive(
			self::getAimeos()->getCustomPaths( 'client/html/templates' ),
			self::getAimeos()->getCustomPaths( 'controller/jobs/templates' )
		);

		$view = new \Aimeos\MW\View\Standard( $tmplpaths );

		$trans = new \Aimeos\MW\Translation\None( 'de_DE' );
		$helper = new \Aimeos\MW\View\Helper\Translate\Standard( $view, $trans );
		$view->addHelper( 'translate', $helper );

		$helper = new \Aimeos\MW\View\Helper\Url\Standard( $view, 'http://baseurl' );
		$view->addHelper( 'url', $helper );

		$helper = new \Aimeos\MW\View\Helper\Number\Standard( $view, '.', '' );
		$view->addHelper( 'number', $helper );

		$helper = new \Aimeos\MW\View\Helper\Date\Standard( $view, 'Y-m-d' );
		$view->addHelper( 'date', $helper );

		$config = new \Aimeos\MW\Config\Decorator\Protect( $config, array( 'controller/jobs', 'client/html' ) );
		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		return $view;
	}
}
