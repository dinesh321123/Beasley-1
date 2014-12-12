<?php
/**
 * Partial for Standard Post Format
 *
 * @package Greater Media
 * @since   0.1.0
 */
?>
<section class="entry--standard__thumbnail">

<?php if ( has_post_thumbnail() ) {

	the_post_thumbnail( 'gm-article-thumbnail' );

} else { ?>

		<img src="http://placehold.it/600x400&text=image">

<?php } ?>

</section>

<section class="entry--standard__meta">

	<time datetime="<?php the_time( 'c' ); ?>" class="entry__date"><?php the_time( 'j F' ); ?></time>

	<h2 class="entry__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

</section>

