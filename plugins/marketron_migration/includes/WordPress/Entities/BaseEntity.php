<?php

namespace WordPress\Entities;

class BaseEntity {

	public $container;

	function get_entity( $name ) {
		// KLUDGE: Does not handle circular entities
		// If an entity knew it's own name this could return $this
		return $this->container->entity_factory->build( $name );
	}

	function get_table( $name ) {
		return $this->container->table_factory->build( $name );
	}

	function get_config_option( $type, $name ) {
		return $this->container->config->get_config_option( $type, $name );
	}

	function get_site_option( $name ) {
		return $this->container->config->get_site_option( $name );
	}

	function get_error_reporter() {
		return $this->container->error_reporter;
	}

	function log_not_found( $path, $parent = null ) {
		return $this->get_error_reporter()->log_not_found( $path, $parent );
	}

	function get_side_loader() {
		return $this->container->side_loader;
	}

	function get_asset_locator() {
		return $this->container->asset_locator;
	}

	function to_time_variants( $timestamp ) {
		$datetime = $this->to_datetime( $timestamp );

		return array(
			'date'     => $datetime->format( 'Y-m-d H:i:s' ),
			'date_gmt' => $datetime->format( 'Y-m-d H:i:s' )
		);
	}

	function to_datetime( $timestamp ) {
		if ( $timestamp instanceof \DateTime ) {
			$date = $timestamp;
		} else if ( is_int( $timestamp ) ) {
			$date = new \DateTime();
			$date->setTimestamp( $timestamp );
		} else {
			$date = new \DateTime( $timestamp );
		}

		//$date->setTimeZone( new \DateTimeZone( 'GMT' ) );

		return $date;
	}

	function locate_asset( $filepath ) {
		return $this->get_asset_locator()->find( $filepath );
	}

	function sideload( $filepath, $timestamp = null ) {
		$located_path = $this->locate_asset( $filepath );

		if ( $located_path !== false ) {
			return $this->get_side_loader()->sideload( $located_path, $timestamp );
		} else {
			$this->log_not_found( $filepath, __CLASS__ );
			return false;
		}
	}

	function can_destroy() {
		return true;
	}

	function destroy() {
		$this->container = null;
		unset( $this->container );
	}

}
