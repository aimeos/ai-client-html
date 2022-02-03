<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();

?>
<link rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/catalog-filter.css', 'fs-theme', true ) ) ?>">
<script defer src="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/catalog-filter.js', 'fs-theme', true ) ) ?>"></script>

<?php if( ( $url = $this->get( 'filterCountUrl' ) ) != null ) : ?>
	<script defer src="<?= $enc->attr( $url ) ?>"></script>
<?php endif ?>
