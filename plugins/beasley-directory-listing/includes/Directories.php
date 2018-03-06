<?php

namespace Beasley\DirectoryListing;

class Directories {

	use \Beasley\DirectoryListing\FeaturedImage;

	const TYPE_DIRECTORIES  = 'directory';
	const TAG_LISTING_SLUG    = '%listing-slug%';
	const TAG_LISTING_CAT     = '%listing-cat%';
	const TAG_LISTING_ARCHIVE = '%listing-archive%';

	public function register() {
		add_action( 'init', array( $this, 'register_cpt' ) );
		add_action( 'init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'save_post_' . self::TYPE_DIRECTORIES, array( $this, 'on_directory_save' ) );

		add_filter( 'post_type_link', array( $this, 'update_post_link' ), 10, 2 );
		add_filter( 'term_link', array( $this, 'update_term_link' ), 10, 3 );
		add_filter( 'archive_template_hierarchy', array( $this, 'update_archive_template' ) );
		add_filter( 'body_class', array( $this, 'update_body_classes' ) );
	}

	protected function _get_directories() {
		$key = 'directories';
		$group = 'beasley-directory-listing';
		$directories = wp_cache_get( $key, $group );
		if ( $directories === false ) {
			$directories = get_posts( array(
				'post_type'   => self::TYPE_DIRECTORIES,
				'numberposts' => 1000, // should be enough
				'fields'      => 'ids',
			) );

			wp_cache_set( $key, $directories, $group );
		}

		return $directories;
	}

	public function register_cpt() {
		global $wp;

		register_post_type( self::TYPE_DIRECTORIES, array(
			'public'        => false,
			'show_ui'       => true,
			'menu_position' => 52,
			'menu_icon'     => 'dashicons-images-alt',
			'has_archive'   => true,
			'rewrite'       => false,
			'supports'      => array( 'title' ),
			'label'         => 'Directories',
			'labels'        => array(
				'name'                  => 'Directories',
				'singular_name'         => 'Directory',
				'add_new_item'          => 'Add New Directory',
				'edit_item'             => 'Edit Directory',
				'new_item'              => 'New Directory',
				'view_item'             => 'View Directory',
				'view_items'            => 'View Directories',
				'search_items'          => 'Search Directories',
				'not_found'             => 'No directories found',
				'not_found_in_trash'    => 'No directories found in Trash',
				'all_items'             => 'All Directories',
				'archives'              => 'Directory Archives',
				'attributes'            => 'Directory Attributes',
				'insert_into_item'      => 'Insert into directory',
				'uploaded_to_this_item' => 'Upload to this directory',
			),
		) );

		$directories = $this->_get_directories();
		if ( ! empty( $directories ) ) {
			$wp->add_query_var( 'directory_id' );

			foreach ( (array) $directories as $directory ) {
				$directory = get_post( $directory );
				if ( is_a( $directory, '\WP_Post' ) ) {
					$post_type = 'directory-' . $directory->ID;
					$cateogry_taxonomy = 'directory-cat-' . $directory->ID;

					register_post_type( $post_type, array(
						'public'        => true,
						'menu_position' => 52,
						'menu_icon'     => 'dashicons-exerpt-view',
						'has_archive'   => true,
						'rewrite'       => false,
						'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
						'label'         => $directory->post_title,
						'labels'        => array(
							'name'                  => $directory->post_title,
							'singular_name'         => $directory->post_title,
							'add_new_item'          => 'Add New Item',
							'edit_item'             => 'Edit Item',
							'new_item'              => 'New Item',
							'view_item'             => 'View Item',
							'view_items'            => 'View Item',
							'search_items'          => 'Search Item',
							'not_found'             => 'No items found',
							'not_found_in_trash'    => 'No items found in Trash',
							'all_items'             => 'All Items',
							'archives'              => 'Archives',
							'attributes'            => 'Attributes',
							'insert_into_item'      => 'Insert into post',
							'uploaded_to_this_item' => 'Upload to this post',
						),
					) );

					add_permastruct( $post_type, sprintf( '/%s/%s/%s/', self::TAG_LISTING_ARCHIVE, self::TAG_LISTING_CAT, self::TAG_LISTING_SLUG ), array(
						'with_front'  => false,
						'paged'       => false,
						'feed'        => false,
						'forcomments' => false,
						'endpoints'   => false,
					) );

					$archive = get_field( 'archive_slug', $directory->ID );
					add_rewrite_rule( "{$archive}/?$",                 "index.php?directory_id={$directory->ID}&post_type={$post_type}",                                                     'top' );
					add_rewrite_rule( "{$archive}/([^/]+)/?$",         "index.php?directory_id={$directory->ID}&post_type={$post_type}&{$cateogry_taxonomy}=\$matches[1]",                   'top' );
					add_rewrite_rule( "{$archive}/([^/]+)/([^/]+)/?$", "index.php?directory_id={$directory->ID}&post_type={$post_type}&{$cateogry_taxonomy}=\$matches[1]&name=\$matches[2]", 'top' );

					register_taxonomy( $cateogry_taxonomy, array( $post_type ), array(
						'public'            => true,
						'show_tagcloud'     => false,
						'show_admin_column' => true,
						'hierarchical'      => true,
						'rewrite'           => false,
					) );

					register_taxonomy( 'directory-tag-' . $directory->ID, array( $post_type ), array(
						'public'            => true,
						'show_tagcloud'     => false,
						'show_admin_column' => true,
						'rewrite'           => false,
					) );

					$this->register_featured_image( $cateogry_taxonomy );
				}
			}
		}
	}

	public function register_settings() {
		if ( function_exists( 'acf_add_local_field_group' ) ) {
			$location = array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => self::TYPE_DIRECTORIES,
					),
				),
			);

