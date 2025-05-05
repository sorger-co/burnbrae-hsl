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

/* Current Year Shortcode */
function current_year_shortcode() {
	$year = date('Y');	
	return $year;	
}
add_shortcode('year', 'current_year_shortcode');
