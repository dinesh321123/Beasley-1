<?php

namespace GreaterMedia\Gigya\Migration;

class MemberGroup {

	static public $id_counter = 0;
	static public function get_next_id() {
		return self::$id_counter++;
	}

	public $id;
	public $name;
	public $description;
	public $is_active;
	public $is_default;

	function parse( $node ) {
		$this->id          = MemberGroup::get_next_id();
		$attr              = $node->attributes;
		$this->name        = $attr->getNamedItem( 'MemberGroupName' )->nodeValue;
		$this->description = $attr->getNamedItem( 'Description' )->nodeValue;
		$this->is_active   = filter_var( $attr->getNamedItem( 'IsActive' )->nodeValue, FILTER_VALIDATE_BOOLEAN );
		$this->is_default  = filter_var( $attr->getNamedItem( 'IsDefault' )->nodeValue, FILTER_VALIDATE_BOOLEAN );

		return $this->id;
	}

}
