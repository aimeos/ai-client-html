<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

/// E-mail HTML title
$title = $this->translate( 'client', 'E-mail notification' );


?>
<?php $this->block()->start( 'email/delivery/html' ); ?>
<html>
	<head>
		<title><?= $enc->html( $title, $enc::TRUST ); ?></title>
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

			<?= $this->block()->get( 'email/delivery/html/intro' ); ?>

			<?= $this->block()->get( 'email/common/html/summary' ); ?>

			<p class="email-common-outro content-block">
				<?= $enc->html( nl2br( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ), $enc::TRUST ); ?>
			</p>

			<p class="email-common-legal content-block">
				<?= nl2br( $enc->html( $this->translate( 'client',  'All orders are subject to our terms and conditions.' ), $enc::TRUST ) ); ?>
			</p>

		</div>

	</body>
</html>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'email/delivery/html' ); ?>
