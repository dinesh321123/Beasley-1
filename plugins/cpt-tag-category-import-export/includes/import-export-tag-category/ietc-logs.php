   <div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h1 class="wp-heading-inline">
        <?php _e('Import Export Logs', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN); ?>
    </h2>
    <hr class="wp-header-end" />    
      <div id="poststuff">    
      <?php
      if (is_file( TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH . 'includes/import-export-tag-category/ietc-logs-class-list-table.php')) {
         include_once TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH . 'includes/import-export-tag-category/ietc-logs-class-list-table.php';
		}
      ?>
      <div id="post-body" class="metabox-holder">
         <div id="post-body-content" style="position: relative;">
            <form id="all-drafts" method="get">
               <input type="hidden" name="page" value="ietc_page" />
               <?php
                  $wp_list_table = new Link_List_Table();
                  $wp_list_table->prepare_items();    
                  //$wp_list_table->search_box('Search', 'search');
                  $wp_list_table->display(); 
               ?>
            </form>
         </div>
      </div>
      </div>   <!-- Close poststuff -->
</div>