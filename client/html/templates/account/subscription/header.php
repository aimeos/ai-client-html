<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2022
 */

$enc = $this->encoder();


?>
<link rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/account-subscription.css', 'fs-theme', true ) ) ?>">
<script defer src="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/account-subscription.js', 'fs-theme', true ) ) ?>"></script>
