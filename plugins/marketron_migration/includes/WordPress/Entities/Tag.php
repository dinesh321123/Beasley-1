<?php

namespace WordPress\Entities;

class Tag extends Taxonomy {

	function get_taxonomy() {
		return 'post_tag';
	}

}
