<?php

/**
 * Outputs meta tags needed for Lytics if,
 *
 * 1. Is Single Post
 *
 */
function lytics_og_tags() {

    if ( is_single() ){

        $post_id = get_queried_object_id();
        $post = get_post( $post_id );

        $topics = array();

        $shows = get_the_terms( $post_id, '_shows' );
        if ( is_array( $shows ) && ! empty( $shows ) ) {
            $topics = wp_list_pluck( $shows, 'name' );
        }

        $categories = wp_get_post_categories( $post_id );
        if ( ! empty( $categories ) ) {
            $categories = array_filter( array_map( 'get_category', $categories ) );
            $topics = array_merge( $topics, wp_list_pluck( $categories, 'name' ) );
        }

        // Ensure there are no commas in each topic name
        $filtered_topics = array();
        foreach($topics as $key=>$value){
            if ( $value != 'Uncategorized' ) {
                $filtered_topics[] = preg_replace( '/[^A-Za-z0-9? ]/', '', $value );
            }
        }

        $filtered_topics = str_replace( ' amp ', ' and ', implode( ', ', $filtered_topics ) );

        // Publish Lytics topics
        if ( ! empty( $filtered_topics ) ) {
            echo "\n";
            echo "<meta name=\"lytics:topics\" content=\"" . $filtered_topics . "\"/>";
        }

        // Publish author user account
        echo "\n";
        echo "<meta name=\"lytics:author\" content=\"" . get_the_author_meta( 'user_login', $post->post_author ) . "\"/>";

    }
}

add_action( 'wp_head', 'lytics_og_tags', 1 );
