<?php
/**
 * Module responsible for Google Analytics (GA) and Tag Manager (GTM)
 *
 * Analytics code is assembled with post data and added to a placeholder div via
 * data attributes. This allows React to handle submission to Google.
 *
 * GTM is embedded via iframe directly after the closing body tag.
 */

namespace Bbgi\Integration;

class Google extends \Bbgi\Module {

	const OPTION_GTM       = 'beasley_google_tag_manager';
	const OPTION_GA_V3_ENABLED = 'ga_v3_enabled';
	const OPTION_UA        = 'gmr_google_analytics';
	const OPTION_MPARTICLE_ENABLED = 'mparticle_enabled';
	const OPTION_MPARTICLE_KEY = 'mparticle_key';
	const OPTION_UA_UID    = 'gmr_google_uid_dimension';
	const OPTION_UA_AUTHOR = 'gmr_google_author_dimension';

	/**
	 * Registers current module.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'wp_head', array( __CLASS__, 'ga_enqueue_scripts' ), 0 );
		add_action( 'wp_head', $this( 'render_inline_ga_code' ), 10 );
		add_action( 'bbgi_ga_placeholder', $this( 'prepare_ga_placeholder' ) );
		add_action( 'wp_head', $this( 'render_gtm_head' ) );
		add_action( 'beasley_after_body', $this( 'render_gtm_body' ) );
		add_action( 'bbgi_register_settings', $this( 'register_settings' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'jacapps_enqueue_scripts' ) );

		add_filter( 'fbia_analytics_makrup', $this( 'get_fbia_analytics_markup' ) );
	}

	/**
	 * Include Google Analytics for all including desktop, mobile, and whiz.
	 */
	static public function ga_enqueue_scripts() {
		$data = Google::get_analytics_data();
		$jsonData = json_encode($data);
		// WITHOUT CNAMES $jsonMParticleConfig = '{"isDevelopmentMode": true, "logLevel": "verbose", "dataPlan": {"planId": "beasley_web_beta_3", "planVersion": 1}}';
		$jsonMParticleConfig = '{"isDevelopmentMode": true,"logLevel": "verbose","dataPlan": {"planId": "beasley_web_beta_4","planVersion": 1},"v1SecureServiceUrl": "mparticle.bbgi.com/webevents/v1/JS/","v2SecureServiceUrl": "mparticle.bbgi.com/webevents/v2/JS/","v3SecureServiceUrl": "mparticle.bbgi.com/webevents/v3/JS/","configUrl": "mparticle.bbgi.com/tags/JS/v2/","identityUrl": "mparticle.bbgi.com/identity/v1/","aliasUrl": "mparticle.bbgi.com/webevents/v1/identity/"}';

		echo sprintf(
			'<script>window.bbgiAnalyticsConfig=JSON.parse(\'%s\'); window.bbgiAnalyticsConfig.mParticleConfig=JSON.parse(\'%s\');</script>',
			$jsonData,
			$jsonMParticleConfig
		);

		wp_enqueue_script(
			'mparticle_enqueue_scripts',
			plugins_url( 'assets/js/mparticle-schema.js', __FILE__ ),
			array(),
			'2.0.33'
		);

		wp_enqueue_script(
			'window_utils_enqueue_scripts',
			plugins_url( 'assets/js/window-utils.js', __FILE__ ),
			array(),
			'2.0.33'
		);

		wp_enqueue_script(
			'ga_enqueue_scripts',
			plugins_url( 'assets/js/beasley-analytics.js', __FILE__ ),
			array( 'window_utils_enqueue_scripts', 'mparticle_enqueue_scripts' ),
			'2.0.33'
		);
	}

	/**
	 * Google Analytics for jacapps and whiz.
	 */
	public static function jacapps_enqueue_scripts() {
		$current_post_type	= get_post_type( get_queried_object_id() );
		if ( function_exists( 'ee_is_common_mobile' ) && ee_is_common_mobile() && in_array( $current_post_type, Google::allow_posttype_list_for_common_mobile() ) ) {
			$data = Google::get_analytics_data();

			if ( empty( $data ) ) {
				return '';
			}
			if( isset($data['google_analytics']) && $data['google_analytics'] != "" ) {
				wp_enqueue_script(
					'enqueue-scripts-for-common-mobile',
					plugins_url( 'assets/js/google-jacapps.js', __FILE__ ),
					array('jquery'),
					'1.0.0',
					true
				);
				wp_localize_script( 'enqueue-scripts-for-common-mobile', 'GaInfoForJacapps', array( 'google_analytics' => $data['google_analytics'] ) );
			}
		}
	}

	/**
	 * Returns array of post type.
	 *
	 * @return array
	 */
	public static function allow_posttype_list_for_common_mobile() {
		return (array) apply_filters( 'allow-font-awesome-for-posttypes', array( 'affiliate_marketing', 'gmr_gallery', 'listicle_cpt' )  );
	}

