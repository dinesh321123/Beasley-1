<?php
/*
 * Class: Access restrict to posts/pages on per site - per user - per post/page basis 
 * part of User Role Editor Pro plugin
 * Author: Vladimir Garagulya
 * email: vladimir@shinephp.com
 * 
 */

class URE_Posts_Edit_Access {
    
    private $lib = null;
    
    private $umk_posts_list = '';    // user meta key for - post IDs list
    private $umk_posts_restriction_type = '';   // user meta key for - allow or prohibit post edit by its ID
    private $umk_post_types = '';   // user meta key for - post types
    
    private $umk_post_authors_list = '';    // user meta key for - post IDs list
    
    private $posts_list = null;
    private $attachments_list = null;
    private $sticky_posts_count = 0;
    private $user_posts_count = 0;
    private $screen;
    
    
    public function __construct(Ure_Lib $lib) {
    
        global $wpdb;
        
        $this->lib = $lib;
        
        $this->umk_posts_list = $wpdb->prefix . 'ure_posts_list';    // comma separated posts/pages ID list
        $this->umk_posts_restriction_type = $wpdb->prefix . 'ure_posts_restriction_type';   // Allow or Prohibit to edit posts/pages from the list of post IDs
        $this->umk_post_types = $wpdb->prefix . 'ure_post_types';        // Posts types from the list of post IDs
        
        $this->umk_post_authors_list = $wpdb->prefix . 'ure_authors_list'; // comma separated list of users ID, which posts it is allowed/prohibeted to edit
                
        add_action( 'edit_user_profile', array($this, 'edit_user_posts_list'), 10, 2 );
        add_action( 'profile_update', array($this, 'save_user_posts_list'), 10 );
        add_action( 'admin_init', array($this, 'set_final_hooks') );
        
        new URE_Posts_Edit_Access_Bulk_Action($lib);
    }
    // end of __construct()                
    
    
    protected function is_restriction_applicable() {
        
        global $current_user;
        
        if ( $this->lib->user_is_admin($current_user->ID) ) {
            return false;
        }
                        
        $caps = $this->lib->get_edit_custom_post_type_caps();
        $min_cap = $this->lib->user_can_which($current_user, $caps);
        
        return !empty($min_cap);
    }
    // end of is_restriction_applicable()
    
    
    public function set_final_hooks() {
                
        if ($this->is_restriction_applicable()) {
            // post_query possibly!!!
            add_filter('pre_get_posts', array(&$this, 'restrict_posts_list' ));
            // set filters for the correct view count
            $post_types = get_post_types(array('public'=>true, 'show_ui'=>true));
            foreach($post_types as $post_type ){
                add_filter('views_edit-'.$post_type, array($this, 'get_views'));
            }
            //add_filter('map_meta_cap', array($this, 'block_edit_post'), 10, 4);
        }
        
    }
    // end of set_final_hooks()
    
    
    public function block_edit_post($caps, $cap='', $user_id=0, $args=array()) {
        
        global $current_user;
        
        $posts_list = $this->get_posts_list();
        if (count($posts_list)==0) {
            return $caps;
        }        
        $posts_restriction_type = get_user_meta($current_user->ID, $this->umk_posts_restriction_type, 1);
        $post_types = get_user_meta($current_user->ID, $this->umk_post_types, 1);
        
        $custom_caps = $this->lib->get_edit_custom_post_type_caps();
        if (in_array($cap, $custom_caps)) {
            if (count($args)>0) {
                $post_id = $args[0];
            } else {
                $post_id = filter_input(INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT);
            }
            if (empty($post_id)) {
                return $caps;
            }
            $post = get_post($post_id);
            if (is_array($post_types) && in_array($post->post_type, $post_types)) {                
                $do_not_allow = in_array($post_id, $posts_list);    // not edit these
                if ($posts_restriction_type==1) {
                    $do_not_allow = !$do_not_allow;   // not edit others
                }
                if ($do_not_allow) {
                    $caps[] = 'do_not_allow';
                }
            }
        }
        
        return $caps;
    }
    // end of block_edit_post()
    
    
    public function edit_user_posts_list($user) {

        $result = stripos($_SERVER['REQUEST_URI'], 'network/user-edit.php');
        if ($result !== false) {  // exit, this code just for single site user profile only, not for network admin center
            return;
        }
        if ($this->is_restriction_applicable()) {
            return;
        }
        
        if (!current_user_can('ure_edit_posts_access')) {
            return;
        }
        
        // by post ID
        $posts_restriction_type = get_user_meta($user->ID, $this->umk_posts_restriction_type, 1);
        if (empty($posts_restriction_type)) {
            $posts_restriction_type = 1;
        }
        $checked1 = ($posts_restriction_type==1) ? 'checked' : '';
        $checked2 = ($posts_restriction_type==2) ? 'checked' : '';
        $posts_list = get_user_meta($user->ID, $this->umk_posts_list, true);
        
        // by post author
        $post_authors_list = get_user_meta($user->ID, $this->umk_post_authors_list, true);        

        $caps = $this->lib->get_edit_custom_post_type_caps();
        if ( $this->lib->user_can_which( $user, $caps) ) {        
?>        
        <h3><?php _e('Posts/Pages Editor Restrictions', 'ure'); ?></h3>
        <table class="form-table">
            <tr>
                <th scope="role">
                    <input type="radio" name="ure_posts_restriction_type" id="ure_posts_restriction_type1" value="1" <?php  echo $checked1;?> >
                    <label for="ure_posts_restriction_type1"><?php esc_html_e('Allow', 'ure'); ?></label>
                    <input type="radio" name="ure_posts_restriction_type" id="ure_posts_restriction_type2" value="2" <?php  echo $checked2;?> >
                    <label for="ure_posts_restriction_type2"><?php esc_html_e('Prohibit', 'ure'); ?></label>
                <th>
                <td>
                </td>    
            </tr>    
        		<tr>
        			<th scope="row">               
               <?php esc_html_e('edit posts/pages/custom post types with ID (comma separated) ', 'ure'); ?>
           </th>
        			<td>
               <input type="text" name="ure_posts_list" id="ure_posts_list" value="<?php echo $posts_list; ?>" size="40" />
        			</td>
        		</tr>         
<?php
            if ( $this->lib->user_can_which( $user, array('edit_others_posts', 'edit_others_pages') ) ) {
?>
          <tr>
        			<th scope="row">
               <?php esc_html_e('edit posts/pages with author user ID (comma separated) ', 'ure'); ?>
           </th>
        			<td>
               <input type="text" name="ure_post_authors_list" id="ure_post_authors_list" value="<?php echo $post_authors_list; ?>" size="40" />
        			</td>
        		</tr>
<?php
            }
?>
        </table>		                
<?php
        }
    }
    // end of set_user_posts_list()   
    
    
    // save posts list when user profile is updated, as WordPress itself doesn't know about it
    public function save_user_posts_list($user_id) {

        global $wpdb;
        
        if (!current_user_can('edit_users', $user_id) || !current_user_can('ure_edit_posts_access')) {
            return;
        }
        
        if (isset($_POST['ure_posts_restriction_type'])) {
            $posts_restriction_type = $_POST['ure_posts_restriction_type'];
            if ($posts_restriction_type!=1 && $posts_restriction_type!=2) {  // sanitize user input
                $posts_restriction_type = 1;
            }
            update_user_meta($user_id, $this->umk_posts_restriction_type, $posts_restriction_type);
        }
        
        // update posts edit by post ID restriction: comma separated posts IDs list
        if (!empty($_POST['ure_posts_list'])) {
            $posts_list = explode(',', trim($_POST['ure_posts_list']));
            if (count($posts_list)>0) {
                $posts_list_str = $this->lib->filter_int_array_to_str($posts_list);
                $query = "select distinct post_type from {$wpdb->posts} where ID in ($posts_list_str);";
                $post_types1 = $wpdb->get_col($query);
                update_user_meta($user_id, $this->umk_posts_list, $posts_list_str);
            }            
        } else {
            delete_user_meta($user_id, $this->umk_posts_list);
        }        
        
        // update posts edit by author ID restriction: comma separated authors IDs list
        if (!empty($_POST['ure_post_authors_list'])) {
            $authors_list = explode(',', trim($_POST['ure_post_authors_list']));
            if (count($authors_list)>0) {
                $post_authors_list_str = $this->lib->filter_int_array_to_str($authors_list);
                $query = "select distinct post_type
                            from {$wpdb->posts}
                            where post_author in ($post_authors_list_str) and post_status!='inherit' and post_status!='revision';";
                $post_types2 = $wpdb->get_col($query);                
                update_user_meta($user_id, $this->umk_post_authors_list, $post_authors_list_str);
            }            
        } else {
            delete_user_meta($user_id, $this->umk_post_authors_list);            
        }
        
        if (isset($post_types1) || isset($post_types2)) {
            if ((isset($post_types1) && is_array($post_types1)) && 
                (isset($post_types2) && is_array($post_types2))) {
                $post_types = array_merge($post_types1, $post_types2);
            } elseif (isset($post_types1) && is_array($post_types1)) {
                $post_types = $post_types1;
            } elseif (isset($post_types2) && is_array($post_types2)) {
                $post_types = $post_types2;
            } else {
                $post_types = null;
            }
            if (!empty($post_types)) {
                update_user_meta($user_id, $this->umk_post_types, $post_types);
            } else {
                delete_user_meta($user_id, $this->umk_post_types);
            }
        } else {
            delete_user_meta($user_id, $this->umk_post_types);
        }
                
    }
    // end of save_user_posts_list()    

    
    private function get_posts_list_by_authors() {
        
        global $current_user, $wpdb;
                
        $post_authors_list = get_user_meta($current_user->ID, $this->umk_post_authors_list, true);
        if (empty($post_authors_list)) {
            return array();
        }
        $posts_restriction_type = get_user_meta($current_user->ID, $this->umk_posts_restriction_type, true);
        if ($posts_restriction_type==1) {   // allow
            $authors = explode(',', $post_authors_list);
            if (!in_array($current_user->ID, $authors)) {
                // add user himself to the authors list to allow him edit his own posts/pages
                $post_authors_list .= ', '. $current_user->ID;
            }
        }
        $query = "select ID
                    from {$wpdb->posts}
                    where post_author in ($post_authors_list) and post_status!='inherit' and post_status!='revision';";
        $post_ids = $wpdb->get_col($query);
        if (!is_array($post_ids)) {
            return array();
        }
        
        return $post_ids;
    }
    // end of get_posts_list_by_authors()
    
    
    private function get_posts_list() {
        
        global $current_user;
    
        if ($this->posts_list==null) {
            $posts_list1 = array();
            $posts_list_str = get_user_meta($current_user->ID, $this->umk_posts_list, true);
            if (!empty($posts_list_str)) {
                $posts_list1 = explode(',', $posts_list_str);
            }
            $posts_list2 = $this->get_posts_list_by_authors();
            $this->posts_list = array_merge($posts_list1, $posts_list2);
            for ($i=0; $i<count($this->posts_list); $i++) {
                $this->posts_list[$i] = trim($this->posts_list[$i]);
            }
        }
        
        return $this->posts_list;
    }
    // end of get_posts_list()
    
    
    private function get_attachments_list() {
        
        global $wpdb;
    
        if ($this->attachments_list==null) {
            $posts_list = $this->get_posts_list();
            if (is_array($posts_list) && count($this->posts_list)>0) {
                $parents_list = implode(',', $this->posts_list);
                $query = "SELECT ID from $wpdb->posts WHERE post_parent in ($parents_list)";
                $this->attachments_list = $wpdb->get_col($query);
            }
        }
        
        return $this->attachments_list;
    }
    // end of get_attachments_list()
    
                                    
    public function restrict_posts_list($query) {
        
        global $current_user, $pagenow;
        
        if (!($pagenow == 'edit.php' || $pagenow == 'upload.php' || 
            ($pagenow=='admin-ajax.php' && !empty($_POST['action']) && $_POST['action']=='query-attachments'))) {
            return $query;
        }
        
        // do not limit user with Administrator role
        if (!$this->is_restriction_applicable()) {
            return $query;
        }

        $suppressing_filters = $query->get('suppress_filters'); // Filter suppression on?

        if ($suppressing_filters) {
            return $query;
        }   
        
        $post_types = get_user_meta($current_user->ID, $this->umk_post_types, true);
        if (is_array($post_types) && in_array($query->query['post_type'], $post_types)) {
            $posts_restriction_type = get_user_meta($current_user->ID, $this->umk_posts_restriction_type, true);
            $posts_list = $this->get_posts_list();
            if ($posts_restriction_type==1) {   // Allow
                $query->set('post__in', $posts_list);
            } else {    // Prohibit
                $query->set('post__not_in', $posts_list);
            }
        } elseif ($query->query['post_type']=='attachment') {
            $posts_restriction_type = get_user_meta($current_user->ID, $this->umk_posts_restriction_type, true);
            $attachments_list = $this->get_attachments_list();
            if ($posts_restriction_type==1) {   // Allow
                $query->set('post__in', $attachments_list);
            } else {    // Prohibit
                $query->set('post__not_in', $attachments_list);
            }
        }        
                       
        return $query;
    }
    // restrict_posts_list()


