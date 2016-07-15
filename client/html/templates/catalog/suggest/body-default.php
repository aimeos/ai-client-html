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

$enc = $this->encoder();

?>
<?php foreach( $this->get( 'suggestItems', array() ) as $id => $item ) : ?>
<li><a class="suggest-item" href="<?php echo $enc->attr( $this->url( $target, $cntl, $action, array( 'd_prodid' => $id ), array(), $config ) ); ?>"><?php echo $enc->html( $item->getName() ); ?></a></li>
<?php endforeach; ?>
<?php echo $this->get( 'suggestBody' ); ?>
