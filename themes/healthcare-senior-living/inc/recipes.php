<?php
/** 
* Recipes Post Type
*/

if ( ! function_exists('recipe_post_type') ) {

	// Register Custom Post Type
	function recipe_post_type() {

		$labels = array(
			'name'                  => _x( 'Recipes', 'Post Type General Name', 'bbf_hsl' ),
			'singular_name'         => _x( 'Recipe', 'Post Type Singular Name', 'bbf_hsl' ),
			'menu_name'             => __( 'Recipes', 'bbf_hsl' ),
			'name_admin_bar'        => __( 'Recipe', 'bbf_hsl' ),
			'archives'              => __( 'Recipe Archives', 'bbf_hsl' ),
			'attributes'            => __( 'Recipe Attributes', 'bbf_hsl' ),
			'parent_item_colon'     => __( 'Parent Recipe:', 'bbf_hsl' ),
			'all_items'             => __( 'All Recipes', 'bbf_hsl' ),
			'add_new_item'          => __( 'Add New Recipe', 'bbf_hsl' ),
			'add_new'               => __( 'Add New', 'bbf_hsl' ),
			'new_item'              => __( 'New Recipe', 'bbf_hsl' ),
			'edit_item'             => __( 'Edit Recipe', 'bbf_hsl' ),
			'update_item'           => __( 'Update Recipe', 'bbf_hsl' ),
			'view_item'             => __( 'View Recipe', 'bbf_hsl' ),
			'view_items'            => __( 'View Recipes', 'bbf_hsl' ),
			'search_items'          => __( 'Search Recipe', 'bbf_hsl' ),
			'not_found'             => __( 'Not found', 'bbf_hsl' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'bbf_hsl' ),
			'featured_image'        => __( 'Featured Image', 'bbf_hsl' ),
			'set_featured_image'    => __( 'Set featured image', 'bbf_hsl' ),
			'remove_featured_image' => __( 'Remove featured image', 'bbf_hsl' ),
			'use_featured_image'    => __( 'Use as featured image', 'bbf_hsl' ),
			'insert_into_item'      => __( 'Insert into recipe', 'bbf_hsl' ),
			'uploaded_to_this_item' => __( 'Uploaded to this recipe', 'bbf_hsl' ),
			'items_list'            => __( 'Recipes list', 'bbf_hsl' ),
			'items_list_navigation' => __( 'Recipes list navigation', 'bbf_hsl' ),
			'filter_items_list'     => __( 'Filter recipes list', 'bbf_hsl' ),
		);
		$rewrite = array(
			'slug'                  => 'recipes',
			'with_front'            => true,
			'pages'                 => true,
			'feeds'                 => true,
		);
		$args = array(
			'label'                 => __( 'Recipe', 'bbf_hsl' ),
			'description'           => __( '', 'bbf_hsl' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail' ),
			// 'taxonomies'            => array( 'category', 'post_tag' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-book',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'rewrite'               => $rewrite,
			'capability_type'       => 'post',
		);
		register_post_type( 'recipe', $args );

	}
	add_action( 'init', 'recipe_post_type', 0 );

}

/**
 * Recipe Summery Meta Box
 */
add_action( 'add_meta_boxes', 'recipe_summary_add_meta_box' );

    add_action( 'save_post', 'recipe_summary_save_postdata' );
    
    function recipe_summary_add_meta_box() {
        add_meta_box(
            'hsl_recipe_summary',
            __( 'Recipe Summary Items', 'bbf_hsl' ),
            'recipe_summary_inner_custom_box',
            'recipe');
    }
    
    function recipe_summary_inner_custom_box() {
        global $post;
        // Use nonce for verification
        wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMeta_noncename' );
        ?>
        <div id="summary_inner">
        <?php

        $recipe_summaries = get_post_meta($post->ID,'recipe_summaries',true);
        
        $c = 0;
        if ( is_array($recipe_summaries) && count( $recipe_summaries ) > 0 ) {
            foreach( $recipe_summaries as $summary_item ) {
                if ( isset( $summary_item['title'] ) || isset( $summary_item['value'] ) ) {
                    printf( '<p>Title: <input type="text" name="recipe_summaries[%1$s][title]" value="%2$s" /> -- Value : <input type="text" name="recipe_summaries[%1$s][value]" value="%3$s" size="50" />&nbsp;<span class="remove-summary">%4$s</span></p>', $c, esc_attr($summary_item['title']), esc_attr($summary_item['value']), __( 'Remove Item' ) );
                    $c = $c +1;
                }
            }
        }

        ?>
				<span id="here-summary"></span>
				<span class="add-summary"><?php _e('Add Item'); ?></span>
				<script>
					var $ =jQuery.noConflict();
					$(document).ready(function() {
						var count = <?php echo $c; ?>;
						$(".add-summary").click(function() {
							count = count + 1;
							$('#here-summary').append('<p> Title: <input type="text" name="recipe_summaries['+count+'][title]" value="" /> -- Value : <input type="text" name="recipe_summaries['+count+'][value]" value="" size="50" />&nbsp;<span class="remove-summary">Remove Item</span></p>' );
							return false;
						});
						$(document).on('click', '.remove-summary', function() {
							$(this).parent().remove();
						});
					});
				</script>
			</div><?php
    }
    
    function recipe_summary_save_postdata( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return;

        if ( !isset( $_POST['dynamicMeta_noncename'] ) )
            return;

        if ( !wp_verify_nonce( $_POST['dynamicMeta_noncename'], plugin_basename( __FILE__ ) ) )
            return;

        if (isset($_POST['recipe_summaries']) && is_array($_POST['recipe_summaries'])) {
            $recipe_summaries = array_values($_POST['recipe_summaries']); // reindex array
            update_post_meta($post_id,'recipe_summaries',$recipe_summaries);
        } else {
            delete_post_meta($post_id,'recipe_summaries');
        }
    }

/**
 * Recipe Ingredients Meta Box
 */
add_action( 'add_meta_boxes', 'recipe_ingredients_add_meta_box' );
add_action( 'save_post', 'recipe_ingredients_save_postdata' );

function recipe_ingredients_add_meta_box() {
    add_meta_box(
        'hsl_recipe_ingredients',
        __( 'Recipe Ingredients', 'bbf_hsl' ),
        'recipe_ingredients_inner_custom_box',
        'recipe');
}

function recipe_ingredients_inner_custom_box() {
    global $post;
    wp_nonce_field( plugin_basename( __FILE__ ), 'ingredientsMeta_noncename' );
    ?>
    <div id="ingredients_inner">
    <?php
    $recipe_ingredients = get_post_meta($post->ID,'recipe_ingredients',true);
    $c = 0;
    if ( is_array($recipe_ingredients) && count( $recipe_ingredients ) > 0 ) {
        foreach( $recipe_ingredients as $ingredient_item ) {
            if ( isset( $ingredient_item['title'] ) || isset( $ingredient_item['value'] ) ) {
                printf( '<p>Amount: <textarea name="recipe_ingredients[%1$s][title]" rows="1" cols="15">%2$s</textarea> -- Ingredient : <textarea name="recipe_ingredients[%1$s][value]" rows="1" cols="50">%3$s</textarea>&nbsp;<span class="remove-ingredient">%4$s</span></p>', $c, esc_textarea($ingredient_item['title']), esc_textarea($ingredient_item['value']), __( 'Remove Item' ) );
                $c = $c +1;
            }
        }
    }
    ?>
        <span id="here-ingredient"></span>
        <span class="add-ingredient"><?php _e('Add Ingredient'); ?></span>
        <script>
            var $ =jQuery.noConflict();
            $(document).ready(function() {
                var count = <?php echo $c; ?>;
                $(".add-ingredient").click(function() {
                    count = count + 1;
                    $('#here-ingredient').append('<p> Amount: <textarea name="recipe_ingredients['+count+'][title]" rows="1" cols="15"></textarea> -- Ingredient : <textarea name="recipe_ingredients['+count+'][value]" rows="1" cols="50"></textarea>&nbsp;<span class="remove-ingredient">Remove Item</span></p>' );
                    return false;
                });
                $(document).on('click', '.remove-ingredient', function() {
                    $(this).parent().remove();
                });
            });
        </script>
    </div><?php
}

function recipe_ingredients_save_postdata( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;
    if ( !isset( $_POST['ingredientsMeta_noncename'] ) )
        return;
    if ( !wp_verify_nonce( $_POST['ingredientsMeta_noncename'], plugin_basename( __FILE__ ) ) )
        return;
    if (isset($_POST['recipe_ingredients']) && is_array($_POST['recipe_ingredients'])) {
        $recipe_ingredients = array_map(function($item) {
            return [
                'title' => isset($item['title']) ? wp_kses_post($item['title']) : '',
                'value' => isset($item['value']) ? wp_kses_post($item['value']) : ''
            ];
        }, array_values($_POST['recipe_ingredients']));
        update_post_meta($post_id,'recipe_ingredients',$recipe_ingredients);
    } else {
        delete_post_meta($post_id,'recipe_ingredients');
    }
}

/**
 * Recipe Nutrients Meta Box
 */
add_action( 'add_meta_boxes', 'recipe_nutrients_add_meta_box' );
add_action( 'save_post', 'recipe_nutrients_save_postdata' );

function recipe_nutrients_add_meta_box() {
    add_meta_box(
        'hsl_recipe_nutrients',
        __( 'Recipe Nutrients', 'bbf_hsl' ),
        'recipe_nutrients_inner_custom_box',
        'recipe');
}

function recipe_nutrients_inner_custom_box() {
    global $post;
    wp_nonce_field( plugin_basename( __FILE__ ), 'nutrientsMeta_noncename' );
    $nutrients_title = get_post_meta($post->ID, 'recipe_nutrients_title', true);
    $recipe_nutrients = get_post_meta($post->ID, 'recipe_nutrients', true);
    $c = 0;
    ?>
    <div id="nutrients_inner">
        <p><strong><?php _e('Nutrients Title:', 'bbf_hsl'); ?></strong> <input type="text" name="recipe_nutrients_title" value="<?php echo esc_attr($nutrients_title); ?>" size="50" /></p>
        <?php
        if ( is_array($recipe_nutrients) && count( $recipe_nutrients ) > 0 ) {
            foreach( $recipe_nutrients as $nutrient_item ) {
                if ( isset( $nutrient_item['label'] ) || isset( $nutrient_item['value'] ) ) {
                    printf( '<p>Label: <input type="text" name="recipe_nutrients[%1$s][label]" value="%2$s" size="20" /> -- Value: <input type="text" name="recipe_nutrients[%1$s][value]" value="%3$s" size="20" />&nbsp;<span class="remove-nutrient">%4$s</span></p>', $c, esc_attr($nutrient_item['label']), esc_attr($nutrient_item['value']), __( 'Remove Item' ) );
                    $c = $c + 1;
                }
            }
        }
        ?>
        <span id="here-nutrient"></span>
        <span class="add-nutrient"><?php _e('Add Nutrient'); ?></span>
        <script>
            var $ =jQuery.noConflict();
            $(document).ready(function() {
                var count = <?php echo $c; ?>;
                $(".add-nutrient").click(function() {
                    count = count + 1;
                    $('#here-nutrient').append('<p> Label: <input type="text" name="recipe_nutrients['+count+'][label]" value="" size="20" /> -- Value: <input type="text" name="recipe_nutrients['+count+'][value]" value="" size="20" />&nbsp;<span class="remove-nutrient">Remove Item</span></p>' );
                    return false;
                });
                $(document).on('click', '.remove-nutrient', function() {
                    $(this).parent().remove();
                });
            });
        </script>
    </div>
    <?php
}

function recipe_nutrients_save_postdata( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;
    if ( !isset( $_POST['nutrientsMeta_noncename'] ) )
        return;
    if ( !wp_verify_nonce( $_POST['nutrientsMeta_noncename'], plugin_basename( __FILE__ ) ) )
        return;
    // Save nutrients title
    if ( isset($_POST['recipe_nutrients_title']) ) {
        update_post_meta($post_id, 'recipe_nutrients_title', sanitize_text_field($_POST['recipe_nutrients_title']));
    } else {
        delete_post_meta($post_id, 'recipe_nutrients_title');
    }
    // Save nutrients list
    if (isset($_POST['recipe_nutrients']) && is_array($_POST['recipe_nutrients'])) {
        $recipe_nutrients = array_map(function($item) {
            return [
                'label' => isset($item['label']) ? sanitize_text_field($item['label']) : '',
                'value' => isset($item['value']) ? sanitize_text_field($item['value']) : ''
            ];
        }, array_values($_POST['recipe_nutrients']));
        update_post_meta($post_id,'recipe_nutrients',$recipe_nutrients);
    } else {
        delete_post_meta($post_id,'recipe_nutrients');
    }
}

/**
 * Recipe Featured Products Meta Box
 */
add_action( 'add_meta_boxes', 'recipe_featured_products_add_meta_box' );
add_action( 'save_post', 'recipe_featured_products_save_postdata' );

function recipe_featured_products_add_meta_box() {
    add_meta_box(
        'hsl_recipe_featured_products',
        __( 'Featured Products', 'bbf_hsl' ),
        'recipe_featured_products_inner_custom_box',
        'recipe');
}

function recipe_featured_products_inner_custom_box() {
    global $post;
    wp_nonce_field( plugin_basename( __FILE__ ), 'featuredProductsMeta_noncename' );
    $featured_products = get_post_meta($post->ID, 'recipe_featured_products', true);
    if (!is_array($featured_products)) $featured_products = array();
    // Get all WooCommerce products
    $products = get_posts(array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC',
    ));
    $product_options = array();
    foreach ($products as $product) {
        $sku = get_post_meta($product->ID, '_sku', true);
        $title = get_the_title($product->ID);
        $product_options[$product->ID] = esc_html($sku . ' - ' . $title);
    }
    $c = 0;
    echo '<div id="featured_products_inner">';
    if (count($featured_products) > 0) {
        foreach ($featured_products as $product_id) {
            echo '<p>Product: <select name="recipe_featured_products[]">';
            echo '<option value="">-- Select Product --</option>';
            foreach ($product_options as $id => $label) {
                $selected = ($id == $product_id) ? 'selected' : '';
                echo '<option value="' . esc_attr($id) . '" ' . $selected . '>' . $label . '</option>';
            }
            echo '</select> <span class="remove-featured-product">' . __( 'Remove', 'bbf_hsl' ) . '</span></p>';
            $c++;
        }
    }
    echo '<span id="here-featured-product"></span>';
    echo '<span class="add-featured-product">' . __( 'Add Product', 'bbf_hsl' ) . '</span>';
    ?>
    <script>
    var $ = jQuery.noConflict();
    $(document).ready(function() {
        var productOptions = <?php echo json_encode($product_options); ?>;
        function getProductDropdown() {
            var html = '<select name="recipe_featured_products[]">';
            html += '<option value="">-- Select Product --</option>';
            $.each(productOptions, function(id, label) {
                html += '<option value="'+id+'">'+label+'</option>';
            });
            html += '</select>';
            return html;
        }
        $(".add-featured-product").click(function() {
            $('#here-featured-product').append('<p>Product: ' + getProductDropdown() + ' <span class="remove-featured-product">Remove</span></p>');
            return false;
        });
        $(document).on('click', '.remove-featured-product', function() {
            $(this).parent().remove();
        });
    });
    </script>
    <?php
    echo '</div>';
}