	/**
	 * Registers Google Analytics and Tag Manager settings.
	 *
	 * @access public
	 * @action bbgi_register_settings
	 * @param string $group
	 * @param string $page
	 */
	public function register_settings( $group, $page ) {
		$section_id = 'beasley_google_settings';

		$uid_dimension_args = array(
			'name' => 'gmr_google_uid_dimension',
			'desc' => 'Sends the current user\'s ID to this custom Google Analytics dimension. Most sites can use dimension1 unless it is already in use.',
		);

		$author_dimension_args = array(
			'name' => 'gmr_google_author_dimension',
			'desc' => 'Sends the current post\'s author login ID to this custom Google Analytics dimension. Most sites can use dimension2 unless it is already in use.',
		);

		add_settings_section( $section_id, 'Google', '__return_false', $page );

		add_settings_field( self::OPTION_GTM, 'Tag Manager Code', 'bbgi_input_field', $page, $section_id, 'name=beasley_google_tag_manager&desc=GTM-xxxxxx' );

		add_settings_field( self::OPTION_GA_V3_ENABLED, 'V3 Analytics Enabled', 'true_defaulted_bbgi_checkbox_field', $page, $section_id, 'name=ga_v3_enabled');
		add_settings_field( self::OPTION_UA, 'V3 Analytics Code', 'bbgi_input_field', $page, $section_id, 'name=gmr_google_analytics&desc=UA-xxxxxx-xx' );
		add_settings_field( self::OPTION_MPARTICLE_ENABLED, 'MParticle Enabled', 'bbgi_checkbox_field', $page, $section_id, 'name=mparticle_enabled');
		add_settings_field( self::OPTION_MPARTICLE_KEY, 'MParticle Key', 'bbgi_input_field', $page, $section_id, 'name=mparticle_key' );

		add_settings_field( self::OPTION_UA_UID, 'User ID Dimension #', 'bbgi_input_field', $page, $section_id, $uid_dimension_args );
		add_settings_field( self::OPTION_UA_AUTHOR, 'Author Dimension #', 'bbgi_input_field', $page, $section_id, $author_dimension_args );

		register_setting( $group, self::OPTION_GTM, 'sanitize_text_field' );
		register_setting( $group, self::OPTION_UA_UID, 'sanitize_text_field' );
		register_setting( $group, self::OPTION_UA_AUTHOR, 'sanitize_text_field' );


		register_setting( $group, self::OPTION_GA_V3_ENABLED, 'sanitize_text_field' );
		register_setting( $group, self::OPTION_UA, 'sanitize_text_field' );
		register_setting( $group, self::OPTION_MPARTICLE_ENABLED, 'sanitize_text_field' );
		register_setting( $group, self::OPTION_MPARTICLE_KEY, 'sanitize_text_field' );
	}

	/**
	 * Returns analytics data.
	 *
	 * @return array
	 */
	public static function get_analytics_data() {
		$google_analytics_v3_enabled = trim( get_option( self::OPTION_GA_V3_ENABLED, true) );
		$google_analytics_ua = trim( get_option( self::OPTION_UA ) );
		$mparticle_enabled = trim( get_option( self::OPTION_MPARTICLE_ENABLED ) );
		$mparticle_key = trim( get_option( self::OPTION_MPARTICLE_KEY ) );

		if (
			( empty($google_analytics_v3_enabled) || empty($google_analytics_ua) )
			&& ( empty($mparticle_enabled) || empty($mparticle_key) )
		) {
			return [];
		}

		$data = [
			'google_analytics_v3_enabled' => $google_analytics_v3_enabled,
			'google_analytics'        	  => $google_analytics_ua,
			'mparticle_enabled' 		  => $mparticle_enabled,
			'mparticle_key'	  	  		  => $mparticle_key,
			'google_uid_dimension'    	  => absint( get_option( self::OPTION_UA_UID ) ),
			'google_author_dimension' 	  => absint( get_option( self::OPTION_UA_AUTHOR ) ),
			'title'                   	  => wp_title( '&raquo;', false ),
			'url'					  	  => esc_url( home_url( $_SERVER['REQUEST_URI'] ) ),
			'shows'                   	  => '',
			'category'                	  => '',
			'author'                  	  => 'non-author',
		];

		if ( is_singular() ) {
			$post = get_queried_object();

			$args = array(
				'orderby' => 'name',
				'order'   => 'ASC',
				'fields'  => 'slugs',
			);

			$data['shows'] = implode( ', ', wp_get_post_terms( $post->ID, '_shows', $args ) );
			$data['category'] = implode( ', ', wp_get_post_terms( $post->ID, 'category', $args ) );

			$primary_author = get_field( 'primary_author_cpt', $post );
			$primary_author = $primary_author ? $primary_author : $post->post_author;
			$data['author'] = get_the_author_meta( 'login', $primary_author );
		}

		return $data;
	}

