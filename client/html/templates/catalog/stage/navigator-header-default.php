<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();


?>
<?php if( $this->param( 'd_pos' ) !== null ) : ?>
<!-- catalog.stage.navigator -->

	<?php	if( isset( $this->navigationPrev ) ) : ?>
		<link rel="prev" href="<?php echo $enc->attr( $this->navigationPrev ); ?>" />
	<?php	endif; ?>

	<?php	if( isset( $this->navigationNext ) ) : ?>
		<link rel="next prefetch" href="<?php echo $enc->attr( $this->navigationNext ); ?>" />
	<?php	endif; ?>

<!-- catalog.stage.navigator -->
<?php endif; ?>
