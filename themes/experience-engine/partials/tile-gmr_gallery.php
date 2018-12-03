<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/tile/thumbnail' ); ?>
	<?php get_template_part( 'partials/tile/title' ); ?>

	<p class="type">
		<svg width="13" height="14" fill="var(--brand-primary)">
			<path fill-rule="evenodd" d="M11.817.985H3.119v8.697h8.698V.985zM3.119 0a.985.985 0 0 0-.984.985v8.697c0 .544.44.985.984.985h8.698c.544 0 .985-.441.985-.985V.985A.985.985 0 0 0 11.817 0H3.119z" clip-rule="evenodd"/>
			<mask id="a" width="10" height="11" x="0" y="3" maskUnits="userSpaceOnUse">
				<path d="M0 3.938h9.846v9.845H0V3.938z"/>
			</mask>
			<g mask="url(#a)">
				<path fill-rule="evenodd" d="M9.989 2.812H1.29v8.697H9.99V2.812zM1.29 1.827a.985.985 0 0 0-.984.985v8.697c0 .544.44.985.984.985H9.99c.543 0 .984-.44.984-.985V2.812a.985.985 0 0 0-.984-.985H1.29z" clip-rule="evenodd"/>
			</g>
			<path d="M5.66 6.646l1.231 1.477 1.723-2.215 2.216 2.954H3.937l1.724-2.216z"/>
		</svg>
		photo gallery
	</p>
</div>
