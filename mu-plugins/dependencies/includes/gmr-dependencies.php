<?php

/**
 * Created by Eduard
 * Date: 07.11.2014 0:09
 */
class GmrDependencies {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_dependencies' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_dependencies' ) );
	}

	public function register_dependencies() {

		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

		// Enqueue modernizr, so we can use feature detection for things
		// Using the modernizr version (2.8.3) plus an extra .1, so we can bust cache if we need to add additional things later
		wp_enqueue_script( 'modernizr', GMRDEPENDENCIES_URL . '/modernizr/modernizr.min.js', array(), '2.8.3.1', false );

		// Register scripts
		wp_register_script(
			'select2'
			, GMRDEPENDENCIES_URL . "/select2/select2{$postfix}.js"
			, array( 'jquery' )
			, '3.5.2'
			, true
		);

		wp_register_script(
			'parsleyjs',
			GMRDEPENDENCIES_URL . "/parsleyjs/dist/parsley{$postfix}.js",
			array( 'jquery' ),
			'2.0.5', // Using daveross/parsley.js fork until word count include issue #765 is merged
			true
		);

		wp_register_script(
			'parsleyjs-words',
			GMRDEPENDENCIES_URL . '/parsleyjs/src/extra/validator/words.js',
			array( 'parsleyjs' ),
			'2.0.5', // Using daveross/parsley.js fork until word count include issue #765 is merged
			true
		);

		wp_register_script(
			'date-format',
			GMRDEPENDENCIES_URL . '/date.format/date.format.js',
			array(),
			false,
			true
		);

		wp_register_script(
			'date-toisostring',
			GMRDEPENDENCIES_URL . '/date-toisostring.js',
			array(),
			null,
			true
		);

		wp_register_script(
			'datetimepicker',
			GMRDEPENDENCIES_URL . '/datetimepicker/jquery.datetimepicker.js',
			array( 'jquery' ),
			'2.3.9',
			true
		);

		wp_register_script(
			'ie8-node-enum',
			GMRDEPENDENCIES_URL . '/ie8-node-enum/index.js',
			array(),
			false,
			true
		);

		if(defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
			$formbuilder_js_url = GMRDEPENDENCIES_URL . "/formbuilder/dist/formbuilder.js";
		}
		else {
			$formbuilder_js_url = GMRDEPENDENCIES_URL . "/formbuilder/dist/formbuilder-min.js";
		}

		wp_register_script(
			'formbuilder',
			$formbuilder_js_url,
			array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-draggable',
				'underscore',
				'underscore-mixin-deepextend',
				'backbone',
				'backbone-deep-model',
				'ie8-node-enum',
				'rivets',
			),
			'0.2.1',
			true
		);

		wp_register_script(
			'backbone-deep-model',
			GMRDEPENDENCIES_URL . '/backbone-deep-model/src/deep-model.js',
			array( 'backbone' ),
			'0.10.4',
			true
		);

		wp_register_script(
			'underscore-mixin-deepextend',
			GMRDEPENDENCIES_URL . '/underscore.mixin.deepExtend/index.js',
			array( 'underscore' ),
			false,
			true
		);

		wp_register_script(
			'rivets',
			GMRDEPENDENCIES_URL . "/rivets/dist/rivets{$postfix}.js",
			array(),
			'0.5.13',
			true
		);

		wp_register_script(
			'jquery-scrollwindowto',
			GMRDEPENDENCIES_URL . "/jquery.scrollWindowTo.js",
			array( 'jquery' ),
			false,
			true
		);

		wp_register_script(
			'classlist-polyfill',
			GMRDEPENDENCIES_URL . 'polyfills/classList.js',
			array(),
			false,
			true
		);

		// Register styles
		wp_register_style(
			'select2'
			, GMRDEPENDENCIES_URL . "/select2/select2.css"
			, array()
			, '3.5.2'
			, 'all'
		);

		// jQuery-ui theme
		wp_register_style(
			'jquery-ui',
			GMRDEPENDENCIES_URL . "/jquery-ui/jquery-ui{$postfix}.css",
			array(),
			'1.11.2',
			'all'
		);

		// Cookies.js
		wp_register_script(
			'cookies-js',
			GMRDEPENDENCIES_URL . "/cookies/cookies{$postfix}.js",
			array(),
			'1.1.0',
			true
		);

		wp_enqueue_style(
			'parsleyjs',
			GMRDEPENDENCIES_URL  . '/parsleyjs/src/parsley.css',
			array(),
			'2.0.5', // Using daveross/parsley.js fork until word count include issue #765 is merged
			'all'
		);

		wp_enqueue_script(
			'pjax',
			GMRDEPENDENCIES_URL  . 'pjax/jquery.pjax.js',
			array('jquery'),
			'1.9.2',
			false
		);

		// Old versions have a bug with MP3s
		if ( ! is_admin() ) {
			wp_deregister_script( 'wp-mediaelement' );
			wp_register_script(
				'wp-mediaelement',
				GMRDEPENDENCIES_URL  . "mediaelement-js/mediaelement-and-player{$postfix}.js",
				array( 'jquery' ),
				'2.16.3',
				true
			);
		}

		wp_register_script(
			'adblock-detect',
			GMRDEPENDENCIES_URL . 'adblock-detect/advert.js',
			array(),
			false,
			true
		);
		
		wp_register_script(
			'waypoints',
			GMRDEPENDENCIES_URL . "waypoints/lib/noframework.waypoints{$postfix}.js",
			array(),
			false,
			true
		);
		wp_register_script(
			'jquery-waypoints',
			GMRDEPENDENCIES_URL . "waypoints/lib/jquery.waypoints{$postfix}.js",
			array( 'jquery' ),
			false,
			true
		);

		wp_register_style( 'jquery-ui', GMRDEPENDENCIES_URL . '/jquery-ui-theme/jquery-ui.min.css' );
		wp_register_style( 'jquery-ui-accordion', GMRDEPENDENCIES_URL . '/jquery-ui-theme/jquery.ui.accordion.min.css', array( 'jquery-ui' ) );
		wp_register_style( 'jquery-ui-autocomplete', GMRDEPENDENCIES_URL . '/jquery-ui-theme/jquery.ui.autocomplete.min.css', array( 'jquery-ui' ) );
		wp_register_style( 'jquery-ui-button', GMRDEPENDENCIES_URL . '/jquery-ui-theme/jquery.ui.button.min.css', array( 'jquery-ui' ) );
		wp_register_style( 'jquery-ui-core', GMRDEPENDENCIES_URL . '/jquery-ui-theme/jquery.ui.core.min.css', array( 'jquery-ui' ) );
		wp_register_style( 'jquery-ui-datepicker', GMRDEPENDENCIES_URL . '/jquery-ui-theme/jquery.ui.datepicker.min.css', array( 'jquery-ui' ) );
		wp_register_style( 'jquery-ui-dialog', GMRDEPENDENCIES_URL . '/jquery-ui-theme/jquery.ui.dialog.min.css', array( 'jquery-ui' ) );
		wp_register_style( 'jquery-ui-menu', GMRDEPENDENCIES_URL . '/jquery-ui-theme/jquery.ui.menu.min.css', array( 'jquery-ui' ) );
		wp_register_style( 'jquery-ui-progressbar', GMRDEPENDENCIES_URL . '/jquery-ui-theme/jquery.ui.progressbar.min.css', array( 'jquery-ui' ) );
		wp_register_style( 'jquery-ui-resizable', GMRDEPENDENCIES_URL . '/jquery-ui-theme/jquery.ui.resizable.min.css', array( 'jquery-ui' ) );
		wp_register_style( 'jquery-ui-selectable', GMRDEPENDENCIES_URL . '/jquery-ui-theme/jquery.ui.selectable.min.css', array( 'jquery-ui' ) );
		wp_register_style( 'jquery-ui-slider', GMRDEPENDENCIES_URL . '/jquery-ui-theme/jquery.ui.slider.min.css', array( 'jquery-ui' ) );
		wp_register_style( 'jquery-ui-spinner', GMRDEPENDENCIES_URL . '/jquery-ui-theme/jquery.ui.spinner.min.css', array( 'jquery-ui' ) );
		wp_register_style( 'jquery-ui-tabs', GMRDEPENDENCIES_URL . '/jquery-ui-theme/jquery.ui.tabs.min.css', array( 'jquery-ui' ) );
		wp_register_style( 'jquery-ui-theme', GMRDEPENDENCIES_URL . '/jquery-ui-theme/jquery.ui.theme.min.css', array( 'jquery-ui' ) );
		wp_register_style( 'jquery-ui-tooltip', GMRDEPENDENCIES_URL . '/jquery-ui-theme/jquery.ui.tooltip.min.css', array( 'jquery-ui' ) );

		wp_register_style(
			'datetimepicker',
			GMRDEPENDENCIES_URL . '/datetimepicker/jquery.datetimepicker.css',
			array(),
			'2.3.9',
			'all'
		);

		wp_register_style(
			'parsleyjs',
			GMRDEPENDENCIES_URL . '/parsleyjs/src/parsley.css',
			array(),
			'2.0.5', // Using daveross/parsley.js fork until word count include issue #765 is merged
			'all'
		);

		wp_register_style(
			'formbuilder',
			GMRDEPENDENCIES_URL . '/formbuilder/dist/formbuilder.css',
			array(),
			'0.2.1',
			'all'
		);

		wp_register_style(
			'font-awesome',
			GMRDEPENDENCIES_URL . "/font-awesome/css/font-awesome{$postfix}.css",
			array(),
			'4.0.3',
			'all'
		);

	}

}

$GmrDependencies = new GmrDependencies();
