<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();
$logoContent = false;

/// E-mail HTML title
$title = $this->translate( 'client', 'E-mail notification' );

/** client/html/email/logo
 * Path to the logo image displayed in HTML e-mails
 *
 * The path can either be an absolute local path or an URL to a file on a
 * remote server. If the file is stored on a remote server, "allow_url_fopen"
 * must be enabled. See {@link http://php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen php.ini allow_url_fopen}
 * documentation for details.
 *
 * @param string Absolute file system path or remote URL to the logo image
 * @since 2014.03
 * @category User
 * @see client/html/email/from-email
 */
if( ( $logo = $this->config( 'client/html/email/logo', 'client/html/themes/elegance/images/aimeos.png' ) ) != '' )
{
	$logoFilename = basename( $logo );

	if( file_exists( $logo ) !== false )
	{
		$logoContent = file_get_contents( $logo );
		$finfo = new finfo( FILEINFO_MIME_TYPE );
		$logoMimetype = $finfo->file( $logo );
	}
}

$path = $this->config( 'client/html/common/template/baseurl', 'client/html/themes/elegance' );
$filename = $path . DIRECTORY_SEPARATOR . 'common.css';
$content = '';

if( file_exists( $filename ) !== false ) {
	$content = file_get_contents( $filename );
}


$salutations = array(
	\Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR,
	\Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MRS,
	\Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MISS,
);

try
{
	$addr = $this->extAddressItem;

	/// E-mail intro with salutation (%1$s), first name (%2$s) and last name (%3$s)
	$intro = sprintf( $this->translate( 'client', 'Dear %1$s %2$s %3$s' ),
		( in_array( $addr->getSalutation(), $salutations ) ? $this->translate( 'client/code', $addr->getSalutation() ) : '' ),
		$addr->getFirstName(),
		$addr->getLastName()
	);
}
catch( Exception $e )
{
	$intro = $this->translate( 'client/html/email', 'Dear Sir or Madam' );
}

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

			<?php echo $content; ?>

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
		</style>

		<div class="aimeos">

			<?php if( $logoContent !== false ) : ?>
				<img class="logo" src="<?php echo $this->mail()->embedAttachment( $logoContent, $logoMimetype, $logoFilename ); ?>" />
			<?php endif; ?>

			<p class="email-common-salutation content-block">
				<?php echo $enc->html( $intro ); ?>
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

		</div>

		<p class="email-common-outro content-block">
			<?php echo $enc->html( nl2br( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ), $enc::TRUST ); ?>
		</p>

	</body>
</html>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/account/html' ); ?>
