<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

?>
<link rel="stylesheet" href="<?= $enc->attr( $this->content( $this->contextSite . '/catalog-session.css', 'fs-theme' ) ) ?>">
<script defer src="<?= $enc->attr( $this->content( $this->contextSite . '/catalog-session.js', 'fs-theme' ) ) ?>"></script>

<?= $this->get( 'sessionHeader' ) ?>
