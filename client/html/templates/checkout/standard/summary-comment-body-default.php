<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */

$enc = $this->encoder();

?>
<?php $this->block()->start( 'checkout/standard/summary/comment' ); ?>
<div class="checkout-standard-summary-comment container">
	<h2><?php echo $enc->html( $this->translate( 'client', 'Comment' ), $enc::TRUST ); ?></h2>
	<div class="header"><h3><?php echo $enc->html( $this->translate( 'client', 'Your comment' ), $enc::TRUST ); ?></h3></div>
	<textarea class="comment-value" name="<?php echo $this->formparam( array( 'cs_comment' ) ); ?>"><?php echo $enc->html( $this->summaryComment ); ?></textarea>
<?php echo $this->get( 'commentBody' ); ?>
</div>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'checkout/standard/summary/comment' ); ?>
