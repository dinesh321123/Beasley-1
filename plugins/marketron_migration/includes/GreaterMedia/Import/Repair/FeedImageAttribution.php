<?php

namespace GreaterMedia\Import\Repair;

class FeedImageAttribution {

	public $container;

	function repair() {
		$this->feed_tool = $this->container->tool_factory->build( 'feed' );
		$this->feed_tool->load( false );

		$this->feed_source = $this->feed_tool->sources[0];
		$this->feed_importer = $this->container->importer_factory->build( 'feed' );
		$articles = $this->feed_importer->articles_from_source( $this->feed_source );

		\WP_CLI::log( 'Total Feed Articles: ' . count( $articles ) );

		$this->repair_articles( $articles );
	}

	function repair_articles( $articles ) {
		$repair_count = 0;
		$this->article_skipped_count = 0;
		$this->article_count = 0;
		$this->featured_image_count = 0;
		$this->attribution_update_count = 0;
		$this->caption_update_count = 0;

		foreach ( $articles as $article ) {
			$this->article_count++;

			if ( ! $this->container->mappings->can_import_marketron_name(
				(string) $article->Feeds->Feed['Feed'], 'feed' ) ) {
				//\WP_CLI::log( '  Excluded Feed: ' . (string) $article->Feeds->Feed['Feed'] );
				$this->article_skipped_count++;
				continue;
			}

			if ( $this->can_repair_article( $article ) ) {
				$this->repair_article( $article );
				$repair_count++;
			}
		}

		\WP_CLI::success( 'Article Count: ' . $this->article_count );
		\WP_CLI::success( 'Article Skip Count: ' . $this->article_skipped_count );
		\WP_CLI::success( 'Featured Image Count: ' . $repair_count );
		\WP_CLI::success( 'Repair Count: ' . $repair_count );
		\WP_CLI::success( 'Caption Update Count: ' . $this->caption_update_count );
		\WP_CLI::success( 'Attribution Update Count: ' . $this->attribution_update_count );
	}

	function repair_article( $article ) {
		$featured_image = $this->featured_image_for_article( $article );
		$caption        = $this->caption_for_article_image( $article );
		$attribution    = $this->attribution_for_article_image( $article );
		$post           = $this->post_for_article( $article );
		$post_image_id  = $this->featured_image_for_post( $post );

		if ( empty( $post_image_id ) ) {
			\WP_CLI::warning( 'Featured Image Not Found For: ' . $post->ID );
			return;
		}

		//\WP_CLI::log( $featured_image );
		//\WP_CLI::log( '  Caption: ' . $caption );
		//\WP_CLI::log( '  Attribution: ' . $attribution );
		//\WP_CLI::log( '  Post: ' . $post->ID );
		//\WP_CLI::log( '  Featured Image: ' . $post_image_id );

		if ( ! empty( $caption ) ) {
			$this->update_image_caption( $post_image_id, $caption );
			$this->caption_update_count++;
		}

		if ( ! empty( $attribution ) ) {
			$this->update_image_attribution( $post_image_id, $attribution );
			$this->attribution_update_count++;
		}
	}

	function update_image_caption( $post_id, $caption ) {
		return wp_update_post(
			array(
				'ID' => $post_id,
				'post_content' => $caption,
			)
		);
	}

	function update_image_attribution( $post_id, $attribution ) {
		$attribution = ltrim( $attribution, '(' );
		$attribution = rtrim( $attribution, ')' );

		update_post_meta( $post_id, 'gmr_image_attribution', $attribution );
	}

	function post_for_article( $article ) {
		$title = $this->feed_importer->title_from_article( $article );
		$query_params = array(
			'post_type'      => 'post',
			'name'           => sanitize_title_with_dashes( $title ),
			'posts_per_page' => 1,
		);

		$query   = new \WP_Query( $query_params );
		$results = $query->get_posts();

		if ( count( $results ) === 1 ) {
			return $results[0];
		} else {
			return false;
		}
	}

	function featured_image_for_post( $post ) {
		return get_post_thumbnail_id( $post->ID );
	}

	function can_repair_article( $article ) {
		$featured_image = $this->featured_image_for_article( $article );

		if ( ! empty( $featured_image ) ) {
			$this->featured_image_count++;

			$caption     = $this->caption_for_article_image( $article );
			$attribution = $this->attribution_for_article_image( $article );

			if ( ! empty( $caption ) || ! empty( $attribution ) ) {
				return true;
			}
		}

		return false;
	}

	function caption_for_article_image( $article ) {
		$caption = $this->feed_importer->import_string( $article['FeaturedImageCaption'] );

		return $caption;
	}

	function attribution_for_article_image( $article ) {
		$attribution = $this->feed_importer->import_string( $article['FeaturedImageAttribute'] );
		return $attribution;
	}

	function featured_image_for_article( $article ) {
		return $this->feed_importer->featured_image_from_article( $article );
	}

}
