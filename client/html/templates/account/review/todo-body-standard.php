<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */

/** client/html/account/review/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2020.10
 * @category Developer
 * @see client/html/account/review/url/controller
 * @see client/html/account/review/url/action
 * @see client/html/account/review/url/config
 */

/** client/html/account/review/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2020.10
 * @category Developer
 * @see client/html/account/review/url/target
 * @see client/html/account/review/url/action
 * @see client/html/account/review/url/config
 */

/** client/html/account/review/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2020.10
 * @category Developer
 * @see client/html/account/review/url/target
 * @see client/html/account/review/url/controller
 * @see client/html/account/review/url/config
 */

/** client/html/account/review/url/config
 * Associative list of configuration options used for generating the URL
 *
 * You can specify additional options as key/value pairs used when generating
 * the URLs, like
 *
 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
 *
 * The available key/value pairs depend on the application that embeds the e-commerce
 * framework. This is because the infrastructure of the application is used for
 * generating the URLs. The full list of available config options is referenced
 * in the "see also" section of this page.
 *
 * @param string Associative list of configuration options
 * @since 2020.10
 * @category Developer
 * @see client/html/account/review/url/target
 * @see client/html/account/review/url/controller
 * @see client/html/account/review/url/action
 * @see client/html/url/config
 */

/** client/html/account/review/url/filter
 * Removes parameters for the detail page before generating the URL
 *
 * For SEO, it's nice to have URLs which contains only required parameters.
 * This setting removes the listed parameters from the URLs. Keep care to
 * remove no required parameters!
 *
 * @param array List of parameter names to remove
 * @since 2020.10
 * @category User
 * @category Developer
 * @see client/html/account/review/url/target
 * @see client/html/account/review/url/controller
 * @see client/html/account/review/url/action
 * @see client/html/account/review/url/config
 */

$enc = $this->encoder();
$productItems = $this->get( 'todoProductItems', map() );


?>
<?php $this->block()->start( 'account/profile/todo' ) ?>
<?php if( !$productItems->isEmpty() ) : ?>

	<div class="account-review-todo">

		<h1 class="header"><?= $this->translate( 'client', 'Reviews' ) ?></h1>

		<form method="POST" action="<?= $enc->attr( $this->link( 'client/html/account/review/url' ) ) ?>">
			<?= $this->csrf()->formfield() ?>

			<?php foreach( $productItems as $prodId => $productItem ) : ?>
				<?php $images = $productItem->getRefItems( 'media', 'default', 'default' ) ?>

				<div class="todo-item">
					<h3 class="todo-name"><?= $enc->html( $productItem->getName() ) ?></h3>
					<input type="hidden" value="<?= $enc->attr( $productItem->get( 'orderProductId' ) ) ?>"
						name="<?= $enc->attr( $this->formparam( ['review-todo', $prodId, 'review.orderproductid'] ) ) ?>"
					>
					<div class="row">
						<div class="col-md-6">
							<div class="row">
								<div class="col-6">
									<?php if( !$images->isEmpty() ) : ?>
										<img class="todo-image"
											sizes="<?= $enc->attr( $this->config( 'client/html/common/imageset-sizes', '240px' ) ) ?>"
											src="<?= $enc->attr( $this->content( $images->first()->getPreview() ) ) ?>"
											srcset="<?= $enc->attr( $this->imageset( $images->first()->getPreviews() ) ) ?>"
											alt="<?= $enc->attr( $images->first()->getProperties( 'title' )->first() ) ?>"
										>
									<?php endif ?>
								</div>
								<div class="col-6">
									<h4><?= $enc->html( $this->translate( 'client', 'Your rating' ) ) ?></h4>
									<div class="todo-rating">
										<div class="rating-line">
											<input id="rating-<?= $enc->attr( $prodId ) ?>-5" class="rating rating-5"
												type="radio" value="5" name="<?= $this->formparam( ['review-todo', $prodId, 'review.rating'] ) ?>"
												<?= $this->param( 'review-todo/' . $prodId . '/review.rating' ) == 5 ? 'selected' : '' ?>
											>
											<label for="rating-<?= $enc->attr( $prodId ) ?>-5">★★★★★</label>
										</div>
										<div class="rating-line">
											<input id="rating-<?= $enc->attr( $prodId ) ?>-4" class="rating rating-4"
												type="radio" value="4" name="<?= $this->formparam( ['review-todo', $prodId, 'review.rating'] ) ?>"
												<?= $this->param( 'review-todo/' . $prodId . '/review.rating' ) == 4 ? 'selected' : '' ?>
											>
											<label for="rating-<?= $enc->attr( $prodId ) ?>-4">★★★★</label>
											</div>
										<div class="rating-line">
											<input id="rating-<?= $enc->attr( $prodId ) ?>-3" class="rating rating-3"
												type="radio" value="3" name="<?= $this->formparam( ['review-todo', $prodId, 'review.rating'] ) ?>"
												<?= $this->param( 'review-todo/' . $prodId . '/review.rating' ) == 3 ? 'selected' : '' ?>
											>
											<label for="rating-<?= $enc->attr( $prodId ) ?>-3">★★★</label>
											</div>
										<div class="rating-line">
											<input id="rating-<?= $enc->attr( $prodId ) ?>-2" class="rating rating-2"
												type="radio" value="2" name="<?= $this->formparam( ['review-todo', $prodId, 'review.rating'] ) ?>"
												<?= $this->param( 'review-todo/' . $prodId . '/review.rating' ) == 2 ? 'selected' : '' ?>
											>
											<label for="rating-<?= $enc->attr( $prodId ) ?>-2">★★</label>
											</div>
										<div class="rating-line">
											<input id="rating-<?= $enc->attr( $prodId ) ?>-1" class="rating rating-1"
												type="radio" value="1" name="<?= $this->formparam( ['review-todo', $prodId, 'review.rating'] ) ?>"
												<?= $this->param( 'review-todo/' . $prodId . '/review.rating' ) == 1 ? 'selected' : '' ?>
											>
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

			<button class="btn btn-primary"><?= $enc->html( $this->translate( 'client', 'Submit' ) ) ?></button>

		</form>

		<?= $this->get( 'todoBody' ) ?>

	</div>

<?php endif ?>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'account/profile/todo' ) ?>
