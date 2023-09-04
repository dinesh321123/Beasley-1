<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class('show-about'); ?>>
	<div class="article-inner-container">
		<div class="content-wrap">
			<?php get_template_part('partials/show/custom-header'); ?>
		</div>
		<div class="content-wrap">
			<?php ee_the_subtitle('About'); ?>
			<?php the_content(); ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>