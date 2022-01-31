<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();


?>
<title><?= $this->translate( 'client', 'Basket' ) ?> | <?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>

<link class="basket-standard" rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/summary.css', 'fs-theme', true ) ) ?>">
<link class="basket-standard" rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/basket-standard.css', 'fs-theme', true ) ) ?>">
<script defer class="basket-standard" src="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/basket-standard.js', 'fs-theme', true ) ) ?>"></script>
