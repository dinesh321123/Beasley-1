<div id="header__search--form" class="header__search--form">
	<form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="screen-reader-text" for="s">Search for:</label>
		<input type="text" value="<?php echo get_search_query(); ?>" name="s" id="header-search" class="header__search--input" placeholder="Search <?php bloginfo( 'name' ); ?>" autocomplete="off">
		<button type="submit" id="searchsubmit" class="header__search--submit">
		<button class="header__search--cancel"></button>
	</form>

	<div class="header-search-body">
		<div id="keyword-search-container"></div>
	
		<button class='header-search__search-all-btn' href='#'>Search All Content</button>
	</div>

	<script type='text/template' id="keyword-search-body-template">
		<div class='keyword-search'>
			<div class='keyword-search__header'></div>
			<div class='keyword-search__items'></div>
		</div>
	</script>

	<script type="text/template" id="keyword-search-item-template">
		<a href='<%= url %>'>
			<div class='keyword-search-item'>
				<div class='keyword-search-item__keyword'>Keyword: <strong><%= keyword %></strong></div>
				<div class='keyword-search-item__article'><%= title %></div>
			</div>
		</a>
	</script>
</div>