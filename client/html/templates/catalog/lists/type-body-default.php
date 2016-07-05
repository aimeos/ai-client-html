<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */

$enc = $this->encoder();

$target = $this->config( 'client/html/catalog/lists/url/target' );
$cntl = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
$action = $this->config( 'client/html/catalog/lists/url/action', 'list' );
$config = $this->config( 'client/html/catalog/lists/url/config', array() );

$params = $this->get( 'listParams', array() );

?>
<?php $this->block()->start( 'catalog/lists/type' ); ?>
<div class="catalog-list-type">
	<a class="type-item type-grid" href="<?php echo $enc->attr( $this->url( $target, $cntl, $action, array( 'l_type' => 'grid' ) + $params, array(), $config ) ); ?>"></a>
	<a class="type-item type-list" href="<?php echo $enc->attr( $this->url( $target, $cntl, $action, array( 'l_type' => 'list' ) + $params, array(), $config ) ); ?>"></a>
<?php echo $this->get( 'typeBody' ); ?>
</div>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'catalog/lists/type' ); ?>
