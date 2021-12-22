<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

$enc = $this->encoder();


?>
<section class="aimeos checkout-standard" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">

	<nav>
		<ol class="steps">

			<li class="step active basket">
				<a href="<?= $enc->attr( $this->link( 'client/html/basket/standard/url' ) ) ?>">
					<?= $enc->html( $this->translate( 'client', 'Basket' ), $enc::TRUST ) ?>
				</a>
			</li>

			<?php foreach( $this->get( 'standardStepsBefore', [] ) as $name ) : ?>
				<li class="step active <?= $name ?>">
					<a href="<?= $enc->attr( $this->link( 'client/html/checkout/standard/url', ['c_step' => $name] ) ) ?>">
						<?= $enc->html( $this->translate( 'client', $name ) ) ?>
					</a>
				</li>
			<?php endforeach ?>

			<?php if( $this->get( 'standardStepActive', false ) ) : ?>
				<li class="step current <?= $this->get( 'standardStepActive', false ) ?>">
					<?= $enc->html( $this->translate( 'client', $this->get( 'standardStepActive', false ) ) ) ?>
				</li>
			<?php endif ?>

			<?php foreach( $this->get( 'standardStepsAfter', [] ) as $name ) : ?>
				<li class="step <?= $name ?>">
					<?= $enc->html( $this->translate( 'client', $name ) ) ?>
				</li>
			<?php endforeach ?>

		</ol>
	</nav>


	<?php if( isset( $this->standardErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->standardErrorList as $errmsg ) : ?>
				<li class="error-item"><?= $enc->html( $errmsg ) ?></li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>


	<form method="<?= $enc->attr( $this->get( 'standardMethod', 'POST' ) ) ?>" action="<?= $enc->attr( $this->get( 'standardUrlNext' ) ) ?>">
		<?= $this->csrf()->formfield() ?>
		<?= $this->get( 'standardBody' ) ?>
	</form>

</section>
