<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();


?>
<section class="aimeos locale-select" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">

	<?= $this->get( 'body' ) ?>

</section>
