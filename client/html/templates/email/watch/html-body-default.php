<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', array( 'absoluteUri' => 1 ) );


?>
<?php $this->block()->start( 'email/watch/html' ); ?>
<html>
	<head>
		<title><?php echo $enc->html( $this->translate( 'client', 'E-mail notification' ), $enc::TRUST ); ?></title>
		<meta name="application-name" content="Aimeos" />
	</head>
	<body>
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

			.aimeos .content-block {
				margin: 1.5em 2%;
				width: 96%;
			}
			.aimeos .logo {
				margin: 1.5em 2%;
				margin-top: 0;
				float: right;
			}
			.aimeos .common-summary {
				clear: both;
			}

			<?php echo $this->get( 'htmlCss' ); ?>

		</style>

		<div class="aimeos">

			<?php if( isset( $this->htmlLogo ) ) : ?>
				<img class="logo" src="<?php echo $this->htmlLogo; ?>" />
			<?php endif; ?>

			<p class="email-common-salutation content-block">
				<?php echo $enc->html( $this->get( 'emailIntro' ) ); ?>
			</p>

			<p class="email-common-intro content-block">
				<?php echo $enc->html( nl2br( $this->translate( 'client', 'One or more products you are watching have been updated.' ) ), $enc::TRUST ); ?>
			</p>

			<div class="common-summary-detail common-summary container content-block">
				<div class="header">
					<h2><?php echo $enc->html( $this->translate( 'client', 'Details' ), $enc::TRUST ); ?></h2>
				</div>

				<div class="basket">
					<table>
						<thead>
							<tr>
								<th class="details"></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach( $this->extProducts as $entry ) : $product = $entry['item']; ?>
								<tr class="product">
									<td class="details">

										<?php $media = $product->getRefItems( 'media', 'default', 'default' ); ?>
										<?php if( ( $image = reset( $media ) ) !== false && ( $url = $image->getPreview() ) != '' ) : ?>
											<img src="<?php echo $enc->attr( $this->content( $url ) ); ?>" />
										<?php endif; ?>

										<?php $params = array( 'd_prodid' => $product->getId(), 'd_name' => $product->getName( 'url' ) ); ?>
										<a class="product-name" href="<?php echo $enc->attr( $this->url( $detailTarget, $detailController, $detailAction, $params, array(), $detailConfig ) ); ?>">
											<?php echo $enc->html( $product->getName(), $enc::TRUST ); ?>
										</a>

										<div class="price-list">
											<?php echo $this->partial( $this->config( 'client/html/common/partials/price', 'common/partials/price-default.php' ), array( 'prices' => array( $entry['price'] ) ) ); ?>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>

			<p class="email-common-outro content-block">
				<?php echo $enc->html( nl2br( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ), $enc::TRUST ); ?>
			</p>

		</div>

	</body>
</html>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/watch/html' ); ?>
