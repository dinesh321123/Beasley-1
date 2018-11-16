<?php

class PrimaryNavWalker extends \Walker_Nav_Menu {

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
			$element .= '<button class="sub-menu-activator" aria-haspopup="true"></button>';
		}

		if ( in_array( 'menu-item-toggle', $item->classes ) ) {
			$element = str_replace( '<a>', '<button aria-haspopup="true">', $element );
			$element = str_replace( '<a ', '<button aria-haspopup="true" ', $element );
			$element = str_replace( '</a>', '</button>', $element );
		}

		$output .= $element;
	}

}
