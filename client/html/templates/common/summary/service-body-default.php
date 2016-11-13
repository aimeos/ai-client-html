<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();
$delivery = $payment = null;
$errors = $this->get( 'summaryErrorCodes', array() );

try {
	$delivery = $this->summaryBasket->getService( 'delivery' );
} catch( \Exception $e ) { ; }

try {
	$payment = $this->summaryBasket->getService( 'payment' );
} catch( \Exception $e ) { ; }


?>
<?php $this->block()->start( 'common/summary/service' ); ?>
<div class="common-summary-service container">
	<h2><?php echo $enc->html( $this->translate( 'client', 'Services' ), $enc::TRUST ); ?></h2>
	<div class="item delivery <?php echo ( isset( $errors['service']['delivery'] ) ? 'error' : '' ); ?>">

		<div class="header">
			<h3><?php echo $enc->html( $this->translate( 'client', 'delivery' ), $enc::TRUST ); ?></h3>

			<?php if( isset( $this->summaryUrlServiceDelivery ) ) : ?>
				<a class="modify" href="<?php echo $enc->attr( $this->summaryUrlServiceDelivery ); ?>">
					<?php echo $enc->html( $this->translate( 'client', 'Change' ), $enc::TRUST ); ?>
				</a>
			<?php endif; ?>

		</div>


		<?php if( $delivery ) : ?>

			<div class="item">
				<?php if( ( $url = $delivery->getMediaUrl() ) != '' ) : ?>
					<div class="item-icons">
						<img src="<?php echo $enc->attr( $this->content( $url ) ); ?>" />
					</div>
				<?php endif; ?>
				<h4><?php echo $enc->html( $delivery->getName() ); ?></h4>
			</div>

			<?php if( ( $attributes = $delivery->getAttributes() ) !== array() ) : ?>
				<ul class="attr-list">

					<?php foreach( $attributes as $attribute ) : ?>
						<?php if( $attribute->getType() === 'delivery' ) : ?>

							<li class="da-<?php echo $enc->attr( $attribute->getCode() ); ?>">
								<span class="name">
									<?php echo $enc->html( ( $attribute->getName() != '' ? $attribute->getName() : $this->translate( 'client/html/service', $attribute->getCode() ) ) ); ?>
								</span>

								<?php switch( $attribute->getValue() ) : case 'array': case 'object': ?>
									<span class="value"><?php echo $enc->html( join( ', ', (array) $attribute->getValue() ) ); ?></span>
								<?php break; default: ?>
									<span class="value"><?php echo $enc->html( $attribute->getValue() ); ?></span>
								<?php endswitch; ?>

							</li>

						<?php endif; ?>
					<?php endforeach; ?>

				</ul>
			<?php endif; ?>

		<?php endif; ?>

	</div><!--


	--><div class="item payment <?php echo ( isset( $errors['service']['payment'] ) ? 'error' : '' ); ?>">
		<div class="header">
			<h3><?php echo $enc->html( $this->translate( 'client', 'payment' ), $enc::TRUST ); ?></h3>

			<?php if( isset( $this->summaryUrlServicePayment ) ) : ?>
				<a class="modify" href="<?php echo $enc->attr( $this->summaryUrlServicePayment ); ?>">
					<?php echo $enc->html( $this->translate( 'client', 'Change' ), $enc::TRUST ); ?>
				</a>
			<?php endif; ?>

		</div>

		<?php if( $payment ) : ?>

			<div class="item">
				<?php if( ( $url = $payment->getMediaUrl() ) != '' ) : ?>
					<div class="item-icons">
						<img src="<?php echo $this->content( $url ); ?>" />
					</div>
				<?php endif; ?>
				<h4><?php echo $payment->getName(); ?></h4>
			</div>

			<?php if( ( $attributes = $payment->getAttributes() ) !== array() ) : ?>
				<ul class="attr-list">

					<?php foreach( $attributes as $attribute ) : ?>
						<?php if( $attribute->getType() === 'payment' ) : ?>
							<li class="pa-<?php echo $enc->attr( $attribute->getCode() ); ?>">
								<span class="name">
									<?php echo $enc->html( ( $attribute->getName() != '' ? $attribute->getName() : $this->translate( 'client/code', $attribute->getCode() ) ) ); ?>
								</span>

								<?php switch( $attribute->getValue() ) : case 'array': case 'object': ?>
									<span class="value"><?php echo $enc->html( join( ', ', (array) $attribute->getValue() ) ); ?></span>
								<?php break; default: ?>
									<span class="value"><?php echo $enc->html( $attribute->getValue() ); ?></span>
								<?php endswitch; ?>

							</li>
						<?php endif; ?>
					<?php endforeach; ?>

				</ul>
			<?php endif; ?>

		<?php endif; ?>

	</div>

</div>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'common/summary/service' ); ?>
