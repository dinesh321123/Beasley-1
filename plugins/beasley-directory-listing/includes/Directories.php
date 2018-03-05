<?php

namespace Beasley\DirectoryListing;

class Directories {

	const TYPE_DIRECTORIES  = 'directory';
	const TAXONOMY_CATEGORY = 'directory-category';
	const TAXONOMY_TAG      = 'directory-tag';

	public function register() {
		add_action( 'init', array( $this, 'register_cpt' ) );
		add_action( 'init', array( $this, 'register_directory_settings' ) );
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'save_post_' . self::TYPE_DIRECTORIES, array( $this, 'on_directory_save' ) );
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
		register_taxonomy( self::TAXONOMY_CATEGORY, array(), array(
			'public'            => true,
			'show_tagcloud'     => false,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'rewrite'           => false,
		) );

		register_taxonomy( self::TAXONOMY_TAG, array(), array(
			'public'            => true,
			'show_tagcloud'     => false,
			'show_admin_column' => true,
			'rewrite'           => false,
		) );

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
			foreach ( (array) $directories as $directory ) {
				$directory = get_post( $directory );
				if ( is_a( $directory, '\WP_Post' ) ) {
					register_post_type( 'directory-' . $directory->ID, array(
						'public'        => true,
						'menu_position' => 52,
						'menu_icon'     => 'dashicons-exerpt-view',
						'has_archive'   => true,
						'rewrite'       => false,
						'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
						'taxonomies'    => array( self::TAXONOMY_CATEGORY, self::TAXONOMY_TAG ),
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
				}
			}
		}
	}

	public function register_directory_settings() {
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

	public function add_admin_menu_separator( $position ) {
		global $menu;

		$index = 0;
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

	public function register_menu() {
		$this->add_admin_menu_separator( 51 );

//		$title = 'Directory Listing';
//		$callback = array( $this, 'render_settings_page' );
//		add_options_page( $title, $title, 'manage_options', 'directory-listing', $callback );
	}

	public function on_directory_save() {
		wp_cache_delete( 'directories', 'beasley-directory-listing' );
	}

}
