<?php
/**
 * Partial for Video Post Format
 *
 * @package Greater Media
 * @since   0.1.0
 */
?>
<section class="entry--video__meta" itemprop="articleBody">

	<time datetime="<?php the_time( 'c' ); ?>" class="entry__date"><?php the_time( 'M. j, Y' ); ?></time>

	<h2 class="entry__title"><?php the_title(); ?></h2>

</section>

<section class="entry--video__thumbnail">

	<?php if ( has_post_thumbnail() ) {

		the_post_thumbnail( 'gm-article-thumbnail' );

	} else { ?>

		<img src="http://placehold.it/600x400&text=image">

	<?php } ?>

</section>