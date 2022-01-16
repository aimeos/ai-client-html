<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();


?>
<link rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSite', 'default' ) . '/catalog.css', 'fs-theme', true ) ) ?>">
<link class="account-favorite" rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSite', 'default' ) . '/account-favorite.css', 'fs-theme', true ) ) ?>">
<script defer class="account-favorite" src="<?= $enc->attr( $this->content( $this->get( 'contextSite', 'default' ) . '/account-favorite.js', 'fs-theme', true ) ) ?>"></script>

<?= $this->get( 'favoriteHeader' ) ?>
