<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
  <div id="product-hero">
    <div class="container">
      <div class="product-hero-content">
        <h1 class="product-title"><?php the_title(); ?></h1>
        <?php echo apply_filters( 'the_content', $product->get_description() ); ?>
      </div>
      
      <?php if ( has_post_thumbnail() ) : ?>
        <div class="product-image">
          <?php the_post_thumbnail( 'large' ); ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <?php
// Gather Product Information fields
$sku = $product->get_sku();
$upc = get_post_meta( get_the_ID(), '_hsl_upc', true );
$scc = get_post_meta( get_the_ID(), '_hsl_scc', true );
$kosher = get_post_meta( get_the_ID(), '_hsl_kosher', true );
$size = get_post_meta( get_the_ID(), '_hsl_size', true );
$net_weight = get_post_meta( get_the_ID(), '_hsl_net_weight', true );
$shelf_life = get_post_meta( get_the_ID(), '_hsl_shelf_life', true );
$ingredients = get_post_meta( get_the_ID(), '_hsl_ingredients', true );
$contains = get_post_meta( get_the_ID(), '_hsl_contains', true );

$nutrition_image_id = get_post_meta( get_the_ID(), '_hsl_nutrition_image_id', true );

$heating_instructions = get_post_meta( get_the_ID(), '_hsl_heating_instructions', true );

$product_info_exists = $sku || $upc || $scc || $kosher || $size || $net_weight || $shelf_life || $ingredients || $contains;
$nutrition_info_exists = !empty($nutrition_image_id);
$heating_instructions_exists = !empty($heating_instructions);

