<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 */

$enc = $this->encoder();
$productItems = $this->get( 'todoProductItems', map() );


?>
<?php if( !$productItems->isEmpty() ) : ?>
	<section class="account-review-todo">
		<form method="POST" action="<?= $enc->attr( $this->url( 'client/html/account/index/url' ) ) ?>"

			<?php foreach( $productItems as $prodId => $productItem ) : ?>
				<?php $images = $productItem->getRefItems( 'media', 'default', 'default' ) ?>

				<div class="todo-item row">
					<div class="col-sm-4">
						<img class="todo-image"
							src="<?= $enc->attr( $images->getPreview()->first() ) ?>"
							srcset="<?= $enc->attr( $images->getPreviews()->first() ) ?>" />
					</div>
					<div class="col-sm-8">
						<h3 class="todo-name"><?= $enc->html( $productItem->getName() ) ?></h3>
						<div class="todo-rating">
							<input id="rating-<?= $enc->attr( $prodId ) ?>-5" class="rating rating-5"
								type="radio" value="5" name="<?= $this->formparam( ['review', 'rating', ''] ) ?>" />
							<label for="rating-<?= $enc->attr( $prodId ) ?>-5">★★★★★</label>
							<input id="rating-<?= $enc->attr( $prodId ) ?>-4" class="rating rating-4"
								type="radio" value="4" name="<?= $this->formparam( ['review', 'rating', ''] ) ?>" />
							<label for="rating-<?= $enc->attr( $prodId ) ?>-5">★★★★</label>
							<input id="rating-<?= $enc->attr( $prodId ) ?>-3" class="rating rating-3"
								type="radio" value="3" name="<?= $this->formparam( ['review', 'rating', ''] ) ?>" />
							<label for="rating-<?= $enc->attr( $prodId ) ?>-5">★★★</label>
							<input id="rating-<?= $enc->attr( $prodId ) ?>-2" class="rating rating-2"
								type="radio" value="2" name="<?= $this->formparam( ['review', 'rating', ''] ) ?>" />
							<label for="rating-<?= $enc->attr( $prodId ) ?>-5">★★</label>
							<input id="rating-<?= $enc->attr( $prodId ) ?>-1" class="rating rating-1"
								type="radio" value="1" name="<?= $this->formparam( ['review', 'rating', ''] ) ?>" />
							<label for="rating-<?= $enc->attr( $prodId ) ?>-5">★</label>
						</div>
						<textarea class="todo-comment" name="<?= $this->formparam( ['review', 'comment', ''] ) ?>">
						</textarea>
						<button class="btn btn-primary"><?= $enc->html( $this->translate( 'client', 'Submit' ) ) ?></button>
					</div>
				</div>

			<?php endforeach ?>

		</form>

		<?= $this->get( 'todoBody' ); ?>

	</section>
<?php endif ?>