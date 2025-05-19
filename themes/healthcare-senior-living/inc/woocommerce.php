<?php
/**
 * Change number of products that are displayed per page
 */ 
add_filter( 'loop_shop_per_page', 'hsl_loop_shop_per_page', 20 );
function hsl_loop_shop_per_page( $cols ) {
  $cols = 24;
  return $cols;
}

/**
 * Change number of products that are displayed per row
 */
add_filter( 'loop_shop_columns', 'hsl_loop_columns' );
function hsl_loop_columns() {
  return 3; 
}

/**
 * Remove breadcrumbs, results count and catalog ordering from loop pages
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

/**
 * Display short descriptions in loop
 */
function hsl_display_short_desciption_in_loop() {
    
	global $product;
    
    $short_description = apply_filters( 'woocommerce_short_description', $product->get_short_description() );
    
    if ( ! empty( $short_description ) ) {
        echo '<p class="info">' . $short_description . '</p>';
    }
}
add_action( 'woocommerce_after_shop_loop_item_title', 'hsl_display_short_desciption_in_loop', 20 );

/**
 * Link image in loop
 */
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 11 );

/**
 * Link title in loop
 */
add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 9 );
add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 11 );

/**
 * Add loop container
 */
function hsl_open_loop_container() {
    echo '<div class="hsl-loop-container">';
}
add_action( 'woocommerce_before_shop_loop', 'hsl_open_loop_container', 9 );

function hsl_close_loop_container() {
    echo '</div>';
}
add_action( 'woocommerce_after_main_content', 'hsl_close_loop_container', 1);

/**
 * Add loop after header container
 */
function hsl_after_loop_container() {
    $current_category = get_queried_object(); 
    $current_category_parent = $current_category->parent;
    if( is_product_category(19) || $current_category_parent == 19 ) {
		echo do_shortcode( '[INSERT_ELEMENTOR id="2100"]' );
    } else  {
        echo do_shortcode( '[INSERT_ELEMENTOR id="2083"]' );
    }

}
add_action( 'woocommerce_after_main_content', 'hsl_after_loop_container', 2);

/**
 * Add loop content container
 */
function hsl_open_loop_content_container() {
    echo '<div class="hsl-loop-content-container">';
}
add_action( 'woocommerce_shop_loop_item_title', 'hsl_open_loop_content_container', 7 );

function hsl_close_div_container() {
    echo '</div>';
}
add_action( 'woocommerce_after_shop_loop_item', 'hsl_close_div_container', 11 );

/**
 * Add loop image container
 */
function hsl_open_loop_image_container() {
    echo '<div class="hsl-loop-img-container">';
}
add_action( 'woocommerce_before_shop_loop_item', 'hsl_open_loop_image_container', 8 );

add_action( 'woocommerce_before_shop_loop_item_title', 'hsl_close_div_container', 13 );

/**
 * Add loop title container
 */
function hsl_open_loop_content_inner_container() {
    echo '<div>';
}
add_action( 'woocommerce_shop_loop_item_title', 'hsl_open_loop_content_inner_container', 8 );
add_action( 'woocommerce_after_shop_loop_item_title', 'hsl_close_div_container', 21 );

/**
 * Add attributes in loop
 */
add_action('woocommerce_before_shop_loop_item_title', 'display_custom_product_attributes_on_loop', 12 );
function display_custom_product_attributes_on_loop() {
    global $product;

    $value = $product->get_attribute('Attribute');

    if ( ! empty($value) ) {
        $attributes = array_map(function($attr) {
            // Convert attribute names to lowercase and replace spaces with dashes
            return '<div class="hsl-attr ' . esc_attr( strtolower( str_replace(' ', '-', $attr) ) ) . '"></div>';
        }, explode(', ', $value)); // Assuming attributes are comma-separated

        echo '<div class="hsl-prod-attrs">'. implode(' ', $attributes) . '</div>';
    }
}

/**
 * Loop product link
 */
add_filter( 'woocommerce_product_add_to_cart_text', 'woo_archive_page_cart_button_custom_text' );
function woo_archive_page_cart_button_custom_text() {
    return __( 'View Product', 'woocommerce' );
}