<?php
	$src = get_bloginfo('template_directory').'/images/featured-bg.png';
	if ( has_post_thumbnail($post->ID) ) {
		$featured = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
		$src = $featured[0];
	}
?>

<div class="show__header<?php if( has_post_thumbnail() ) echo ' has-thumbnail'; ?>"

	<?php if( has_post_thumbnail() ) { ?>
	style="
	background-image: linear-gradient(to bottom, rgba(86, 16, 21, .85), rgba(86, 16, 21, .85)), url(<?php echo $src; ?>);
    background-image: -moz-linear-gradient(top, rgba(86, 16, 21, .85), rgba(86, 16, 21, .85)), url(<?php echo $src; ?>);
    background-image: -o-linear-gradient(top, rgba(86, 16, 21, .85), rgba(86, 16, 21, .85)), url(<?php echo $src; ?>);
    background-image: -ms-linear-gradient(top, rgba(86, 16, 21, .85), rgba(86, 16, 21, .85)), url(<?php echo $src; ?>);
    background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(86, 16, 21, .85)), to(rgba(86, 16, 21, .85))), url(<?php echo $src; ?>);
    background-image: -webkit-linear-gradient(top, rgba(86, 16, 21, .85), rgba(86, 16, 21, .85)), url(<?php echo $src; ?>);
	"
	<?php } ?>
>
	<div class="show__header-content">
		<div class="show__cast">
			<?php if ( get_post_meta( $post->ID, 'logo_image', true ) ) {
		        $src = get_post_meta( $post->ID, 'logo_image', true );
		        echo wp_get_attachment_image( $src );
			} ?>
		</div>
		<nav class="show__nav">
			<a href="<?php the_permalink(); ?>"><h1 class="show__title"><?php the_title(); ?></h1></a>
			<ul>
				<?php \GreaterMedia\Shows\about_link_html( get_the_ID() ); ?>
				<?php \GreaterMedia\Shows\podcasts_link_html( get_the_ID() ); ?>
				<?php \GreaterMedia\Shows\galleries_link_html( get_the_ID() ); ?>
				<?php \GreaterMedia\Shows\videos_link_html( get_the_ID() ); ?>
			</ul>
		</nav>
		<div class="show__meta">
			<em>Weekdays</em>
			<em>5:30am - 10:30am</em>
			<a href="http://www.facebook.com/sharer/sharer.php?u=[URL]&title=[TITLE]" class="icon-facebook social-share-link"></a>
			<a href="http://twitter.com/home?status=[TITLE]+[URL]" class="icon-twitter social-share-link"></a>
			<a href="https://plus.google.com/share?url=[URL]" class="icon-google-plus social-share-link"></a>
		</div>
	</div>
</div>