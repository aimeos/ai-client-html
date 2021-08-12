<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

$enc = $this->encoder();


/** client/html/account/history/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2014.03
 * @category Developer
 * @see client/html/account/history/url/controller
 * @see client/html/account/history/url/action
 * @see client/html/account/history/url/config
 */
$accountTarget = $this->config( 'client/html/account/history/url/target' );

/** client/html/account/history/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2014.03
 * @category Developer
 * @see client/html/account/history/url/target
 * @see client/html/account/history/url/action
 * @see client/html/account/history/url/config
 */
$accountController = $this->config( 'client/html/account/history/url/controller', 'account' );

/** client/html/account/history/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2014.03
 * @category Developer
 * @see client/html/account/history/url/target
 * @see client/html/account/history/url/controller
 * @see client/html/account/history/url/config
 */
$accountAction = $this->config( 'client/html/account/history/url/action', 'history' );

/** client/html/account/history/url/config
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
 * @since 2014.03
 * @category Developer
 * @see client/html/account/history/url/target
 * @see client/html/account/history/url/controller
 * @see client/html/account/history/url/action
 * @see client/html/url/config
 */
$accountConfig = $this->config( 'client/html/account/history/url/config', [] );


/// Date format with year (Y), month (m) and day (d). See http://php.net/manual/en/function.date.php
$dateformat = $this->translate( 'client', 'Y-m-d' );
/// Order status (%1$s) and date (%2$s), e.g. "received at 2000-01-01"
$attrformat = $this->translate( 'client', '%1$s at %2$s' );


?>
<?php $this->block()->start( 'account/history/list' ) ?>

<?php if( !$this->get( 'listsOrderItems', map() )->isEmpty() ) : ?>

	<div class="account-history-list">
		<h1 class="header"><?= $enc->html( $this->translate( 'client', 'Order history' ), $enc::TRUST ) ?></h1>

		<div class="history-list">

			<?php foreach( $this->get( 'listsOrderItems', [] ) as $id => $orderItem ) : ?>

				<div class="history-item row">

					<div class="col-12">
						<h2 class="order-basic">
							<span class="name">
								<?= $enc->html( $this->translate( 'client', 'Order ID' ), $enc::TRUST ) ?>
							</span>
							<span class="value">
								<?= $enc->html( $id ) ?>
							</span>
						</h2>
					</div>

					<div class="col-10">
						<div class="row">
							<div class="col-md-6">
								<div class="order-created row">
									<span class="name col-5">
										<?= $enc->html( $this->translate( 'client', 'Created' ), $enc::TRUST ) ?>
									</span>
									<span class="value col-7">
										<?= $enc->html( date_create( $orderItem->getTimeCreated() )->format( $dateformat ) ) ?>
									</span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="order-channel row">
									<span class="name col-5">
										<?= $enc->html( $this->translate( 'client', 'Channel' ), $enc::TRUST ) ?>
									</span>
									<span class="value col-7">
										<?php $code = 'order:' . $orderItem->getType() ?>
										<?= $enc->html( $this->translate( 'mshop/code', $code ), $enc::TRUST ) ?>
									</span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="order-payment row">
									<span class="name col-5">
										<?= $enc->html( $this->translate( 'client', 'Payment' ), $enc::TRUST ) ?>
									</span>
									<span class="value col-7">
										<?php if( ( $date = $orderItem->getDatePayment() ) !== null ) : ?>
											<?php $code = 'pay:' . $orderItem->getStatusPayment(); $paystatus = $this->translate( 'mshop/code', $code ) ?>
											<?= $enc->html( sprintf( $attrformat, $paystatus, date_create( $date )->format( $dateformat ) ), $enc::TRUST ) ?>
										<?php endif ?>
									</span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="order-delivery row">
									<span class="name col-5">
										<?= $enc->html( $this->translate( 'client', 'Delivery' ), $enc::TRUST ) ?>
									</span>
									<span class="value col-7">
										<?php if( ( $date = $orderItem->getDateDelivery() ) !== null ) : ?>
											<?php $code = 'stat:' . $orderItem->getStatusDelivery(); $status = $this->translate( 'mshop/code', $code ) ?>
											<?= $enc->html( sprintf( $attrformat, $status, date_create( $date )->format( $dateformat ) ), $enc::TRUST ) ?>
										<?php endif ?>
									</span>
								</div>
							</div>
						</div>
					</div>

					<div class="action col-md-2">
						<?php $params = ['his_action' => 'order', 'his_id' => $id] ?>
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
<?= $this->block()->get( 'account/history/list' ) ?>