function recipe_featured_products_save_postdata( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;
    if ( !isset( $_POST['featuredProductsMeta_noncename'] ) )
        return;
    if ( !wp_verify_nonce( $_POST['featuredProductsMeta_noncename'], plugin_basename( __FILE__ ) ) )
        return;
    if (isset($_POST['recipe_featured_products']) && is_array($_POST['recipe_featured_products'])) {
        $featured_products = array_filter(array_map('intval', $_POST['recipe_featured_products']));
        update_post_meta($post_id, 'recipe_featured_products', $featured_products);
    } else {
        delete_post_meta($post_id, 'recipe_featured_products');
    }
}

// Register custom taxonomies for recipes
add_action( 'init', 'register_recipe_taxonomies', 1 );
function register_recipe_taxonomies() {
    // Meal Type taxonomy
    $labels_meal = array(
        'name'              => _x( 'Meal Types', 'taxonomy general name', 'bbf_hsl' ),
        'singular_name'     => _x( 'Meal Type', 'taxonomy singular name', 'bbf_hsl' ),
        'search_items'      => __( 'Search Meal Types', 'bbf_hsl' ),
        'all_items'         => __( 'All Meal Types', 'bbf_hsl' ),
        'parent_item'       => __( 'Parent Meal Type', 'bbf_hsl' ),
        'parent_item_colon' => __( 'Parent Meal Type:', 'bbf_hsl' ),
        'edit_item'         => __( 'Edit Meal Type', 'bbf_hsl' ),
        'update_item'       => __( 'Update Meal Type', 'bbf_hsl' ),
        'add_new_item'      => __( 'Add New Meal Type', 'bbf_hsl' ),
        'new_item_name'     => __( 'New Meal Type Name', 'bbf_hsl' ),
        'menu_name'         => __( 'Meal Type', 'bbf_hsl' ),
        'view_item'         => __( 'View Meal Type', 'bbf_hsl' ),
        'popular_items'     => __( 'Popular Meal Types', 'bbf_hsl' ),
        'separate_items_with_commas' => __( 'Separate meal types with commas', 'bbf_hsl' ),
        'add_or_remove_items' => __( 'Add or remove meal types', 'bbf_hsl' ),
        'choose_from_most_used' => __( 'Choose from the most used meal types', 'bbf_hsl' ),
        'not_found'         => __( 'No meal types found', 'bbf_hsl' ),
        'back_to_items'     => __( '← Back to Meal Types', 'bbf_hsl' ),
    );
    register_taxonomy( 'meal_type', array( 'recipe' ), array(
        'hierarchical'      => true,
        'labels'            => $labels_meal,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'meal-type', 'with_front' => false ),
    ) );

    // Product Family taxonomy
    $labels_family = array(
        'name'              => _x( 'Product Families', 'taxonomy general name', 'bbf_hsl' ),
        'singular_name'     => _x( 'Product Family', 'taxonomy singular name', 'bbf_hsl' ),
        'search_items'      => __( 'Search Product Families', 'bbf_hsl' ),
        'all_items'         => __( 'All Product Families', 'bbf_hsl' ),
        'parent_item'       => __( 'Parent Product Family', 'bbf_hsl' ),
        'parent_item_colon' => __( 'Parent Product Family:', 'bbf_hsl' ),
        'edit_item'         => __( 'Edit Product Family', 'bbf_hsl' ),
        'update_item'       => __( 'Update Product Family', 'bbf_hsl' ),
        'add_new_item'      => __( 'Add New Product Family', 'bbf_hsl' ),
        'new_item_name'     => __( 'New Product Family Name', 'bbf_hsl' ),
        'menu_name'         => __( 'Product Family', 'bbf_hsl' ),
        'view_item'         => __( 'View Product Family', 'bbf_hsl' ),
        'popular_items'     => __( 'Popular Product Families', 'bbf_hsl' ),
        'separate_items_with_commas' => __( 'Separate product families with commas', 'bbf_hsl' ),
        'add_or_remove_items' => __( 'Add or remove product families', 'bbf_hsl' ),
        'choose_from_most_used' => __( 'Choose from the most used product families', 'bbf_hsl' ),
        'not_found'         => __( 'No product families found', 'bbf_hsl' ),
        'back_to_items'     => __( '← Back to Product Families', 'bbf_hsl' ),
    );
    register_taxonomy( 'product_family', array( 'recipe' ), array(
        'hierarchical'      => true,
        'labels'            => $labels_family,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'product-family', 'with_front' => false ),
    ) );

    // Attributes taxonomy
    $labels_attr = array(
        'name'              => _x( 'Attributes', 'taxonomy general name', 'bbf_hsl' ),
        'singular_name'     => _x( 'Attribute', 'taxonomy singular name', 'bbf_hsl' ),
        'search_items'      => __( 'Search Attributes', 'bbf_hsl' ),
        'all_items'         => __( 'All Attributes', 'bbf_hsl' ),
        'parent_item'       => __( 'Parent Attribute', 'bbf_hsl' ),
        'parent_item_colon' => __( 'Parent Attribute:', 'bbf_hsl' ),
        'edit_item'         => __( 'Edit Attribute', 'bbf_hsl' ),
        'update_item'       => __( 'Update Attribute', 'bbf_hsl' ),
        'add_new_item'      => __( 'Add New Attribute', 'bbf_hsl' ),
        'new_item_name'     => __( 'New Attribute Name', 'bbf_hsl' ),
        'menu_name'         => __( 'Attributes', 'bbf_hsl' ),
        'view_item'         => __( 'View Attribute', 'bbf_hsl' ),
        'popular_items'     => __( 'Popular Attributes', 'bbf_hsl' ),
        'separate_items_with_commas' => __( 'Separate attributes with commas', 'bbf_hsl' ),
        'add_or_remove_items' => __( 'Add or remove attributes', 'bbf_hsl' ),
        'choose_from_most_used' => __( 'Choose from the most used attributes', 'bbf_hsl' ),
        'not_found'         => __( 'No attributes found', 'bbf_hsl' ),
        'back_to_items'     => __( '← Back to Attributes', 'bbf_hsl' ),
    );
    register_taxonomy( 'recipe_attribute', array( 'recipe' ), array(
        'hierarchical'      => false,
        'labels'            => $labels_attr,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'recipe-attribute', 'with_front' => false ),
    ) );
}

