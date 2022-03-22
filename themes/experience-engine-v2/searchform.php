<?php
	$aria_label = isset($args['aria_label']) && $args['aria_label'] != "" ? $args['aria_label'] : "" ;
	$for_header_section = isset($args['for_header_section']) && $args['for_header_section'] == true ? true : false ;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="search-q" id="q" class="screen-reader-text">Search for:</label>
	<input id="search-q" type="search" class="search-field <?php echo $aria_label; ?>" name="s" value="<?php echo get_search_query( true /* <-- to escape? */ ); ?>" placeholder="Search" <?php echo $for_header_section ? 'style="display: none;"' : ''; ?>>
	<button type="submit" class="search-submit" <?php echo $for_header_section ? 'id="wp-search-submit"' : ''; ?> aria-label="Submit search">
		<?php if ( $for_header_section ) : ?>
			<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0 0 50 50" style=" fill:#ffffff;">
				<path d="M 21 3 C 11.601563 3 4 10.601563 4 20 C 4 29.398438 11.601563 37 21 37 C 24.355469 37 27.460938 36.015625 30.09375 34.34375 L 42.375 46.625 L 46.625 42.375 L 34.5 30.28125 C 36.679688 27.421875 38 23.878906 38 20 C 38 10.601563 30.398438 3 21 3 Z M 21 7 C 28.199219 7 34 12.800781 34 20 C 34 27.199219 28.199219 33 21 33 C 13.800781 33 8 27.199219 8 20 C 8 12.800781 13.800781 7 21 7 Z"></path>
			</svg>
		<?php else : ?>
			<svg xmlns="http://www.w3.org/2000/svg" width="14" height="15">
			<path d="M10.266 9.034h-.65l-.23-.222a5.338 5.338 0 0 0 1.216-4.385C10.216 2.144 8.312.32 6.012.042a5.342 5.342 0 0 0-5.97 5.97c.279 2.3 2.102 4.204 4.385 4.59a5.338 5.338 0 0 0 4.385-1.215l.222.23v.649l3.49 3.49c.337.336.887.336 1.224 0s.336-.887 0-1.224l-3.482-3.498zm-4.928 0c-2.044 0-3.695-1.65-3.695-3.696s1.65-3.695 3.695-3.695 3.696 1.65 3.696 3.695-1.65 3.696-3.696 3.696z" fill="currentcolor"/>
		</svg>
		<?php endif; ?>
	</button>
</form>
