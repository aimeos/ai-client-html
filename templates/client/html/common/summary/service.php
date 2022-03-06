<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

/* Available data:
 * - service : List of order service items
 * - type : Type of the service item, i.e. "delivery" or "payment"
 */


$enc = $this->encoder();
$type = $this->get( 'type' );


?>
<?php foreach( $this->service as $service ) : ?>
	<div class="item">
		<?php if( ( $url = $service->getMediaUrl() ) != '' ) : ?>
			<div class="item-icons">
				<img src="<?= $enc->attr( $this->content( $url ) ) ?>">
			</div>
		<?php endif ?>
		<h4><?= $enc->html( $service->getName() ) ?></h4>
	</div>

	<?php if( !( $attributes = $service->getAttributeItems() )->isEmpty() ) : ?>
		<ul class="attr-list">

			<?php foreach( $attributes as $attribute ) : ?>
				<?php if( strpos( $attribute->getType(), 'hidden' ) === false ) : ?>
					<li class="<?= $enc->attr( $type . '-' . $attribute->getCode() ) ?>">

						<span class="name">
							<?php if( $attribute->getName() != '' ) : ?>
								<?= $enc->html( $attribute->getName() ) ?>
							<?php else : ?>
								<?= $enc->html( $this->translate( 'client/code', $attribute->getCode() ) ) ?>
							<?php endif ?>
						</span>

						<?php switch( $attribute->getValue() ) : case 'array': case 'object': ?>
							<span class="value"><?= $enc->html( join( ', ', (array) $attribute->getValue() ) ) ?></span>
						<?php break; default: ?>
							<span class="value"><?= $enc->html( $attribute->getValue() ) ?></span>
						<?php endswitch ?>

					</li>

				<?php endif ?>
			<?php endforeach ?>

		</ul>
	<?php endif ?>
<?php endforeach ?>
