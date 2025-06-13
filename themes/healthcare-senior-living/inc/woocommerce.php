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
add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 12 );

/**
 * Add loop container
 */
function hsl_open_loop_container() {
    $current_category = get_queried_object(); 
    $current_category_parent = $current_category->parent;
    $prod_loop_class = 'hsl-loop-container';
    if( is_product_category(19) || $current_category_parent == 19 ){
        $prod_loop_class .=' shell-eggs';
    }
    echo '<div class="'.$prod_loop_class.'">';
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
    } else if (!is_product())  {
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
add_action('woocommerce_shop_loop_item_title', 'display_egg_size_attributes_on_loop', 11 );
function display_egg_size_attributes_on_loop() {
    global $product;

    $value = $product->get_attribute('Egg Size');

    if ( ! empty($value) ) {
        $attributes = array_map(function($attr) {
            // Convert attribute names to lowercase and replace spaces with dashes
            return '<div class="hsl-egg-size-attr ' . esc_attr( strtolower( str_replace(' ', '-', $attr) ) ) . '">'.$attr.'</div>';
        }, explode(', ', $value)); // Assuming attributes are comma-separated

        echo '<div class="hsl-egg-sizes">'. implode(' ', $attributes) . '</div>';
    }
}

/**
 * Loop product link
 */
add_filter( 'woocommerce_product_add_to_cart_text', 'woo_archive_page_cart_button_custom_text' );
function woo_archive_page_cart_button_custom_text() {
    return __( 'View Product', 'woocommerce' );
}

/**
 * SINGLE PRODUCT PAGE
 */
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

// --- WooCommerce Product Metaboxes ---
add_action('add_meta_boxes', function() {
    add_meta_box('hsl_product_info', __('Product Information', 'bbf_hsl'), 'hsl_product_info_metabox', 'product', 'normal', 'default');
    add_meta_box('hsl_nutrition_info', __('Nutrition Information', 'bbf_hsl'), 'hsl_nutrition_info_metabox', 'product', 'normal', 'default');
    add_meta_box('hsl_heating_instructions', __('Heating Instructions', 'bbf_hsl'), 'hsl_heating_instructions_metabox', 'product', 'normal', 'default');
});

function hsl_product_info_metabox($post) {
    $upc = get_post_meta($post->ID, '_hsl_upc', true);
    $scc = get_post_meta($post->ID, '_hsl_scc', true);
    $kosher = get_post_meta($post->ID, '_hsl_kosher', true);
    $size = get_post_meta($post->ID, '_hsl_size', true);
    $net_weight = get_post_meta($post->ID, '_hsl_net_weight', true);
    $shelf_life = get_post_meta($post->ID, '_hsl_shelf_life', true);
    $ingredients = get_post_meta($post->ID, '_hsl_ingredients', true);
    $contains = get_post_meta($post->ID, '_hsl_contains', true);
    $product_info_image_id = get_post_meta($post->ID, '_hsl_product_info_image_id', true);
    $product_info_image_url = $product_info_image_id ? wp_get_attachment_url($product_info_image_id) : '';
    ?>
    <p><label>UPC: <input type="text" name="hsl_upc" value="<?php echo esc_attr($upc); ?>" class="widefat"></label></p>
    <p><label>SCC: <input type="text" name="hsl_scc" value="<?php echo esc_attr($scc); ?>" class="widefat"></label></p>
    <p>Kosher:
        <label><input type="radio" name="hsl_kosher" value="yes" <?php checked($kosher, 'yes'); ?>> Yes</label>
        <label><input type="radio" name="hsl_kosher" value="no" <?php checked($kosher, 'no'); if($kosher===''){echo ' checked';} ?>> No</label>
    </p>
    <p><label>Size: <input type="text" name="hsl_size" value="<?php echo esc_attr($size); ?>" class="widefat"></label></p>
    <p><label>Net weight: <input type="text" name="hsl_net_weight" value="<?php echo esc_attr($net_weight); ?>" class="widefat"></label></p>
    <p><label>Shelf Life/Storage: <input type="text" name="hsl_shelf_life" value="<?php echo esc_attr($shelf_life); ?>" class="widefat"></label></p>
    <p><label>Ingredients:<br><textarea name="hsl_ingredients" class="widefat" rows="3"><?php echo esc_textarea($ingredients); ?></textarea></label></p>
    <p><label>Contains:<br><textarea name="hsl_contains" class="widefat" rows="3"><?php echo esc_textarea($contains); ?></textarea></label></p>
    <div>
        <label><strong>Product Information Image</strong></label><br>
        <input type="hidden" name="hsl_product_info_image_id" id="hsl_product_info_image_id" value="<?php echo esc_attr($product_info_image_id); ?>">
        <div id="hsl_product_info_image_wrapper">
            <?php if($product_info_image_url) echo '<img src="'.esc_url($product_info_image_url).'" style="max-width:200px;display:block;" />'; ?>
        </div>
        <button type="button" class="button" id="hsl_product_info_image_upload">Upload/Select Image</button>
        <button type="button" class="button" id="hsl_product_info_image_remove" style="<?php echo $product_info_image_id ? '' : 'display:none;'; ?>">Remove Image</button>
    </div>
    <script>
    jQuery(document).ready(function($){
        var frame;
        $('#hsl_product_info_image_upload').on('click', function(e){
            e.preventDefault();
            if(frame){ frame.open(); return; }
            frame = wp.media({
                title: '<?php _e('Select or Upload Product Information Image', 'bbf_hsl'); ?>',
                button: { text: '<?php _e('Use this image', 'bbf_hsl'); ?>' },
                multiple: false
            });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#hsl_product_info_image_id').val(attachment.id);
                $('#hsl_product_info_image_wrapper').html('<img src="'+attachment.url+'" style="max-width:200px;display:block;" />');
                $('#hsl_product_info_image_remove').show();
            });
            frame.open();
        });
        $('#hsl_product_info_image_remove').on('click', function(){
            $('#hsl_product_info_image_id').val('');
            $('#hsl_product_info_image_wrapper').html('');
            $(this).hide();
        });
    });
    </script>
    <?php
}

