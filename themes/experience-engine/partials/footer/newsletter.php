<?php if ( ee_has_newsletter_page() ) : ?>
	<div class="newsletter">
		<h6>Newsletter sign up</h6>
		<p>Don't miss on pre-sales, member-only contests and member only events.</p>
		<a class="btn -square -empty -secondary" href="<?php ee_the_newsletter_page_permalink(); ?>">
			<?php bloginfo( 'name' ) ?> Newsletter
		</a>
	</div>
<?php endif; ?>