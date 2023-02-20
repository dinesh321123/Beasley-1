<?php
/**
 * Template Name: My Account
 */
?>

<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/page/header' ); ?>

	<div class="content-wrap">
		<?php get_template_part( 'partials/page/description' ); ?>
	</div>
</div>
<div class="accountCancellation"></div>
<?php get_footer(); ?>
