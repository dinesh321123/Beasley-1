<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text">Search for:</span>
		<input type="search" class="search-field" name="s" value="<?php echo get_search_query( true /* <-- to escape? */ ); ?>" placeholder="Search">
	</label>
	<button type="submit" class="search-submit">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 15" width="14" height="15">
			<path d="M10.2656 9.03376h-.64878l-.22994-.22172c.98542-1.14969 1.49462-2.71819 1.21542-4.38523C10.2163 2.14386 8.3111.320784 6.01173.041574 2.53804-.385452-.385452 2.53804.041574 6.01173.320784 8.3111 2.14386 10.2163 4.42681 10.6023c1.66704.2792 3.23554-.23 4.38523-1.21542l.22172.22994v.64878l3.49014 3.4901c.3367.3367.8869.3367 1.2236 0s.3367-.8869 0-1.2236l-3.4819-3.49834zm-4.92726 0c-2.0448 0-3.69542-1.65062-3.69542-3.69542s1.65062-3.69542 3.69542-3.69542 3.69542 1.65062 3.69542 3.69542-1.65062 3.69542-3.69542 3.69542z" fill="currentcolor" />
		</svg>
	</button>
</form>
