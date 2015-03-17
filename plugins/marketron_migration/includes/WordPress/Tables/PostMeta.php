<?php

namespace WordPress\Tables;

class PostMeta extends BaseTable {

	public $primary_key = 'meta_id';
	public $columns = array(
		'meta_id',
		'post_id',
		'meta_key',
		'meta_value',
	);

	public $indices = array(
		'post_id',
	);

	function get_table_name() {
		return 'postmeta';
	}

}
