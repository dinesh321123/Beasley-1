<div class="wrap">
     <h1 class="wp-heading-inline">Add List</h1>
     <hr class="wp-header-end">
         
     <form method="post" action="" enctype="multipart/form-data" id="post">
          <div id="poststuff">
               <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content" style="position: relative;" >

                         <div id="titlediv">
                              <div id="titlewrap">
                                  
                                   <input type="text" id="title"  name="title" value="" placeholder="Enter title here" required/>
                              </div>
                         </div>
                    </div>
                     <div id="postbox-container-2" class="postbox-container">
                         <div id="normal-sortables" class="meta-box-sortables ui-sortable postbox">
                         <table class="form-table" >
                              <tr>
                                   <td>
                              
                                        <label class="" id="title-prompt-text" for="title"><?php _e( 'Description', GENERAL_SETTINGS_CPT_TEXT_DOMAIN ); ?></label>
                                   </td>
                                   <td>
                                        <textarea name="des" id="des" col="30"></textarea>
                                   </td>
                              </tr>

                               <tr>
                                   <td>
                                        <label for="subscription_type"><?php _e( 'Choose network source', GENERAL_SETTINGS_CPT_TEXT_DOMAIN ); ?></label>
                                   </td>
                                   <td>
                                        <select name="site" class="general-settings-select2">
                                             <option disabled="" selected=""><?php _e( 'Select Site', GENERAL_SETTINGS_CPT_TEXT_DOMAIN ); ?></option>
                                             <?php 
                                                  $sites = get_sites();                         
                                                  foreach ( $sites as $subsite ) {
                                                       $subsite_id = get_object_vars($subsite)["blog_id"];
                                                       $subsite_name = get_blog_details($subsite_id)->blogname;
                                                       echo '<option value="'.$subsite_id.'">'.$subsite_name.'</option>';                             
                                                      
                                                 }
                                             ?>
                                        </select>
                                   </td>
                              </tr>

                              <tr>
                                   <td>
                                        <label for="subscription_type"><?php _e( 'File Upload', 'ietc' ); ?></label>
                                   </td>
                                   <td>
                                        <input type="file" id="fileToUpload" name="fileToUpload" value="" accept=".csv" required/>
                                        <span>
                                             <?php $target_file = GENERAL_SETTINGS_CPT_URL. '/uploads/import-export-tag-category/sample-file.csv'; ?>
                                             <a href="<?php echo $target_file; ?>"> Sample file to upload </a></span>
                                   </td>
                              </tr>

                               <tr>
                                   <td>
                                        <label for="subscription_type"><?php _e( 'Type', 'ietc' ); ?></label>
                                   </td>
                                   <td>
                                        <select name="type" class="general-settings-select2">
                                             <option value="" disabled selected>Select Type</option>
                                             <option value="post_tag">Tag</option>
                                             <option value="category">Category</option>
                                        </select>   
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
                                             <span><?php _e( 'Actions', 'ietc' ); ?></span>
                                        </h2>
                                   </div>

                                   <div class="inside">
                                        <div class="submitbox" id="submitpost">
                                            

                                             <div id="major-publishing-actions">
                                                  
                                                  <div id="publishing-action">
                                                       <span class="spinner"></span>
                                                       <input name="list_publish" type="hidden" id="original_publish" value="Save Changes">
                                                       <input name="list_publish" type="submit" class="button button-primary button-large" id="publish" value="Save Changes">
                                                  </div>
                                                  <div class="clear"></div>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                             
</div></div>
        </form>
      </div>
<?php