	/**
	 * Generates the GA placeholder
	 *
	 * @return void
	 */
	public function prepare_ga_placeholder() {

		$data = $this->get_analytics_data();

		if ( empty( $data ) || ! apply_filters( 'bbgi_render_ga_placeholder', true ) ) {
			return;
		}

		echo sprintf(
			'<div class="ga-info"
				  data-title="%s"
				  data-url="%s"
				  data-contentgroup1="%s"
				  data-contentgroup2="%s"
				  data-dimensionkey="%s"
				  data-dimensionvalue="%s"></div>',
			esc_attr( $data['title'] ),
			esc_attr( $data['url'] ),
			esc_attr( $data['shows'] ),
			esc_attr( $data['category'] ),
			esc_attr( sprintf( 'dimension%s', $data['google_author_dimension'] ) ),
			esc_attr( $data['author'] )
		);
	}

	/**
	 * Renders the inline GA code.
	 *
	 * It only renders the minimal stuff and delegates handling events to React.
	 *
	 * @return void
	 */
	public function render_inline_ga_code() {
		$onload = apply_filters( 'bbgi_google_onload_code', '' );

		if ( function_exists( 'ee_is_common_mobile' ) && ee_is_common_mobile() ) {
			echo $this->get_analytics_code( $onload,  true );
		} else {
			echo $this->get_analytics_code( $onload, apply_filters( 'bbgi_google_inline_page_view', false ) );
		}

	}

	/**
	 * Render the GA placeholder
	 *
	 * @return void
	 */
	public static function render_ga_placeholder() {
		do_action( 'bbgi_ga_placeholder' );
	}

	/**
	 * Assembles Google Analytics code and returns it.
	 *
	 * @access public
	 * @param string $extra
	 * @return string
	 */
	public function get_analytics_code( $extra = '', $inline_pageview = true ) {
		$data = $this->get_analytics_data();

		if ( empty( $data ) ) {
			return '';
		}

		$script  = '<script>';

		// We Now Create GA_V3 and/or mparticle in beasleyAnalytics Class.
		// $script .= "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');";
		$script .= "window.beasleyanalytics = new BeasleyAnalytics();";

		$script .= sprintf( "var googleUidDimension = '%s';", esc_js( $data['google_uid_dimension'] ) );
		$script .= sprintf( "window.beasleyanalytics.createAnalytics( '%s', 'auto');", esc_js( $data['google_analytics'] ) );
		$script .= "window.beasleyanalytics.requireAnalytics('displayfeatures');";


		if ( $inline_pageview ) {
			$script .= $this->render_inline_targeting_values( $data );
		}

		$script .= $extra;

		if ( $inline_pageview ) {
			$script .= "window.beasleyanalytics.sendEvent('pageview');";
		}

		$script .= '</script>';

		return $script;
	}

	public function render_inline_targeting_values( $data ) {
		$script = '';

		if ( ! empty( $data['shows'] ) ) {
			$script .= sprintf( "window.beasleyanalytics.setAnalytics('contentGroup1', '%s');", esc_js( $data['shows'] ) );
		}

		if ( ! empty( $data['category'] ) ) {
			$script .= sprintf( "window.beasleyanalytics.setAnalytics('contentGroup2', '%s');", esc_js( $data['category'] ) );
		}

		if ( ! empty( $data['author'] ) && ! empty( $data['google_author_dimension'] ) ) {
			$script .= sprintf( "window.beasleyanalytics.setAnalytics('dimension%s', '%s');", esc_js( $data['google_author_dimension'] ), esc_js( $data['author'] ) );
		}

		return $script;
	}
	/**
	 * Returns Google Analytics code for FB instant articles.
	 *
	 * @access public
	 * @filter fbia_analytics_makrup
	 * @return string
	 */
	public function get_fbia_analytics_markup() {
		$extra = <<<EOL
window.beasleyanalytics.setAnalytics('campaignSource', 'Facebook');
window.beasleyanalytics.setAnalytics('campaignMedium', 'Social Instant Article');
window.beasleyanalytics.setAnalytics('title', 'FBIA: ' + ia_document.title);
EOL;

		return $this->get_analytics_code( $extra );
	}

	/**
	 * Renders Google Analytics code in the header.
	 *
	 * @access public
	 * @action wp_head
	 */
	public function render_analytics_head() {
		$onload = apply_filters( 'bbgi_google_onload_code', '' );
		echo $this->get_analytics_code( $onload );
	}

	/**
	 * Renders GTM script in the header if GTM Code has been provided.
	 *
	 * @access public
	 * @action wp_head
	 */
	public function render_gtm_head() {
		$gtm = trim( get_option( self::OPTION_GTM ) );
		if ( empty( $gtm ) ) {
			return;
		}

		$gtm = esc_js( $gtm );

		echo <<< EOL
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$gtm}');</script>
<!-- End Google Tag Manager -->
EOL;
	}

	/**
	 * Renders GTM script in the body if GTM Code has been provided.
	 *
	 * @access public
	 * @action beasley_after_body
	 */
	public function render_gtm_body() {
		$gtm = trim( get_option( self::OPTION_GTM ) );
		if ( empty( $gtm ) ) {
			return;
		}

		$gtm = esc_attr( urlencode( $gtm ) );

		echo <<<EOL
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$gtm}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
EOL;
	}

}
