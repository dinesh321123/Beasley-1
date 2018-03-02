<?php

/*
 * User Role Editor WordPress plugin
 * Class URE_Admin_Menu_Access - prohibit selected menu items for role or user
 * Author: Vladimir Garagulya
 * Author email: support@role-editor.com
 * Author URI: https://www.role-editor.com
 * License: GPL v2+ 
 */

class URE_Widgets_Access {

// reference to the code library object
    private $lib = null;    
    private $objects = null;
    private $notice = '';

    public function __construct($lib) {
        
        $this->lib = $lib;
        $this->objects = new URE_Widgets($this->lib);
        
        add_action('ure_role_edit_toolbar_service', array(&$this, 'add_toolbar_buttons'));
        add_action('ure_load_js', array(&$this, 'add_js'));
        add_action('ure_dialogs_html', array(&$this, 'dialog_html'));
        add_action('ure_process_user_request', array(&$this, 'update_access'));
        add_action('ure_process_user_request', array(&$this, 'update_access_notification'));        
        add_action('widgets_init', array($this, 'unregister_blocked_widgets'), 100);
    }
    // end of __construct()

    
    public function add_toolbar_buttons() {
        if (current_user_can('ure_widgets_access')) {
?>                
        <button id="ure_widgets_access_button" class="ure_toolbar_button" title="<?php esc_html__('Prohibit access to selected widgets','ure');?>">Widgets</button>                     
<?php

        }
    }
    // end of add_toolbar_buttons()


    public function add_js() {
        wp_register_script( 'ure-widgets-access', plugins_url( '/js/pro/ure-pro-widgets-access.js', URE_PLUGIN_FULL_PATH ) );
        wp_enqueue_script ( 'ure-widgets-access' );
        wp_localize_script( 'ure-widgets-access', 'ure_data_widgets_access',
                array(
                    'widgets' => esc_html__('Widgets', 'ure'),
                    'dialog_title' => esc_html__('Widgets', 'ure'),
                    'update_button' => esc_html__('Update', 'ure'),
                    'edit_theme_options_required' => esc_html__('Turn ON "edit_theme_options" capability to manage widgets permissions', 'ure')
                ));
    }
    // end of add_js()    
    
    
    public function dialog_html() {
        
?>
        <div id="ure_widgets_access_dialog" class="ure-modal-dialog">
            <div id="ure_widgets_access_container">
            </div>    
        </div>
<?php        
        
    }
    // end of dialog_html()

            
    public function update_access() {
    
        if (!isset($_POST['action']) || $_POST['action']!=='ure_update_widgets_access') {
            return;
        }
        
        if (!current_user_can('ure_widgets_access')) {
            $this->notice = esc_html__('URE: you do not have enough permissions to access this module.', 'ure');
            return;
        }
        
        $ure_object_type = filter_input(INPUT_POST, 'ure_object_type', FILTER_SANITIZE_STRING);
        if ($ure_object_type!=='role' && $ure_object_type!=='user') {
            $this->notice = esc_html__('URE: widgets access: Wrong object type. Data was not updated.', 'ure');
            return;
        }
        $ure_object_name = filter_input(INPUT_POST, 'ure_object_name', FILTER_SANITIZE_STRING);
        if (empty($ure_object_name)) {
            $this->notice = esc_html__('URE: widgets access: Empty object name. Data was not updated', 'ure');
            return;
        }
                        
        if ($ure_object_type=='role') {
            $this->objects->save_access_data_for_role($ure_object_name);
        } else {
            $this->objects->save_access_data_for_user($ure_object_name);
        }
        
    }
    // end of update_menu()
    
    
    public function update_access_notification() {
        $this->lib->show_message($this->notice);
    }
    // end of update_menu_access_notification()
       
    
    public function unregister_blocked_widgets() {
        global $current_user;
        
        if ($this->lib->multisite && is_super_admin()) {
            return;
        }
        
        if ($this->lib->user_has_capability($current_user, 'administrator')) {
            return;
        }
        
        $blocked = $this->objects->load_access_data_for_user($current_user);
        if (empty($blocked)) {
            return;
        }
                
        foreach($blocked as $widget) {
            unregister_widget($widget);            
        }        
        
    }
    // end of unregister_blocked_widgets()
                        
}
// end of URE_Widgets_Access class
