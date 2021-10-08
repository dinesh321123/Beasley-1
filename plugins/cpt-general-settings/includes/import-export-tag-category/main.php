<?php
// function nietc_list_page(){
	?>
   <div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h1 class="wp-heading-inline">
        <?php _e('Import Export', 'wpbc')?>
        <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'network/admin.php?page=ietc_add_new');?>"><?php _e('Add new', 'wpbc')?></a>
    </h2>
    <hr class="wp-header-end">    
      <div id="poststuff">    
               <?php
               if(isset($_GET['action']) && $_GET['action'] == 'edit'){
                  $action =  'edit';
                  global $wpdb;
                  $id = $_GET['edit'];                    
                  $sql = "SELECT * FROM {$wpdb->prefix}ietc where id = ".$id;
                  $result = $wpdb->get_results( $sql );
                  foreach ($result as $key => $value) {                                            
               ?>
               <form id="contacts-table" method="POST">
               <div id="post-body" class="metabox-holder columns-2">
               <div id="post-body-content" style="position: relative;">
               <div id="titlediv">
                  <div id="titlewrap">                                   
                     <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
                     <input type="text" id="title"  name="title" value="<?php echo $value->title; ?>" />
                      <input type="hidden" id="site_id"  name="site_id" class="site_id" value="<?php echo $value->site; ?>" />
                     <input type="hidden" id="list_id" name="list_id" value="<?php echo $value->id; ?>" class="list_id" />
                     <input type="hidden" id="file" name="file" value="<?php echo $value->file; ?>" class="file" />
                  </div>
               </div>

              
            </div>

             <div id="postbox-container-2" class="postbox-container">
               <div id="normal-sortables" class="meta-box-sortables ui-sortable postbox">
                <table class="form-table">
                  <tr>
                       <td>Description</td>
                       <td>
                          <textarea name="des"><?php echo $value->description; ?></textarea>
                      </td>
                  </tr>
                  <tr>
                     <td>Network Site</td>
                     <td>
                        <select name="site" class="blog_id general-settings-select2">
                             <option disabled="" selected="">Select Site</option>
                        <?php 
                             $sites = get_sites();                         
                             foreach ( $sites as $subsite )
                             {

                                  $subsite_id = get_object_vars($subsite)["blog_id"];
                                  $subsite_name = get_blog_details($subsite_id)->blogname;

                                  $selected = '';
                                  if($subsite_id == $value->site){
                                       $selected = 'selected';
                                  }
                                  echo '<option value="'.$subsite_id.'" '.$selected.'>'.$subsite_name.'</option>';                             
                                 
                            }
                        ?>
                        </select>
                
                     </td>
                  </tr>
                  

                  <tr>
                        <td>File Name</td>
                        <td>
                           <?php echo $value->file; ?>             
                        </td>
                  </tr>

                  <tr>
                        <td>Type</td>
                        <td>
                           
                           <select name="taxonomy_type" class="general-settings-select2">
                              <option value="post_tag" <?php if($value->taxonomy_type == 'post_tag'){ echo 'selected'; } ?>>Tag</option>
                              <option value="category" <?php if($value->taxonomy_type == 'category'){ echo 'selected'; } ?> >Category</option>
                           </select>
                             <input type="hidden" class="taxonomy_type" value="<?php echo $value->taxonomy_type; ?>">          
                        </td>
                  </tr>

                 
               </table>
                  
               <div id="slugdiv" class="postbox  hide-if-js" style="">
                  <h2 class="hndle ui-sortable-handle"><span>Slug</span></h2>
                     <div class="inside">
                        <label class="screen-reader-text" for="post_name">Slug</label><input name="post_name" type="text" size="13" id="post_name" value="post-from-wmmr">
                     </div>
                  </div>
               </div>

               <div id="advanced-sortables" class="meta-box-sortables ui-sortable">
                  <div id="import_log_box" class="postbox postbox-header">
                     <h2 class="hndle ui-sortable-handle"><span>Import Logs</span></h2>
                        <div class="inside">
                           <div class="show_import_log">
                              <!-- Import log will print here -->
                           </div>                    
                        </div>
                  </div>
               </div>
            </div>

            <div id="postbox-container-1" class="postbox-container">
               <div id="side-sortables" class="meta-box-sortables ui-sortable">
                  <div id="submitdiv" class="postbox">
                     <div class="postbox-header">
                        <h2 class="hndle ui-sortable-handle">
                           <span>Import Control</span>
                        </h2>

                      </div>

                        <div class="inside">
                           <div class="submitbox" id="submitpost">
                              <div id="minor-publishing">
                                 <div id="misc-publishing-actions">

                                    <div class="misc-pub-section curtime misc-pub-last-log">
                                      <?php
                                            $sql = "SELECT inserted_date FROM {$wpdb->prefix}ietc_import_log where id = ".$id." ORDER by log_id DESC" ;
                                            
                                            $last_date = $wpdb->get_var( $sql );
                                             
                                            if($last_date != ''){                                            
                                               $sql = "SELECT log_filename FROM {$wpdb->prefix}ietc_import_log where id = ".$id." ORDER by log_id DESC" ;
                                               
                                               $last_log_filename = $wpdb->get_var( $sql );
                                               $file_url = GENERAL_SETTINGS_CPT_URL . "uploads/import-export-tag-category/logs/".$last_log_filename;
                                               $newDate = date("l jS \of F Y h:i:s A", strtotime($last_date));
                                             
                                             ?>
                                       <p>
                                          <span id="timestamp" class="timestamp">
                                             <a href ="<?php echo $file_url; ?>" download>
                                             Download last import data log: 
                                             <b><?php echo $newDate; ?></b>
                                             </a>
                                             <span></span>
                                          </span>
                                       </p>
                                       <?php } ?>
                                    </div>
                                    <div class="misc-pub-section curtime import-result">                              
                                       <div id="error_msg">
                                          <span class="spinner" id="tp_spinner"></span>
                                       </div>
                                    </div>
                                    <div class="misc-pub-section curtime misc-pub-section-last">
                                       <input type="button" id="import_file" name="import_file" value="Import Data" class="button button-large" />
                                    </div>
                                 </div>
                                 <div class="clear"></div>
                              </div>

                              <div id="major-publishing-actions">
                                    <div id="publishing-action">
                                        <span class="spinner"></span>
                                        <input name="list_update" type="hidden" id="original_publish" value="Update">
                                        <input name="list_update" type="submit" class="button button-primary button-large" id="publish" value="Update">
                                   </div>
                                   <div class="clear"></div>
                              </div>
                           </div>
                        </div>
                     </div>   

                     <div id="submitdiv" class="postbox" style="display: none;">
                     <div class="postbox-header">
                        <h2 class="hndle ui-sortable-handle">
                           <span>Export Control</span>
                        </h2>

                       </div>

                        <div class="inside">
                           <div class="submitbox" id="submitpost">
                              <div id="minor-publishing">
                                 <div id="misc-publishing-actions">

                                    <div class="misc-pub-section curtime misc-pub-last-log">
                                        <?php
                                                $sql = "SELECT inserted_date FROM {$wpdb->prefix}ietc_export_log where id = ".$id." ORDER by elog_id DESC" ;
                                                //echo $sql;
                                                $last_date = $wpdb->get_var( $sql );
                                                
                                                $newDate = date("l jS \of F Y h:i:s A", strtotime($last_date));

                                                $fsql = "SELECT log_filename FROM {$wpdb->prefix}ietc_export_log where id = ".$id." ORDER by elog_id DESC" ;
                                                
                                                $last_log_filename = $wpdb->get_var( $fsql );
                                                $file_url = GENERAL_SETTINGS_CPT_URL . "uploads/import-export-tag-category/export/".$last_log_filename;
                                                if($last_date != ''){         
                                             ?>
                                       <p>
                                          <span id="timestamp" class="timestamp">
                                            
                                             <a href="<?php echo $file_url; ?>" download>
                                             Download last export data log: <b><?php echo $newDate; ?></b>
                                             </a>
                                             <span></span>
                                          </span>
                                       </p>  
                                       <?php } ?> 
                                       </p>
                                    </div>

                                    <div class="misc-pub-section curtime export-result">                              
                                          
                                       <div id="error_msg">
                                          <span class="spinner" id="tp_spinner"></span>
                                       </div>
                                    </div>
                                    <div class="misc-pub-section curtime misc-pub-section-last">
                                       <input type="button" id="export_file" name="export_file" value="Export Data" class="button button-large" />
                                    </div>
                                 </div>
                                 <div class="clear"></div>
                              </div>

                           </div>
                        </div>
                     </div>                          
                  </div>
               </div>    
               </div>
               </div> 
               </form>         
         <?php
        }
    }
    else{
      require plugin_dir_path( __FILE__ ) . 'class-list-table.php';
      ?>
      <div id="post-body" class="metabox-holder">
         <div id="post-body-content" style="position: relative;">
            <form id="all-drafts" method="get">
               <input type="hidden" name="page" value="ietc_page" />
               <?php
                  $wp_list_table = new Link_List_Table();
                  $wp_list_table->prepare_items();    
                  $wp_list_table->search_box('Search', 'search');
                  $wp_list_table->display(); 
               ?>
            </form>
         </div>
      </div>
      <?php
    }
   ?>  

</div>
<?php
// }
