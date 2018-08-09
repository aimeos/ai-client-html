<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */

$enc = $this->encoder();

$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', array( 'absoluteUri' => 1 ) );

$product = $this->extOrderProductItem;


?>
<?php $this->block()->start( 'email/subscription/html' ); ?>
<html>
	<head>
		<title><?= $enc->html( $this->translate( 'client', 'E-mail notification' ), $enc::TRUST ); ?></title>
		<meta name="application-name" content="Aimeos" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<style type="text/css">
			html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre,
			a, abbr, acronym, address, big, cite, code, del, dfn, em, img, ins, kbd, q, s, samp,
			small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li,
			fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td,
			article, aside, canvas, details, embed, figure, figcaption, footer, header, hgroup,
			menu, nav, output, ruby, section, summary, time, mark, audio, video {
				margin: 0;
				padding: 0;
				border: 0;
				font-size: 100%;
				font: inherit;
				vertical-align: baseline;
			}
			/* HTML5 display-role reset for older browsers */
			article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {
				display: block;
			}

			<?= $this->get( 'htmlCss' ); ?>

		</style>
	</head>
	<body>
		<div class="aimeos">

			<?php if( isset( $this->htmlLogo ) ) : ?>
				<img class="logo" src="<?= $this->htmlLogo; ?>" />
			<?php endif; ?>

			<p class="email-common-salutation content-block">
				<?= $enc->html( $this->get( 'emailIntro' ) ); ?>
			</p>

			<p class="email-common-intro content-block">
				<?= nl2br( $enc->html( $this->translate( 'client', 'The subscription for the product has ended' ), $enc::TRUST ) ); ?>:

				<?php switch( $this->extSubscriptionItem->getReason() ) : case -1: ?>
					<?= nl2br( $enc->html( $this->translate( 'client', 'The payment couldn\'t be renewed' ), $enc::TRUST ) ); ?>
				<?php break; case 1: ?>
					<?= nl2br( $enc->html( $this->translate( 'client', 'You\'ve cancelled the subscription' ), $enc::TRUST ) ); ?>
				<?php endswitch; ?>
			</p>

			<div class="common-summary-detail common-summary container content-block">
				<div class="header">
					<h2><?= $enc->html( $this->translate( 'client', 'Details' ), $enc::TRUST ); ?></h2>
				</div>

				<div class="basket">
					<table>
						<thead>
							<tr>
								<th class="details"></th>
							</tr>
						</thead>
						<tbody>
							<tr class="product">
								<td class="details">

									<?php if( ( $url = $product->getMediaUrl() ) !== '' ) : ?>
										<img src="<?= $enc->attr( $this->content( $url ) ); ?>" />
									<?php endif; ?>

									<?php $params = array_merge( $this->param(), ['currency' => $product->getPrice()->getCurrencyId(), 'd_prodid' => $product->getProductId(), 'd_name' => $product->getName( 'url' )] ); ?>
									<a class="product-name" href="<?= $enc->attr( $this->url( ( $product->getTarget() ?: $detailTarget ), $detailController, $detailAction, $params, [], $detailConfig ) ); ?>">
										<?= $enc->html( $product->getName(), $enc::TRUST ); ?>
									</a>

									<div class="price-list">
										<?= $this->partial( $this->config( 'client/html/common/partials/price', 'common/partials/price-standard.php' ), [
											'prices' => array( $product->getPrice() )
										] ); ?>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<p class="email-common-outro content-block">
				<?= nl2br( $enc->html( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ), $enc::TRUST ) ); ?>
			</p>

		</div>

	</body>
</html>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'email/subscription/html' ); ?>
