<?php
/**
 * Class CommonSettings
 */
class CanonicalMetaBox {
	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_canonical' ));
		add_action( 'save_post', array( __CLASS__, 'save_canonical' ));
		}

	 /**
         * Set up and add the canonical meta box.
         */
        public static function add_canonical() {
            add_meta_box('canonical_url','Canonical Url', array( __CLASS__, 'html_canonical'));
        }
        /**
         * Save the meta box selections.
         *
         * @param int $post_id  The post ID.
         */
        public static function save_canonical( int $post_id ) {
            if ( array_key_exists( 'canonical_url', $_POST ) ) {
                update_post_meta($post_id,'_canonical_url',esc_url_raw($_POST['canonical_url']));
            }
        }
        /**
         * Display the meta box HTML to the user.
         *
         * @param \WP_Post $post   Post object.
         */
        public function html_canonical( $post ) {
            $canonical_url = get_post_meta( $post->ID, '_canonical_url', true ); ?>
            <label for="canonical_url">Url</label>
            <input type="url" name="canonical_url" value="<?php echo esc_attr($canonical_url);?>">
            <?php
        }

	/**
	 * Returns array of post type.
	 *
	 * @return array
	 */
	public static function allow_fontawesome_posttype_list() {
		return (array) apply_filters( 'allow-font-awesome-for-posttypes', array( 'listicle_cpt', 'affiliate_marketing' )  );
	}

	/**
	 * Enqueues admin scripts and styles.
	 *
	 * @global string $typenow The current type.
	 * @global string $pagenow The current page.
	 */
	public static function enqueue_scripts() {
		global $typenow, $pagenow;

		/* if ( in_array( $typenow, CommonSettings::allow_fontawesome_posttype_list() ) && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
			wp_register_style('general-font-awesome',GENERAL_SETTINGS_CPT_URL . "assets/css/general-font-awesome". $postfix .".css", array(), GENERAL_SETTINGS_CPT_VERSION, 'all');
			wp_enqueue_style('general-font-awesome');
		} */
	}
}

CanonicalMetaBox::init();