			acf_add_local_field_group( array(
				'key'                   => 'group_5a7b2b84a6adb',
				'title'                 => 'Settings',
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'active'                => 1,
				'location'              => $location,
				'fields'                => array(
					array(
						'key'          => 'field_5a9d41267af95',
						'label'        => 'Archive Slug',
						'name'         => 'archive_slug',
						'type'         => 'text',
						'instructions' => 'Enter directory archive slug which will be used as the root folder for all items in the directory.',
						'required'     => 1,
					),
					array(
						'key'           => 'field_5a9d43377af96',
						'label'         => 'Hero Image',
						'name'          => 'hero_image',
						'type'          => 'image',
						'instructions'  => 'Select a hero image for the directory homepage.',
						'required'      => 1,
						'return_format' => 'id',
						'preview_size'  => 'thumbnail',
						'library'       => 'all',
					),
					array(
						'key'          => 'field_5a9d43977af97',
						'label'        => 'Description',
						'name'         => 'description',
						'type'         => 'wysiwyg',
						'instructions' => 'Directory welcome text which will be visible only on the directory archive page.',
						'toolbar'      => 'full',
						'media_upload' => 1,
					),
				),
			) );
		}
	}

	public function register_menu() {
		global $menu;

		$index = 0;
		$position = 51;

		foreach ( $menu as $offset => $section ) {
			if ( substr( $section[ 2 ], 0, 9 ) == 'separator' ) {
				$index++;
			}

			if ( $offset >= $position ) {
				$menu[ $position ] = array( '', 'read', "separator{$index}", '', 'wp-menu-separator' );
				break;
			}
		}

		ksort( $menu );
	}

	public function on_directory_save() {
		wp_cache_delete( 'directories', 'beasley-directory-listing' );
	}

	public function update_post_link( $link, $post ) {
		$prefix = 'directory-';
		if ( substr( $post->post_type, 0, strlen( $prefix ) ) != $prefix ) {
			return $link;
		}

		$directory = get_post( intval( substr( $post->post_type, strlen( $prefix ) ) ) );
		if ( ! is_a( $directory, '\WP_Post' ) ) {
			return $link;
		}

		if ( stripos( $link, self::TAG_LISTING_ARCHIVE ) !== false ) {
			$archive = get_field( 'archive_slug', $directory->ID );
			if ( ! empty( $archive ) ) {
				$link = str_replace( self::TAG_LISTING_ARCHIVE, $archive, $link );
			}
		}

		if ( stripos( $link, self::TAG_LISTING_SLUG ) !== false ) {
			$link = str_replace( self::TAG_LISTING_SLUG, $post->post_name, $link );
		}

		if ( stripos( $link, self::TAG_LISTING_CAT ) !== false ) {
			$category_slug = '';

			$cats = wp_get_post_terms( $post->ID, 'directory-cat-' . $directory->ID );
			if ( is_array( $cats ) && ! empty( $cats ) ) {
				$category = current( $cats );
				if ( is_a( $category, '\WP_Term' ) ) {
					$category_slug = $category->slug;
				}
			}

			$link = str_replace( self::TAG_LISTING_CAT, $category_slug, $link );
		}

		return $link;
	}

	public function update_term_link( $link, $term, $taxonomy ) {
		$prefix = 'directory-cat-';
		if ( substr( $taxonomy, 0, strlen( $prefix ) ) == $prefix ) {
			$directory_id = substr( $taxonomy, strlen( $prefix ) );
			if ( $directory_id > 0 ) {
				$archive = get_field( 'archive_slug', $directory_id );
				if ( ! empty( $archive ) ) {
					return sprintf( '/%s/%s/', urlencode( $archive ), urlencode( $term->slug ) );
				}
			}
		}

		return $link;
	}

	public function update_archive_template( $templates ) {
		$directory_id = get_query_var( 'directory_id' );
		if ( $directory_id > 0 ) {
			$category = get_query_var( 'directory-cat-' . $directory_id );

			$_templates = array();
			foreach ( $templates as $template ) {
				if ( $template == 'archive.php' ) {
					$_templates[] = ! empty( $category )
						? 'taxonomy-listing-category.php'
						: 'archive-listing.php';
				}

				$_templates[] = $template;
			}

			$templates = $_templates;
		}

		return $templates;
	}

	public function update_body_classes( $classes ) {
		$directory_id = get_query_var( 'directory_id' );
		if ( ! empty( $directory_id ) && $directory_id > 0 ) {
			$category = get_query_var( 'directory-cat-' . $directory_id );
			$classes[] = empty( $category )
				? 'post-type-archive-listing'
				: 'tax-listing-category';
		}

		return $classes;
	}

}
