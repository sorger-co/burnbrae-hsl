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
  <div class="hsl-product-tabs">
    <div class="tab-buttons">
      <div class="container">
        <?php if ( $product_info_exists ) : ?>
          <div>
            <button type="button" class="tab-btn" data-tab="tab1"><span class="tab-title-left"><span class="tab-title-before tab-pi"></span>Product Information</span><span class="tab-title-after"></span></button>
          </div>
        <?php endif; ?>
        <?php if ( $nutrition_info_exists ) : ?>
        <div>
          <button type="button" class="tab-btn" data-tab="tab2"><span class="tab-title-left"><span class="tab-title-before tab-ni"></span>Nutrition Information</span><span class="tab-title-after"></span></button>
        </div>
        <?php endif; ?>
        <?php if ( $heating_instructions_exists ) : ?>
          <div>
            <button type="button" class="tab-btn" data-tab="tab3"><span class="tab-title-left"><span class="tab-title-before tab-hi"></span>Heating Instructions</span><span class="tab-title-after"></span></button>
          </div>
        <?php endif; ?>
        <?php if ( $has_product_recipes ) : ?>
          <div>
            <button type="button" class="tab-btn tab-rr" data-tab="tab4"><span class="tab-title-left"><span class="tab-title-before tab-rr"></span>Recipes</span><span class="tab-title-after"></span></button>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <div class="tab-contents">      
      <?php if ( $product_info_exists ) : ?>
      <div id="tab1" class="tab-content tab-pi-content" style="display:none;">     
        <div class="container"> 
          <div class="tab-pi-content-left">
            <div class="tab-pi-left-1">
              <p>  
                <?php if ( $sku ) : ?>
                  <strong>Product Number:</strong> <?php echo esc_html( $sku ); ?><br />
                <?php endif; ?>
                <?php if ( $upc ) : ?>
                  <strong>UPC:</strong> <?php echo esc_html( $upc ); ?><br />
                <?php endif; ?>
                <?php if ( $scc ) : ?>
                  <strong>SCC:</strong> <?php echo esc_html( $scc ); ?><br />
                <?php endif; ?>
              </p>
              <p>
                <?php if ( $kosher ) : ?>
                  <strong>Kosher:</strong> <?php echo $kosher === 'yes' ? 'Yes' : 'No'; ?><br />
                <?php endif; ?>
                <?php if ( $size ) : ?>
                  <strong>Size:</strong> <?php echo esc_html( $size ); ?><br />
                <?php endif; ?>
                <?php if ( $net_weight ) : ?>
                  <strong>Net weight:</strong> <?php echo esc_html( $net_weight ); ?><br />
                <?php endif; ?>
                <?php if ( $shelf_life ) : ?>
                  <strong>Shelf Life/Storage:</strong> <?php echo esc_html( $shelf_life ); ?><br />
                <?php endif; ?>
              </p>
            </div>
            <div class="tab-pi-left-2">
              <?php if ( $ingredients ) : ?>
                <p><strong>Ingredients:</strong> <br /><?php echo nl2br( esc_html( $ingredients ) ); ?></p>
              <?php endif; ?>
              <?php if ( $contains ) : ?>
                <p><strong>Contains:</strong> <?php echo nl2br( esc_html( $contains ) ); ?></p>
              <?php endif; ?>
            </div>
          </div>
          <div class="tab-pi-content-right">
            <?php 
              $product_info_image_id = get_post_meta( get_the_ID(), '_hsl_product_info_image_id', true );
              if ( $product_info_image_id ) {
                echo wp_get_attachment_image( $product_info_image_id, 'large', false, [ 'class' => 'product-info-image' ] );
              }
            ?>
          </div>
        </div>
      </div>
      <?php endif; ?>
      <?php if ( $nutrition_info_exists ) : ?>
      <div id="tab2" class="tab-content tab-ni-content" style="display:none;">
        <div class="container">
          <?php echo wp_get_attachment_image( $nutrition_image_id, 'large', false, [ 'class' => 'nutrition-info-image' ] ); ?>
        </div>
      </div>
      <?php endif; ?>
      <?php if ( $heating_instructions_exists ) : ?>
      <div id="tab3" class="tab-content tab-hi-content" style="display:none;">
        <div class="container"><?php echo apply_filters( 'the_content', $heating_instructions ); ?></div>
      </div>
      <?php endif; ?>
      <?php if ( $has_product_recipes ) : ?>
      <div id="tab4" class="tab-content tab-rr-content" style="display:none;">
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
        <div class="container">
          <div class="archive-grid">
            <?php while ( $recipe_query->have_posts() ) : $recipe_query->the_post(); ?>
              <div class="archive-grid-item">
                <?php if ( has_post_thumbnail() ) : ?>
                  <div class="item-thumbnail-link">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('full'); ?></a>
                    <div class="healthcare-formulated" ?=""></div>
                  </div>
                <?php endif; ?>
                <div class="item-details">
                  <h4 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                  <a class="elementor-button elementor-button-link elementor-size-sm archive-view-btn" href="<?php the_permalink(); ?> ">View Recipe</a>
                </div>
              </div>
            <?php endwhile; wp_reset_postdata(); ?>
          </div>
          <div class="healthcare-formulated-legend"><span class="healthcare-formulated"></span> Healthcare formulated</div>
        <?php else : ?>
          <p>No recipes found featuring this product.</p>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>        
    </div>
  </div>
  <script>
    (function(){
      const tabBtns = document.querySelectorAll('.hsl-product-tabs .tab-btn');
      const tabContents = document.querySelectorAll('.hsl-product-tabs .tab-content');
      let openTab = null;

      tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          const tabId = this.getAttribute('data-tab');
          const content = document.getElementById(tabId);

          if (openTab === content) {
            content.style.display = 'none';
            openTab = null;
            this.classList.remove('active');
          } else {
            tabContents.forEach(tc => tc.style.display = 'none');
            tabBtns.forEach(b => b.classList.remove('active'));
            content.style.display = 'block';
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
          <div class="slider-track">
            <?php while ($related_query->have_posts()) : $related_query->the_post(); global $product; ?>
              <div class="related-product-slide">
                <?php wc_get_template_part('content', 'product-slider'); ?>
              </div>
            <?php endwhile; ?>
          </div>
        </div>
        <div class="slider-nav">
          <button class="slider-arrow slider-arrow-left fa fa-caret-left" type="button"></button>
          <button class="slider-arrow slider-arrow-right fa fa-caret-right" type="button"></button>
        </div>
        <div class="slider-dots"></div>
      <?php endif; wp_reset_postdata();
    }
    ?>
  </div>
  <style>
    .related-products-slider {
      overflow: hidden;
      position: relative;
      width: 100%;
    }
    .slider-track {
      display: flex;
      transition: transform 0.4s cubic-bezier(0.4,0,0.2,1);
      will-change: transform;
      width: 100%;
    }
    .related-product-slide {
      min-width: 25%;
      max-width: 25%;
      box-sizing: border-box;
      flex: 0 0 25%;
    }
    @media (max-width: 767px) {
      .related-product-slide {
        min-width: 50%;
        max-width: 50%;
        flex: 0 0 50%;
      }
    }
  </style>
  <script>
    (function(){
      // Responsive slider logic: 4 columns by default, 2 columns below 767px, with sliding effect and touch support
      const slider = document.querySelector('.related-products-slider');
      const track = slider.querySelector('.slider-track');
      const slides = Array.from(track.children);
      const leftArrow = document.querySelector('.slider-arrow-left');
      const rightArrow = document.querySelector('.slider-arrow-right');
      const dotsContainer = document.querySelector('.slider-dots');
      let slidesPerView = window.innerWidth < 767 ? 2 : 4;
      let currentIndex = 0;
      const totalSlides = slides.length;
      let startX = 0, currentTranslate = 0, isDragging = false, animationID = 0;
      function getTotalPages() {
        return Math.max(1, Math.ceil(totalSlides / slidesPerView));
      }
      function updateSlider() {
        slidesPerView = window.innerWidth < 767 ? 2 : 4;
        if (currentIndex > totalSlides - slidesPerView) {
          currentIndex = Math.max(0, totalSlides - slidesPerView);
        }
        const slideWidth = slides[0].offsetWidth;
        track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
        // Update dots
        if (dotsContainer) {
          dotsContainer.innerHTML = '';
          const totalPages = getTotalPages();
          for (let i = 0; i < totalPages; i++) {
            let dotIndex = i * slidesPerView;
            if (i === totalPages - 1) {
              dotIndex = Math.max(0, totalSlides - slidesPerView);
            }
            const dot = document.createElement('span');
            dot.className = 'slider-dot' + (currentIndex === dotIndex ? ' active' : '');
            dot.addEventListener('click', function() {
              currentIndex = dotIndex;
              updateSlider();
            });
            dotsContainer.appendChild(dot);
          }
        }
        // Show/hide arrows
        if (leftArrow) {
          if (currentIndex === 0) {
            leftArrow.style.display = 'none';
          } else {
            leftArrow.style.display = '';
          }
        }
        if (rightArrow) {
          if (currentIndex >= totalSlides - slidesPerView) {
            rightArrow.style.display = 'none';
          } else {
            rightArrow.style.display = '';
          }
        }
      }
      if (leftArrow) leftArrow.addEventListener('click', function() {
        slidesPerView = window.innerWidth < 767 ? 2 : 4;
        currentIndex = Math.max(0, currentIndex - slidesPerView);
        updateSlider();
      });
      if (rightArrow) rightArrow.addEventListener('click', function() {
        slidesPerView = window.innerWidth < 767 ? 2 : 4;
        if (currentIndex + slidesPerView >= totalSlides) {
          currentIndex = Math.max(0, totalSlides - slidesPerView);
        } else {
          currentIndex += slidesPerView;
        }
        updateSlider();
      });
      // Touch/drag support
      track.addEventListener('touchstart', startDrag, {passive: true});
      track.addEventListener('touchmove', onDrag, {passive: false});
      track.addEventListener('touchend', endDrag);
      track.addEventListener('mousedown', startDrag);
      track.addEventListener('mousemove', onDrag);
      track.addEventListener('mouseup', endDrag);
      track.addEventListener('mouseleave', endDrag);
      function startDrag(e) {
        isDragging = true;
        startX = e.touches ? e.touches[0].clientX : e.clientX;
        currentTranslate = -currentIndex * slides[0].offsetWidth;
        track.style.transition = 'none';
        cancelAnimationFrame(animationID);
      }
      function onDrag(e) {
        if (!isDragging) return;
        e.preventDefault();
        const x = e.touches ? e.touches[0].clientX : e.clientX;
        const dx = x - startX;
        track.style.transform = `translateX(${currentTranslate + dx}px)`;
      }
      function endDrag(e) {
        if (!isDragging) return;
        isDragging = false;
        track.style.transition = '';
        const x = e.changedTouches ? e.changedTouches[0].clientX : e.clientX;
        const dx = x - startX;
        const slideWidth = slides[0].offsetWidth;
        if (Math.abs(dx) > slideWidth / 4) {
          if (dx < 0 && currentIndex < totalSlides - slidesPerView) {
            currentIndex += slidesPerView;
          } else if (dx > 0 && currentIndex > 0) {
            currentIndex -= slidesPerView;
          }
        }
        updateSlider();
      }
      // Initial display
      updateSlider();
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
