<?php

namespace WordPress\Entities;

class Category extends Taxonomy {

	function get_taxonomy() {
		return 'category';
	}

}
