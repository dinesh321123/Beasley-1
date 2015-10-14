<?php
/**
 * Partial for Standard Post Format
 *
 * @package Greater Media
 * @since   0.1.0
 */
?>

<section class="entry-content--standard">

	<header class="entry-header">

		<h2 class="entry-title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

	</header>

	<section class="entry-content" itemprop="articleBody">

		<?php the_content(); ?>

	</section>

</section>


<section class="entry-thumbnail--standard">

<?php if ( has_post_thumbnail() ) {

	the_post_thumbnail( 'gm-article-thumbnail' );

} else { ?>

		<img src="http://placehold.it/600x400&text=image">

<?php } ?>

</section>