    /**
     * Initally was taken from Admin for Authors plugin by Marcus Sykes (http://msyk.es)
     * Modified by Vladimir Garagulya (role-editor.com)
     * 
     */
    protected function count_posts($type = 'post', $perm = '') {
        global $wpdb, $current_user;

        $user = wp_get_current_user();

        $cache_key = $type . '_' . $user->ID;

        $query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s";

        if ('readable' == $perm && is_user_logged_in()) {
            
            $posts_list = $this->get_posts_list();
            $posts_restriction_type = get_user_meta($current_user->ID, $this->umk_posts_restriction_type, true);
            if (count($posts_list)>0) {
                $posts_list_str = implode(',', $posts_list);
                if ($posts_restriction_type==1) {
                    $query .= " AND ID in ($posts_list_str)";
                } else {
                    $query .= " AND ID not in ($posts_list_str)";
                }
            }
            $post_type_object = get_post_type_object($type);
            if (!current_user_can($post_type_object->cap->read_private_posts)) {
                $cache_key .= '_' . $perm . '_' . $user->ID;
                $query .= " AND (post_status != 'private' OR ( post_author = '$user->ID' AND post_status = 'private' ))";
            }
        }
        $query .= ' GROUP BY post_status';

        $count = wp_cache_get($cache_key, 'counts');
        if (false !== $count)
            return $count;

        $count = $wpdb->get_results($wpdb->prepare($query, $type), ARRAY_A);

        $stats = array();
        foreach (get_post_stati() as $state)
            $stats[$state] = 0;

        foreach ((array) $count as $row)
            $stats[$row['post_status']] = $row['num_posts'];

        $stats = (object) $stats;
        wp_cache_set($cache_key, $stats, 'counts');

        return $stats;
    }
    // end of count_posts()


