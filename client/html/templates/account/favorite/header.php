<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();


?>
<link class="account-favorite" rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/account-favorite.css', 'fs-theme', true ) ) ?>">
<script defer class="account-favorite" src="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/account-favorite.js', 'fs-theme', true ) ) ?>"></script>
