<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();


?>
<link class="account-watch" rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/account-watch.css', 'fs-theme', true ) ) ?>">
<script defer class="account-watch" src="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/account-watch.js', 'fs-theme', true ) ) ?>"></script>
