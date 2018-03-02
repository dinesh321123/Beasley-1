<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of URE_Content_View_Restrictions
 *
 * @author vladimir
 */
class URE_Content_View_Restrictions {
    
    const view_posts_access_cap = 'ure_view_posts_access';
    const content_for_roles = 'ure_content_for_roles';
    const prohibit_allow_flag = 'ure_prohibit_allow_flag';
    
    private $lib;
    private $prohibited_message = '';
    
    public function __construct($lib) {
        $this->lib = $lib;
        $this->prohibited_message = esc_html__('You have no enough permissions to view this page', 'URE');
        add_action('add_meta_boxes', array($this, 'add_post_meta_box'));
        add_action('admin_enqueue_scripts', array($this, 'admin_css_action'));
        add_action('admin_enqueue_scripts', array($this, 'admin_load_js'));                
        add_action('save_post', array($this, 'save_meta_data'));
        add_filter('the_posts', array($this, 'hide_prohibited_posts'));
        add_filter('get_pages', array($this, 'hide_prohibited_pages'));
        add_filter('the_content', array($this, 'restrict'));  
        add_filter('auth_post_meta_'. self::content_for_roles, array($this, 'auth_post_meta'), 10, 6);
        add_filter('auth_post_meta_'. self::prohibit_allow_flag, array($this, 'auth_post_meta'), 10, 6);
    }
    // end of __construct()
    
    
    // block access to URE's post meta (custom) fields, if user does not have enough permissions
    public function auth_post_meta($allowed, $meta_key, $post_id, $user_id, $cap, $caps) {
        
        $allowed = current_user_can(self::view_posts_access_cap);
        
        return $allowed;
        
    }
    // end of auth_post_meta()
    
    
    public function add_post_meta_box() {

        if (!current_user_can(self::view_posts_access_cap)) {
            return;
        }
        $post_types = get_post_types(array('public'=>true, 'show_ui'=>true));
        foreach ($post_types as $post_type) {
            add_meta_box(
                    'ure_content_view_restrictions_meta_box', 
                    'Content View Restrictions', 
                    array($this, 'render_post_meta_box'),
                    $post_type, 
                    'normal', 
                    'default'
            );
        }
    }
    // end of add_meta_box()
    
    
    /**
     * Output needed HTML for metadata meta box
     * 
     */
    function render_post_meta_box($post) {
        global $wp_roles;
        
        /*
         * Use get_post_meta() to retrieve an existing value
         * from the database and use the value for the form.
         */
        $ure_prohibit_allow_flag = get_post_meta($post->ID, self::prohibit_allow_flag, true);
        $selected1 = (empty($ure_prohibit_allow_flag) || $ure_prohibit_allow_flag==1) ? 'checked' : '';
        $selected2 = ($ure_prohibit_allow_flag==2) ? 'checked' : '';
        $ure_content_for_roles = get_post_meta($post->ID, self::content_for_roles, true);
        $selected_roles = explode(', ', $ure_content_for_roles);
        $roles_list = '';
        foreach($wp_roles->roles as $role_id=>$role_data) {
            if (in_array($role_id, $selected_roles)) {
                $role_selected = 'checked';
            } else {
                $role_selected = '';
            }
            $roles_list .= '<input type="checkbox" id="'. $role_id .'" name="'. $role_id .'" value="1" '. $role_selected .'>&nbsp'.
                           '<label for="'. $role_id .'">' .$role_data['name'] .' ('. $role_id .')</label><br>'."\n";
        }        
        
        // Add an nonce field so we can check for it later.
        wp_nonce_field('ure_content_view_restrictions_meta_box', 'ure_content_view_restrictions_meta_box_nonce');
    ?>
<strong><?php esc_html_e('Action:','ure');?></strong>&nbsp;&nbsp;<input type="radio" id="ure_prohibit_flag" name="ure_prohibit_allow_flag" value="1"  <?php echo $selected1;?> > <label for="ure_prohibit_flag">Prohibit Access</label>&nbsp;
<input type="radio" id="ure_allow_flag" name="ure_prohibit_allow_flag" value="2"  <?php echo $selected2;?> > <label for="ure_allow_flag">Allow Access</label><br>
<strong><?php esc_html_e('for Roles:','ure');?></strong>&nbsp;&nbsp;<button id="edit_content_for_roles">Edit Roles List</button><br>
<textarea id="ure_content_for_roles" name="ure_content_for_roles" rows="3" style="width: 100%;" readonly="readonly"><?php echo $ure_content_for_roles;?></textarea>
<div style="text-align: right; color: #cccccc; font-size: 0.8em;">User Role Editor Pro</div>

<div id="edit_roles_list_dialog" style="display: none;">
    <div id="edit_roles_list_dialog_content" style="padding:10px;">
        <?php echo $roles_list; ?>
    </div>    
</div>    
    <?php        
    }
    // end of hpn_render_meta_box()


