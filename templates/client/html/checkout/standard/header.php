<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */

$enc = $this->encoder();


?>
<title><?= $enc->html( sprintf( $this->config( 'client/html/common/title/format', '%1$s | %2$s' ), $this->translate( 'client', $this->get( 'standardStepActive' ) ), $this->get( 'contextSiteLabel', 'Aimeos' ) ) ) ?></title>
<meta name="description" content="<?= $enc->attr( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?>">

<link rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/summary.css', 'fs-theme', true ) ) ?>">
<link rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/checkout-standard.css', 'fs-theme', true ) ) ?>">
<script defer src="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/checkout-standard.js', 'fs-theme', true ) ) ?>"></script>
