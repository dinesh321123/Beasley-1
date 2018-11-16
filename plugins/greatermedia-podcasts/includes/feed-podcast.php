<?php
/**
 * Podcast RSS feed template
 *
 * @package WordPress
 * @subpackage SeriouslySimplePodcasting
 */

function feed_podcast_clean_output( $output, $filter = null ) {
	$output = html_entity_decode( $output );

	if ( ! is_null( $filter ) ) {
		$output = apply_filters( $filter, $output );
	}

	$output = wp_strip_all_tags( $output );

	return $output;
}

$podcast;
$parent_podcast_id = 0;
// Hide all errors
error_reporting( 1 );

// Action hook for plugins/themes to intercept template
// Any add_action( 'do_feed_podcast' ) calls must be made before the 'template_redirect' hook
// If you are still going to load this template after using this hook then you must not output any data
do_action( 'do_feed_podcast' );

if( isset( $_GET['podcast_series'] ) && strlen( $_GET['podcast_series'] ) > 0 ) {
	$args = array(
		'post_type' => 'podcast',
		'post_status' => 'publish',
		'posts_per_page' => 1,
		'name' => esc_attr( $_GET['podcast_series'] )
	);
	$podcast = get_posts( $args );
	if( $podcast ) {
		$parent_podcast_id = $podcast[0]->ID;
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $podcast[0]->ID ), 'full' );
		if( is_array( $image ) ) {
			$image = $image[0];
		}
	}
	wp_reset_query();
} else {
	$image = false;
}

$category = sanitize_text_field( get_post_meta( $parent_podcast_id, 'gmp_category', true ) );
if( ! $category || strlen( $category ) == 0 || $category == '' ) {
	$category = 'Music';
} else {
	$subcategory = sanitize_text_field( get_post_meta( $parent_podcast_id, 'gmp_sub_category', true ) );
	if( ! $subcategory || strlen( $subcategory ) == 0 || $subcategory == '' ) {
		$subcategory = false;
	}
}

// Get podcast data
$title = $podcast[0]->post_title;
if( ! $title || strlen( $title ) == 0 || $title == '' ) {
	$title = get_bloginfo( 'name' );
}

$description = $podcast[0]->post_content;
if( ! $description || strlen( $description ) == 0 || $description == '' ) {
	$description = get_bloginfo( 'description' );
}

$itunes_description = strip_tags( $description );
$language = get_bloginfo( 'language' );
$copyright = '&#xA9; ' . date( 'Y' ) . ' ' . get_bloginfo( 'name' );

$subtitle = sanitize_text_field( get_post_meta( $parent_podcast_id, 'gmp_subtitle', true ) );
if( ! $subtitle || strlen( $subtitle ) == 0 || $subtitle == '' ) {
	$subtitle = get_bloginfo( 'description' );
}

$author = sanitize_text_field( get_post_meta( $parent_podcast_id, 'gmp_author', true ) );
if( ! $author || strlen( $author ) == 0 || $author == '' ) {
	$author_id = $podcast[0]->post_author;
	$author = get_user_by( 'id', $author_id );
	$author = $author->first_name;
	if( ! $author || strlen( $author ) == 0 || $author == '' ) {
		$author = get_bloginfo( 'name' );
	}
}

$owner_name = get_bloginfo( 'name' );
$owner_email = get_bloginfo( 'admin_email' );

$explicit = sanitize_text_field( get_post_meta( $post->ID, 'gmp_explicit', true ) );
if( $explicit && $explicit == 'on' ) {
	$explicit = 'Yes';
} else {
	$explicit = 'No';
}

header( 'Content-Type: ' . feed_content_type( 'rss-http' ) . '; charset=' . get_option( 'blog_charset' ), true );

echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
	<?php do_action( 'rss2_ns' ); ?>
>

