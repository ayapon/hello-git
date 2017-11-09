<?php
/**
 * @package DEWEY
 * @subpackage Welcart_Basic
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">
		
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<article <?php post_class() ?> id="post-<?php the_ID(); ?>">
				
				<div class="entry-meta">
					<span class="date"><time><?php the_date(); ?></time></span>
					<span class="cat"><?php _e("Filed under:"); ?> <?php the_category(',') ?></span>
					<span class="tag"><?php the_tags(__('Tags: ')); ?></span>
					<span class="author"><?php the_author() ?><?php edit_post_link(__('Edit This')); ?></span>
				</div>
				
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; else: ?>
			<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
		<?php endif; ?>

		<div class="pagination_wrapper">
			<?php
			$args = array (
				'type' => 'list',
				'prev_text' => __( ' &laquo; ', 'welcart_basic' ),
				'next_text' => __( ' &raquo; ', 'welcart_basic' ),
			);
			echo paginate_links( $args );
			?>
		</div><!-- .pagenation-wrapper -->
		
		</div><!-- #content -->
	</div><!-- #primary -->


<?php get_sidebar('home'); ?>
<?php get_footer(); ?>