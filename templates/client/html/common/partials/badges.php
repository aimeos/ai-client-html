<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
 */

$enc = $this->encoder();


?>
<div class="badges">
	<span class="badge-item new"><?= $enc->html( $this->translate( 'client', 'New' ) ) ?></span>
	<span class="badge-item sale"><?= $enc->html( $this->translate( 'client', 'Sale' ) ) ?></span>
</div>
