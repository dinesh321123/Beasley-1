<?php

/*
 * main class of User Role Editor WordPress plugin
 * Author: Vladimir Garagulya
 * Author email: vladimir@shinephp.com
 * Author URI: http://shinephp.com
 * License: GPL v3
 * 
*/

class User_Role_Editor_Pro extends User_Role_Editor {
       
    public $screen_help = null;

    public function __construct($lib) {

        parent::__construct($lib);
                        
        add_action('ure_activation', array($this, 'update_on_activation'));
        add_action('plugins_loaded', array($this, 'load_extra_stuff'));
        
        $activate_create_post_capability = $this->lib->get_option('activate_create_post_capability', false);
        if ($activate_create_post_capability) {        
            add_action('init', array($this, 'activate_create_post_capability'), 10, 2);
        }
        
        $activate_content_for_roles_shortcode = $this->lib->get_option('activate_content_for_roles_shortcode', false);
        if ($activate_content_for_roles_shortcode) {        
            add_action('init', array($this, 'add_content_shortcode_for_roles'));
        }
                 
        $this->allow_unfiltered_html();
                
    }
    // end of __construct()

    
    public function update_on_activation() {
    
        // update from versions earlier 4.14.2
        $activate_admin_menu_access_module = $this->lib->get_option('activate_admin_menu_access_module', false);
        if (!empty($activate_admin_menu_access_module)) {
            require_once( URE_PLUGIN_DIR .'includes/pro/class-admin-menu.php');
            require_once( URE_PLUGIN_DIR .'includes/pro/class-update-admin-menu-hashes.php');
            URE_Update_Admin_Menu_Hashes::act($this->lib);
        }
        // end of update from versions earlier 4.14.2
        
        $this->lib->delete_option('licensed_domain', true);
        
    }
    // end of update_on_activation()
    

    public function plugin_init() {
        parent::plugin_init();

        add_action('ure_settings_update1', array($this, 'settings_update1'));
        add_action('ure_settings_update2', array($this, 'settings_update2'));
        add_action('ure_settings_show1', array($this, 'settings_show1'));
        add_action('ure_settings_show2', array($this, 'settings_show2'));
        
        if ($this->lib->multisite) {
            add_action('ure_settings_ms_show', array($this, 'settings_ms_show'));
            add_action('ure_settings_ms_update', array($this, 'settings_ms_update'));
        }
        add_action('ure_load_js', array($this, 'add_js'));             
        
        if ($this->lib->multisite && is_network_admin()) {
            if (!$this->lib->active_for_network) {
                add_filter('network_admin_plugin_action_links_'. URE_PLUGIN_BASE_NAME, 
                           array($this, 'network_admin_plugin_action_links'), 10, 1);
            }
            add_action('ms_user_row_actions', array( $this, 'user_row'), 10, 2);
            add_action('ure_role_edit_toolbar_update', array($this, 'add_role_update_network_button'));
            add_action('ure_user_edit_toolbar_update', array($this, 'add_user_update_network_button'));
        }
        
        $this->screen_help = new URE_Screen_Help_Pro();
    }
    // end of plugin_init()
    
    
    /**
     * Modify plugin action links
     * 
     * @param array $links
     * @param string $file
     * @return array
     */
    public function network_admin_plugin_action_links($links) {
/*
        $settings_link = "<a href='settings.php?page=settings-" . URE_PLUGIN_FILE . "'>" . esc_html__('Settings', 'ure') . "</a>";
        $links = array_merge($links, array($settings_link));
*/
        return $links;
    }
    // end of network_admin_plugin_action_links()

    
    /**
     * It is fully overriden version of the parent method
     */
    public function admin_css_action() {

        wp_enqueue_style('wp-jquery-ui-dialog');
        if (stripos($_SERVER['REQUEST_URI'], 'settings-user-role-editor')!==false) {
            $use_jquery_cdn_for_ui_css = $this->lib->get_option('use_jquery_cdn_for_ui_css', false);
            if ($use_jquery_cdn_for_ui_css) {
                wp_enqueue_style('ure-jquery-ui-tabs', '//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css', array(), false, 'screen');
            } else {
                wp_enqueue_style('ure-jquery-ui-tabs', URE_PLUGIN_URL . 'css/jquery-ui-1.10.4.custom.min.css', array(), false, 'screen');
            }            
        }
        wp_enqueue_style('ure-admin-css', URE_PLUGIN_URL . 'css/ure-admin.css', array(), false, 'screen');        
                        
    }
    // end of admin_css_action()
        
