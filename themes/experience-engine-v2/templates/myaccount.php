<?php
/**
 * Template Name: My Account
 */
?>
<?php get_header(); ?>
<?php
if ( 'disabled' === get_option( 'ee_login', '' ) ) {

	global $wp_query;
	$wp_query->set_404();
	status_header(404);
	include(get_404_template());
	exit;

} else {

	the_post(); 
	
	$publisher_id = get_option( 'ee_publisher' );

	$publisher = array();
	$publishers_map = array();
	$ee = \Bbgi\Module::get( 'experience-engine' );
	foreach ( $ee->get_publisher_list() as $item ) {
		$publishers_map[ $item['id'] ] = $item['title'];
		if ( $item['id'] == $publisher_id ) {
			$publisher = $item;
		}
	}
	
	?>

	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="post-info">
			<h1><?php the_title(); ?></h1>
			<p>Welcome back to your <?php echo $publisher['title']; ?> account!</p>
			<p>Thanks for joining us and unlocking exclusive content, contests, and customizing your experience with <?php echo $publisher['title']; ?>.</p>
		</header>

	<div class="entry-content content-wrap">
		<div class="description"><?php the_content(); ?>
            <div class="info_account"></div>
			<div class="preference-section"></div>
			<div class="cancel_account">
				<?php echo do_shortcode('[cancel_account]'); ?>
			</div>
		</div>

			<?php get_template_part( 'partials/footer/common', 'description' ); ?>
			<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
		</div>
	</div>

	<?php
}
get_footer(); ?>
