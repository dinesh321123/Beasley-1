<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Convert blocked menu items hashes from version 4.12 ot version 4.15 using changed format
 *
 * @author vladimir
 */
class URE_Update_Admin_Menu_Hashes {
    
    private static function calc_menu_item_id_412($menu_kind, $menu_item) {
        
        $prefix = strpos($menu_item, '?')===false ? 'admin.php?page=' : '';
        $item_id = md5($menu_kind . $prefix . $menu_item);
        
        return $item_id;
    }
    // end of calc_menu_item_id_412()
    
    
    private static function update_hash($prefix, $menu_item_key, &$menu_access_data) {
        
        $hash412 = self::calc_menu_item_id_412($prefix, $menu_item_key);
        foreach($menu_access_data as $role=>$access_data_for_role) {
            for($i=0; $i<count($access_data_for_role); $i++) {
                if ($access_data_for_role[$i]==$hash412) {
                    $menu_access_data[$role][$i] = URE_ADMIN_MENU::calc_menu_item_id($prefix, $menu_item_key);
                }
            }
        }
        
    }
    // end of update_hash()
    
    
    protected static function update_site() {
        $menu_access_data = get_option(URE_Admin_Menu::ADMIN_MENU_ACCESS_DATA_KEY);
        if (empty($menu_access_data)) { // nothing to update
            return;
        }
        $current_menu = get_option(URE_Admin_Menu::ADMIN_MENU_COPY_KEY);                
        foreach($current_menu as $menu_item) {
            self::update_hash('menu', $menu_item[2], $menu_access_data);
        } 
        $current_submenu = get_option(URE_Admin_Menu::ADMIN_SUBMENU_COPY_KEY);
        foreach($current_submenu as $submenu) {
            foreach($submenu as $menu_item) {
                self::update_hash('submenu', $menu_item[2], $menu_access_data);
            }
        }
        update_option(URE_Admin_Menu::ADMIN_MENU_ACCESS_DATA_KEY, $menu_access_data);        
    }
    // end of update_site()
    
    
    public static function act($lib) {
        global $wpdb;

        if ($lib->multisite && is_network_admin()) {    // assume network activation and update all sites
            $old_blog = $wpdb->blogid;
            foreach ($lib->blog_ids as $blog_id) {
                switch_to_blog($blog_id);
                self::update_site();
            }
            $lib->restore_after_blog_switching($old_blog);
        } else {    // single site activation - update current site only
            self::update_site();
        }
        
    }
    // end of update_menu_hashes()
    
}
// end of URE_Update_Admin_Menu_Hashes class
