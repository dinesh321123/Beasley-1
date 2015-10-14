<?php

namespace GreaterMedia\Gigya\Migration;

class FacebookLink {

	public $id;
	public $from;
	public $name;
	public $message;
	public $picture;
	public $description;
	public $link;
	public $created_on;
	public $icon;

	function parse( $node ) {
		$attr = $node->attributes;

		$this->parse_field( $attr, 'ID', 'id' );
		$this->parse_field( $attr, 'From', 'from' );
		$this->parse_field( $attr, 'Name', 'name' );
		$this->parse_field( $attr, 'Message', 'message' );
		$this->parse_field( $attr, 'Picture', 'picture' );
		$this->parse_field( $attr, 'Description', 'description' );
		$this->parse_field( $attr, 'Link', 'link' );
		$this->parse_field( $attr, 'Icon', 'icon' );
		$this->parse_field( $attr, 'created_time', 'created_on' );
	}

	function parse_field( $attr, $name, $field ) {
		$item = $attr->getNamedItem( $name );

		if ( ! is_null( $item ) ) {
			$value = $item->nodeValue;

			if ( ! is_null( $value ) ) {
				$this->$field = $value;
			}
		}
	}
}