// Flush rewrite rules on theme activation
add_action('after_switch_theme', function() {
    // Register post type and taxonomies before flushing
    recipe_post_type();
    register_recipe_taxonomies();
    flush_rewrite_rules();
});

// Add image field to recipe taxonomies (Meal Type, Product Family, Attributes)
add_action( 'meal_type_add_form_fields', 'recipe_taxonomy_add_image_field', 10, 2 );
add_action( 'meal_type_edit_form_fields', 'recipe_taxonomy_edit_image_field', 10, 2 );
add_action( 'product_family_add_form_fields', 'recipe_taxonomy_add_image_field', 10, 2 );
add_action( 'product_family_edit_form_fields', 'recipe_taxonomy_edit_image_field', 10, 2 );
add_action( 'recipe_attribute_add_form_fields', 'recipe_taxonomy_add_image_field', 10, 2 );
add_action( 'recipe_attribute_edit_form_fields', 'recipe_taxonomy_edit_image_field', 10, 2 );

function recipe_taxonomy_add_image_field($taxonomy) {
    ?>
    <div class="form-field term-group">
        <label for="taxonomy-image-id"><?php _e('Image', 'bbf_hsl'); ?></label>
        <input type="hidden" id="taxonomy-image-id" name="taxonomy-image-id" value="" />
        <div id="taxonomy-image-wrapper"></div>
        <button type="button" class="button button-secondary taxonomy-add-image"><?php _e('Add Image', 'bbf_hsl'); ?></button>
        <button type="button" class="button button-secondary taxonomy-remove-image" style="display:none;"><?php _e('Remove Image', 'bbf_hsl'); ?></button>
    </div>
    <script>
    jQuery(document).ready(function($){
        var frame;
        $('.taxonomy-add-image').on('click', function(e){
            e.preventDefault();
            if(frame){ frame.open(); return; }
            frame = wp.media({
                title: '<?php _e('Select or Upload Image', 'bbf_hsl'); ?>',
                button: { text: '<?php _e('Use this image', 'bbf_hsl'); ?>' },
                multiple: false
            });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#taxonomy-image-id').val(attachment.id);
                $('#taxonomy-image-wrapper').html('<img src="'+attachment.sizes.thumbnail.url+'" style="max-width:100px;" />');
                $('.taxonomy-remove-image').show();
            });
            frame.open();
        });
        $('.taxonomy-remove-image').on('click', function(){
            $('#taxonomy-image-id').val('');
            $('#taxonomy-image-wrapper').html('');
            $(this).hide();
        });
    });
    </script>
    <?php
}

