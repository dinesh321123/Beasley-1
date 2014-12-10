<?php

namespace GreaterMedia\Shows;

/**
 * Returns user query for users that are associated with a particular show.
 *
 * IMPORTANT: This only will pull users that are ACTUALLY MEMBERS OF THE CURRENT BLOG. If you are a superadmin, and not
 * specifically a user of this blog, you will NOT be returned by this function!
 *
 * @param int|WP_Post $show The show id or show object to retrieve personalities for.
 *
 * @return WP_User_Query
 */
function get_show_personality_query( $show ) {
	global $wpdb; // to get blog prefix

	if ( ! function_exists( '\TDS\get_related_term' ) ) {
		return array();
	}

	$show_term = \TDS\get_related_term( $show );

	if ( ! is_object( $show_term ) ) {
		return array();
	}

	$show_tt_id = $show_term->term_taxonomy_id;

	$args = array(
		'meta_query' => array(
			array(
				'key' => $wpdb->prefix . 'show_tt_id_' . intval( $show_tt_id ),
			),
		),
		'number' => 100, // I seriously doubt there will EVER be this many users for one show...
	);
	$user_query = new \WP_User_Query( $args );

	return $user_query;
}

/**
 * Returns the users associated with a particular show.
 *
 * @param int|WP_Post $show The show to get users for.
 *
 * @return array Users for the provided show
 */
function get_show_personalities( $show ) {
	$query = get_show_personality_query( $show );

	return $query->get_results();
}

/**
 * Returns any available social data for the provided user.
 *
 * @param int|WP_User $user User ID or user object to get social data for.
 *
 * @return array Array of social data for the user.
 */
function get_personality_social_data( $user ) {
	$social_data = array();

	if ( is_numeric( $user ) ) {
		$user_id = $user;
	} else if ( is_object( $user ) && is_a( $user, 'WP_User' ) ) {
		$user_id = $user->ID;
	}

	$twitter = get_user_meta( $user_id, 'twitter', true );
	$facebook = get_user_meta( $user_id, 'facebook', true );
	$googleplus = get_user_meta( $user_id, 'googleplus', true );

	if ( $twitter ) {
		$social_data['twitter'] = $twitter;
	}

	if ( $facebook ) {
		$social_data['facebook'] = $facebook;
	}

	if ( $googleplus ) {
		$social_data['googleplus'] = $googleplus;
	}

	return $social_data;
}

function get_personality_social_ul( $user ) {
	$social_data = get_personality_social_data( $user );
	if ( count( $social_data ) > 0 ) : ?>
		<ul class="personality-social">
			<?php foreach( $social_data as $social_provider => $provider_value ) : ?>
				<?php
				switch( $social_provider ) {
					case 'twitter':
						?><li class="social-item twitter"><a href="http://twitter.com/<?php echo esc_attr( $provider_value ); ?>">Twitter</a></li><?php
						break;
					case 'facebook':
						?><li class="social-item facebook"><a href="<?php echo esc_url( $provider_value ); ?>">Facebook</a></li><?php
						break;
					case 'googleplus':
						?><li class="social-item googleplus"><a href="<?php echo esc_url( $provider_value ); ?>">Google Plus</a></li><?php
						break;
				}
				?>
			<?php endforeach; ?>
		</ul>
	<?php endif;
}