function hsl_nutrition_info_metabox($post) {
    $image_id = get_post_meta($post->ID, '_hsl_nutrition_image_id', true);
    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
    ?>
    <div>
        <input type="hidden" name="hsl_nutrition_image_id" id="hsl_nutrition_image_id" value="<?php echo esc_attr($image_id); ?>">
        <div id="hsl_nutrition_image_wrapper">
            <?php if($image_url) echo '<img src="'.esc_url($image_url).'" style="max-width:200px;display:block;" />'; ?>
        </div>
        <button type="button" class="button" id="hsl_nutrition_image_upload">Upload/Select Image</button>
        <button type="button" class="button" id="hsl_nutrition_image_remove" style="<?php echo $image_id ? '' : 'display:none;'; ?>">Remove Image</button>
    </div>
    <script>
    jQuery(document).ready(function($){
        var frame;
        $('#hsl_nutrition_image_upload').on('click', function(e){
            e.preventDefault();
            if(frame){ frame.open(); return; }
            frame = wp.media({
                title: '<?php _e('Select or Upload Nutrition Image', 'bbf_hsl'); ?>',
                button: { text: '<?php _e('Use this image', 'bbf_hsl'); ?>' },
                multiple: false
            });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#hsl_nutrition_image_id').val(attachment.id);
                $('#hsl_nutrition_image_wrapper').html('<img src="'+attachment.url+'" style="max-width:200px;display:block;" />');
                $('#hsl_nutrition_image_remove').show();
            });
            frame.open();
        });
        $('#hsl_nutrition_image_remove').on('click', function(){
            $('#hsl_nutrition_image_id').val('');
            $('#hsl_nutrition_image_wrapper').html('');
            $(this).hide();
        });
    });
    </script>
    <?php
}

function hsl_heating_instructions_metabox($post) {
    $content = get_post_meta($post->ID, '_hsl_heating_instructions', true);
    // Use a unique editor ID per post to avoid conflicts
    $editor_id = 'hsl_heating_instructions_' . $post->ID;
    wp_editor($content, $editor_id, [
        'textarea_name' => 'hsl_heating_instructions',
        'media_buttons' => true,
        'textarea_rows' => 8,
    ]);
}

add_action('save_post_product', function($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    $fields = [
        '_hsl_upc' => 'hsl_upc',
        '_hsl_scc' => 'hsl_scc',
        '_hsl_kosher' => 'hsl_kosher',
        '_hsl_size' => 'hsl_size',
        '_hsl_net_weight' => 'hsl_net_weight',
        '_hsl_shelf_life' => 'hsl_shelf_life',
        '_hsl_ingredients' => 'hsl_ingredients',
        '_hsl_contains' => 'hsl_contains',
        '_hsl_nutrition_image_id' => 'hsl_nutrition_image_id',
        '_hsl_product_info_image_id' => 'hsl_product_info_image_id',
        '_hsl_heating_instructions' => 'hsl_heating_instructions',
    ];
    foreach($fields as $meta_key => $field) {
        if(isset($_POST[$field])) {
            if($meta_key === '_hsl_heating_instructions') {
                update_post_meta($post_id, $meta_key, wp_kses_post($_POST[$field]));
            } else {
                update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$field]));
            }
        } else if($meta_key === '_hsl_kosher') {
            update_post_meta($post_id, $meta_key, 'no');
        } else if($meta_key === '_hsl_nutrition_image_id' || $meta_key === '_hsl_product_info_image_id') {
            update_post_meta($post_id, $meta_key, '');
        }
    }
});

