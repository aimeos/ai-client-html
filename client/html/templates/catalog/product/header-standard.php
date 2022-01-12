<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2022
 */

$enc = $this->encoder();


?>
<link rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSite', 'default' ) . '/catalog.css', 'fs-theme', true ) ) ?>">
<link rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSite', 'default' ) . '/catalog-product.css', 'fs-theme', true ) ) ?>">

<script defer src="<?= $enc->attr( $this->content( $this->get( 'contextSite', 'default' ) . '/catalog.js', 'fs-theme', true ) ) ?>"></script>
<script defer src="<?= $enc->attr( $this->content( $this->get( 'contextSite', 'default' ) . '/catalog-product.js', 'fs-theme', true ) ) ?>"></script>

<?php if( isset( $this->itemsStockUrl ) ) : ?>
	<?php foreach( $this->itemsStockUrl as $url ) : ?>
		<script defer src="<?= $enc->attr( $url ) ?>"></script>
	<?php endforeach ?>
<?php endif ?>
