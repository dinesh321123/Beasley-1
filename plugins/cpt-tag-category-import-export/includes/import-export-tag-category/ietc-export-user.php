<div class="wrap">
     <h1 class="wp-heading-inline"><?php _e('Export User', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN)?></h1>
     <hr class="wp-header-end">

     <form name="export_users_data" method="post" action="" enctype="multipart/form-data" id="post">
         <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                  <div id="postbox-container-2" class="postbox-container">
                        <div id="normal-sortables" class="meta-box-sortables ui-sortable postbox">
                        <table class="form-table" >
							<?php /* ?>
							<tr>
                              <td>
                                 <label for="subscription_type"><?php _e( 'Network source', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN ); ?></label>
                              </td>
                              <td>
                                 <?php $sites = get_sites(); ?>
                                 <select name="network_source" id="network_source" class="general-settings-select2">
                                    <option disabled="" selected=""><?php _e( 'Select Site', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN ); ?></option>
                                    <?php
                                    foreach ( $sites as $subsite ) {
                                    $subsite_id = get_object_vars($subsite)["blog_id"];
                                    $subsite_name = get_blog_details($subsite_id)->blogname;
                                    echo '<option value="'.$subsite_id.'">'.$subsite_name.'</option>';
                                    }
                                    ?>
                                 </select>
                              </td>
                           </tr><?php */ ?>
                           <tr>
                              <td>
                                    <label for="type"><?php _e( 'Type', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN ); ?></label>
                              </td>
                              <td>
                                    <select name="type" id="type" class="general-settings-select2">
                                       <option value="" disabled selected>Select Type</option>
                                       <option value="station_list">User Station list</option>
                                       <option value="post_list">User Post list</option>
                                    </select>
                              </td>
                           </tr>
							<tr id="post_list_date" style="display: none;">
								<td>
									<label for="subscription_type"><?php _e( 'Date', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN ); ?></label>
								</td>
								<td>
									From: <input type="date" id="export_from" name="export_from" value="" >
									To: <input type="date" id="export_to" name="export_to" value="" >
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
                                             <span><?php _e( 'Actions', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN ); ?></span>
                                        </h2>
                                   </div>

                                    <div class="inside">
                                       <div class="submitbox" id="submitpost">
                                       <div id="minor-publishing">
                                          <div id="misc-publishing-actions">

                                             <div class="misc-pub-section curtime misc-pub-last-log" id="export_msg">

                                             </div>
                                             <div class="misc-pub-section curtime misc-pub-last-log">
                                                <div id="error_msg">
                                                   <span class="spinner" id="export_tag_category_spinner"></span>
													<span class="spinner" id="export_users_spinner"></span>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="clear"></div>
                                       </div>

                                          <div id="major-publishing-actions">
                                                  <div id="publishing-action">
													  <div id="station_list_div">
														  <input name="list_publish" type="button" class="button button-primary button-large" id="export_users_station" value="Export User Station">
													  </div>
													  <div id="post_list_div" style="display: none;">
														  <input name="list_publish" type="button" class="button button-primary button-large" id="export_users_post" value="Export User Posts" >
													  </div>
                                                  </div>
                                                  <div class="clear"></div>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                           </div>
                        </div>
   </form>
</div>
