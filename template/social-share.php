<?php
/**
 * Social share
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Vars
$product_title 	= get_the_title();
$product_url	= get_permalink();
$product_img	= wp_get_attachment_url( get_post_thumbnail_id() ); ?>

<div class="entry-share clr">

	<ul class="pluton-social-share clr">

		<li class="twitter">
			<a href="http://twitter.com/intent/tweet?status=<?php echo rawurlencode( $product_title ); ?>+<?php echo esc_url( $product_url ); ?>" target="_blank">
				<span class="fa fa-twitter"></span>
				<div class="product-share-text"><?php esc_html_e( 'Tweet This Product', 'pluton' ); ?></div>
			</a>
		</li>

		<li class="facebook">
			<a href="http://www.facebook.com/share.php?u=<?php echo rawurlencode( esc_url( $product_url ) ); ?>" target="_blank">
				<span class="fa fa-facebook"></span>
				<div class="product-share-text"><?php esc_html_e( 'Share on Facebook', 'pluton' ); ?></div>
			</a>
		</li>

		<li class="pinterest">
			<a href="https://www.pinterest.com/pin/create/button/?url=<?php echo rawurlencode( esc_url( $product_url ) ); ?>&amp;media=<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>&amp;description=<?php echo rawurlencode( $product_title ); ?>" target="_blank">
				<span class="fa fa-pinterest-p"></span>
				<div class="product-share-text"><?php esc_html_e( 'Pin This Product', 'pluton' ); ?></div>
			</a>
		</li>

		<li class="email">
			<a href="mailto:?subject=<?php echo rawurlencode( $product_title ); ?>&amp;body=<?php echo esc_url( $product_url ); ?>" target="_blank">
				<span class="fa fa-envelope-o"></span>
				<div class="product-share-text"><?php esc_html_e( 'Mail This Product', 'pluton' ); ?></div>
			</a>
		</li>

	</ul>

</div><!-- .entry-share -->