<?php
/*
Template Name: Normal with HistoryBack
 * @package Welcart
 * @subpackage Welcart_Basic
 */

get_header(); ?>

<section id="primary" class="container spacer">
	<div id="content" role="main">

				<h2 class="pagetitle"><?php the_title(); ?></h2>
					<div class="well bs-component">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<?php the_content(); ?>
					<?php edit_post_link(__('編集'), '<p>', '</p>'); ?>

			<?php endwhile; endif; ?>
			</div><!-- #bs-component -->
	</div><!-- #content -->
</section><!-- #primary -->    
<div class="go_kiyaku wow fadeIn" data-wow-delay="0.2s"><a href="index.php" class="btn btn-tertiary btn-lg historyback">前のページへ戻る</a></div>
<br />
<br />

	
	<?php get_sidebar(); ?>
	<?php get_footer(); ?>