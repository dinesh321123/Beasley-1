<div class="wrap">
     <h1 class="wp-heading-inline"><?php _e('Import tag & category', GENERAL_SETTINGS_CPT_TEXT_DOMAIN)?></h1>
     <hr class="wp-header-end">
         
     <form method="post" action="" enctype="multipart/form-data" id="post">
         <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                  <div id="postbox-container-2" class="postbox-container">
                        <div id="normal-sortables" class="meta-box-sortables ui-sortable postbox">
                        <table class="form-table" >
                           <tr>
                              <td>
                                 <label for="subscription_type"><?php _e( 'Network source', GENERAL_SETTINGS_CPT_TEXT_DOMAIN ); ?></label>
                              </td>
                              <td>
                                 <select name="network_source" id="network_source" class="general-settings-select2">
                                    <option disabled="" selected=""><?php _e( 'Select Site', GENERAL_SETTINGS_CPT_TEXT_DOMAIN ); ?></option>
                                    <?php 
                                    $sites = get_sites();    
                                                                    
                                    foreach ( $sites as $subsite ) {
                                         $subsite_id = get_object_vars($subsite)["blog_id"];                                                       
                                         $subsite_name = get_blog_details($subsite_id)->blogname;
                                         //echo '<option value="'.$subsite_id.'">'.$subsite_name.'</option>';                             
                                         $new_site_arr[$subsite_id] = strtolower($subsite_name);
                                   }
                                   asort($new_site_arr);                       
                                    foreach ( $new_site_arr as $site_id => $site_name ) {
                                         $subsite_id = get_object_vars($subsite)["blog_id"];
                                         var_dump($subsite_id);
                                         $subsite_name = get_blog_details($subsite_id)->blogname;
                                         echo '<option value="'.$site_id.'">'. ucfirst($site_name) .'</option>';                             
                                        
                                   }

                                    /* foreach ( $ntworkSources as $subsite ) {
                                       $subsite_id = get_object_vars($subsite)["blog_id"];
                                       $subsite_name = get_blog_details($subsite_id)->blogname;
                                       $new_site_arr[$subsite_id] = $subsite_name;
                                       echo '<option value="'.$subsite_id.'">'.$subsite_name.'</option>';                             
                                    } */
                                    ?>
                                 </select>
                              </td>
                           </tr>
                           <tr>
                              <td>
                                    <label for="network_type"><?php _e( 'Type', GENERAL_SETTINGS_CPT_TEXT_DOMAIN ); ?></label>
                              </td>
                              <td>
                                    <select name="network_type" id="network_type" class="general-settings-select2">
                                       <option value="" disabled selected>Select Type</option>
                                       <option value="post_tag">Tag</option>
                                       <option value="category">Category</option>
                                    </select>   
                              </td>
                           </tr>
                           <tr>
                              <td>
                                    <label for="csv_file"><?php _e( 'Choose File', GENERAL_SETTINGS_CPT_TEXT_DOMAIN ); ?></label>
                              </td>
                              <td>
                                    <input type="file" id="csv_file" name="csv_file" value="" accept=".csv" required/>
                              </td>
                           </tr>
                        </table>
                    </div>
                    </div>
                    <div id="postbox-container-1" class="postbox-container">
                         <div id="side-sortables" class="meta-box-sortables ui-sortable">
                              <div id="submitdiv" class="postbox">
                                   <div class="postbox-header">
                                        <h2 class="hndle ui-sortable-handle">
                                             <span><?php _e( 'Actions', GENERAL_SETTINGS_CPT_TEXT_DOMAIN ); ?></span>
                                        </h2>
                                   </div>

                                    <div class="inside">
                                       <div class="submitbox" id="submitpost">
                                       <div id="minor-publishing">
                                          <div id="misc-publishing-actions">

                                             <div class="misc-pub-section curtime misc-pub-last-log" id="import_msg">
                                                
                                             </div>
                                             <div class="misc-pub-section curtime misc-pub-last-log">                              
                                                <div id="error_msg">
                                                   <span class="spinner" id="import_tag_category_spinner"></span>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="clear"></div>
                                       </div>
                                          
                                          <div id="major-publishing-actions">
                                                  <div id="publishing-action">
                                                      <input name="list_publish" type="button" class="button button-primary button-large" id="import_tag_category" value="Import">
                                                  </div>
                                                  <div class="clear"></div>
                                             </div>
                                        </div>
                                   </div>
                              </div>
</div></div>
</form>
</div>