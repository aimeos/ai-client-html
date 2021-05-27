<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */

$enc = $this->encoder();

?>
<title><?= $this->translate( 'client', 'Profile' ) ?> | <?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>

<?= $this->get( 'profileHeader' );