    /** 
     * Prevent 'insufficient permissions' message from 'edit.php?post_type=custom' link 
     * in case 'create_custom' capability is active, but user does not have 'edit_posts' capability
     */    
    public function allow_edit_custom_post_type($menu_order) {
        global $_wp_menu_nopriv;
        global $_wp_submenu_nopriv;
        global $pagenow;
                
        if ($pagenow!=='edit.php') {
            return $menu_order;
        }
        
        $post_type_name = filter_input(INPUT_GET, 'post_type', FILTER_SANITIZE_STRING);
        if (empty($post_type_name)) {
            return $menu_order;
        }
        
        if (current_user_can('edit_posts')) {
            return $menu_order;
        }
        
        $post_type = get_post_type_object($post_type_name);
        if (current_user_can($post_type->cap->edit_posts)) {
            unset($_wp_menu_nopriv[$pagenow]);
            unset($_wp_submenu_nopriv[$pagenow][$pagenow]);
        }
        
        return $menu_order;
    }
    //

    /**
     * Replace create_posts capability from 'edit_posts' to 'create_posts' for stadnard WP posts,
     * from 'edit_pages' to 'create_pages' for standard WP pages, and do the same for all public custom post types
     * 
     */
    public function activate_create_post_capability() {
        global $wp_post_types;
                        
        $post_types = get_post_types(array('public'=>true, 'show_ui'=>true), 'objects');
        foreach($post_types as $post_type) {
            if ($post_type->name=='attachment') {   // exclude Media
                continue;
            }
            if ($post_type->cap->create_posts==$post_type->cap->edit_posts) {
                $wp_post_types[$post_type->name]->cap->create_posts = str_replace('edit_', 'create_', $post_type->cap->edit_posts);
            }
        }  
        // use the nearest filter available
        add_filter('custom_menu_order', array($this, 'allow_edit_custom_post_type'));
                
    }
    // end of activate_create_post_capability()
    
    
    public function add_content_shortcode_for_roles() {
                
        add_shortcode('user_role_editor', array($this, 'process_content_shortcode_for_roles'));        
        
    }
    // end of add_content_shortcode_for_roles()

    
    public function process_content_shortcode_for_roles($atts, $content=null) {
        
        global $current_user;
        
        if (current_user_can('administrator')) {
            return do_shortcode($content);
        }
                
        $attrs = shortcode_atts(
                array(
                    'roles' => 'subscriber'
                ), 
                $atts);
        $roles = explode(',', $attrs['roles']);
        $show_content = false;
        foreach($roles as $role) {
            $role = trim($role);
            if ($role=='none' && $current_user->ID==0) {
                $show_content = true;
                break;
            }
            if (current_user_can($role)) {
                $show_content = true;
                break;
            }
        }
        if (!$show_content) {
            $content = '';
        } else {
            $content = do_shortcode($content);
        }
        
        return $content;
    }
    // end of process_content_shortcode_for_roles()
    
    
    protected function is_user_profile_extention_allowed() {
        // no limits for the Pro version
        return true;
    }
    // end of is_user_profile_extention_allowed()

    
    protected function load_admin_menu_access_module() {
        
        if (is_network_admin()) {
            return;
        }
        $activate_admin_menu_access_module = $this->lib->get_option('activate_admin_menu_access_module', false);
        if (!empty($activate_admin_menu_access_module)) {
            require_once( URE_PLUGIN_DIR .'includes/pro/class-admin-menu.php');
            require_once( URE_PLUGIN_DIR .'includes/pro/class-admin-menu-access.php');
            new URE_Admin_Menu_Access($this->lib);
        }
                
    }
    // end of load_admin_menu_access_module()
    
    
    protected function load_widgets_access_module() {
        
        if (is_network_admin()) {
            return;
        }
        if (!is_admin()) {
            return;
        }
        $activate_widgets_access_module = $this->lib->get_option('activate_widgets_access_module', false);
        if (!empty($activate_widgets_access_module)) {
            require_once( URE_PLUGIN_DIR .'includes/pro/class-widgets.php');
            require_once( URE_PLUGIN_DIR .'includes/pro/class-widgets-access.php');
            new URE_Widgets_Access($this->lib);
        }
                
    }
    // end of load_widgets_access_module()
    
    
    protected function load_other_roles_access_module() {
        
        if (is_network_admin()) {
            return;
        }
        if (!is_admin()) {
            return;
        }
        $activate_other_roles_access_module = $this->lib->get_option('activate_other_roles_access_module', false);
        if (!empty($activate_other_roles_access_module)) {
            require_once( URE_PLUGIN_DIR .'includes/pro/class-other-roles.php');
            require_once( URE_PLUGIN_DIR .'includes/pro/class-other-roles-access.php');
            new URE_Other_Roles_Access($this->lib);
        }
                
    }
    // end of load_widgets_access_module()

    
    protected function load_post_edit_access_module() {
        if (is_network_admin()) {
            return;
        }
        if (!is_admin()) {
            return;
        }
        $manage_posts_edit_access = $this->lib->get_option('manage_posts_edit_access', false);
        if (!empty($manage_posts_edit_access)) {
            require_once( URE_PLUGIN_DIR .'includes/pro/class-posts-edit-access.php');
            require_once( URE_PLUGIN_DIR .'includes/pro/class-posts-edit-access-bulk-action.php');
            new URE_Posts_Edit_Access($this->lib);
        }
    }
    // end of load_post_edit_access_module()

    
    protected function load_plugin_activation_access_module() {
        if (is_network_admin()) {
            return;
        }
        if (!is_admin()) {
            return;
        }
        $manage_plugin_activation_access = $this->lib->get_option('manage_plugin_activation_access', false);
        if (!empty($manage_plugin_activation_access)) {            
            require_once( URE_PLUGIN_DIR .'includes/pro/class-plugins-activation-access.php');
            new URE_Plugins_Activation_Access($this->lib);
        }
    }
    // end of load_plugin_activation_access_module()
    
    
    protected function load_themes_access_module() {
        if (!$this->lib->multisite) {
            return;
        }
        if (is_network_admin()) {
            return;
        }        
        if (!is_admin()) {
            return;
        }
        $manage_themes_access = $this->lib->get_option('manage_themes_access', false);
        if (!empty($manage_themes_access)) {
            require_once( URE_PLUGIN_DIR .'includes/pro/class-themes-access.php');
            new URE_Themes_Access($this->lib);
        }

    }
    // end of load_themes_access_module()
    

