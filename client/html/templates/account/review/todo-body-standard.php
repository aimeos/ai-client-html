<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 */

$enc = $this->encoder();
$productItems = $this->get( 'todoProductItems', map() );


?>
<?php if( !$productItems->isEmpty() ) : ?>
	<div class="account-review-todo">
		<form method="POST" action="<?= $enc->attr( $this->link( 'client/html/account/index/url' ) ) ?>">
			<?= $this->csrf()->formfield(); ?>

			<?php foreach( $productItems as $prodId => $productItem ) : ?>
				<?php $images = $productItem->getRefItems( 'media', 'default', 'default' ) ?>

				<div class="todo-item">
					<h3 class="todo-name"><?= $enc->html( $productItem->getName() ) ?></h3>
					<input type="hidden" value="<?= $enc->attr( $productItem->get( 'orderProductId' ) ) ?>"
						name="<?= $enc->attr( $this->formparam( ['review-todo', $prodId, 'review.orderproductid'] ) ) ?>" />
					<div class="row">
						<div class="col-md-6">
							<div class="row">
								<div class="col-6">
									<?php if( !$images->isEmpty() ) : ?>
										<img class="todo-image"
											src="<?= $enc->attr( $this->content( $images->first()->getPreview() ) ) ?>"
											srcset="<?= $enc->attr( $this->imageset( $images->first()->getPreviews() ) ) ?>" />
									<?php endif ?>
								</div>
								<div class="col-6">
									<h4><?= $enc->html( $this->translate( 'client', 'Your rating' ) ) ?></h4>
									<div class="todo-rating">
										<div class="rating-line">
											<input id="rating-<?= $enc->attr( $prodId ) ?>-5" class="rating rating-5"
												type="radio" value="5" name="<?= $this->formparam( ['review-todo', $prodId, 'review.rating'] ) ?>"
												<?= $this->param( 'review-todo/' . $prodId . '/review.rating' ) == 5 ? 'selected' : '' ?> />
											<label for="rating-<?= $enc->attr( $prodId ) ?>-5">★★★★★</label>
										</div>
										<div class="rating-line">
											<input id="rating-<?= $enc->attr( $prodId ) ?>-4" class="rating rating-4"
												type="radio" value="4" name="<?= $this->formparam( ['review-todo', $prodId, 'review.rating'] ) ?>"
												<?= $this->param( 'review-todo/' . $prodId . '/review.rating' ) == 4 ? 'selected' : '' ?> />
											<label for="rating-<?= $enc->attr( $prodId ) ?>-4">★★★★</label>
											</div>
										<div class="rating-line">
											<input id="rating-<?= $enc->attr( $prodId ) ?>-3" class="rating rating-3"
												type="radio" value="3" name="<?= $this->formparam( ['review-todo', $prodId, 'review.rating'] ) ?>"
												<?= $this->param( 'review-todo/' . $prodId . '/review.rating' ) == 3 ? 'selected' : '' ?> />
											<label for="rating-<?= $enc->attr( $prodId ) ?>-3">★★★</label>
											</div>
										<div class="rating-line">
											<input id="rating-<?= $enc->attr( $prodId ) ?>-2" class="rating rating-2"
												type="radio" value="2" name="<?= $this->formparam( ['review-todo', $prodId, 'review.rating'] ) ?>"
												<?= $this->param( 'review-todo/' . $prodId . '/review.rating' ) == 2 ? 'selected' : '' ?> />
											<label for="rating-<?= $enc->attr( $prodId ) ?>-2">★★</label>
											</div>
										<div class="rating-line">
											<input id="rating-<?= $enc->attr( $prodId ) ?>-1" class="rating rating-1"
												type="radio" value="1" name="<?= $this->formparam( ['review-todo', $prodId, 'review.rating'] ) ?>"
												<?= $this->param( 'review-todo/' . $prodId . '/review.rating' ) == 1 ? 'selected' : '' ?> />
											<label for="rating-<?= $enc->attr( $prodId ) ?>-1">★</label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<h4><?= $enc->html( $this->translate( 'client', 'Your review' ) ) ?></h4>
							<textarea class="todo-comment" maxlength="1024"
								name="<?= $this->formparam( ['review-todo', $prodId, 'review.comment'] ) ?>"
								placeholder="<?= $enc->attr( $this->translate( 'client', 'What do you think about the product' ) ) ?>">
								<?= $enc->html( $this->param( 'review-todo/' . $prodId . '/review.comment' ) ) ?>
							</textarea>
						</div>
					</div>
				</div>

			<?php endforeach ?>

			<button class="btn btn-primary">Submit</button>

		</form>

		<?= $this->get( 'todoBody' ); ?>

	</div>
<?php endif ?>