<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();

?>
<?php if( $this->param( 'd_pos' ) !== null ) : ?>
	<div class="catalog-detail-navigator">
		<nav>

			<?php if( isset( $this->navigationPrev ) ) : ?>
					<a class="prev" href="<?= $enc->attr( $this->navigationPrev ) ?>" rel="prev">
							<?= $enc->html( $this->translate( 'client', 'Previous' ), $enc::TRUST ) ?>
					</a>
			<?php endif ?>

			<?php if( isset( $this->navigationNext ) ) : ?>
					<a class="next" href="<?= $enc->attr( $this->navigationNext ) ?>" rel="next">
							<?= $enc->html( $this->translate( 'client', 'Next' ), $enc::TRUST ) ?>
					</a>
			<?php endif ?>

		</nav>
	</div>
<?php endif ?>