    /**
     * Load Gravity Forms Access Restriction module
     * @return void
     */
    protected function load_gf_access_module() {
        if (is_network_admin()) {
            return;
        }        
        if (!is_admin()) {
            return;
        }
        if ( !class_exists('GFForms') ) {
            return;        
        }
        $manage_gf_access = $this->lib->get_option('manage_gf_access', false);
        if ($manage_gf_access) {
            require_once( URE_PLUGIN_DIR .'includes/pro/class-gf-access.php');
            new URE_GF_Access($this->lib);
        }
    }
    // end of load_gf_access_module()
    
    
    protected function load_content_view_restrictions_module() {
        $activate_content_for_roles = $this->lib->get_option('activate_content_for_roles', false);
        if ($activate_content_for_roles) {
            require_once( URE_PLUGIN_DIR .'includes/pro/class-content-view-restrictions.php');
            new URE_Content_View_Restrictions($this->lib);
        }
    }
    // end of load_content_view_restrictions_module()
    
    
    protected function load_export_import_module() {
        if (!is_admin() && !is_network_admin()) {
            return;
        }
        
        new Ure_Export_Import($this->lib);

    }
    // end of load_export_import_module()
    
    
    /**
     * Conditionally load additional modules
     * 
     */
    public function load_extra_stuff() {
        
        global $current_user;
        
        $this->load_admin_menu_access_module();
        $this->load_widgets_access_module();
        $this->load_other_roles_access_module();
        $this->load_post_edit_access_module();
        $this->load_plugin_activation_access_module();
        $this->load_themes_access_module();
        $this->load_gf_access_module();
        $this->load_content_view_restrictions_module();                
        $this->load_export_import_module();

    }
    // end of load_extra_stuff()
    
    
    protected function update_ure_key_capability() {
        global $wp_roles;
        
        $this->ure_key_capability_error = '';
        $ure_key_capability = $this->lib->get_request_var('ure_key_capability', 'post');
        if (!empty($ure_key_capability)) {
            $valid_name = preg_match('/[A-Za-z0-9_\-]*/', $ure_key_capability, $match);
            if ( $valid_name && ($match[0] == $ure_key_capability) ) { 
                $old_value = $this->lib->get_option('ure_key_capability');
                if ($old_value!==$ure_key_capability) {
                    $this->lib->put_option('ure_key_capability', $ure_key_capability);
                    $wp_roles->use_db = true;
                    $administrator = $wp_roles->get_role('administrator');
                    $administrator->remove_cap($old_value);
                    if (!$administrator->has_cap($ure_key_capability)) {
                        $wp_roles->add_cap('administrator', $ure_key_capability);
                    }
                }
            } else {
                $this->ure_key_capability_error = __('Error: Capability name must contain latin characters, digits, hyphens and underscores only!', 'ure');                
            }            
        } else {    // empty value
            $old_value = $this->lib->get_option('ure_key_capability');
            $this->lib->put_option('ure_key_capability', '');
            $wp_roles->use_db = true;
            $administrator = $wp_roles->get_role('administrator');
            $administrator->remove_cap($old_value);            
        }
    }
    // end update_ure_key_capability()
    
    
    /*
     * General options tab update
     */
    public function settings_update1() {
        
        $this->update_ure_key_capability();
        
        $use_jquery_cdn_for_ui_css = $this->lib->get_request_var('use_jquery_cdn_for_ui_css', 'checkbox');
        $this->lib->put_option('use_jquery_cdn_for_ui_css', $use_jquery_cdn_for_ui_css);
                
        $license_key = $this->lib->get_request_var('license_key', 'post');
        if (!empty($license_key) && strpos($license_key, '*')===false) {
            $this->lib->put_option('license_key', $license_key);            
            $install_hash = $this->calc_install_hash($license_key);
            $this->lib->put_option('install_hash', $install_hash);
        }
    }
    // end of settings_update1()
    
    
    /*
     * Additional Modules options tab update
     */
    public function settings_update2() {
                            
        $activate_admin_menu_access_module = $this->lib->get_request_var('activate_admin_menu_access_module', 'checkbox');
        $this->lib->put_option('activate_admin_menu_access_module', $activate_admin_menu_access_module);
        
        $activate_widgets_access_module = $this->lib->get_request_var('activate_widgets_access_module', 'checkbox');
        $this->lib->put_option('activate_widgets_access_module', $activate_widgets_access_module);
        
        $activate_other_roles_access_module = $this->lib->get_request_var('activate_other_roles_access_module', 'checkbox');
        $this->lib->put_option('activate_other_roles_access_module', $activate_other_roles_access_module);
        
        $manage_posts_edit_access = $this->lib->get_request_var('manage_posts_edit_access', 'checkbox');
        $this->lib->put_option('manage_posts_edit_access', $manage_posts_edit_access);

        if ($manage_posts_edit_access) {
            $activate_create_post_capability = 1;
        } else {
            $activate_create_post_capability = $this->lib->get_request_var('activate_create_post_capability', 'checkbox');
        }
        $this->lib->put_option('activate_create_post_capability', $activate_create_post_capability);
        
        $manage_plugin_activation_access = $this->lib->get_request_var('manage_plugin_activation_access', 'checkbox');
        $this->lib->put_option('manage_plugin_activation_access', $manage_plugin_activation_access);
        
        if (class_exists('GFForms')) {
            $manage_gf_access = $this->lib->get_request_var('manage_gf_access', 'checkbox');
            $this->lib->put_option('manage_gf_access', $manage_gf_access);
        }

        $activate_content_for_roles_shortcode = $this->lib->get_request_var('activate_content_for_roles_shortcode', 'checkbox');
        $this->lib->put_option('activate_content_for_roles_shortcode', $activate_content_for_roles_shortcode);
        
        $activate_content_for_roles = $this->lib->get_request_var('activate_content_for_roles', 'checkbox');
        $this->lib->put_option('activate_content_for_roles', $activate_content_for_roles);
        
    }
    // end of settings_update2()
    

    
    // Update settings from Multisite tab
    public function settings_ms_update() {
        if (!$this->lib->multisite) {
            return;
        }
        
        if (defined('URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE') && (URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE == 1)) {
            $enable_simple_admin_for_multisite = 1;
        } else {
            $enable_simple_admin_for_multisite = $this->lib->get_request_var('enable_simple_admin_for_multisite', 'checkbox');
        }
        $this->lib->put_option('enable_simple_admin_for_multisite', $enable_simple_admin_for_multisite);
        
        $enable_unfiltered_html_ms = $this->lib->get_request_var('enable_unfiltered_html_ms', 'checkbox');
        $this->lib->put_option('enable_unfiltered_html_ms', $enable_unfiltered_html_ms);
        
        $enable_help_links_for_simple_admin_ms = $this->lib->get_request_var('enable_help_links_for_simple_admin_ms', 'checkbox');
        $this->lib->put_option('enable_help_links_for_simple_admin_ms', $enable_help_links_for_simple_admin_ms);
        
        $manage_themes_access = $this->lib->get_request_var('manage_themes_access', 'checkbox');
        $this->lib->put_option('manage_themes_access', $manage_themes_access);
        
        $caps_access_restrict_for_simple_admin = $this->lib->get_request_var('caps_access_restrict_for_simple_admin', 'checkbox');
        $this->lib->put_option('caps_access_restrict_for_simple_admin', $caps_access_restrict_for_simple_admin);
        if ($caps_access_restrict_for_simple_admin) {
            $add_del_role_for_simple_admin = $this->lib->get_request_var('add_del_role_for_simple_admin', 'checkbox');
            $caps_allowed_for_single_admin = $this->lib->filter_existing_caps_input('caps_allowed_for_single_admin');            
        } else {
            $add_del_role_for_simple_admin = 1;
            $caps_allowed_for_single_admin = array();            
        }
        $this->lib->put_option('add_del_role_for_simple_admin', $add_del_role_for_simple_admin);
        $this->lib->put_option('caps_allowed_for_single_admin', $caps_allowed_for_single_admin);
        
    }
    // end of settings_ms_update()
    
