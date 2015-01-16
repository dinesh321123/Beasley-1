/*! GreaterMedia Keywords - v0.0.1
 * http://wordpress.org/plugins
 * Copyright (c) 2015; * Licensed GPLv2+ */
// Silence jslint warning about _ being undefined.
/*global _ */

jQuery( function ( $ ) {
	'use strict';

	function Keyword_Search( keywords ) {
		this.keywords = keywords;
		this.last_search = '';
	}

	Keyword_Search.prototype.search = function ( search_term ) {
		this.last_search = search_term;

		search_term = this.normalize_search_term( search_term );

		if ( !search_term.length ) {
			return [];
		}

		var me = this; // This feels clunky, but it's what MDN recommends...
		var matches = _.filter( this.keywords, function ( item ) {
			return 0 === me.normalize_search_term( item.keyword ).indexOf( me.normalize_search_term( search_term ) );
		} );

		return matches;
	};

	Keyword_Search.prototype.normalize_search_term = function ( search_term ) {
		return search_term.trim().toLowerCase().replace( /[^a-z0-9]+/g, '' );
	};

	function Arrow_Key_Navigator( $items, $context, position_change_callback, cancel_callback ) {
		var self = this;

		self.$items = $items;
		self.$context = $context;
		self.position_change_callback = position_change_callback;
		self.cancel_callback = cancel_callback;

		self.is_navigating = false;
		self.position = null;
		self.stack_size = $items.children().length;

		function _highlight_item() {
			$items.find( '.is-highlighted' ).removeClass( 'is-highlighted' );
			$items.children().eq( self.position ).addClass( 'is-highlighted' );
		}

		function up() {
			if ( null === self.position || $items.children().length !== self.stack_size ) {
				self.position = $items.children().length + 1;
				self.stack_size =  $items.children().length;
			}

			if ( 0 === self.position ) {
				self.position = $items.children().length - 1;
			} else {
				self.position--;
			}

			_highlight_item();
		}

		function down() {
			if ( null === self.position || $items.children().length !== self.stack_size ) {
				self.position = -1;
				self.stack_size =  $items.children().length;
			}

			if ( self.position === $items.children().length - 1 ) {
				self.position = 0;
			} else {
				self.position++;
			}

			_highlight_item();
		}

		$context.keydown( function( e ) {
			if ( 38 === e.which || 40 === e.which ) { // Up and down
				self.is_navigating = true;

				self.$items.addClass( 'is-navigating' )
					.removeClass( 'is-hoverable' );

				e.preventDefault();
				e.stopPropagation();

				if ( 38 === e.which ) {
					up();
				} else {
					down();
				}

				if ( self.position_change_callback ) {
					self.position_change_callback( self.position );
				}
			} else if ( 27 === e.which ) { // Escape key
				self.$items.removeClass( 'is-navigating' )
					.addClass( 'is-hoverable' );
				self.cancel();
				e.preventDefault();
				e.stopPropagation();
			} else {
				self.$items.removeClass( 'is-navigating' )
					.addClass( 'is-hoverable' );
				self.is_navigating = false;
			}
		} );

		$items.find( 'is-highlighted' ).removeClass( 'is-highlighted' );
		$items.addClass( 'is-hoverable' );
	}

	Arrow_Key_Navigator.prototype.cancel = function () {
		this.is_navigating = false;
		this.position = null;

		if ( this.cancel_callback ) {
			this.cancel_callback();
		}
	};

	function handle_search_field_change() {
		var items = search.search( $search_field.val() );

		// Bail if we don't have any items, and make sure the container is hidden.
		if ( ! items || ! items.length ) {
			$container.hide();
			return;
		}

		// Trim results
		items = items.slice( 0, max_results );

		// Update header.
		$header.html( items.length > 1 ? items.length + ' keyword matches found' : '1 keyword match found' );

		// Update the item list.
		$item_list.empty();
		_.each( items, function ( item ) {
			var $item = $( item_template( item ) );
			$item.data( 'keyword', item.keyword );
			$item_list.append( $item );
		} );

		// Display the container.
		$container.show();
	}

	// Wire things together.
	var max_results = 6;
	var search = new Keyword_Search( GMRKeywords );
	var $search_field = $( '#header-search' );

	var item_template = _.template( $( '#keyword-search-item-template' ).html() );
	var body_template = _.template( $( '#keyword-search-body-template' ).html() );

	// Add the body to the page, making sure the container is hidden.
	var $container = $( '#keyword-search-container' );
	$container.hide();
	$container.append( $( body_template() ) );

	// Reference header and item list for later.
	var $header = $container.find( '.keyword-search__header' );
	var $item_list = $container.find( '.keyword-search__items' );

	// Hook up the button.
	$container.find( '.keyword-search__btn' ).click( function() {
		$( '#header-search' ).parent( 'form' ).submit();
	} );

	// Hook up arrow key navigator.
	function handle_nav_update( position ) {
		$search_field.val( $item_list.children().eq( position ).data( 'keyword' ) );
	}

	function handle_nav_cancel() {
		$search_field.val( search.last_search );
	}

	var key_nav = new Arrow_Key_Navigator( $item_list, $search_field, handle_nav_update, handle_nav_cancel );

	// Hook up the search field handler.
	$search_field.on( 'keyup', function () {
		if ( ! key_nav.is_navigating ) {
			handle_search_field_change();
		}
	} );

	// Finally, run at least once whenever the search field is opened.
	$search_field.one( 'focus', function() {
		handle_search_field_change();
	} );

} );