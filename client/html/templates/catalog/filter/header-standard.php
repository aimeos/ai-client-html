<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

$enc = $this->encoder();

?>
<?php if( ( $url = $this->get( 'filterCountUrl' ) ) != null ) : ?>
	<script defer src="<?= $enc->attr( $url ) ?>"></script>
<?php endif ?>

<?= $this->get( 'filterHeader' ) ?>
