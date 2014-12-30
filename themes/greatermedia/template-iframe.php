<?php get_header( 'iframe' ); ?>
	<main class="main" role="main" id="gmr-iframe-main">
		<?php get_template_part( 'content', get_post_format() ); ?>
	</main>
<?php get_footer( 'iframe' );