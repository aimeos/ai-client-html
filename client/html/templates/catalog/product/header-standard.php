<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2020
 */

$enc = $this->encoder();


?>
<?php if( isset( $this->itemsStockUrl ) ) : ?>
	<?php foreach( (array) $this->itemsStockUrl as $url ) : ?>
		<script type="text/javascript" defer="defer" src="<?= $enc->attr( $url ); ?>"></script>
	<?php endforeach ?>
<?php endif; ?>
