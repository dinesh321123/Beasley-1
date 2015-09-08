<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header();

// 	get_template_part( 'partials/article', 'page' );
?>
	
<div class="container">
	<section class="content">
		<article id="post-242" class="article cf post-242 page type-page status-publish hentry" role="article" itemscope="" itemtype="http://schema.org/BlogPosting">
		<header class="article__header">
			<h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>
			<?php get_template_part( 'partials/social-share' ); ?>
		</header>
		<section class="article__content" itemprop="articleBody">
			<?php the_content(); ?>
			<div id="gmcltTraffic_mapLoading" class="gmcltTraffic_loading">
				<p>Loading...</p>
				<img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>images/WBTajaxLoader.gif">
			</div>
			<div id="trafficMap-canvas"></div>
			<div class="ad__in-loop ad__in-loop--desktop">
				<?php do_action( 'acm_tag_gmr_variant', 'leaderboard-body', 'desktop' ); ?>
			</div>
			<div class="ad__in-loop ad__in-loop--mobile">
				<?php do_action( 'acm_tag_gmr_variant', 'mrec-lists', 'mobile' ); ?>
			</div>
			<div id="gmcltTraffic_listLoading" class="gmcltTraffic_loading">
				<p>Loading...</p>
				<img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>images/WBTajaxLoader.gif">
			</div>
			<div id="gmcltTraffic_list" class="gmcltTraffic_list">
				
			</div>
		</section>
		
	
	</section>
	</article>
</div>

<script type="text/javascript" src="<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>assets/js/vendor/handlebars-v3.0.3.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>

<script id="list-template" type="text/x-handlebars-template">
	{{#each this}}
		<article class="entry">
				<section style="float: right;">
					<img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>traffic/{{marker}}">
				</section>
				<section style="float: left; width: 80%;">
					<time>{{dateString}}</time>
					<h2>{{headline}}</h2>
					<p>{{body}}</p>
				</section>
			</article>

	{{/each}}
</script>

<script id="error-template" type="text/x-handlebars-template">
	<h2>Sorry!</h2>
	<p>An error has occurred while loading traffic information. Please refresh the page and try again.</p>
</script>

<script type="text/javascript">
jQuery(document).ready(function(){
	GMCLT.Traffic.init();
});


</script>

<?php get_footer(); ?>




		