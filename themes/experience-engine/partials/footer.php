<footer id="footer" class="footer">
	<?php if ( has_custom_logo() ) : ?>
		<?php the_custom_logo(); ?>
	<?php endif; ?>
	<div class="footer-meta">
		<?php get_template_part( 'partials/footer/download-app' ); ?>
		<?php get_template_part( 'partials/footer/newsletter' ); ?>
		<?php get_template_part( 'partials/footer/about' ); ?>
		<?php get_template_part( 'partials/footer/connect' ); ?>
	</div>
</footer>