    /**
     * Modification to this WP function was done by Marcus Sykes (http://msyk.es)
     * His comments follow untouched:
     * Almost-exact copy of WP_Posts_List_Table::get_views(), but makes subtle changes for $this references and calls internal Admin_For_Authors::wp_count_posts() function instead
	    * Changes highlighted with comments starting //EDIT 
	    */
    public function get_views() {
        global $locked_post_status, $avail_post_stati;

        $this->screen = get_current_screen(); //EDIT - get $screen for use on $this->screen
        $post_type = $this->screen->post_type;

        if (!empty($locked_post_status))
            return array();

        $status_links = array();
        $num_posts = $this->count_posts($post_type, 'readable');
        $class = '';
        $allposts = '';

        $current_user_id = get_current_user_id();

        if ($this->user_posts_count) {
            if (isset($_GET['author']) && ( $_GET['author'] == $current_user_id ))
                $class = ' class="current"';
            $status_links['mine'] = "<a href='edit.php?post_type=$post_type&author=$current_user_id'$class>" . sprintf(_nx('Mine <span class="count">(%s)</span>', 'Mine <span class="count">(%s)</span>', $this->user_posts_count, 'posts'), number_format_i18n($this->user_posts_count)) . '</a>';
            $allposts = '&all_posts=1';
        }

        $total_posts = array_sum((array) $num_posts);

        // Subtract post types that are not included in the admin all list.
        foreach (get_post_stati(array('show_in_admin_all_list' => false)) as $state)
            $total_posts -= $num_posts->$state;

        $class = empty($class) && empty($_REQUEST['post_status']) && empty($_REQUEST['show_sticky']) ? ' class="current"' : '';
        $status_links['all'] = "<a href='edit.php?post_type=$post_type{$allposts}'$class>" . sprintf(_nx('All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts'), number_format_i18n($total_posts)) . '</a>';

        foreach (get_post_stati(array('show_in_admin_status_list' => true), 'objects') as $status) {
            $class = '';

            $status_name = $status->name;

            if (!in_array($status_name, $avail_post_stati))
                continue;

            if (empty($num_posts->$status_name))
                continue;

            if (isset($_REQUEST['post_status']) && $status_name == $_REQUEST['post_status'])
                $class = ' class="current"';

            $status_links[$status_name] = "<a href='edit.php?post_status=$status_name&amp;post_type=$post_type'$class>" . sprintf(translate_nooped_plural($status->label_count, $num_posts->$status_name), number_format_i18n($num_posts->$status_name)) . '</a>';
        }

        //EDIT - START this whole if statement gets sticky posts stat, copied from WP_Posts_List_Table::_construct() but there's maybe a better way for this
        global $wpdb;
        if ('post' == $post_type && $sticky_posts = get_option('sticky_posts')) {
            $sticky_posts = implode(', ', array_map('absint', (array) $sticky_posts));
            $this->sticky_posts_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT( 1 ) FROM $wpdb->posts WHERE post_type = %s AND post_status != 'trash' AND ID IN ($sticky_posts)", $post_type));
        }
        //EDIT - END

        if (!empty($this->sticky_posts_count)) {
            $class = !empty($_REQUEST['show_sticky']) ? ' class="current"' : '';

            $sticky_link = array('sticky' => "<a href='edit.php?post_type=$post_type&amp;show_sticky=1'$class>" . sprintf(_nx('Sticky <span class="count">(%s)</span>', 'Sticky <span class="count">(%s)</span>', $this->sticky_posts_count, 'posts'), number_format_i18n($this->sticky_posts_count)) . '</a>');

            // Sticky comes after Publish, or if not listed, after All.
            $split = 1 + array_search(( isset($status_links['publish']) ? 'publish' : 'all'), array_keys($status_links));
            $status_links = array_merge(array_slice($status_links, 0, $split), $sticky_link, array_slice($status_links, $split));
        }

        return $status_links;
    }
    // end of get_views()

    
    protected function bulk_update_prepare() {
        global $wpdb;                
        
        
        if (!current_user_can('ure_edit_posts_access')) {
            $answer = array('result'=>'failure', 'message'=>html_esc__('You do not have enough permissions for this action.','ure'));
            return $answer;
        }
        $what_todo = $this->lib->get_request_var('what_todo', 'post', 'int');
        if ($what_todo!=1 && $what_todo!=2) {
            $what_todo = 1;
        }
        
        $post_ids_str = $this->lib->get_request_var('post_ids', 'post');
        
        $user_ids_str = $this->lib->get_request_var('user_ids', 'post');
        if (empty($user_ids_str)) {
            $answer = array('result'=>'failure', 'message'=>html_esc__('Provide users ID list.','ure'));
            return $answer;
        }
        
        $posts_ids_arr = explode(',',$post_ids_str);
        $posts_list_str = $this->lib->filter_int_array_to_str($posts_ids_arr);
        
        $users_ids_arr = explode(',',$user_ids_str);
        $users_ids_str1 = $this->lib->filter_int_array_to_str($users_ids_arr);
        if (empty($users_ids_str1)) {
            $answer = array('result'=>'failure', 'message'=>'Provide valid users ID list (integers separated by commas).');
            return $answer;
        }        
                
        $posts_restriction_type = $this->lib->get_request_var('posts_restriction_type', 'post', 'int');
        if ($posts_restriction_type!=1 && $posts_restriction_type!=2) {
            $posts_restriction_type = 1;
        }
        
        if (!empty($posts_list_str)) {
            $query = "select distinct post_type from {$wpdb->posts} where ID in ($posts_list_str);";
            $post_types = $wpdb->get_col($query);                
        } else {
            $post_types = array('post', 'page');
        }
        
        $result = array();
        $result['users_list'] = explode(',', $users_ids_str1);
        $result['what_todo'] = $what_todo;
        $result['posts_restriction_type'] = $posts_restriction_type;
        $result['posts_list_str'] = $posts_list_str;        
        $result['post_types'] = $post_types;
        
        return $result;
    }
    // end of bulk_update_prepare()
    

