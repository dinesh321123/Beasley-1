<?php

namespace GreaterMedia\Import;

class LiveStream extends BaseImporter {

	function import() {
		$live_streams = $this->get_config_option( 'live_player', 'streams' );
		$entity       = $this->get_entity( 'live_stream' );
		$total        = count( $live_streams );

		foreach ( $live_streams as $live_stream ) {
			$entity->add( $live_stream );
		}

		\WP_CLI::success( "Imported $total Live Stream(s)" );
	}

}
