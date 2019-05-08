<?php

class PrimaryNavWalker extends \Walker_Nav_Menu {

	public function walk( $elements, $max_depth, ...$args ) {
		$home = trailingslashit( home_url() );

		$newhome = new \WP_Post( new \stdClass );
		$discovery = new \WP_Post( new \stdClass );

		$newhome->post_title = $newhome->title = 'Home';
		$newhome->url = $home;
		$newhome->post_type = $discovery->post_type = 'nav_menu_item';
		$newhome->object = $newhome->type = $discovery->object = $discovery->type = 'custom';
		$newhome->current = $discovery->current = false;
		$newhome->classes = $discovery->classes = array (
			'menu-item',
			'menu-item-type-custom',
			'menu-item-object-custom',
			'menu-item-home',
		);

		if ( is_home() || is_front_page() ) {
			$newhome->current = true;
			$newhome->classes[] = 'current-menu-item';
			$newhome->classes[] = 'current_page_item';
		}

		$discovery->post_title = $discovery->title = 'Discovery';
		$discovery->url = '#';
		$discovery->classes[] = 'menu-item-discovery';

		$newelements = array( $newhome, $discovery );
		$newelements = array_merge( $newelements, $elements );

		return parent::walk( $newelements, $max_depth, ...$args );
	}

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$element = '';
		parent::start_lvl( $element, $depth, $args );

		$element = str_replace( '<ul ', '<ul aria-hidden="true" aria-label="Submenu" ', $element );
		$output .= $element;
	}

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		if ( $item->url == '#' || empty( $item->url ) ) {
			$item->url = '';
			$item->classes[] = 'menu-item-toggle';
		}

		$element = '';
		parent::start_el( $element, $item, $depth, $args, $id );

		if ( in_array( 'menu-item-has-children', $item->classes ) ) {
			$element .= '<button class="sub-menu-activator" aria-haspopup="true" aria-label="Open Submenu"></button>';
		}

		if ( in_array( 'menu-item-toggle', $item->classes ) ) {
			$element = str_replace( '<a>', '<button aria-haspopup="true">', $element );
			$element = str_replace( '<a ', '<button aria-haspopup="true" ', $element );
			$element = str_replace( '</a>', '</button>', $element );
		}

		$output .= $element;
	}

}