    protected function update_user_edit_restrictions($user_id, $what_todo, $posts_restriction_type, $posts_list, $post_types) {
        
        update_user_meta($user_id, $this->umk_posts_restriction_type, $posts_restriction_type);
        if ($what_todo==1) {    // add to existing data
            $current_posts_list = get_user_meta($user_id, $this->umk_posts_list, true);
            if (!empty($current_posts_list)) {
                if (!empty($posts_list)) {
                    $posts_list = $current_posts_list .','. $posts_list;
                } else {
                    $posts_list = $current_posts_list;
                }
            }
        }
        update_user_meta($user_id, $this->umk_posts_list, $posts_list);
        update_user_meta($user_id, $this->umk_post_types, $post_types);
        
    }
    // end of _save_user_posts_list()

    
    public function set_users_edit_restrictions() {
                
        $answer = $this->bulk_update_prepare();
        if (array_key_exists('result', $answer)) {
            return $answer;
        }
        
        extract($answer);   // create variables from array
        foreach($users_list as $user_id) {
            $this->update_user_edit_restrictions($user_id, $what_todo, $posts_restriction_type, $posts_list_str, $post_types);
        }
        
        $answer = array('result'=>'success', 'message'=>'Data updated successfully.');
        return $answer;
    } // end of set_users_edit_restrictions()
    
}
// end of URE_Posts_Edit_Access
