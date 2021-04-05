<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */

$enc = $this->encoder();


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
<?php $this->block()->start( 'account/subscription/list' ) ?>

<?php if( !$this->get( 'listsItems', map() )->isEmpty() ) : ?>

	<div class="account-subscription-list">
		<h1 class="header"><?= $enc->html( $this->translate( 'client', 'Subscriptions' ), $enc::TRUST ) ?></h1>

		<div class="subscription-list">

			<?php foreach( $this->get( 'listsItems', [] ) as $id => $item ) : ?>

				<div class="subscription-item row">

					<div class="col-12">
						<h2 class="subscription-basic">
							<span class="name">
								<?= $enc->html( $this->translate( 'client', 'Subscription ID' ), $enc::TRUST ) ?>
							</span>
							<span class="value">
								<?= $enc->html( $id ) ?>
							</span>
						</h2>
					</div>

					<div class="col-10">
						<div class="row">
							<div class="col-md-6">
								<div class="subscription-created row">
									<span class="name col-5">
										<?= $enc->html( $this->translate( 'client', 'Created' ), $enc::TRUST ) ?>
									</span>
									<span class="value col-7">
										<?= $enc->html( date_create( $item->getTimeCreated() )->format( $dateformat ) ) ?>
									</span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="subscription-interval row">
									<span class="name col-5">
										<?= $enc->html( $this->translate( 'client', 'Interval' ), $enc::TRUST ) ?>
									</span>
									<span class="value col-7">
										<?php if( $interval = $this->get( 'listsIntervalItems', map() )->get( $item->getInterval() ) ) : ?>
											<?= $enc->html( $interval->getName(), $enc::TRUST ) ?>
										<?php else : ?>
											<?= $enc->html( $item->getInterval(), $enc::TRUST ) ?>
										<?php endif ?>
									</span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="subscription-datenext row">
									<span class="name col-5">
										<?= $enc->html( $this->translate( 'client', 'Next order' ), $enc::TRUST ) ?>
									</span>
									<span class="value col-7">
										<?php if( ( $date = $item->getDateNext() ) != null ) : ?>
											<?= $enc->html( date_create( $date )->format( $dateformat ), $enc::TRUST ) ?>
										<?php endif ?>
									</span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="subscription-dateend row">
									<span class="name col-5">
										<?= $enc->html( $this->translate( 'client', 'End date' ), $enc::TRUST ) ?>
									</span>
									<span class="value col-7">
										<?php if( ( $date = $item->getDateEnd() ) != null ) : ?>
											<?= $enc->html( date_create( $date )->format( $dateformat ), $enc::TRUST ) ?>
										<?php endif ?>
									</span>
								</div>
							</div>
						</div>
					</div>

					<div class="action col-md-2">
						<?php $params = ['sub_action' => 'detail', 'sub_id' => $id] ?>
						<a class="btn btn-outline" href="<?= $enc->attr( $this->url( $accountTarget, $accountController, $accountAction, $params, [], $accountConfig ) ) ?>">
							<?= $enc->html( $this->translate( 'client', 'Show' ) ) ?>
						</a>
					</div>
				</div>

			<?php endforeach ?>

		</div>

	</div>

<?php endif ?>

<?php $this->block()->stop() ?>
<?= $this->block()->get( 'account/subscription/list' ) ?>
