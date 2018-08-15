<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */

$enc = $this->encoder();
$voucher = $this->extVoucherCode;
$product = $this->extOrderProductItem;

/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $this->translate( 'client', '%1$s %2$s' );


?>
<?php $this->block()->start( 'email/voucher/html' ); ?>
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
				<?= nl2br( $enc->html( $this->translate( 'client', 'Your voucher: ' ) . $voucher, $enc::TRUST ) ); ?>
			</p>

			<p class="email-common-body content-block">
				<?php $price = $product->getPrice(); $priceCurrency = $this->translate( 'currency', $price->getCurrencyId() ); ?>
				<?php $value = sprintf( $priceFormat, $this->number( $price->getValue() + $price->getRebate() ), $priceCurrency ); ?>
				<?= nl2br( $enc->html( sprintf( $this->translate( 'client', 'The value of your voucher is %1$s' ), $value ), $enc::TRUST ) ); ?>
			</p>

			<p class="email-common-outro content-block">
				<?= nl2br( $enc->html( $this->translate( 'client', 'You can use your voucher any time in our online shop' ), $enc::TRUST ) ); ?>
			</p>

		</div>

	</body>
</html>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'email/voucher/html' ); ?>