<channel>
	<title><?php echo esc_html( $title ); ?></title>
	<atom:link href="<?php esc_url( self_link() ); ?>" rel="self" type="application/rss+xml" />
	<link><?php esc_url( bloginfo_rss('url') ) ?></link>
	<description><?php echo esc_html( $description ); ?></description>
	<lastBuildDate><?php echo esc_html( mysql2date( 'D, d M Y H:i:s +0000', get_lastpostmodified( 'GMT' ), false ) ); ?></lastBuildDate>
	<language><?php echo esc_html( $language ); ?></language>
	<copyright><?php echo esc_html( $copyright ); ?></copyright>
	<itunes:subtitle><?php echo esc_html( $subtitle ); ?></itunes:subtitle>
	<itunes:author><?php echo esc_html( $author ); ?></itunes:author>
	<itunes:summary><?php echo esc_html( $itunes_description ); ?></itunes:summary>
	<itunes:owner>
		<itunes:name><?php echo esc_html( $owner_name ); ?></itunes:name>
		<itunes:email><?php echo esc_html( $owner_email ); ?></itunes:email>
	</itunes:owner>
	<itunes:explicit><?php echo esc_html( $explicit ); ?></itunes:explicit>
	<?php if( $image ) { ?>
	<itunes:image href="<?php echo esc_url( $image ); ?>"></itunes:image>
	<?php } ?>
	<?php if( $category ) { ?>
	<itunes:category text="<?php echo esc_attr( $category ); ?>">
		<?php if( $subcategory ) { ?>
		<itunes:category text="<?php echo esc_attr( $subcategory ); ?>"></itunes:category>
		<?php } ?>
	</itunes:category>
	<?php } ?>
	<?php if( isset( $new_feed_url ) && strlen( $new_feed_url ) > 0 && $new_feed_url != '' ) { ?>
	<itunes:new-feed-url><?php echo esc_url( $new_feed_url ); ?></itunes:new-feed-url>
	<?php }

	// paged views for podcasts
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$posts_per_page = get_option( 'posts_per_rss', '' );
	$posts_per_page = ( '' !== $posts_per_page ) ? $posts_per_page : 10;

	// Fetch podcast episodes
	$args = array(
		'post_type'      => 'episode',
		'post_status'    => 'publish',
		'posts_per_page' => absint( $posts_per_page ),
		'paged'          => absint( $paged ),
		'post_parent'    => absint( $parent_podcast_id )
	);

	$qry = new WP_Query( $args );

	if ( $qry->have_posts() ) :
		while( $qry->have_posts() ) :

		$qry->the_post();
		$episode_id = get_the_ID();

		// Featured image
		$image = false;
		$image_width = 500;
		$image_height = 375;
		$thumbnail_id = get_post_thumbnail_id( $episode_id );
		if ( ! empty( $thumbnail_id ) ) {
			$image = bbgi_get_image_url( $thumbnail_id, $image_width, $image_height );
		}

		// Enclosure (audio file)
		$enclosure = get_post_meta( $episode_id, 'enclosure', true );

		// Episode duration
		$duration = get_post_meta( $episode_id, 'duration' , true );
		if ( empty( $duration ) ) {
			$duration = '0:00';
		}

		// File size
		$size = get_post_meta( $episode_id, 'filesize_raw' , true );
		if ( ! $size || strlen( $size ) == 0 || $size == '' ) {
			$size = GMPFeed::get_file_size( $enclosure );
			$size = esc_html( $size['raw'] );
		}

		if ( ! $size || strlen( $size ) == 0 || $size == '' ) {
			$size = 1;
		}

		// File MIME type (default to MP3 to ensure that there is always a value for this)
		$mime_type = GMPFeed::get_attachment_mimetype( $enclosure );
		if ( ! $mime_type || strlen( $mime_type ) == 0 || $mime_type == '' ) {
			$mime_type = 'audio/mpeg';
		}

		// Episode explicit flag
		$ep_explicit = get_post_meta( $episode_id, 'gmp_episode_explicit' , true );
		if ( $ep_explicit && $ep_explicit == 'on' ) {
			$explicit_flag = 'Yes';
		} else {
			$explicit_flag = 'No';
		}

		// Episode block flag
		$ep_block = get_post_meta( $episode_id, 'gmp_block' , true );
		if( $ep_block && $ep_block == 'on' ) {
			$block_flag = 'Yes';
		} else {
			$block_flag = 'No';
		}

		// Episode keywords
		$keyword_list = wp_get_post_terms( $episode_id, 'keywords' );
		$keywords = false;
		if ( $keyword_list && count( $keyword_list ) > 0 ) {
			$c = 0;
			foreach ( $keyword_list as $k ) {
				if ( $c == 0 ) {
					$keywords = esc_html( $k->name );
					++$c;
				} else {
					$keywords .= ', ' . esc_html( $k->name );
				}
			}
		}

		// Episode content
		$content = get_the_content_feed( 'rss2' );
		$content = html_entity_decode( $content );

		// iTunes summary does not allow any HTML and must be shorter than 4000 characters
		$itunes_summary = wp_strip_all_tags( get_the_content() );
		$itunes_summary = substr( $itunes_summary, 0, 3950 );
		$itunes_summary = strip_shortcodes( $itunes_summary );

		// iTunes short description does not allow any HTML and must be shorter than 4000 characters
		$itunes_excerpt = wp_strip_all_tags( get_the_excerpt() );
		$itunes_excerpt = substr( $itunes_excerpt, 0, 3950 );

		$item_description     = feed_podcast_clean_output( get_the_excerpt(), 'the_excerpt_rss' );
		$item_itunes_subtitle = feed_podcast_clean_output( $itunes_excerpt );
		$item_itunes_summary  = feed_podcast_clean_output( $itunes_summary );

	?>
	<item>
		<title><?php esc_html( the_title_rss() ); ?></title>
		<link><?php esc_url( the_permalink_rss() ); ?></link>
		<pubDate><?php echo esc_html( mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', false ), false ) ); ?></pubDate>
		<dc:creator><?php echo esc_html( $author ); ?></dc:creator>
		<guid isPermaLink="false"><?php esc_html( the_guid() ); ?></guid>
		<description><![CDATA[<?php echo wp_strip_all_tags( $item_description ); ?>]]></description>
		<itunes:subtitle><![CDATA[<?php echo wp_strip_all_tags( $item_itunes_subtitle ); ?>]]></itunes:subtitle>
		<content:encoded><![CDATA[<?php echo wp_strip_all_tags( $content ); ?>]]></content:encoded>
		<itunes:summary><![CDATA[<?php echo wp_strip_all_tags( $item_itunes_summary ); ?>]]></itunes:summary>
		<enclosure url="<?php echo esc_url( $enclosure ); ?>" length="<?php echo esc_attr( $size ); ?>" type="<?php echo esc_attr( $mime_type ); ?>"></enclosure>
		<itunes:explicit><?php echo esc_html( $explicit_flag ); ?></itunes:explicit>
		<itunes:block><?php echo esc_html( $block_flag ); ?></itunes:block>
		<itunes:duration><?php echo esc_html( $duration ); ?></itunes:duration>
		<itunes:author><?php echo esc_html( $author ); ?></itunes:author>
		<?php if( $keywords ) : ?>
		<itunes:keywords><?php echo esc_html( $keywords ); ?></itunes:keywords>
		<?php endif; ?>
		<?php if ( ! empty( $image ) ) : ?>
		<media:content medium="image" url="<?php echo esc_url( $image ); ?>" width="<?php echo esc_attr( $image_width ); ?>" height="<?php echo esc_attr( $image_height ); ?>" />
		<?php endif; ?>
	</item><?php endwhile; endif; ?>
</channel>
</rss><?php exit; ?>
