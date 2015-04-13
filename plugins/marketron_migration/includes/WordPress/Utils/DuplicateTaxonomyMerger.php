<?php

namespace WordPress\Utils;

class DuplicateTaxonomyMerger {

	function merge( $taxonomy ) {
		$terms          = $this->get_terms_for_taxonomy( $taxonomy );
		$terms_to_merge = $this->get_terms_to_merge( $terms );
		$merge_count    = 0;

		if ( count( $terms_to_merge ) > 0 ) {
			foreach ( $terms_to_merge as $slug => $term_ids ) {
				$this->merge_terms( $term_ids, $taxonomy );
				$merge_count += count( $term_ids );
			}
		}

		return $merge_count;
	}

	function get_terms_for_taxonomy( $taxonomy ) {
		return \get_terms(
			array( $taxonomy ),
			array( 'fields' => 'all', 'hide_empty' => false )
		);
	}

	function get_terms_to_merge( $terms ) {
		$slugs_found    = array();
		$terms_to_merge = array();

		foreach ( $terms as $term ) {
			$slug       = $term->slug;
			$term_id    = $term->term_id;
			$term_count = $term->count;

			if ( ! array_key_exists( $slug, $slugs_found ) ) {
				// first time slug was found
				$slugs_found[ $slug ] = array( 'term_id' => $term_id, 'count' => $term_count );
			} else if ( ! array_key_exists( $slug, $terms_to_merge ) ) {
				// duplicate was found
				if ( ! array_key_exists( $slug, $terms_to_merge ) ) {
					$terms_to_merge[ $slug ] = array( $slugs_found[ $slug ]['term_id'] );
				}

				if ( $term_count > $slugs_found[ $slug ]['count'] ) {
					$slugs_found[ $slug ]['term_id'] = $term_id;
					$slugs_found[ $slug ]['count']   = $term_count;

					array_unshift( $terms_to_merge[ $slug ], $term_id );
				} else {
					array_push( $terms_to_merge[ $slug ], $term_id );
				}
			}
		}

		return $terms_to_merge;
	}

	function merge_terms( $term_ids, $taxonomy ) {
		$new_term_id = array_shift( $term_ids );
		$args = array(
			'default'       => $new_term_id,
			'force_default' => true,
		);

		foreach ( $term_ids as $term_id ) {
			wp_delete_term( $term_id, $taxonomy, $args );
		}

		return $new_term_id;
	}

}
