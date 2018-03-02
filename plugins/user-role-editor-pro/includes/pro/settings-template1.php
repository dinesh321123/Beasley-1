<?php
/*
 * User Role Editor Pro WordPress plugin options page
 *
 * @Author: Vladimir Garagulya
 * @URL: http://role-editor.com
 * @package UserRoleEditor
 *
 */

if (!$license_key_only) {
?>
    <tr>
        <td>
            <input type="checkbox" name="use_jquery_cdn_for_ui_css" id="use_jquery_cdn_for_ui_css" value="1" 
                   <?php echo ($use_jquery_cdn_for_ui_css == 1) ? 'checked="checked"' : ''; ?> /> 
            <label for="use_jquery_cdn_for_ui_css"><?php esc_html_e('Use jQuery UI CSS from jQuery CDN', 'ure'); ?></label></td>
        <td>                        
        </td>
    </tr>
<?php
}   // if (!$license_key_only) {
if (!$this->lib->multisite || $license_key_only || $this->lib->active_for_network) {
?>
      <tr>
          <td cospan="2"><h3><?php esc_html_e('License', 'ure');?></h3></td>
      </tr>      
      <tr>
        <td>
            <label for="license_key"><?php esc_html_e('License Key:', 'ure'); ?></label>
<?php
    $license_key_value = empty($license_key) ? '': str_repeat('*', 64);
?>
            <input type="text" name="license_key" id="license_key" value="<?php echo $license_key_value; ?>" size="15" style="width:450px;" /> 
<?php            
            if (!empty($license_key)) {
?>                
                <span style="font-weight: bold; color: green;" title="<?php esc_html_e('License key is hidden to limit access to it', 'ure'); ?>" ><?php esc_html_e('Installed', 'ure'); ?></span>
<?php                
                } else {
?>
                <span style="font-weight: bold; color: red"><?php esc_html_e('Not installed!', 'ure');?></span>
                 <?php esc_html_e('Input license key to activate automatic updates from role-editor.com', 'ure'); ?>
<?php                
                }                
?>                            
        </td>
        <td>
        </td>
      </tr>
<?php
}   // if ($licens_key_only ||
