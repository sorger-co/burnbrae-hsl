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
        
          if (get_post_type() === 'recipe') : ?>
          <div class="recipe-accordion-filter">
            <div class="accordion-item parent-accordion">
              <button class="accordion-toggle" type="button" aria-expanded="false">All Filters</button>
              <div class="accordion-panel" style="display:none;">
            <?php
            $taxonomies = [
              'meal_type' => __('Meal Type', 'bbf_hsl'),
              'product_family' => __('Product Family', 'bbf_hsl'),
              'recipe_attribute' => __('Attributes', 'bbf_hsl'),
            ];
            foreach ($taxonomies as $tax => $label) :
              $terms = get_terms([
                'taxonomy' => $tax,
                'hide_empty' => false
              ]);
              if (!empty($terms) && !is_wp_error($terms)) : ?>
                <div class="accordion-item">
                  <button class="accordion-toggle" type="button"><?php echo esc_html($label); ?></button>
                  <div class="accordion-panel">
                    <?php foreach ($terms as $term) : ?>
                      <label><input type="checkbox" class="recipe-filter" name="<?php echo esc_attr($tax); ?>[]" value="<?php echo esc_attr($term->slug); ?>"> <?php echo esc_html($term->name); ?></label><br>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif;
            endforeach;
            ?>
              </div>
            </div>
          </div>
          <?php endif; ?>
      </div>
      <?php
      the_archive_description( '<p class="archive-description">', '</p>' );
      ?>
    </div>

    <div class="page-content">
      <div class="archive-grid">
      <?php
       if ( get_post_type() === 'recipe' ) {
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
      <?php }
      } else {
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
      <?php }
      }
      ?>
      </div>
      
      <?php
      // Show legend only for recipe post type archives or recipe taxonomies
      if ( is_post_type_archive('recipe') || (is_tax() && get_queried_object() && get_queried_object()->taxonomy && in_array(get_queried_object()->taxonomy, array('meal_type','product_family','recipe_attribute'))) ) : ?>
        <div class="healthcare-formulated-legend"><span class="healthcare-formulated"></span> Healthcare formulated</div>
      <?php endif; ?>
      <?php
      global $wp_query;
      if ( $wp_query->max_num_pages > 1 ) :
        $current_page = max(1, get_query_var('paged'));
      ?>
        <div class="load-more-wrapper">
          <button id="load-more-posts" 
                  data-current-page="<?php echo esc_attr($current_page); ?>" 
                  data-max-pages="<?php echo esc_attr($wp_query->max_num_pages); ?>"
                  data-archive-url="<?php echo esc_url(get_pagenum_link()); ?>"
                  class="elementor-button elementor-button-link elementor-size-sm load-more-btn">
            <?php 
              $post_type = get_post_type();
              if (!$post_type && is_post_type_archive()) {
                $post_type = get_queried_object()->name;
              }
              $post_type_obj = get_post_type_object($post_type);
              $plural = $post_type_obj ? $post_type_obj->labels->name : esc_html__('Posts', 'hello-elementor');
              echo esc_html__('Load More', 'hello-elementor') . ' ' . esc_html($plural);
            ?>
          </button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>
