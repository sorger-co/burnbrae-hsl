<?php

/**
 * Change number of products that are displayed per page
 */ 
add_filter( 'loop_shop_per_page', 'hsl_loop_shop_per_page', 20 );
function hsl_loop_shop_per_page( $cols ) {
  $cols = 99;
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
#remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
#add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 11 );

remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

function hsl_get_product_cat_translation_ids( $term_id ) {
    $term_ids = array( (int) $term_id );

    if ( function_exists( 'pll_get_term' ) ) {
        if ( function_exists( 'pll_languages_list' ) ) {
            foreach ( pll_languages_list( array( 'fields' => 'slug' ) ) as $language ) {
                $translated_term_id = pll_get_term( $term_id, $language );
                if ( $translated_term_id ) {
                    $term_ids[] = (int) $translated_term_id;
                }
            }
        } else {
            foreach ( array( 'en', 'fr' ) as $language ) {
                $translated_term_id = pll_get_term( $term_id, $language );
                if ( $translated_term_id ) {
                    $term_ids[] = (int) $translated_term_id;
                }
            }
        }
    }

    return array_unique( array_filter( $term_ids ) );
}

function hsl_is_product_category_translation( $term, $term_id ) {
    if ( ! $term instanceof WP_Term ) {
        return false;
    }

    return in_array( (int) $term->term_id, hsl_get_product_cat_translation_ids( $term_id ), true );
}

function hsl_is_current_product_category_translation( $term_id ) {
    return hsl_is_product_category_translation( get_queried_object(), $term_id );
}

function hsl_current_product_category_parent_is_translation( $term_id ) {
    $term = get_queried_object();
    if ( ! $term instanceof WP_Term ) {
        return false;
    }

    return in_array( (int) $term->parent, hsl_get_product_cat_translation_ids( $term_id ), true );
}

/**
 * Link title in loop
 */
#add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 9 );
#add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 22 );

/**
 * Add loop container
 */
function hsl_open_loop_container() {
    $prod_loop_class = 'hsl-loop-container';
    if( hsl_is_current_product_category_translation( 19 ) || hsl_current_product_category_parent_is_translation( 19 ) ){
        $prod_loop_class .=' shell-eggs';
    }
    if ( ! hsl_is_current_product_category_translation( 19 ) ) {
        echo '<div class="'.$prod_loop_class.'">';
    }
}
add_action( 'woocommerce_before_shop_loop', 'hsl_open_loop_container', 9 );

function hsl_close_loop_container() {
    if ( ! hsl_is_current_product_category_translation( 19 ) ) {
        echo '</div>';
    }
}
add_action( 'woocommerce_after_main_content', 'hsl_close_loop_container', 1);

/**
 * Add loop after header container
 */
function hsl_after_loop_container() {
    if( hsl_is_current_product_category_translation( 19 ) || hsl_current_product_category_parent_is_translation( 19 ) ) {
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
add_action('woocommerce_shop_loop_item_title', 'display_egg_size_attributes_on_loop', 21 );
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
    return hsl__( 'View Product' );
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

/**
 * Change product title in loop
 */ 
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );

function bbf_loop_product_title() {
    global $product;

    $prod_name = $product->get_name();

    // Get brand name from 'product_brand' taxonomy
    $brand_names = wp_get_post_terms( $product->get_id(), 'product_brand', array( 'fields' => 'names' ) );
    $brand_html = '';
    if ( ! is_wp_error( $brand_names ) && ! empty( $brand_names ) ) {
        $brand_html = '<span class="product-brand">' . esc_html( implode( ', ', $brand_names ) ) . '</span> ';
    }

    $output = '<h2 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . $brand_html . $prod_name . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.

    echo $output;
}
add_action( 'woocommerce_shop_loop_item_title', 'bbf_loop_product_title', 11 );


// Force WooCommerce to always use custom sorting (drag & drop)
add_filter( 'woocommerce_get_catalog_ordering_args', function( $args ) {
    $args['orderby']  = 'menu_order title'; // custom order first, then alphabetical
    $args['order']    = 'ASC';
    $args['meta_key'] = '';
    return $args;
});

// Set default orderby to menu_order
add_filter( 'woocommerce_default_catalog_orderby', function() {
    return 'menu_order';
});

/**
 * Translate the WooCommerce product permalink base for French product URLs.
 *
 */
function hsl_get_french_product_base() {
    return 'nos-produits';
}

function hsl_get_french_product_category_base() {
    return hsl_get_french_product_base() . '/categorie';
}

function hsl_get_french_recipe_product_family_base() {
    return 'famille-de-produits';
}

function hsl_get_default_recipe_product_family_base() {
    return 'product-family';
}

function hsl_get_default_product_base() {
    if ( function_exists( 'wc_get_permalink_structure' ) ) {
        $permalinks = wc_get_permalink_structure();
        if ( ! empty( $permalinks['product_rewrite_slug'] ) ) {
            $rewrite_slug = trim( str_replace( '/%product_cat%', '', $permalinks['product_rewrite_slug'] ), '/' );
            if ( $rewrite_slug ) {
                return $rewrite_slug;
            }
        }
    }

    return 'our-products';
}

function hsl_get_default_product_category_base() {
    if ( function_exists( 'wc_get_permalink_structure' ) ) {
        $permalinks = wc_get_permalink_structure();
        if ( ! empty( $permalinks['category_rewrite_slug'] ) ) {
            return trim( $permalinks['category_rewrite_slug'], '/' );
        }
    }

    return hsl_get_default_product_base() . '/category';
}

function hsl_product_permalink_language( $post ) {
    if ( function_exists( 'pll_get_post_language' ) ) {
        $language = pll_get_post_language( $post->ID, 'slug' );
        if ( $language ) {
            return $language;
        }
    }

    return function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : '';
}

function hsl_is_french_url_context( $url = '' ) {
    if ( function_exists( 'pll_current_language' ) && pll_current_language( 'slug' ) === 'fr' ) {
        return true;
    }

    return is_string( $url ) && strpos( $url, '/fr/' ) !== false;
}

function hsl_translate_french_product_permalink_base( $permalink, $post ) {
    if ( ! $post instanceof WP_Post || $post->post_type !== 'product' ) {
        return $permalink;
    }

    if ( hsl_product_permalink_language( $post ) !== 'fr' ) {
        return $permalink;
    }

    $default_base = hsl_get_default_product_base();
    $french_base = hsl_get_french_product_base();

    return str_replace( '/' . $default_base . '/', '/' . $french_base . '/', $permalink );
}
add_filter( 'post_type_link', 'hsl_translate_french_product_permalink_base', 30, 2 );

function hsl_translate_french_product_category_link( $termlink, $term, $taxonomy ) {
    if ( $taxonomy !== 'product_cat' || ! $term instanceof WP_Term ) {
        return $termlink;
    }

    if ( ! hsl_is_french_url_context( $termlink ) && function_exists( 'pll_get_term_language' ) && pll_get_term_language( $term->term_id, 'slug' ) !== 'fr' ) {
        return $termlink;
    }

    $default_base = hsl_get_default_product_category_base();
    $french_base = hsl_get_french_product_category_base();

    return str_replace( '/' . $default_base . '/', '/' . $french_base . '/', $termlink );
}
add_filter( 'term_link', 'hsl_translate_french_product_category_link', 30, 3 );

function hsl_translate_french_recipe_product_family_link( $termlink, $term, $taxonomy ) {
    if ( $taxonomy !== 'product_family' || ! $term instanceof WP_Term ) {
        return $termlink;
    }

    if ( ! hsl_is_french_url_context( $termlink ) && function_exists( 'pll_get_term_language' ) && pll_get_term_language( $term->term_id, 'slug' ) !== 'fr' ) {
        return $termlink;
    }

    return str_replace( '/' . hsl_get_default_recipe_product_family_base() . '/', '/' . hsl_get_french_recipe_product_family_base() . '/', $termlink );
}
add_filter( 'term_link', 'hsl_translate_french_recipe_product_family_link', 30, 3 );

function hsl_add_french_product_rewrite_rules() {
    add_rewrite_rule(
        '^fr/' . hsl_get_french_recipe_product_family_base() . '/(.+?)/?$',
        'index.php?product_family=$matches[1]&lang=fr',
        'top'
    );

    add_rewrite_rule(
        '^fr/' . hsl_get_french_product_category_base() . '/(.+?)/?$',
        'index.php?product_cat=$matches[1]&lang=fr',
        'top'
    );

    add_rewrite_rule(
        '^fr/' . hsl_get_french_product_base() . '/(?:.+/)?([^/]+)/?$',
        'index.php?product=$matches[1]&lang=fr',
        'top'
    );
}
add_action( 'init', 'hsl_add_french_product_rewrite_rules', 20 );

function hsl_redirect_old_french_recipe_product_family_base() {
    if ( is_admin() || ! is_tax( 'product_family' ) || ! function_exists( 'pll_current_language' ) || pll_current_language( 'slug' ) !== 'fr' ) {
        return;
    }

    $request_path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
    $old_base = 'fr/' . hsl_get_default_recipe_product_family_base();

    if ( strpos( $request_path, $old_base . '/' ) !== 0 ) {
        return;
    }

    $redirect_path = preg_replace(
        '#^' . preg_quote( $old_base, '#' ) . '#',
        'fr/' . hsl_get_french_recipe_product_family_base(),
        $request_path
    );

    wp_safe_redirect( home_url( '/' . $redirect_path . '/' ), 301 );
    exit;
}
add_action( 'template_redirect', 'hsl_redirect_old_french_recipe_product_family_base' );

function hsl_redirect_mistranslated_english_recipe_product_family_base() {
    if ( is_admin() || ( function_exists( 'pll_current_language' ) && pll_current_language( 'slug' ) === 'fr' ) ) {
        return;
    }

    $request_path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
    $wrong_base = hsl_get_french_recipe_product_family_base();

    if ( $request_path !== $wrong_base && strpos( $request_path, $wrong_base . '/' ) !== 0 ) {
        return;
    }

    $redirect_path = preg_replace(
        '#^' . preg_quote( $wrong_base, '#' ) . '#',
        hsl_get_default_recipe_product_family_base(),
        $request_path
    );

    wp_safe_redirect( home_url( '/' . trailingslashit( $redirect_path ) ), 301 );
    exit;
}
add_action( 'template_redirect', 'hsl_redirect_mistranslated_english_recipe_product_family_base' );

function hsl_replace_old_french_taxonomy_links_in_output() {
    if ( is_admin() || wp_doing_ajax() || ! function_exists( 'pll_current_language' ) || pll_current_language( 'slug' ) !== 'fr' ) {
        return;
    }

    ob_start( function( $html ) {
        return str_replace(
            array(
                home_url( '/fr/' . hsl_get_default_recipe_product_family_base() . '/' ),
                home_url( '/fr/' . hsl_get_default_product_category_base() . '/' ),
                home_url( '/' . hsl_get_french_product_category_base() . '/' ),
                '/fr/' . hsl_get_default_recipe_product_family_base() . '/',
                '/fr/' . hsl_get_default_product_category_base() . '/',
                'href="/' . hsl_get_french_product_category_base() . '/',
                "href='/" . hsl_get_french_product_category_base() . '/',
            ),
            array(
                home_url( '/fr/' . hsl_get_french_recipe_product_family_base() . '/' ),
                home_url( '/fr/' . hsl_get_french_product_category_base() . '/' ),
                home_url( '/' . hsl_get_default_product_category_base() . '/' ),
                '/fr/' . hsl_get_french_recipe_product_family_base() . '/',
                '/fr/' . hsl_get_french_product_category_base() . '/',
                'href="/' . hsl_get_default_product_category_base() . '/',
                "href='/" . hsl_get_default_product_category_base() . '/',
            ),
            $html
        );
    } );
}
add_action( 'template_redirect', 'hsl_replace_old_french_taxonomy_links_in_output', 20 );

function hsl_redirect_mistranslated_english_product_category_base() {
    if ( is_admin() || ( function_exists( 'pll_current_language' ) && pll_current_language( 'slug' ) === 'fr' ) ) {
        return;
    }

    $request_path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
    $wrong_base = hsl_get_french_product_category_base();

    if ( $request_path !== $wrong_base && strpos( $request_path, $wrong_base . '/' ) !== 0 ) {
        return;
    }

    $redirect_path = preg_replace(
        '#^' . preg_quote( $wrong_base, '#' ) . '#',
        hsl_get_default_product_category_base(),
        $request_path
    );

    wp_safe_redirect( home_url( '/' . trailingslashit( $redirect_path ) ), 301 );
    exit;
}
add_action( 'template_redirect', 'hsl_redirect_mistranslated_english_product_category_base' );

function hsl_redirect_old_french_product_category_base() {
    if ( is_admin() || ! is_product_category() || ! function_exists( 'pll_current_language' ) || pll_current_language( 'slug' ) !== 'fr' ) {
        return;
    }

    $request_path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
    $old_base = 'fr/' . hsl_get_default_product_category_base();

    if ( strpos( $request_path, $old_base . '/' ) !== 0 ) {
        return;
    }

    $redirect_path = preg_replace(
        '#^' . preg_quote( $old_base, '#' ) . '#',
        'fr/' . hsl_get_french_product_category_base(),
        $request_path
    );

    wp_safe_redirect( home_url( '/' . $redirect_path . '/' ), 301 );
    exit;
}
add_action( 'template_redirect', 'hsl_redirect_old_french_product_category_base' );

function hsl_fix_taxonomy_language_switcher_home_fallback() {
    if ( is_admin() || wp_doing_ajax() || ! function_exists( 'pll_current_language' ) || ! function_exists( 'pll_get_term' ) ) {
        return;
    }

    if ( ! is_tax( array( 'product_cat', 'product_family', 'meal_type', 'recipe_attribute' ) ) ) {
        return;
    }

    $term = get_queried_object();
    if ( ! $term instanceof WP_Term ) {
        return;
    }

    $request_path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
    $current_language = strpos( $request_path, 'fr/' ) === 0 ? 'fr' : pll_current_language( 'slug' );
    $target_language = $current_language === 'fr' ? 'en' : 'fr';
    $translated_term_id = pll_get_term( $term->term_id, $target_language );
    if ( ! $translated_term_id ) {
        return;
    }

    $target_link = get_term_link( (int) $translated_term_id, $term->taxonomy );
    if ( is_wp_error( $target_link ) ) {
        return;
    }
    if ( $target_language === 'en' ) {
        $target_link = str_replace(
            array(
                '/' . hsl_get_french_product_category_base() . '/',
                '/' . hsl_get_french_recipe_product_family_base() . '/',
            ),
            array(
                '/' . hsl_get_default_product_category_base() . '/',
                '/' . hsl_get_default_recipe_product_family_base() . '/',
            ),
            $target_link
        );
    }

    $fallback_link = untrailingslashit( $target_language === 'fr' ? home_url( '/fr/home-fr/' ) : home_url( '/' ) );
    $switcher_label = $target_language === 'fr' ? 'Français' : 'English';

    ob_start( function( $html ) use ( $fallback_link, $target_link, $switcher_label ) {
        $pattern = '#<a\b([^>]*\bhref=["\'])' . preg_quote( $fallback_link, '#' ) . '/?(["\'][^>]*)>\s*' . preg_quote( $switcher_label, '#' ) . '\s*</a>#i';
        return preg_replace( $pattern, '<a$1' . esc_url( $target_link ) . '$2>' . esc_html( $switcher_label ) . '</a>', $html );
    } );

    add_action( 'wp_footer', function() use ( $target_link, $switcher_label ) {
        ?>
        <script>
        document.querySelectorAll('a').forEach(function(link) {
            if (link.textContent.trim() === <?php echo wp_json_encode( $switcher_label ); ?>) {
                link.href = <?php echo wp_json_encode( esc_url( $target_link ) ); ?>;
            }
        });
        </script>
        <?php
    }, 99 );
}
add_action( 'template_redirect', 'hsl_fix_taxonomy_language_switcher_home_fallback', 25 );

function hsl_flush_french_product_rewrite_rules() {
    $version = '3';
    if ( get_option( 'hsl_fr_product_base_rewrite_version' ) === $version ) {
        return;
    }

    hsl_add_french_product_rewrite_rules();
    flush_rewrite_rules();
    update_option( 'hsl_fr_product_base_rewrite_version', $version );
}
add_action( 'admin_init', 'hsl_flush_french_product_rewrite_rules' );