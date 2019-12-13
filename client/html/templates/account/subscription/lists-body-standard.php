<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */

$enc = $this->encoder();
$items = $this->get( 'listsItems', [] );
$intervals = $this->get( 'listsIntervalItems', [] );


/** client/html/account/subscription/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2018.04
 * @category Developer
 * @see client/html/account/subscription/url/controller
 * @see client/html/account/subscription/url/action
 * @see client/html/account/subscription/url/config
 */
$accountTarget = $this->config( 'client/html/account/subscription/url/target' );

/** client/html/account/subscription/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2018.04
 * @category Developer
 * @see client/html/account/subscription/url/target
 * @see client/html/account/subscription/url/action
 * @see client/html/account/subscription/url/config
 */
$accountController = $this->config( 'client/html/account/subscription/url/controller', 'account' );

/** client/html/account/subscription/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2018.04
 * @category Developer
 * @see client/html/account/subscription/url/target
 * @see client/html/account/subscription/url/controller
 * @see client/html/account/subscription/url/config
 */
$accountAction = $this->config( 'client/html/account/subscription/url/action', 'subscription' );

/** client/html/account/subscription/url/config
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
 * @since 2018.04
 * @category Developer
 * @see client/html/account/subscription/url/target
 * @see client/html/account/subscription/url/controller
 * @see client/html/account/subscription/url/action
 * @see client/html/url/config
 */
$accountConfig = $this->config( 'client/html/account/subscription/url/config', [] );


/// Date format with year (Y), month (m) and day (d). See http://php.net/manual/en/function.date.php
$dateformat = $this->translate( 'client', 'Y-m-d' );


?>
<?php $this->block()->start( 'account/subscription/list' ); ?>
<?php if( !empty( $items ) ) : ?>
	<div class="account-subscription-list">
		<h1 class="header"><?= $enc->html( $this->translate( 'client', 'Subscriptions' ), $enc::TRUST ); ?></h1>

		<?php if( empty( $items ) === false ) : ?>
			<ul class="subscription-list">

				<?php foreach( $items as $id => $item ) : ?>
					<li class="subscription-item row">

						<?php $params = array( 'sub_action' => 'detail', 'sub_id' => $id ); ?>
						<a class="subscription-data col-sm-10" href="<?= $enc->attr( $this->url( $accountTarget, $accountController, $accountAction, $params, [], $accountConfig ) ); ?>">

							<div class="row">
								<div class="attr-item subscription-basic col-sm-6 row">
									<span class="name col-sm-6">
										<?= $enc->html( $this->translate( 'client', 'Subscription ID' ), $enc::TRUST ); ?>
									</span>
									<span class="value col-sm-6">
										<?= $enc->html( sprintf(
												$this->translate( 'client', '%1$s at %2$s' ),
												$id,
												date_create( $item->getTimeCreated() )->format( $dateformat )
											), $enc::TRUST ); ?>
									</span>
								</div>

								<div class="attr-item subscription-interval col-sm-6 row">
									<span class="name col-sm-6">
										<?= $enc->html( $this->translate( 'client', 'Subscription interval' ), $enc::TRUST ); ?>
									</span>
									<span class="value col-sm-6">
										<?php if( isset( $intervals[$item->getInterval()] ) ) : ?>
											<?= $enc->html( $intervals[$item->getInterval()]->getName(), $enc::TRUST ); ?>
										<?php else : ?>
											<?= $enc->html( $item->getInterval(), $enc::TRUST ); ?>
										<?php endif; ?>
									</span>
								</div>
							</div>

							<div class="row">
								<div class="attr-item subscription-datenext col-sm-6 row">
									<span class="name col-sm-6">
										<?= $enc->html( $this->translate( 'client', 'Next order' ), $enc::TRUST ); ?>
									</span>
									<span class="value col-sm-6">
										<?php if( ( $date = $item->getDateNext() ) != null ) : ?>
											<?= $enc->html( date_create( $date )->format( $dateformat ), $enc::TRUST ); ?>
										<?php endif; ?>
									</span>
								</div>

								<div class="attr-item subscription-dateend col-sm-6 row">
									<span class="name col-sm-6">
										<?= $enc->html( $this->translate( 'client', 'End date' ), $enc::TRUST ); ?>
									</span>
									<span class="value col-sm-6">
										<?php if( ( $date = $item->getDateEnd() ) != null ) : ?>
											<?= $enc->html( date_create( $date )->format( $dateformat ), $enc::TRUST ); ?>
										<?php endif; ?>
									</span>
								</div>
							</div>
						</a>

						<div class="subscription-cancel col-sm-2">
							<?php $params = array( 'sub_action' => 'cancel', 'sub_id' => $id ); ?>
							<?php if( $item->getDateEnd() == null ) : ?>
							<a class="minibutton delete"
								href="<?= $enc->attr( $this->url( $accountTarget, $accountController, $accountAction, $params, [], $accountConfig ) ); ?>"></a>
							<?php endif; ?>
						</div>

					</li>
				<?php endforeach; ?>

			</ul>
		<?php endif; ?>

	</div>
<?php endif; ?>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'account/subscription/list' ); ?>
