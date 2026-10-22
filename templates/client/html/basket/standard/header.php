<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */

$enc = $this->encoder();


?>
<title><?= $enc->html( sprintf( $this->config( 'client/html/common/title/format', '%1$s | %2$s' ), $this->translate( 'client', 'Basket' ), $this->get( 'contextSiteLabel', 'Aimeos' ) ) ) ?></title>

<link class="basket-standard" rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/summary.css', 'fs-theme', true ) ) ?>">
<link class="basket-standard" rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/basket-standard.css', 'fs-theme', true ) ) ?>">
<script defer class="basket-standard" src="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/basket-standard.js', 'fs-theme', true ) ) ?>"></script>
