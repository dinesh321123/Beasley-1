<?php

namespace GreaterMedia\Gigya\Sync;

class TaskFactory {

	public $task_types = array(
		'profile'         => 'GreaterMedia\Gigya\Sync\ProfileQueryTask',
		'data_store'      => 'GreaterMedia\Gigya\Sync\DataStoreQueryTask',
		'compile_results' => 'GreaterMedia\Gigya\Sync\CompileResultsTask',
		'preview_results' => 'GreaterMedia\Gigya\Sync\PreviewResultsTask',
		'export_results'  => 'GreaterMedia\Gigya\Sync\ExportResultsTask',
		'cleanup'         => 'GreaterMedia\Gigya\Sync\CleanupTask',
	);

	function build( $task_type ) {
		$task_class = $this->get_task( $task_type );
		return new $task_class();
	}

	function get_task( $task_type ) {
		if ( array_key_exists( $task_type, $this->task_types ) ) {
			return $this->task_types[ $task_type ];
		} else {
			throw new \Exception(
				"TaskFactory: Unknown task_type - {$task_type}"
			);
		}
	}

	function set_task( $task_type, $task_class ) {
		$this->task_types[ $task_type ] = $task_class;
	}

}
