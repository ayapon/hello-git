<?php
/*
Template Name: Normal
 * @package Welcart
 * @subpackage Welcart_Basic
 */

get_header(); ?>

	<section id="primary" class="container spacer">
		<div id="content" role="main">

					<div class="bs-component">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<?php the_content(); ?>
					<?php edit_post_link(__('編集'), '<p>', '</p>'); ?>
				</article>
			<?php endwhile; endif; ?>
			</div><!-- #bs-component -->
		</div><!-- #content -->
	</section><!-- #primary -->    
	
	<?php get_sidebar(); ?>
	<?php get_footer(); ?>