<?php

namespace WordPress\Tables;

class UserMeta extends BaseTable {

	public $columns = array(
		'umeta_id',
		'user_id',
		'meta_key',
		'meta_value',
	);

	public $indices = array(
		'umeta_id',
	);

	public $columns_with_defaults = array(
		'user_id',
	);

	public $primary_key = 'umeta_id';

	function get_table_name() {
		return 'usermeta';
	}

	function is_multisite_table() {
		return false;
	}

}
