<?php

namespace GreaterMedia\Import\Repair;

class FeedCategoryRepairer {

	public $category_id_cache = array();

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
		$total        = count( $articles );
		$msg          = "Repairing Categories in $total articles";
		$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );
		$this->repair_count = 0;

		foreach ( $articles as $article ) {
			if ( ! $this->container->mappings->can_import_marketron_name(
				(string) $article->Feeds->Feed['Feed'], 'feed' ) ) {
				continue;
			}

			$post = $this->feed_importer->post_from_article( $article );

			if ( ! $this->feed_importer->can_import_by_time( $post ) ) {
				continue;
			}

			$wordpress_post = $this->get_wordpress_post_for_article_post( $post );
			if ( $wordpress_post !== false ) {
				$this->repair_article_categories( $article, $post, $wordpress_post );
				$this->repair_count++;
			}

			$progress_bar->tick();
		}

		$progress_bar->finish();

		\WP_CLI::success( "Repaired Categories in " . $this->repair_count . ' Feed Posts' );
	}

	function repair_article_categories( $article, $post, $wordpress_post ) {
		$post_id = $wordpress_post->ID;
		$to_remove = $this->get_category_ids_to_remove( $article );
		$to_add    = $this->get_category_ids_to_add( $article );

		//\WP_CLI::log( "To Remove: " . implode( ',', $to_remove ) );
		//\WP_CLI::log( 'To Add: ' . implode( ',', $to_add ) );

		if ( ! empty( $to_remove ) ) {
			wp_remove_object_terms( $post_id, $to_remove, 'category' );
		}

		if ( ! empty( $to_add ) ) {
			wp_set_post_categories( $post_id, $to_add, true );
		}
	}

	function get_wordpress_post_for_article_post( $post ) {
		$post_date = date_parse( $post['created_on'] );
		$query_params = array(
			'post_type' => 'post',
			'name' => $post['post_title'],
			'post_status' => 'publish',
			'posts_per_page' => 1,
			'date_query' => array(
				'year' => $post_date['year'],
				'month' => $post_date['month'],
				'day' => $post_date['day'],
			)
		);

		$query = new \WP_Query( $query_params );
		$posts = $query->get_posts();

		if ( count( $posts ) === 1 ) {
			return $posts[0];
		} else {
			return false;
		}
	}

	function get_categories_to_remove( $article ) {
		$names = array();

		if ( ! empty( $article->Feeds->Feed[0]->FeedCategories ) ) {
			$feed_categories = $article->Feeds->Feed[0]->FeedCategories->FeedCategory;

			foreach ( $feed_categories as $feed_category ) {
				$names[] = $feed_category['Category'];
			}
		}

		return $names;
	}

	function get_category_ids_to_remove( $article ) {
		$names = $this->get_categories_to_remove( $article );
		$hash_key = md5( json_encode( $names ) );

		if ( array_key_exists( $hash_key, $this->category_id_cache ) ) {
			return $this->category_id_cache[ $hash_key ];
		}

		$term_ids = array();

		foreach ( $names as $name ) {
			$term = get_term_by( 'name', $name, 'category', ARRAY_A );

			if ( ! empty( $term['term_id'] ) ) {
				$term_ids[] = intval( $term['term_id'] );
			}
		}

		$this->category_id_cache[ $hash_key ] = $term_ids;

		return $term_ids;
	}

	function get_categories_to_add( $article ) {
		$names = array();

		if ( ! empty( $article->ArticleCategories ) ) {
			$article_categories = $article->ArticleCategories->ArticleCategory;

			foreach ( $article_categories as $article_category ) {
				$names[] = $article_category['CategoryName'];
			}
		}

		return $names;
	}

	function get_category_ids_to_add( $article ) {
		$names = $this->get_categories_to_add( $article );
		$hash_key = md5( json_encode( $names ) );
		$term_ids = array();

		if ( array_key_exists( $hash_key, $this->category_id_cache ) ) {
			return $this->category_id_cache[ $hash_key ];
		}

		foreach ( $names as $name ) {
			$term = get_term_by( 'name', $name, 'category', ARRAY_A );

			if ( $term === false ) {
				$term = wp_insert_term(
					$name, 'category'
				);
			}

			if ( is_wp_error( $term ) ) {
				\WP_CLI::error( $term->get_error_message() . ' - ' . $name );
			}

			$term_ids[] = intval( $term['term_id'] );
		}

		$this->category_id_cache[ $hash_key ] = $term_ids;

		return $term_ids;
	}

}
