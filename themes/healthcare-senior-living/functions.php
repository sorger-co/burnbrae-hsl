<?php
/**
 * Load child theme css and optional scripts
 *
 * @return void
 */
function hsl_enqueue_scripts() {
	wp_enqueue_style(
		'healthcare-senior-living',
		get_stylesheet_directory_uri() . '/style.css');
}
add_action( 'wp_enqueue_scripts', 'hsl_enqueue_scripts', 20 );

function hsl_load_theme_textdomain() {
	load_child_theme_textdomain( 'healthcare-senior-living', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'hsl_load_theme_textdomain' );

include(get_stylesheet_directory() . '/inc/woocommerce.php');
include(get_stylesheet_directory() . '/inc/polylang-strings.php');
include(get_stylesheet_directory() . '/inc/archive-grid.php');
include(get_stylesheet_directory() . '/inc/recipes.php');

/* Current Year Shortcode */
function current_year_shortcode() {
	$year = date('Y');	
	return $year;	
}
add_shortcode('year', 'current_year_shortcode');

function healthcare_enqueue_loadmore_script() {
    if (is_archive()) {
        global $wp_query;
        wp_enqueue_script('healthcare-load-more', get_stylesheet_directory_uri() . '/assets/load-more.js', array('jquery'), null, true);
        wp_enqueue_script('healthcare-filter-recipes', get_stylesheet_directory_uri() . '/assets/filter-recipes.js', array('jquery'), null, true);
        $ajax_data = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'query_vars' => json_encode($wp_query->query)
        );
        $i18n = array(
            'loading' => hsl__( 'Loading...' ),
        );
        wp_localize_script('healthcare-load-more', 'healthcare_ajax_loadmore', $ajax_data);
        wp_localize_script('healthcare-filter-recipes', 'healthcare_ajax_loadmore', $ajax_data);
        wp_localize_script('healthcare-load-more', 'healthcare_i18n', $i18n);
        wp_localize_script('healthcare-filter-recipes', 'healthcare_i18n', $i18n);
    }

    if (is_front_page() || is_home()) {
      wp_enqueue_script('bbf-slider-autoplay', get_stylesheet_directory_uri() . '/assets/bbf-slider-autoplay.js', array('jquery'), null, true);
    }
}
add_action('wp_enqueue_scripts', 'healthcare_enqueue_loadmore_script');

function healthcare_load_more_ajax_handler() {
    $query_vars = json_decode(stripslashes($_POST['query_vars']), true);
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $query_vars['paged'] = $paged;
    $query_vars['post_status'] = 'publish';
    $archive_query = new WP_Query($query_vars);
    ob_start();
    if ($archive_query->have_posts()) {
        while ($archive_query->have_posts()) {
            $archive_query->the_post();
            hsl_render_archive_grid_item();
        }
    }
    wp_reset_postdata();
    $html = ob_get_clean();
    wp_send_json_success(['html' => $html]);
}
add_action('wp_ajax_healthcare_load_more', 'healthcare_load_more_ajax_handler');
add_action('wp_ajax_nopriv_healthcare_load_more', 'healthcare_load_more_ajax_handler');

function healthcare_filter_recipes_ajax_handler() {
    $query_vars = json_decode(stripslashes($_POST['query_vars']), true);
    $tax_query = array('relation' => 'AND');
    $taxonomies = array('meal_type', 'product_family', 'recipe_attribute');
    foreach ($taxonomies as $tax) {
        if (!empty($_POST[$tax]) && is_array($_POST[$tax])) {
            $tax_query[] = array(
                'taxonomy' => $tax,
                'field' => 'slug',
                'terms' => array_map('sanitize_text_field', $_POST[$tax]),
            );
        }
    }
    if (count($tax_query) > 1) {
        $query_vars['tax_query'] = $tax_query;
    }
    $query_vars['post_status'] = 'publish';
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $query_vars['paged'] = $paged;
    $archive_query = new WP_Query($query_vars);
    ob_start();
    if ($archive_query->have_posts()) {
        while ($archive_query->have_posts()) {
            $archive_query->the_post();
            hsl_render_archive_grid_item();
        }
    }
    wp_reset_postdata();
    $html = ob_get_clean();
    $has_more = ($archive_query->max_num_pages > $paged);

    // Calculate unavailable terms for each taxonomy
    $unavailable_terms = array();
    foreach ($taxonomies as $tax) {
        $all_terms = get_terms([
            'taxonomy' => $tax,
            'hide_empty' => false
        ]);
        $unavailable_terms[$tax] = array();
        foreach ($all_terms as $term) {
            $test_query = $query_vars;
            $test_tax_query = isset($test_query['tax_query']) ? $test_query['tax_query'] : array('relation' => 'AND');
            // Add this term to the tax_query for this taxonomy
            $test_tax_query = array_filter($test_tax_query, function($q) use ($tax) {
                return !(isset($q['taxonomy']) && $q['taxonomy'] === $tax);
            });
            $test_tax_query[] = array(
                'taxonomy' => $tax,
                'field' => 'slug',
                'terms' => array($term->slug),
            );
            $test_query['tax_query'] = $test_tax_query;
            $test_query['posts_per_page'] = 1;
            $test = new WP_Query($test_query);
            if (!$test->have_posts()) {
                $unavailable_terms[$tax][] = $term->slug;
            }
            wp_reset_postdata();
        }
    }

    wp_send_json_success(['html' => $html, 'has_more' => $has_more, 'unavailable_terms' => $unavailable_terms]);
}
add_action('wp_ajax_healthcare_filter_recipes', 'healthcare_filter_recipes_ajax_handler');
add_action('wp_ajax_nopriv_healthcare_filter_recipes', 'healthcare_filter_recipes_ajax_handler');