function recipe_taxonomy_edit_image_field($term, $taxonomy) {
    $image_id = get_term_meta($term->term_id, 'taxonomy-image-id', true);
    $image_url = $image_id ? wp_get_attachment_thumb_url($image_id) : '';
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="taxonomy-image-id"><?php _e('Image', 'bbf_hsl'); ?></label></th>
        <td>
            <input type="hidden" id="taxonomy-image-id" name="taxonomy-image-id" value="<?php echo esc_attr($image_id); ?>" />
            <div id="taxonomy-image-wrapper">
                <?php if($image_url) echo '<img src="'.esc_url($image_url).'" style="max-width:100px;" />'; ?>
            </div>
            <button type="button" class="button button-secondary taxonomy-add-image"><?php _e('Add Image', 'bbf_hsl'); ?></button>
            <button type="button" class="button button-secondary taxonomy-remove-image" <?php if(!$image_id) echo 'style="display:none;"'; ?>><?php _e('Remove Image', 'bbf_hsl'); ?></button>
        </td>
    </tr>
    <script>
    jQuery(document).ready(function($){
        var frame;
        $('.taxonomy-add-image').on('click', function(e){
            e.preventDefault();
            if(frame){ frame.open(); return; }
            frame = wp.media({
                title: '<?php _e('Select or Upload Image', 'bbf_hsl'); ?>',
                button: { text: '<?php _e('Use this image', 'bbf_hsl'); ?>' },
                multiple: false
            });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#taxonomy-image-id').val(attachment.id);
                $('#taxonomy-image-wrapper').html('<img src="'+attachment.sizes.thumbnail.url+'" style="max-width:100px;" />');
                $('.taxonomy-remove-image').show();
            });
            frame.open();
        });
        $('.taxonomy-remove-image').on('click', function(){
            $('#taxonomy-image-id').val('');
            $('#taxonomy-image-wrapper').html('');
            $(this).hide();
        });
    });
    </script>
    <?php
}

