<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Check if the product is a valid WooCommerce product and ensure its visibility before proceeding.
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( '', $product ); ?>>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	do_action( 'woocommerce_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_rating - 5
	 * @hooked woocommerce_template_loop_price - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
	// Check if product is in category 19 or has parent 19
	$product_cats = get_the_terms( $product->get_id(), 'product_cat' );
	$in_cat_19 = false;
	if ( ! empty( $product_cats ) && ! is_wp_error( $product_cats ) ) {
	    foreach ( $product_cats as $cat ) {
	        if ( $cat->term_id == 19 || $cat->parent == 19 ) {
	            $in_cat_19 = true;
	        }
	    }
	    if ( $in_cat_19 ) {
	        remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	        // Check for _hsl_nutrition_image_id metabox and display image URL if exists
	        $nutrition_image_id = get_post_meta( $product->get_id(), '_hsl_nutrition_image_id', true );
	        if ( $nutrition_image_id ) {
	            $nutrition_image_url = wp_get_attachment_url( $nutrition_image_id );
	            if ( $nutrition_image_url ) {
									echo '<a class="button product_type_simple" href="' . esc_url( $nutrition_image_url ) . '" data-elementor-open-lightbox="yes">Nutritional Information</a>';
	            }
	        }
	    } else {
	        add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	    }
	}

	do_action( 'woocommerce_after_shop_loop_item' );
	?>
</li>
