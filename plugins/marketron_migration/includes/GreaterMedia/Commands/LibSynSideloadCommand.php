<?php

namespace GreaterMedia\Commands;

/**
 * Sideload Libsyn into WordPress
 */
class LibSynSideloadCommand extends \WP_CLI_Command {

    /**
     * Sideload embedded libsyn podcasts, and update post references.
     *
     * ## OPTIONS
     *
     * [--domain=<domain>]
     * : Only sideload podcasts hosted on a specific domain.
     *
     * [--post_type=<post-type>]
     * : Only sideload podcasts embedded in a specific post type.
     *
     * [--verbose]
     * : Show more information about the process on STDOUT.
     */
    public function podcasts( $args, $assoc_args ) {
        global $wpdb;

        $defaults = array(
            'domain'      => '',
            'post_type'   => '',
            'verbose'     => false,
            );
        $assoc_args = array_merge( $defaults, $assoc_args );

        $where_parts = array();

        //$domain_str = '%' . esc_url_raw( $assoc_args['domain'] ) . '%';
        $domain_str = '%html5-player.libsyn.com%';
        $where_parts[] = $wpdb->prepare( "post_content LIKE %s", $domain_str );

        if ( ! empty( $assoc_args['post_type'] ) ) {
            $where_parts[] = $wpdb->prepare( "post_type = %s", sanitize_key( $assoc_args['post_type'] ) );
        } else {
            $where_parts[] = "post_type NOT IN ('revision')";
        }

        if ( ! empty( $where_parts ) ) {
            $where = 'WHERE ' . implode( ' AND ', $where_parts );
        } else {
            $where = '';
        }

        $query = "SELECT ID, post_content FROM $wpdb->posts $where";

        $num_updated_posts = 0;
        foreach( new \WP_CLI\Iterators\Query( $query ) as $post ) {

            //\WP_CLI::line( sprintf( "Post #%d found.", $post->ID ) );

            $num_sideloaded_images = 0;

            if ( empty( $post->post_content ) ) {
                continue;
            }

            $document = new \DOMDocument;
            @$document->loadHTML( $post->post_content );

            $iframe_srcs = array();
            foreach( $document->getElementsByTagName( 'iframe' ) as $iframe ) {

                $iframe_src = esc_url_raw( $iframe->getAttribute( 'src' ) );
                //\WP_CLI::line( sprintf( "iframe found: %s", $iframe_src ) );

                // Get iframe source
                $tmp = download_url( $iframe_src );
                $tmpHtml = file_get_contents( $tmp );
                @unlink( $tmp );

                // Parse out feature image
                $pattern = '/<img class="info-show-icon" src="(.*?)"/';
                $m = preg_match( $pattern, $tmpHtml, $matches );

                if ($m){
                                        \WP_CLI::line( sprintf( "Post #%d image found: %s", $post->ID, $matches[0] ) );

                                    \WP_CLI::line( 'Downloading image...' );
                                    // Download mp3 to temp file
                                    $tmp = download_url( $matches[1] );
                                    \WP_CLI::line( 'Downloaded image.' );

                                    // Set variables for storage
                                    $file_array = array();
                                    $file_array['name'] = sanitize_file_name( urldecode( basename( $matches[0] ) ) ).'.jpg';
                                    $file_array['tmp_name'] = $tmp;

                    // If error storing temporarily, unlink
                                    if ( is_wp_error( $tmp ) ) {
                                            @unlink( $file_array['tmp_name'] );
                                            $file_array['tmp_name'] = '';
                                            \WP_CLI::warning( $tmp->get_error_message() );
                                            $bad_posts[] = $post->ID;
                                            continue;
                                    }

                                    \WP_CLI::line( 'Sideloading image...' );
                                    // do the validation and storage stuff
                                    $image_id = media_handle_sideload( $file_array, $post->ID );
                                    \WP_CLI::line( 'Sideloaded image...' );

                                    // If error storing permanently, unlink
                                    if ( is_wp_error( $image_id ) ) {
                                            var_dump( $image_id );
                                            @unlink( $file_array['tmp_name']);
                                            \WP_CLI::warning( $image_id->get_error_message() );
                                            $bad_posts[] = $post->ID;
                                            continue;
                                    }

                    // assign thumbnail to post
                    set_post_thumbnail( $post->ID, $image_id );

                                    @unlink( $file_array['tmp_name'] );
                }else{
                                        \WP_CLI::line( sprintf( "Post #%d image not found.", $post->ID ) );
                                }

                // Parse out mp3 url
                $pattern = '/http(.*?)mp3/i';
                $m = preg_match( $pattern, $tmpHtml, $matches );

                if ($m){
                    \WP_CLI::line( sprintf( "Post #%d MP3 found: %s", $post->ID, $matches[0] ) );
                }else{
                    \WP_CLI::line( sprintf( "Post #%d MP3 not found.", $post->ID ) );
                    $bad_posts[] = $post->ID;
                }

                \WP_CLI::line( 'Downloading MP3...' );
                // Download mp3 to temp file
                $tmp = download_url( $matches[0] );
                \WP_CLI::line( 'Downloaded MP3.' );

                // Set variables for storage
                                $file_array = array();
                                $file_array['name'] = sanitize_file_name( urldecode( basename( $matches[0] ) ) );
                                $file_array['tmp_name'] = $tmp;

                                // If error storing temporarily, unlink
                                if ( is_wp_error( $tmp ) ) {
                                        @unlink( $file_array['tmp_name'] );
                                        $file_array['tmp_name'] = '';
                                        \WP_CLI::warning( $tmp->get_error_message() );
                                        $bad_posts[] = $post->ID;
                    continue;
                                }

                \WP_CLI::line( 'Sideloading MP3...' );
                // do the validation and storage stuff
                                $id = media_handle_sideload( $file_array, $post->ID );
                \WP_CLI::line( 'Sideloaded MP3...' );

                // If error storing permanently, unlink
                                if ( is_wp_error( $id ) ) {
                    var_dump( $id );
                                        @unlink( $file_array['tmp_name']);
                                        \WP_CLI::warning( $id->get_error_message() );
                    $bad_posts[] = $post->ID;
                    continue;
                                }

                @unlink( $file_array['tmp_name'] );

                // determine mp3 attachment url
                $attachment_url = wp_get_attachment_url( $id );

                // replace iframe of post with audio shortcode
                $new_content = preg_replace( '/<iframe.*?libsyn.*?<\/iframe>/', '[audio mp3="'. $attachment_url  .'"][/audio]', $post->post_content );
                //var_dump( $new_content );
                $post->post_content = $new_content;
                $num_sideloaded_images++;

            }

            if ( $num_sideloaded_images ) {
                $num_updated_posts++;
                $wpdb->update( $wpdb->posts, array( 'post_content' => $post->post_content, 'post_parent' => 1022, 'post_type' => 'episode' ), array( 'ID' => $post->ID ) );

                clean_post_cache( $post->ID );
                \WP_CLI::line( sprintf( "Sideloaded %d media references for post #%d", $num_sideloaded_images, $post->ID ) );
            } else if ( ! $num_sideloaded_images && $assoc_args['verbose'] ) {
                \WP_CLI::line( sprintf( "No media sideloading necessary for post #%d", $post->ID ) );
            }

        }
        var_dump( $bad_posts );
        \WP_CLI::success( sprintf( "Sideload complete. Updated media references for %d posts.", $num_updated_posts ) );
    }

}

