<?php
/**
 * @package DEWEY
 * @subpackage Welcart_Basic
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">

					<div class="well bs-component">
			<section class="error-404 not-found">

					<h1 class="page-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'welcart_basic' ); ?></h1>


				<div class="page-content">
<!--					<p><?php _e( 'It looks like nothing was found at this location. Maybe try a search?', 'welcart_basic' ); ?></p>-->
<p>トップページやメニューからお探しください</p>

<!--					<?php get_search_form(); ?>-->
				</div><!-- .page-content -->
			</section><!-- .error-404 -->

			</div><!-- #bs-component -->
		</div><!-- #content -->
	</div><!-- #primary -->


<?php get_sidebar(); ?>
<?php get_footer(); ?>
