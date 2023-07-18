<?php
/**
 * Template Name: App Only
 */

// Include the header template
get_header();

// Set up the current post data
the_post();

// Default description for app-only content
$app_only_description = "
Welcome to our exclusive app-only content! This content is specifically crafted for our loyal app users to provide an enhanced experience. Download our amazing mobile app today to access this unique content and unlock a world of possibilities Don't miss out on this app-only content. Download our app now and join our thriving community of users.

Click here to download our app: <a href=\"https://example.com/app\">Download App</a>

By downloading our app, you'll enjoy a seamless and personalised experience. Take advantage of this opportunity to discover the best that our app has to offer and elevate your experience with our app-only content.

Unlock the full potential of our platform by downloading our app today! See you on the inside!
";

// Get the page that has the selected template as "App Only"
$app_only_pages = get_pages(array(
    'meta_key' => '_wp_page_template',
    'meta_value' => 'templates/app-only.php', // Replace with the actual template file name
    'hierarchical' => 0,
    'number' => 1
));

// If a matching page is found, update the description
if ($app_only_pages) {
    $app_only_page = $app_only_pages[0];
    if ($app_only_page && $app_only_page->post_content) {
        // Use the page's content as the updated description
        $app_only_description = $app_only_page->post_content;
    }
}
?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php get_template_part('partials/page/header'); ?>

    <div class="entry-content content-wrap">
        <?php if (bbgi_featured_image_layout_is(null, 'inline')) : ?>
            <?php get_template_part('partials/featured-media'); ?>
        <?php endif; ?>
        <div class="description">
            <?php echo apply_filters('the_content', $app_only_description); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
