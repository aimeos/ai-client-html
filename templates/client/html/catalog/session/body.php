<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();


?>
<section class="aimeos catalog-session" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">

	<?= $this->block()->get( 'catalog/session/pinned' ) ?>
	<?= $this->block()->get( 'catalog/session/seen' ) ?>

</section>