   /**
     * Load plugin javascript stuff
     * 
     * @param string $hook_suffix
     */
    public function admin_load_js($hook_suffix) {
        if ($hook_suffix !== 'post.php') {
            return;
        }   
                
        if (!current_user_can(self::view_posts_access_cap)) {
            return;
        }
        wp_enqueue_script('jquery-ui-dialog', false, array('jquery-ui-core', 'jquery-ui-button', 'jquery'));            
        wp_register_script('ure-pro-content-view-restrictions', plugins_url('/js/pro/ure-pro-content-view-restrictions.js', URE_PLUGIN_FULL_PATH));
        wp_enqueue_script('ure-pro-content-view-restrictions');
        wp_localize_script('ure-pro-content-view-restrictions', 'ure_data_pro', array(
            'wp_nonce' => wp_create_nonce('user-role-editor'),
            'edit_content_for_roles' => esc_html__('Edit Roles List', 'ure'),
            'edit_content_for_roles_title' => esc_html__('Roles List restrict/allow content view', 'ure'),
            'save_roles_list' => esc_html__('Save', 'ure'),
            'close' => esc_html__('Close', 'ure')
        ));

    }
    // end of admin_load_js()
    
    
    public function admin_css_action($hook_suffix) {        
        if ($hook_suffix!=='post.php') {
            return;
        }
        if (!current_user_can(self::view_posts_access_cap)) {
            return;
        }
        
        wp_enqueue_style('wp-jquery-ui-dialog');

    }
    // end of admin_css_action()
    
    
    // Check the user's permissions.
    protected function can_edit($post) {
        
        if (!is_a( $post, 'WP_Post' )) {
            $post = get_post($post);            
        }
        $post_id = $post->ID;
        $post_type = $post->post_type;
        $post_type_obj = get_post_type_object($post_type);
        if (!current_user_can($post_type_obj->cap->edit_post, $post_id)) {
            return false;
        }
        
        return true;
    }
    // end of can_edit()
    
    
    protected function check_security($post_id) {

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }

        // Verify that the nonce is valid.
        $nonce = filter_input(INPUT_POST, 'ure_content_view_restrictions_meta_box_nonce', FILTER_SANITIZE_STRING);                
        if (empty($nonce) || !wp_verify_nonce($nonce, 'ure_content_view_restrictions_meta_box')) {
            return false;
        }

        if (!current_user_can(self::view_posts_access_cap)) {
            return false;
        }
        
        if (!$this->can_edit($post_id)) {
            return false;
        }
        
