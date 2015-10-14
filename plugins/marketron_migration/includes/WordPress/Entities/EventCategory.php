<?php

namespace WordPress\Entities;

class EventCategory extends Taxonomy {

	function get_taxonomy() {
		return 'tribe_events_cat';
	}

}