add_action('created_meal_type', 'recipe_taxonomy_save_image', 10, 2);
add_action('edited_meal_type', 'recipe_taxonomy_save_image', 10, 2);
add_action('created_product_family', 'recipe_taxonomy_save_image', 10, 2);
add_action('edited_product_family', 'recipe_taxonomy_save_image', 10, 2);
add_action('created_recipe_attribute', 'recipe_taxonomy_save_image', 10, 2);
add_action('edited_recipe_attribute', 'recipe_taxonomy_save_image', 10, 2);

function recipe_taxonomy_save_image($term_id, $tt_id) {
    if (isset($_POST['taxonomy-image-id'])) {
        update_term_meta($term_id, 'taxonomy-image-id', intval($_POST['taxonomy-image-id']));
    }
}

// Enqueue media uploader for taxonomy image fields
add_action('admin_enqueue_scripts', function($hook) {
    // Only load on taxonomy add/edit screens
    if (
        isset($_GET['taxonomy']) && in_array($_GET['taxonomy'], ['meal_type','product_family','recipe_attribute'])
    ) {
        wp_enqueue_media();
    }
});

// Add edit taxonomy links to the admin bar on the front-end
add_action('admin_bar_menu', function($wp_admin_bar) {
    if (!is_admin() && is_user_logged_in() && current_user_can('manage_categories')) {
        global $wp_query;
        $parent = 'site-name'; // Attach to the site name menu for visibility
        // Meal Type
        if (is_tax('meal_type')) {
            $term = $wp_query->get_queried_object();
            if ($term && isset($term->term_id)) {
                $edit_link = get_edit_term_link($term->term_id, 'meal_type');
                $wp_admin_bar->add_node([
                    'id'    => 'edit-meal-type',
                    'title' => __('Edit Meal Type', 'bbf_hsl'),
                    'href'  => $edit_link,
                    'parent'=> $parent,
                ]);
            }
        }
        // Product Family
        if (is_tax('product_family')) {
            $term = $wp_query->get_queried_object();
            if ($term && isset($term->term_id)) {
                $edit_link = get_edit_term_link($term->term_id, 'product_family');
                $wp_admin_bar->add_node([
                    'id'    => 'edit-product-family',
                    'title' => __('Edit Product Family', 'bbf_hsl'),
                    'href'  => $edit_link,
                    'parent'=> $parent,
                ]);
            }
        }
        // Recipe Attribute
        if (is_tax('recipe_attribute')) {
            $term = $wp_query->get_queried_object();
            if ($term && isset($term->term_id)) {
                $edit_link = get_edit_term_link($term->term_id, 'recipe_attribute');
                $wp_admin_bar->add_node([
                    'id'    => 'edit-recipe-attribute',
                    'title' => __('Edit Attribute', 'bbf_hsl'),
                    'href'  => $edit_link,
                    'parent'=> $parent,
                ]);
            }
        }
    }
}, 100);
