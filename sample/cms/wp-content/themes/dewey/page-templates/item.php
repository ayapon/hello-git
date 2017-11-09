<?php
/*
Template Name: Item
 * @package Welcart
 * @subpackage Welcart_Basic
 */

get_header(); ?>

<section class="container spacer">
<div id="primary" class="site-content">
	<div id="content" role="main">
			
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<article class="page">
					<?php the_content(); ?>
					<?php edit_post_link(__('編集'), '<p>', '</p>'); ?>
				</article>
			<?php endwhile; endif; ?>
			
	</div><!-- #content -->
</div><!-- #primary -->
	
	<?php get_sidebar(); ?>
	<?php get_footer(); ?>