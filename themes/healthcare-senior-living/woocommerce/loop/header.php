<?php
/**
 * Product taxonomy archive header
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/header.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<header class="woocommerce-products-header">
	<?php
	/**
	 * Hook: woocommerce_show_page_title.
	 *
	 * Allow developers to remove the product taxonomy archive page title.
	 *
	 * @since 2.0.6.
	 */
	if( is_shop() ) {
		if ( apply_filters( 'woocommerce_show_page_title', true ) ) :
			?>
			<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
		<?php endif;
	} else { 
		?>
		<div class="hsl-loop-header">
			<div class="hsl-loop-header___content">
				<h1><?php woocommerce_page_title(); ?></h1>
				<?php
					/**
					 * Hook: woocommerce_archive_description.
					 *
					 * @since 1.6.2.
					 * @hooked woocommerce_taxonomy_archive_description - 10
					 * @hooked woocommerce_product_archive_description - 10
					 */
					do_action( 'woocommerce_archive_description' );
				?>
			</div>
			<?php
			$thumbnail_id = get_woocommerce_term_meta( get_queried_object()->term_id, 'thumbnail_id', true );
			$image = wp_get_attachment_url( $thumbnail_id );
			if ( $image ) { ?>
				<div class="hsl-loop-header_image">
					<?php echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( get_queried_object()->name ) . '" />'; ?>
				</div>
			<?php
			}
			?>
		</div>
		<?php
		$curr_cat = get_queried_object(); 
	$curr_cat_parent = $curr_cat->parent;
		if ( $curr_cat_parent == 19) {
			echo do_shortcode( '[INSERT_ELEMENTOR id="2133"]' );
	} elseif (
		$curr_cat->term_id != 21 &&
		$curr_cat->term_id != 22 &&
		$curr_cat_parent != 21 &&
		$curr_cat_parent != 22
	) {
			echo do_shortcode( '[INSERT_ELEMENTOR id="2051"]' );
		}

		if( is_product_category(20) || $this_category->category_parent == 20 ) {
			echo do_shortcode( '[INSERT_ELEMENTOR id="2068"]' );
		}

		if( is_product_category(21) || $this_category->category_parent == 21 || is_product_category(22) || $this_category->category_parent == 22 ) {
			echo do_shortcode( '[INSERT_ELEMENTOR id="37948"]' );
		}
	}

	?>
</header>
