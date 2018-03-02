<?php

/*
 * User Role Editor WordPress plugin
 * Class URE_Other_Roles - support stuff for "Other Roles Access" add-on
 * Author: Vladimir Garagulya
 * Author email: support@role-editor.com
 * Author URI: https://www.role-editor.com
 * License: GPL v2+ 
 */

class URE_Other_Roles {

    private $lib = null;
    const ACCESS_DATA_KEY = 'ure_other_roles_access_data';
    
    
    public function __construct($lib) {
        
        $this->lib = $lib;
        
    }
    // end of __construct()
    
        
    /**
     * Load access data for role
     * @param string $role_id
     * @return array
     */
    public function load_access_data_for_role($role_id) {
        
        $access_data = get_option(self::ACCESS_DATA_KEY);
        if (is_array($access_data) && array_key_exists($role_id, $access_data)) {
            $result =  $access_data[$role_id];
        } else {
            $result = array();
        }
        
        return $result;
    }
    // end of load_access_data_for_role()
    
    
    public function load_access_data_for_user($user) {
    
        if (is_object($user)) {
            $id = $user->ID;
        } else if (is_int($user)) {
            $id = $user;
            $user = get_user_by('id', $user);
        } else {
            $user = get_user_by('login', $user);
            $id = $user->ID;
        }
        
        $blocked = get_user_meta($user->ID, self::ACCESS_DATA_KEY, true);
        if (!is_array($blocked)) {
            $blocked = array();
        }
        
        $access_data = get_option(self::ACCESS_DATA_KEY);
        if (empty($access_data)) {
            $access_data = array();
        }
        
        foreach ($user->roles as $role) {
            if (isset($access_data[$role])) {
                $blocked = array_merge($blocked, $access_data[$role]);
            }
        }
        
        $blocked = array_unique ($blocked);
        
        return $blocked;
    }
    // end of load_access_data_for_user()

    
    protected function get_access_data_from_post() {
        
        $keys_to_skip = array('action', 'ure_nonce', '_wp_http_referer', 'ure_object_type', 'ure_object_name', 'user_role');
        $access_data = array();
        foreach ($_POST as $key=>$value) {
            if (in_array($key, $keys_to_skip)) {
                continue;
            }
            $access_data[] = $key;
        }
        
        return $access_data;
    }
    // end of get_access_data_from_post()
        
    
    public function save_access_data_for_role($role_id) {
        $access_for_role = $this->get_access_data_from_post();
        $access_data = get_option(self::ACCESS_DATA_KEY);        
        if (!is_array($access_data)) {
            $access_data = array();
        }
        if (count($access_for_role)>0) {
            $access_data[$role_id] = $access_for_role;
        } else {
            unset($access_data[$role_id]);
        }
        update_option(self::ACCESS_DATA_KEY, $access_data);
    }
    // end of save_access_data_for_role()
    
    
    public function save_access_data_for_user($user_login) {
        $access_for_user = $this->get_access_data_from_post();
        // TODO ...
    }
    // end of save_menu_access_data_for_role()   
                    
    
    protected function get_allowed_roles($user) {
        $allowed_roles = array();
        if (empty($user)) {   // request for Role Editor - work with currently selected role
            $current_role = filter_input(INPUT_POST, 'current_role', FILTER_SANITIZE_STRING);
            $allowed_roles[] = $current_role;
        } else {    // request from user capabilities editor - work with that user roles
            $allowed_roles = $user->roles;
        }
        
        return $allowed_roles;
    }
    // end of get_allowed_roles()
                            
    
    public function get_html($user=null) {        
                        
        $allowed_roles = $this->get_allowed_roles($user);                
        $roles = $this->lib->get_user_roles();
        if (empty($user)) {
            $ure_object_type = 'role';
            $ure_object_name = $allowed_roles[0];
            $blocked_items = $this->load_access_data_for_role($ure_object_name);
        } else {
            $ure_object_type = 'user';
            $ure_object_name = $user->user_login;
            $blocked_items = $this->load_access_data_for_user($ure_object_name);
        }
        
        ob_start();
?>
<form name="ure_other_roles_access_form" id="ure_other_roles_access_form" method="POST"
      action="<?php echo URE_WP_ADMIN_URL . URE_PARENT.'?page=users-'.URE_PLUGIN_FILE;?>" >
<table id="ure_other_roles_access_table">
    <th style="color:red;"><?php esc_html_e('Block', 'ure');?></th>
    <th><?php esc_html_e('Role Name','ure');?></th>
    <th><?php esc_html_e('Role ID', 'ure');?></th>    
<?php
        foreach($roles as $role_id=>$role) {            
?>
    <tr>
        <td>   
<?php     
            $checked = in_array($role_id, $blocked_items) ? 'checked' : '';
            if ($role_id=='administrator') {
                $disabled = 'disabled';
                $checked = 'checked';                               
            } else {
                $disabled = '';
            }
?>
            <input type="checkbox" name="<?php echo $role_id;?>" id="<?php echo $role_id;?>" <?php echo $checked .' '. $disabled;?> />
        </td>
        <td><?php echo $role['name'];?></td>
        <td><?php echo $role_id;?></td>
    </tr>        
<?php
        }   // foreach($this->lib->roles)
?>
</table> 
    <input type="hidden" name="action" id="action" value="ure_update_other_roles_access" />
    <input type="hidden" name="ure_object_type" id="ure_object_type" value="<?php echo $ure_object_type;?>" />
    <input type="hidden" name="ure_object_name" id="ure_object_name" value="<?php echo $ure_object_name;?>" />
<?php
    if ($ure_object_type=='role') {
?>
    <input type="hidden" name="user_role" id="ure_role" value="<?php echo $ure_object_name;?>" />
<?php
    }
?>
    <?php wp_nonce_field('user-role-editor', 'ure_nonce'); ?>
</form>    
<?php    
        $html = ob_get_contents();
        ob_end_clean();
        
        if (!empty($user)) {
            $current_object = $user->user_login;
        } else {
            $current_object = $allowed_roles[0];
        }
     
        return array('result'=>'success', 'message'=>'Widgets permissions for '+ $current_object, 'html'=>$html);
    }
    // end of get_html()

}
// end of URE_Other_Roles class
