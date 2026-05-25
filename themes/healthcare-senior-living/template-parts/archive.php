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

              <button class="accordion-toggle" type="button" aria-expanded="false"><?php echo hsl_esc_html__( 'All Filters' ); ?></button>

              <div class="accordion-panel" style="display:none;">

            <?php

            $taxonomies = [

              'meal_type' => hsl__( 'Meal Type' ),

              'product_family' => hsl__( 'Product Family' ),

              'recipe_attribute' => hsl__( 'Attributes' ),

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

        hsl_render_archive_grid_item();

      }

      } else {

        while ( have_posts() ) {

        the_post();

        hsl_render_archive_grid_item();

      }

      }

      ?>

      </div>

      

      <?php

      if ( hsl_show_healthcare_formulated_badge() ) : ?>

        <div class="healthcare-formulated-legend"><span class="healthcare-formulated"></span> <?php echo hsl_esc_html__( 'Healthcare formulated' ); ?></div>

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

              $plural = $post_type_obj ? $post_type_obj->labels->name : hsl__( 'Posts' );

              /* translators: %s: post type plural name */

              echo esc_html( sprintf( hsl__( 'Load More %s' ), $plural ) );

            ?>

          </button>

        </div>

      <?php endif; ?>

    </div>

  </div>

</main>

