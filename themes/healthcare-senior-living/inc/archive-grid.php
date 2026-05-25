<?php
/**
 * Shared archive grid markup for templates and AJAX handlers.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Singular post type label for archive "View" buttons.
 *
 * @return string
 */
function hsl_archive_singular_label() {
	$post_type_obj = get_post_type_object( get_post_type() );
	return $post_type_obj ? $post_type_obj->labels->singular_name : hsl__( 'Post' );
}

/**
 * Translated "View {post type}" button text.
 *
 * @return string
 */
function hsl_archive_view_button_text() {
	if ( get_post_type() === 'recipe' ) {
		return hsl_esc_html__( 'View Recipe' );
	}

	/* translators: %s: post type singular name, e.g. Recipe */
	return sprintf( hsl_esc_html__( 'View %s' ), esc_html( hsl_archive_singular_label() ) );
}

/**
 * Whether the healthcare-formulated badge should show on archive items.
 *
 * @return bool
 */
function hsl_show_healthcare_formulated_badge() {
	if ( is_post_type_archive( 'recipe' ) ) {
		return true;
	}
	if ( is_tax() && get_queried_object() && get_queried_object()->taxonomy && in_array( get_queried_object()->taxonomy, array( 'meal_type', 'product_family', 'recipe_attribute' ), true ) ) {
		return true;
	}
	if ( get_post_type() === 'recipe' ) {
		return true;
	}
	return false;
}

/**
 * Output a single archive grid item.
 */
function hsl_render_archive_grid_item() {
	$post_link = get_permalink();
	?>
	<article class="archive-grid-item">
		<?php if ( has_post_thumbnail() ) : ?>
			<a href="<?php echo esc_url( $post_link ); ?>" class="item-thumbnail-link">
				<?php the_post_thumbnail( 'large' ); ?>
				<?php if ( hsl_show_healthcare_formulated_badge() ) : ?>
					<div class="healthcare-formulated"></div>
				<?php endif; ?>
			</a>
		<?php endif; ?>
		<div class="item-details">
			<h2 class="entry-title">
				<a href="<?php echo esc_url( $post_link ); ?>">
					<?php echo wp_kses_post( get_the_title() ); ?>
				</a>
			</h2>
			<a href="<?php echo esc_url( $post_link ); ?>" class="elementor-button elementor-button-link elementor-size-sm archive-view-btn">
				<?php echo hsl_archive_view_button_text(); ?>
			</a>
		</div>
	</article>
	<?php
}
