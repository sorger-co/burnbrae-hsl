<?php
/**
 * Site hardening.
 *
 * Lives in the theme because the theme is the only code deployed from this repo.
 */

// Disable the plugin/theme file editor in wp-admin (no-op if wp-config already sets it).
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

/* Hide the WordPress version */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/* Disable XML-RPC (brute-force amplification and pingback abuse) */
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'xmlrpc_methods', '__return_empty_array' );
remove_action( 'wp_head', 'rsd_link' );

function hsl_remove_pingback_header( $headers ) {
	unset( $headers['X-Pingback'] );
	return $headers;
}
add_filter( 'wp_headers', 'hsl_remove_pingback_header' );

/* Block username enumeration for logged-out visitors */
function hsl_block_author_enumeration() {
	if ( isset( $_GET['author'] ) && ! is_user_logged_in() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'hsl_block_author_enumeration', 1 );

function hsl_block_rest_user_enumeration( $result, $server, $request ) {
	if ( ! is_user_logged_in() && 0 === strpos( $request->get_route(), '/wp/v2/users' ) ) {
		return new WP_Error( 'rest_forbidden', __( 'Sorry, you are not allowed to do that.' ), array( 'status' => 401 ) );
	}
	return $result;
}
add_filter( 'rest_pre_dispatch', 'hsl_block_rest_user_enumeration', 10, 3 );

function hsl_remove_user_sitemap( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}
add_filter( 'wp_sitemaps_add_provider', 'hsl_remove_user_sitemap', 10, 2 );

function hsl_strip_oembed_author( $data ) {
	unset( $data['author_name'], $data['author_url'] );
	return $data;
}
add_filter( 'oembed_response_data', 'hsl_strip_oembed_author' );

/* Don't reveal whether a username exists on failed logins */
function hsl_generic_login_error() {
	return __( '<strong>Error:</strong> Invalid username or password.' );
}
add_filter( 'login_errors', 'hsl_generic_login_error' );

/* Security headers for front-end responses */
function hsl_security_headers() {
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
}
add_action( 'send_headers', 'hsl_security_headers' );
