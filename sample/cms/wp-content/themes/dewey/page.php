<?php
/**
 * @package DEWEY
 * @subpackage Welcart_Basic
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">

					<div class="well bs-component">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<?php get_template_part( 'template-parts/content', get_post_format() ); ?>
			<?php posts_nav_link(' &#8212; ', __('&laquo; Newer Posts'), __('Older Posts &raquo;')); ?>
			
		<?php endwhile; else: ?>
			
			<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
				</article>
		<?php endif; ?>
			</div><!-- #bs-component -->
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar('other'); ?>
<?php get_footer(); ?>