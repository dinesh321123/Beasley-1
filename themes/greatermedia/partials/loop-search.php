<?php while ( have_posts() ) : the_post(); ?>
	<?php
	$title = get_the_title();
	$keys= explode(" ",$s);
	$title = preg_replace('/('.implode('|', $keys) .')/iu', '<span class="search__result--term">\0</span>', $title);
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'search__result' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

		<time datetime="<?php the_time( 'c' ); ?>" class="search__result--date"><?php the_time( 'M j, Y' ); ?></time>

		<h3 class="search__result--title"><a href="<?php the_permalink(); ?>"><?php echo $title; ?></a></h3>

	</article>
<?php endwhile; ?>