    /**
     * Show options at General tab
     * 
     */
    public function settings_show1() {
		                
        $use_jquery_cdn_for_ui_css = $this->lib->get_option('use_jquery_cdn_for_ui_css', false);
        $ure_key_capability = $this->lib->get_option('ure_key_capability', '');
        
        $this->check_license();
        $license_key = $this->lib->get_option('license_key', '');
    
        if ($this->lib->multisite) {
            $link = 'settings.php';
        } else {
            $link = 'options-general.php';
        }
        $license_key_only = $this->lib->multisite && is_network_admin() && !$this->lib->active_for_network;
        
        require_once(URE_PLUGIN_DIR . 'includes/pro/settings-template1.php');
    }
    // end of settings_show1()
     

    /**
     * Show options at Additional Modules tab
     * 
     */
    public function settings_show2() {
		                
        
        $activate_admin_menu_access_module = $this->lib->get_option('activate_admin_menu_access_module', false);
        $activate_widgets_access_module = $this->lib->get_option('activate_widgets_access_module', false);
        $activate_other_roles_access_module = $this->lib->get_option('activate_other_roles_access_module', false);
        
// content editing restrictions        
        $activate_create_post_capability = $this->lib->get_option('activate_create_post_capability', false);
        $manage_posts_edit_access = $this->lib->get_option('manage_posts_edit_access', false);
        $manage_plugin_activation_access = $this->lib->get_option('manage_plugin_activation_access', false);
        if (class_exists('GFForms')) {
            $manage_gf_access = $this->lib->get_option('manage_gf_access', false);
        }

//content view restrictions
        $activate_content_for_roles_shortcode = $this->lib->get_option('activate_content_for_roles_shortcode', false);
        $activate_content_for_roles = $this->lib->get_option('activate_content_for_roles', false);
            
        if ($this->lib->multisite) {
            $link = 'settings.php';
        } else {
            $link = 'options-general.php';
        }
		
        require_once(URE_PLUGIN_DIR . 'includes/pro/settings-template2.php');
    }
    // end of settings_show2()
    
    
    public function settings_ms_show() {
        if (!$this->lib->multisite) {
            return;
        }

        if (defined('URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE') && (URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE == 1)) {
            $enable_simple_admin_for_multisite = 1;
        } else {
            $enable_simple_admin_for_multisite = $this->lib->get_option('enable_simple_admin_for_multisite', 0);
        }
        $enable_help_links_for_simple_admin_ms = $this->lib->get_option('enable_help_links_for_simple_admin_ms', 1);
        $enable_unfiltered_html_ms = $this->lib->get_option('enable_unfiltered_html_ms', 0);
        $manage_themes_access = $this->lib->get_option('manage_themes_access', 0);
        $caps_access_restrict_for_simple_admin = $this->lib->get_option('caps_access_restrict_for_simple_admin', 0);
        if ($caps_access_restrict_for_simple_admin) {  
            $add_del_role_for_simple_admin = $this->lib->get_option('add_del_role_for_simple_admin', 1);
            $html_caps_blocked_for_single_admin = $this->lib->build_html_caps_blocked_for_single_admin();
            $html_caps_allowed_for_single_admin = $this->lib->build_html_caps_allowed_for_single_admin();
        }
        
        require_once(URE_PLUGIN_DIR . 'includes/pro/settings-template-ms.php');

    }
    // end of settings_ms_show()
            
 
    public function network_plugin_menu() {
        
        parent::network_plugin_menu();
        
        if ($this->lib->multisite) {
            $ure_page = add_submenu_page('users.php', __('User Role Editor', 'ure'), __('User Role Editor', 'ure'), 
            $this->key_capability, 'users-'.URE_PLUGIN_FILE, array($this, 'edit_roles'));
            add_action("admin_print_styles-$ure_page", array($this, 'admin_css_action'));        
        }
        
    } 
    // end of network_plugin_menu()

    
    protected function calc_install_hash($license_key) {
            
        $value = md5($license_key .'-'. ABSPATH .'-'. DB_HOST .'-'. DB_NAME );
        
        return $value;
    }
    // end of calc_install_hash()
    
    
    protected function check_license() {

        $license_key = $this->lib->get_option('license_key');
        if (empty($license_key)) {
            return;
        }
        $install_hash = $this->lib->get_option('install_hash');
        if (!empty($install_hash)) {
            $current_value = $this->calc_install_hash($license_key);
            if ($current_value != $install_hash) { 
                // site absolute path or MySQL server name of MySQL database name were changed, 
                // assume new WordPress installation and force plugin owner to input the license key again
                $this->lib->put_option('license_key', '');
                $this->lib->put_option('install_hash', '');
                $this->lib->flush_options();
            }
        } else { // just calc a value for licensed_domain option
            $install_hash = $this->calc_install_hash($license_key);
            $this->lib->put_option('install_hash', $install_hash, true);
        }
    }
    // end of check_license();
    
	
    public function filter_update_checks($query_args) {
    
        $this->check_license();
        $license_key = $this->lib->get_option('license_key');
        if (!empty($license_key)) {
            $query_args['license_key'] = $license_key;
        }

        return $query_args;
    }
    // end of filter_update_checks()
    
    
    public function add_js() {
        wp_register_script( 'ure-jquery-dual-listbox', plugins_url( '/js/pro/jquery.dualListBox-1.3.js', URE_PLUGIN_FULL_PATH ) );
        wp_enqueue_script ( 'ure-jquery-dual-listbox' );
        wp_register_script( 'ure-js-pro', plugins_url( '/js/pro/ure-js-pro.js', URE_PLUGIN_FULL_PATH ) );
        wp_enqueue_script ( 'ure-js-pro' );
        wp_localize_script( 'ure-js-pro', 'ure_data_pro', 
                array(
                    'update_network' => __('Update Network', 'ure'),
                    'update_network_warning' => __('After confirmation all sites of the network will get permissions from the main site. Are you sure?', 'ure')
                ));
    }
    // end of add_js()

    
    public function add_role_update_network_button() {
?>        
    <div style="margin-top:10px;">
        <button id="ure_update_all_network" class="ure_toolbar_button" title="Update roles for all network">Update Network</button>
    </div>
<?php        
    }
    // end of add_role_update_network_button()

    
    public function add_user_update_network_button() {
?>        
    <div style="margin-top:10px;">
        <button id="ure_update_all_network" class="ure_toolbar_button" title="Update user roles and capabilities for all network">Update Network</button>
    </div>
<?php        
    }
    // end of add_user_update_network_button()
    
    
    public function edit_user_profile($user) {

        global $current_user;
    
        if (!is_network_admin()) {
            parent::edit_user_profile($user);
            return;
        }
        
        if (!$this->lib->user_is_admin($current_user->ID)) {
            return;
        }
?>
        <h3><?php _e('User Role Editor', 'ure'); ?></h3>
        <table class="form-table">
        		<tr>
        			<th scope="row"><?php _e('Roles', 'ure'); ?></th>
        			<td>
        <?php        
        $output = $this->lib->roles_text($user->roles);
        echo $output . '&nbsp;&nbsp;&gt;&gt;&nbsp;<a href="' . wp_nonce_url("users.php?page=users-".URE_PLUGIN_FILE."&object=user&amp;user_id={$user->ID}", "ure_user_{$user->ID}") . '">' . __('Edit', 'ure') . '</a>';
        ?>
        			</td>
        		</tr>
        </table>		
        <?php
    }
    // end of edit_user_profile()

    
    protected function allow_unfiltered_html() {
        
        if ( !$this->lib->multisite || !is_admin() ||  
             ((defined( 'DISALLOW_UNFILTERED_HTML' ) && DISALLOW_UNFILTERED_HTML)) ) {
            return;
        }
        
        $enable_unfiltered_html_ms = $this->lib->get_option('enable_unfiltered_html_ms', 0);
        if ($enable_unfiltered_html_ms) {
            add_filter('map_meta_cap', array($this, 'allow_unfiltered_html_filter'), 10, 2);
        }
        
    }
    // end of allow_unfiltered_html()
    
    
    public function allow_unfiltered_html_filter($caps, $cap='') {

        global $current_user;

        if ($cap=='unfiltered_html') {
            if ($current_user->allcaps['unfiltered_html'] && $caps[0]=='do_not_allow') {
                $caps[0] = 'unfiltered_html';
                return $caps;
            }        
        }

        return $caps;

    }
    // end of allow_unfiltered_html_for_simple_admin()

    
    public function ure_ajax() {
        
        require_once(URE_PLUGIN_DIR . 'includes/class-ajax-processor.php');
        require_once(URE_PLUGIN_DIR . 'includes/pro/class-pro-ajax-processor.php');
        $ajax_processor = new URE_Pro_Ajax_Processor($this->lib);
        $ajax_processor->dispatch();
        
    }
    // end of ure_ajax()

    
}
// end of class User_Role_Editor_Pro