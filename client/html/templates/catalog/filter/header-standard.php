<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();

?>
<link rel="stylesheet" href="<?= $enc->attr( $this->content( $this->contextSite . '/catalog-filter.css', 'fs-theme' ) ) ?>">
<script defer src="<?= $enc->attr( $this->content( $this->contextSite . '/catalog-filter.js', 'fs-theme' ) ) ?>"></script>

<?php if( ( $url = $this->get( 'filterCountUrl' ) ) != null ) : ?>
	<script defer src="<?= $enc->attr( $url ) ?>"></script>
<?php endif ?>

<?= $this->get( 'filterHeader' ) ?>
