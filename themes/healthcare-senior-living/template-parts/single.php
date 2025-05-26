<?php
/**
 * The template for displaying singular post-types: posts, pages and user-defined custom post types.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

while ( have_posts() ) :
	the_post();
	?>

<main id="content" <?php post_class( 'site-main' ); ?>>

<?php if ( get_post_type() === 'recipe' ) : ?>
	
	<div class="page-header">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="post-thumbnail">
				<?php the_post_thumbnail( 'full', array( 'class' => 'recipe-image-main' ) ); ?> 
			</div>
		<?php endif; ?>
		<div class="post-info">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>	
			<?php 
			$recipe_summary = get_post_meta( get_the_ID(), 'recipe_summaries', true );
			if ( ! empty( $recipe_summary ) && is_array( $recipe_summary ) ) {
				echo '<div class="recipe-summary">';
				foreach ( $recipe_summary as $item ) {
					if ( ! empty( $item['title'] ) && ! empty( $item['value'] ) ) {
						echo '<div class="recipe-summary-item">';
						echo '<span class="recipe-summary-title">' . esc_html( $item['title'] ) . '</span> ';
						echo '<span class="recipe-summary-value">' . esc_html( $item['value'] ) . '</span>';
						echo '</div>';
					}
				}
				echo '</div>';
				
			}
			?>	
			<div><a href="<?php echo get_site_url(); ?>/recipes/" class="elementor-button elementor-button-link elementor-size-sm">See all recipes</a></div>
		</div>
	</div>
	<div class="page-content">
		<?php
		$ingredients = get_post_meta( get_the_ID(), 'recipe_ingredients', true );
		if ( ! empty( $ingredients ) && is_array( $ingredients ) ) {
			echo '<div class="recipe-ingredients"><div class="container">';
			echo '<h2>Ingredients</h2><div class="recipe-ingredients-list">';
			foreach ( $ingredients as $item ) {
				if ( ! empty( $item['title'] ) && ! empty( $item['value'] ) ) {
					echo '<div class="recipe-ingredient-item">';
					echo '<div class="recipe-ingredient-title">' . esc_html( $item['title'] ) . '</div> ';
					echo '<div class="recipe-ingredient-value">' . wp_kses_post( $item['value'] ) . '</div>';
					echo '</div>';
				}
			}
			echo '</div></div></div>';
		}
		?>
		<?php 
		if( get_post()->post_content !== '' ) {
			echo '<div class="recipe-directions"><div class="container">';
			echo '<h2>Directions</h2>';
			the_content();
			echo '</div></div>';
		}
		?>
		<?php 
		$nutrients_title = get_post_meta( get_the_ID(), 'recipe_nutrients_title', true );
		$nutrients = get_post_meta( get_the_ID(), 'recipe_nutrients', true );
		if ( ! empty( $nutrients ) && is_array( $nutrients ) ) {
			$display_title = $nutrients_title ? esc_html( $nutrients_title ) : __( 'Nutrients', 'bbf-hsl' );
			echo '<div class="recipe-nutrients"><div class="container">';
			echo '<h2>' . $display_title . '</h2><div class="recipe-nutrients-list">';
			$col = 0;
			foreach ( $nutrients as $i => $item ) {
				if ( ! empty( $item['label'] ) && ! empty( $item['value'] ) ) {
					if ( $col % 7 === 0 ) {
						if ( $col > 0 ) echo '</div>';
						echo '<div class="recipe-nutrient-row">';
					}
					echo '<div class="recipe-nutrient-item">';
					echo '<div class="recipe-nutrient-label">' . esc_html( $item['label'] ) . '</div> ';
					echo '<div class="recipe-nutrient-value">' . esc_html( $item['value'] ) . '</div>';
					echo '</div>';
					$col++;
				}
			}
			if ($col > 0) echo '</div>';
			echo '</div></div></div>';
		}
		?>
		<?php
		$featured_products = get_post_meta( get_the_ID(), 'recipe_featured_products', true );
		if ( ! empty( $featured_products ) && is_array( $featured_products ) ) {
			echo '<div class="recipe-featured-products"><div class="container"><ul class="products-list">';
			foreach ( $featured_products as $product_id ) {
				$product = wc_get_product( $product_id );
				if ( $product && $product->get_type() ) {
					global $post;
					$backup_post = $post;
					$post = get_post( $product_id );
					setup_postdata( $post );
					wc_get_template_part( 'content', 'product' );
					wp_reset_postdata();
					$post = $backup_post;
				}
			}
			echo '</ul></div></div>';
		}
		?>
		<?php wp_link_pages(); ?>
		<?php if ( has_tag() ) : ?>
		<div class="post-tags">
			<?php the_tags( '<span class="tag-links">' . esc_html__( 'Tagged ', 'hello-elementor' ), ', ', '</span>' ); ?>
		</div>
		<?php endif; ?>
	</div>
	<?php comments_template(); ?>

<?php else: ?>
	<?php if ( apply_filters( 'hello_elementor_page_title', true ) ) : ?>
		<div class="page-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</div>
	<?php endif; ?>

	<div class="page-content">
		<?php the_content(); ?>

		<?php wp_link_pages(); ?>

		<?php if ( has_tag() ) : ?>
		<div class="post-tags">
			<?php the_tags( '<span class="tag-links">' . esc_html__( 'Tagged ', 'hello-elementor' ), ', ', '</span>' ); ?>
		</div>
		<?php endif; ?>
	</div>

	<?php comments_template(); ?>
<?php endif; ?>
</main>

	<?php
endwhile;