// Query recipes where this product is in the Featured Products metabox
$recipe_args = array(
  'post_type' => 'recipe',
  'posts_per_page' => -1,
  'meta_query' => array(
    array(
      'key'     => 'recipe_featured_products',
      'value'   => $product->get_id(),
      'compare' => 'LIKE',
    ),
  ),
);
$recipe_query = new WP_Query($recipe_args);
$has_product_recipes = $recipe_query->have_posts();
?>
  <div class="custom-product-tabs">
    <div class="tab-buttons">
      <?php if ( $product_info_exists ) : ?>
      <button type="button" class="tab-btn" data-tab="tab1">Product Information</button>
      <?php endif; ?>
      <?php if ( $nutrition_info_exists ) : ?>
      <button type="button" class="tab-btn" data-tab="tab2">Nutrition Information</button>
      <?php endif; ?>
      <?php if ( $heating_instructions_exists ) : ?>
      <button type="button" class="tab-btn" data-tab="tab3">Heating Instructions</button>
      <?php endif; ?>
      <?php if ( $has_product_recipes ) : ?>
      <button type="button" class="tab-btn" data-tab="tab4">Recipes</button>
      <?php endif; ?>
    </div>
    <div class="tab-contents">
      <?php if ( $product_info_exists ) : ?>
      <div id="tab1" class="tab-content" style="display:none;">
        <ul class="product-info-list">
          <?php if ( $sku ) : ?>
            <li><strong>SKU:</strong> <?php echo esc_html( $sku ); ?></li>
          <?php endif; ?>
          <?php if ( $upc ) : ?>
            <li><strong>UPC:</strong> <?php echo esc_html( $upc ); ?></li>
          <?php endif; ?>
          <?php if ( $scc ) : ?>
            <li><strong>SCC:</strong> <?php echo esc_html( $scc ); ?></li>
          <?php endif; ?>
          <?php if ( $kosher ) : ?>
            <li><strong>Kosher:</strong> <?php echo $kosher === 'yes' ? 'Yes' : 'No'; ?></li>
          <?php endif; ?>
          <?php if ( $size ) : ?>
            <li><strong>Size:</strong> <?php echo esc_html( $size ); ?></li>
          <?php endif; ?>
          <?php if ( $net_weight ) : ?>
            <li><strong>Net weight:</strong> <?php echo esc_html( $net_weight ); ?></li>
          <?php endif; ?>
          <?php if ( $shelf_life ) : ?>
            <li><strong>Shelf Life/Storage:</strong> <?php echo esc_html( $shelf_life ); ?></li>
          <?php endif; ?>
          <?php if ( $ingredients ) : ?>
            <li><strong>Ingredients:</strong> <?php echo nl2br( esc_html( $ingredients ) ); ?></li>
          <?php endif; ?>
          <?php if ( $contains ) : ?>
            <li><strong>Contains:</strong> <?php echo nl2br( esc_html( $contains ) ); ?></li>
          <?php endif; ?>
        </ul>
      </div>
      <?php endif; ?>
      <?php if ( $nutrition_info_exists ) : ?>
      <div id="tab2" class="tab-content" style="display:none;">
        <?php echo wp_get_attachment_image( $nutrition_image_id, 'large', false, [ 'class' => 'nutrition-info-image' ] ); ?>
      </div>
      <?php endif; ?>
      <?php if ( $heating_instructions_exists ) : ?>
      <div id="tab3" class="tab-content" style="display:none;">
        <?php echo apply_filters( 'the_content', $heating_instructions ); ?>
      </div>
      <?php endif; ?>
      <?php if ( $has_product_recipes ) : ?>
      <div id="tab4" class="tab-content" style="display:none;">
        <?php
        // Query recipes where this product is in the Featured Products metabox
        $recipe_args = array(
          'post_type' => 'recipe',
          'posts_per_page' => -1,
          'meta_query' => array(
            array(
              'key'     => 'recipe_featured_products',
              'value'   => $product->get_id(),
              'compare' => 'LIKE',
            ),
          ),
        );
        $recipe_query = new WP_Query($recipe_args);
        if ( $recipe_query->have_posts() ) : ?>
          <div class="product-recipes-list">
            <?php while ( $recipe_query->have_posts() ) : $recipe_query->the_post(); ?>
              <div class="product-recipe-item">
                <?php if ( has_post_thumbnail() ) : ?>
                  <div class="product-recipe-thumb">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
                  </div>
                <?php endif; ?>
                <h4 class="product-recipe-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                <a class="button view-recipe-btn" href="<?php the_permalink(); ?>">View Recipe</a>
              </div>
            <?php endwhile; wp_reset_postdata(); ?>
          </div>
        <?php else : ?>
          <p>No recipes found featuring this product.</p>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <script>
    (function(){
      const tabBtns = document.querySelectorAll('.custom-product-tabs .tab-btn');
      const tabContents = document.querySelectorAll('.custom-product-tabs .tab-content');
      const productHero = document.getElementById('product-hero');
      let openTab = null;

      tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          const tabId = this.getAttribute('data-tab');
          const content = document.getElementById(tabId);

          if (openTab === content) {
            content.style.display = 'none';
            productHero.style.display = '';
            openTab = null;
            this.classList.remove('active');
          } else {
            tabContents.forEach(tc => tc.style.display = 'none');
            tabBtns.forEach(b => b.classList.remove('active'));
            content.style.display = 'block';
            productHero.style.display = 'none';
            openTab = content;
            this.classList.add('active');
          }
        });
      });
    })();
  </script>

  <div class="related-products-slider-wrapper">
    <?php
    $related_ids = wc_get_related_products($product->get_id(), -1); // get all related products
    if ($related_ids) {
      $args = array(
        'post_type' => 'product',
        'post__in' => $related_ids,
        'posts_per_page' => -1,
      );
      $related_query = new WP_Query($args);
      if ($related_query->have_posts()) : ?>
        <div class="related-products-slider">
          <?php while ($related_query->have_posts()) : $related_query->the_post(); global $product; ?>
            <div class="related-product-slide">
              <?php wc_get_template_part('content', 'product'); ?>
            </div>
          <?php endwhile; ?>
        </div>
        <div class="slider-nav">
          <button class="slider-arrow slider-arrow-left" type="button">&#8592;</button>
          <button class="slider-arrow slider-arrow-right" type="button">&#8594;</button>
        </div>
        <div class="slider-dots"></div>
      <?php endif; wp_reset_postdata();
    }
    ?>
  </div>
  <script>
    (function(){
      // Simple slider logic for 4 columns, arrows, and dots
      const slider = document.querySelector('.related-products-slider');
      if (!slider) return;
      const slides = Array.from(slider.children);
      const leftArrow = document.querySelector('.slider-arrow-left');
      const rightArrow = document.querySelector('.slider-arrow-right');
      const dotsContainer = document.querySelector('.slider-dots');
      const slidesPerView = 4;
      let currentIndex = 0;
      const totalSlides = slides.length;
      const totalPages = Math.ceil(totalSlides / slidesPerView);
      function updateSlider() {
        slides.forEach((slide, i) => {
          slide.style.display = (i >= currentIndex && i < currentIndex + slidesPerView) ? 'block' : 'none';
        });
        // Update dots
        if (dotsContainer) {
          dotsContainer.innerHTML = '';
          for (let i = 0; i < totalPages; i++) {
            const dot = document.createElement('span');
            dot.className = 'slider-dot' + (i === Math.floor(currentIndex / slidesPerView) ? ' active' : '');
            dot.addEventListener('click', function() {
              currentIndex = i * slidesPerView;
              updateSlider();
            });
            dotsContainer.appendChild(dot);
          }
        }
      }
      if (leftArrow) leftArrow.addEventListener('click', function() {
        currentIndex = Math.max(0, currentIndex - slidesPerView);
        updateSlider();
      });
      if (rightArrow) rightArrow.addEventListener('click', function() {
        currentIndex = Math.min(totalSlides - slidesPerView, currentIndex + slidesPerView);
        updateSlider();
      });
      // Initial display
      updateSlider();
      // Responsive: show less if not enough space
      window.addEventListener('resize', updateSlider);
    })();
  </script>
  
	<?php
	/**
	 * Hook: woocommerce_before_single_product_summary.
	 *
	 * @hooked woocommerce_show_product_sale_flash - 10
	 * @hooked woocommerce_show_product_images - 20
	 */
	do_action( 'woocommerce_before_single_product_summary' );
	?>

	<?php
	/**
	 * Hook: woocommerce_after_single_product_summary.
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */
	do_action( 'woocommerce_after_single_product_summary' );
	?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
