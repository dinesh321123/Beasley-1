<?php

namespace GreaterMedia\Gigya\Migration;

class FacebookLike {

	public $id;
	public $category;
	public $name;

	function parse( $node ) {
		$attr           = $node->attributes;
		$this->id       = $attr->getNamedItem( 'ID' )->nodeValue;
		$this->name     = $attr->getNamedItem( 'Name' )->nodeValue;
		$this->category = $attr->getNamedItem( 'Category' )->nodeValue;
	}

}
