<?php
/**
 * The template for displaying archive pages.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<main id="content" class="site-main">
  <div class="container">
    <div class="page-header">
      <div class="archive-title">
        <?php
          // Remove context prefixes from archive titles
          add_filter('get_the_archive_title', function($title) {
            if ( is_category() || is_tag() || is_tax() ) {
              // Remove anything up to and including the colon and space
              $title = preg_replace('/^[^:]+:\s*/', '', $title);
            } elseif ( is_post_type_archive() ) {
              // Remove 'Archives: ' from post type archives
              $title = preg_replace('/^Archives:\s*/', '', $title);
            }
            return $title;
          });
          the_archive_title( '<h1 class="entry-title">', '</h1>' ); 
        ?>
      </div>
      <?php
      the_archive_description( '<p class="archive-description">', '</p>' );
      ?>
    </div>

    <div class="page-content">
      <div class="archive-grid">
      <?php
      while ( have_posts() ) {
        the_post();
        $post_link = get_permalink();
        $post_type_obj = get_post_type_object(get_post_type());
        $post_type_label = $post_type_obj ? $post_type_obj->labels->singular_name : __('View', 'hello-elementor');
        ?>
        <article class="archive-grid-item">
          <?php if ( has_post_thumbnail() ) : ?>
            <a href="<?php echo esc_url($post_link); ?>" class="item-thumbnail-link">
              <?php the_post_thumbnail('large'); ?>
              <?php
              // Show legend only for recipe post type archives or recipe taxonomies
              if ( is_post_type_archive('recipe') || (is_tax() && get_queried_object() && get_queried_object()->taxonomy && in_array(get_queried_object()->taxonomy, array('meal_type','product_family','recipe_attribute'))) ) : ?>
                <div class="healthcare-formulated"?></div>
              <?php endif; ?>
            </a>
          <?php endif; ?>
          <div class="item-details">
            <h2 class="entry-title">
              <a href="<?php echo esc_url($post_link); ?>">
                <?php echo wp_kses_post(get_the_title()); ?>
              </a>
            </h2>
            <a href="<?php echo esc_url($post_link); ?>" class="elementor-button elementor-button-link elementor-size-sm archive-view-btn">
              <?php echo esc_html__('View ', 'hello-elementor') . esc_html($post_type_label); ?>
            </a>
          </div>
        </article>
      <?php } ?>
      </div>
      <?php
      // Show legend only for recipe post type archives or recipe taxonomies
      if ( is_post_type_archive('recipe') || (is_tax() && get_queried_object() && get_queried_object()->taxonomy && in_array(get_queried_object()->taxonomy, array('meal_type','product_family','recipe_attribute'))) ) : ?>
        <div class="healthcare-formulated-legend"><span class="healthcare-formulated"></span> Healthcare formulated</div>
      <?php endif; ?>
    </div>

    <?php
    global $wp_query;
    if ( $wp_query->max_num_pages > 1 ) :
      $prev_arrow = is_rtl() ? '&rarr;' : '&larr;';
      $next_arrow = is_rtl() ? '&larr;' : '&rarr;';
      ?>
      <nav class="pagination">
        <div class="nav-previous"><?php
          /* translators: %s: HTML entity for arrow character. */
          previous_posts_link( sprintf( esc_html__( '%s Previous', 'hello-elementor' ), sprintf( '<span class="meta-nav">%s</span>', $prev_arrow ) ) );
        ?></div>
        <div class="nav-next"><?php
          /* translators: %s: HTML entity for arrow character. */
          next_posts_link( sprintf( esc_html__( 'Next %s', 'hello-elementor' ), sprintf( '<span class="meta-nav">%s</span>', $next_arrow ) ) );
        ?></div>
      </nav>
    <?php endif; ?>
  </div>
</main>
