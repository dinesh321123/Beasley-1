<?php

namespace GreaterMedia\Import\Repair;

class VideoRefresher {

	function refresh() {
		$post_ids     = $this->get_post_ids();
		$total        = count( $post_ids );
		$msg          = "Refreshing $total Video Posts";
		$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );

		foreach ( $this->get_post_ids() as $item ) {
			$post_id = $item->ID;
			$this->refresh_post( $post_id );
			$progress_bar->tick();
		}

		$progress_bar->finish();
	}

	function refresh_post( $post_id ) {
		$post    = get_post( $post_id );
		$content = $post->post_content;
		$content = $this->repair_youtube_embed( $content );

		$params = array(
			'ID'           => $post_id,
			'post_content' => $content,
		);

		//error_log( $content );
		wp_update_post( $params );
	}

	function repair_youtube_embed( $content ) {
		# https://www.youtube.com/watch?v=2ZHyePh2tEU
		$pattern = '#\s*(https://www\.youtube\.com/watch\?v=[a-zA-Z0-9_-]+)\s*#';
		$content = preg_replace( $pattern, '[embed]${1}[/embed]<br/>', $content );

		return $content;
	}

	function get_post_ids() {
		$args = array(
			'nopaging' => true,
			'fields' => array( 'ID' ),
			'tax_query' => array(
				array(
					'taxonomy' => 'post_format',
					'field' => 'slug',
					'terms' => array( 'post-format-video' )
				)
			)
		);

		$query   = new \WP_Query( $args );
		$results = $query->get_posts();

		return $results;
	}

}