        return true;        
    }
    // end of check_security()
    
    
    // Save meta data with post/page data save event together
    public function save_meta_data($post_id) {

        global $wp_roles;
        
        if (!$this->check_security($post_id)) {
            return $post_id;
        }
        /* OK, its safe for us to save the data now. */
        // Sanitize the user input.
        $ure_prohibit_allow_flag = sanitize_key($_POST['ure_prohibit_allow_flag']);
        // Update the meta field.
        update_post_meta($post_id, self::prohibit_allow_flag, $ure_prohibit_allow_flag);

        $ure_content_for_roles0 = sanitize_text_field($_POST['ure_content_for_roles']);
        // Update the meta field.
        $roles_to_check = explode(',', $ure_content_for_roles0);
        $roles_to_save = array();
        foreach($roles_to_check as $role) {
            $role = trim($role);
            if (isset($wp_roles->roles[$role])) {
                $roles_to_save[] = $role;
            }
        }
        $ure_content_for_roles1 = implode(', ', $roles_to_save);
        update_post_meta($post_id, self::content_for_roles, $ure_content_for_roles1);
    }
    // end of save_meta_data()

    
    public function restrict($content) {
        
        global $post;
        
        if (empty($post->ID)) { 
            return $content;
        }
        
        if (current_user_can(self::view_posts_access_cap)) {
            return $content;
        }
        
        // no restrictions for users who may edit this post
        if ($this->can_edit($post)) {
            return $content;
        }
        
        $ure_prohibit_allow_flag = get_post_meta($post->ID, self::prohibit_allow_flag, true);
        $ure_content_for_roles = get_post_meta($post->ID, self::content_for_roles, true);
        if (empty($ure_content_for_roles)) {
            return $content;
        }
        $roles = explode(', ', $ure_content_for_roles);
        if (count($roles)==0) {
            return $content;
        }

        if (!is_user_logged_in()) {
            return $this->prohibited_message;
        } else if ($ure_prohibit_allow_flag==1) {  
            $result0 = $content;
            $result1 = $this->prohibited_message;    // for prohibited access
        } else {    
            $result0 = $this->prohibited_message;
            $result1 = $content;     // for allowed access
        }
        
        foreach($roles as $role) {
            if (current_user_can($role)) {
                return $result1;
            }
        }
        
        return $result0;
    }
    // end of restrict()
    
    
    protected function is_post_allowed($post) {
        
        
        $ure_prohibit_allow_flag = get_post_meta($post->ID, self::prohibit_allow_flag, true);
        $ure_content_for_roles = get_post_meta($post->ID, self::content_for_roles, true);
        if (empty($ure_content_for_roles)) {
            return true;
        }
        $roles = explode(', ', $ure_content_for_roles);
        if (count($roles)==0) {
            return true;
        }

        // no restrictions for users who may edit this post/page
        if ($this->can_edit($post)) {
            return true;
        }
        
        if (!is_user_logged_in()) {
            return false;
        } elseif ($ure_prohibit_allow_flag==1) {  
            $result0 = true;
            $result1 = false;    // for prohibited access
        } else {    
            $result0 = false;
            $result1 = true;     // for allowed access
        }
        
        foreach($roles as $role) {
            if (current_user_can($role)) {
                return $result1;
            }
        }
        
        return $result0;
    }
    // end of is_post_allowed()
    
    
    public function hide_prohibited_posts($posts) {
        
        if (count($posts)==0) {
            return $posts;
        }
        $post_type_obj = get_post_type_object($posts[0]->post_type);
        if (current_user_can($post_type_obj->cap->edit_others_posts)) {
            return $posts;
        }
        $posts1 = array();
        foreach($posts as $post) {
            if ($this->is_post_allowed($post)) {
                $posts1[] = $post;
            }
        }

        return $posts1;
    }
    // end of hide_prohibited_posts()
    
    
    public function hide_prohibited_pages($pages) {
        
        if (count($pages)==0) {
            return $pages;
        }
        
        if (current_user_can('edit_others_pages')) {
            return $pages;
        }
        $pages1 = array();
        foreach($pages as $page) {
            if ($this->is_post_allowed($page)) {
                $pages1[] = $page;
            }
        }

        return $pages1;
    }
    // end of hide_prohibited_pages()
        
}
// end of URE_Content_View_Restrictions class
