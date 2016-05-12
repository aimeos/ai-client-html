<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$target = $this->config( 'client/html/catalog/detail/url/target' );
$cntl = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$action = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$config = $this->config( 'client/html/catalog/detail/url/config', array() );

$suggestTextItems = array();
$template = '<a class="suggest-item" href="%1$s">%2$s</a>';

foreach( $this->get( 'suggestTextItems', array() ) as $id => $name )
{
	$url = $this->url( $target, $cntl, $action, array( 'd_prodid' => $id ), array(), $config );
	$suggestTextItems[] = array( 'id' => $id, 'label' => $name, 'value' => sprintf( $template, $url, $name ) );
}

?>
<?php $this->block()->start( 'catalog/suggest' ); ?>
<?php echo json_encode( $suggestTextItems ); ?>
<?php echo $this->get( 'suggestBody' ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'catalog/suggest' ); ?>
