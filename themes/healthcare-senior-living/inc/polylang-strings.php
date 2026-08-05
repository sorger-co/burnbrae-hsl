<?php
/**
 * Polylang string registration for Healthcare theme front-end strings.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Translate a registered front-end string with Polylang when available.
 *
 * @param string $string String to translate.
 * @return string
 */
function hsl__( $string ) {
	if ( function_exists( 'pll__' ) ) {
		return pll__( $string );
	}

	return __( $string, 'healthcare-senior-living' );
}

/**
 * Translate and escape a registered front-end string for HTML output.
 *
 * @param string $string String to translate.
 * @return string
 */
function hsl_esc_html__( $string ) {
	return esc_html( hsl__( $string ) );
}

/**
 * Register translatable theme strings with Polylang.
 */
function hsl_register_polylang_strings() {
	if ( ! function_exists( 'pll_register_string' ) ) {
		return;
	}

	$context = 'Healthcare Theme';
	$strings = array(
		'nutritional_information'   => 'Nutritional Information',
		'product_information'       => 'Product Information',
		'nutrition_information'     => 'Nutrition Information',
		'heating_instructions'      => 'Heating Instructions',
		'recipes'                   => 'Recipes',
		'product_number'            => 'Product Number:',
		'upc'                       => 'UPC:',
		'scc'                       => 'SCC:',
		'kosher'                    => 'Kosher:',
		'yes'                       => 'Yes',
		'no'                        => 'No',
		'size'                      => 'Size:',
		'net_weight'                => 'Net weight:',
		'shelf_life_storage'        => 'Shelf Life/Storage:',
		'ingredients'               => 'Ingredients:',
		'contains'                  => 'Contains:',
		'view_recipe'               => 'View Recipe',
		'healthcare_formulated'     => 'Healthcare formulated',
		'no_recipes_for_product'    => 'No recipes found featuring this product.',
		'all_filters'               => 'All Filters',
		'meal_type'                 => 'Meal Type',
		'product_family'            => 'Product Family',
		'attributes'                => 'Attributes',
		'view_post_type'            => 'View %s',
		'post'                      => 'Post',
		'posts'                     => 'Posts',
		'load_more_post_type'       => 'Load More %s',
		'see_all_recipes'           => 'See all recipes',
		'ingredients_heading'       => 'Ingredients',
		'directions'                => 'Directions',
		'nutrients'                 => 'Nutrients',
		'tagged'                    => 'Tagged ',
		'view_product'              => 'View Product',
		'loading'                   => 'Loading...',
	);

	foreach ( $strings as $name => $string ) {
		pll_register_string( $name, $string, $context, false );
	}
}
add_action( 'init', 'hsl_register_polylang_strings' );
