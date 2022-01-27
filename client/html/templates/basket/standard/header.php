<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();


?>
<title><?= $this->translate( 'client', 'Basket' ) ?> | <?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>

<link class="basket" rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSite', 'default' ) . '/summary.css', 'fs-theme', true ) ) ?>">
<link class="basket" rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSite', 'default' ) . '/basket.css', 'fs-theme', true ) ) ?>">
<script defer class="basket" src="<?= $enc->attr( $this->content( $this->get( 'contextSite', 'default' ) . '/basket.js', 'fs-theme', true ) ) ?>"></script>

<?= $this->get( 'standardHeader' ) ?>
