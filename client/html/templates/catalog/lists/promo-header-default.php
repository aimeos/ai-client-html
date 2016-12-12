<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

$stockTarget = $this->config( 'client/html/catalog/stock/url/target' );
$stockCntl = $this->config( 'client/html/catalog/stock/url/controller', 'catalog' );
$stockAction = $this->config( 'client/html/catalog/stock/url/action', 'stock' );
$stockConfig = $this->config( 'client/html/catalog/stock/url/config', array() );


?>
<?php if( ( $productCodes = $this->get( 'promoProductCodes', array() ) ) !== array() ) : ?>
	<?php $url = $this->url( $stockTarget, $stockCntl, $stockAction, array( 's_proddcode' => $productCodes ), array(), $stockConfig ); ?>
	<script type="text/javascript" defer="defer" src="<?php echo $enc->attr( $url ); ?>"></script>
<?php endif; ?>
