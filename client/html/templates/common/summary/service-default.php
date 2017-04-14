<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

/* Available data:
 * - service : Order service item
 * - type : Type of the service item, i.e. "delivery" or "payment"
 */


$enc = $this->encoder();
$service = $this->service;
$type = $this->get( 'type' );


?>
<div class="item">
	<?php if( ( $url = $service->getMediaUrl() ) != '' ) : ?>
		<div class="item-icons">
			<img src="<?php echo $enc->attr( $this->content( $url ) ); ?>" />
		</div>
	<?php endif; ?>
	<h4><?php echo $enc->html( $service->getName() ); ?></h4>
</div>

<?php if( ( $attributes = $service->getAttributes() ) !== [] ) : ?>
	<ul class="attr-list">

		<?php foreach( $attributes as $attribute ) : ?>
			<?php if( $attribute->getType() === $type ) : ?>

				<li class="<?php echo $enc->attr( $type . '-' . $attribute->getCode() ); ?>">

					<span class="name">
						<?php if( $attribute->getName() != '' ) : ?>
							<?php echo $enc->html( $attribute->getName() ); ?>
						<?php else : ?>
							<?php echo $enc->html( $this->translate( 'client/code', $attribute->getCode() ) ); ?>
						<?php endif; ?>
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
