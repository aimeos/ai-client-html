<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

/// E-mail HTML title
$title = $this->translate( 'client', 'E-mail notification' );


?>
<?php $this->block()->start( 'email/account/html' ); ?>
<html>
	<head>
		<title><?php echo $enc->html( $title, $enc::TRUST ); ?></title>
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
			.account-detail h2 {
				font-weight: bold;
			}
			.account-detail .name:after {
				content: ":";
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
				<?php echo $enc->html( nl2br( $this->translate( 'client', 'An account has been created for you.' ) ), $enc::TRUST ); ?>
			</p>

			<div class="account-detail content-block">
				<div class="header">
					<h2><?php echo $enc->html( $this->translate( 'client', 'Your account' ), $enc::TRUST ); ?></h2>
				</div>
				<div class="details">
					<ul class="attr-list">
						<li class="attr-item account-code">
							<span class="name"><?php echo $enc->html( $this->translate( 'client', 'Account' ), $enc::TRUST ); ?></span>
							<span class="value"><?php echo $enc->html( $this->extAccountCode, $enc::TRUST ); ?></span>
						</li><!--
						--><li class="attr-item account-password">
							<span class="name"><?php echo $enc->html( $this->translate( 'client', 'Password' ), $enc::TRUST ); ?></span>
							<span class="value"><?php echo $enc->html( $this->extAccountPassword, $enc::TRUST ); ?></span>
						</li>
					</ul>
				</div>
			</div>

			<p class="email-common-outro content-block">
				<?php echo $enc->html( nl2br( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ), $enc::TRUST ); ?>
			</p>

		</div>

	</body>
</html>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/account/html' ); ?>
