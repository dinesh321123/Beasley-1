<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="search-q" id="q" class="screen-reader-text">Search for:</label>
	<input id="search-q" type="search" class="search-field" name="s" value="<?php echo get_search_query( true /* <-- to escape? */ ); ?>" placeholder="Search">
	<button type="submit" class="search-submit" aria-label="Submit search">
		<svg xmlns="http://www.w3.org/2000/svg" width="14" height="15">
			<path d="M10.266 9.034h-.65l-.23-.222a5.338 5.338 0 0 0 1.216-4.385C10.216 2.144 8.312.32 6.012.042a5.342 5.342 0 0 0-5.97 5.97c.279 2.3 2.102 4.204 4.385 4.59a5.338 5.338 0 0 0 4.385-1.215l.222.23v.649l3.49 3.49c.337.336.887.336 1.224 0s.336-.887 0-1.224l-3.482-3.498zm-4.928 0c-2.044 0-3.695-1.65-3.695-3.696s1.65-3.695 3.695-3.695 3.696 1.65 3.696 3.695-1.65 3.696-3.696 3.696z" fill="currentcolor"/>
		</svg>
	</button>
</form>
