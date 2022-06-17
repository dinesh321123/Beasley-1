<?php
//Our class extends the WP_List_Table class, so we need to make sure that it's there
if(!class_exists('WP_List_Table')){
   require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
    class Link_List_Table extends WP_List_Table {
        public static function get_log_data($per_page, $page_number ) {
            global $wpdb;
             $sql = "SELECT * FROM {$wpdb->prefix}ietc_log WHERE 1 ";
            $filter_by_user_type_site="";
            /*if(isset($_GET['s'])) {
              $search=$_GET['s'];
              $search = trim($search);
               $filter_by_user_type_site.=" AND title LIKE '%$search%' ";
            }*/
            if(!empty(filter_input(INPUT_GET, "filtuserid", FILTER_SANITIZE_STRIPPED))) {
               $getUserid=filter_input(INPUT_GET, "filtuserid", FILTER_SANITIZE_STRIPPED);
               $filter_by_user_type_site.=" AND userid=$getUserid ";
            }

            if(!empty(filter_input(INPUT_GET, "filttypeid", FILTER_SANITIZE_STRIPPED))) {

               if(filter_input(INPUT_GET, "filttypeid", FILTER_SANITIZE_STRIPPED)==1){
                    $filter_by_user_type_site.=" AND type='category' AND import_export=2";
                }else if(filter_input(INPUT_GET, "filttypeid", FILTER_SANITIZE_STRIPPED)==2){
                     $filter_by_user_type_site.=" AND type='category' AND import_export=1";
                }else if(filter_input(INPUT_GET, "filttypeid", FILTER_SANITIZE_STRIPPED)==3){
                     $filter_by_user_type_site.=" AND type='post_tag' AND import_export=2";
                }else{
                     $filter_by_user_type_site.=" AND type='tag' AND import_export=1";
                }
            }

            if(!empty(filter_input(INPUT_GET, "filtnetworkid", FILTER_SANITIZE_STRIPPED))) {
              $getNetworkid=filter_input(INPUT_GET, "filtnetworkid", FILTER_SANITIZE_STRIPPED);
              $filter_by_user_type_site.=" AND blog_id=$getNetworkid ";

            }
              $sql.= $filter_by_user_type_site;

            if ( ! empty(filter_input(INPUT_GET, "orderby", FILTER_SANITIZE_STRIPPED)) ) {
            // $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            //$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' DESC';
            }else{
                 $sql.=" ORDER BY id DESC ";
            }
            $sql .= " LIMIT $per_page";
            $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

            // echo $sql;
            $result = $wpdb->get_results( $sql, 'ARRAY_A' );

            foreach ($result as $key => $value) {
            // Assign network name
                $site_id = $value['blog_id'];
                $subsite_name = get_blog_details($site_id)->blogname;
                $result[$key]['site_name'] = $subsite_name;
            // Assign Import Export type
                $importExportType = isset($value['import_export']) && $value['import_export'] == 1 ? 'Export' : 'Import' ;
                $result[$key]['import_export'] = $importExportType;
            // Assign user information
                $userInfo = "";
                if( isset($value['userid']) && $value['userid'] != "" ) {
                    $user_info  = get_userdata($value['userid']);
                    $username   = $user_info->user_login;
                    $userInfo   =   $username;
                }
                $result[$key]['userid'] = $userInfo;
            }
            // echo "<pre>", print_r($result), "</pre>";
            return $result;
        }
        function __construct() {
            global $status, $page;
            //Set parent defaults
            parent::__construct( array(
                'singular'  => 'log',     //singular name of the listed records
                'plural'    => 'log',    //plural name of the listed records
                'ajax'      => false        //does this table support ajax?
            ) );
        }
        function column_default($item, $column_name) {
            switch($column_name){
                case 'userid':
                case 'type':
                case 'site_name':
                case 'file':
                case 'inserted_date':
                    return $item[$column_name];
                default:
                    return print_r($item,true); //Show the whole array for troubleshooting purposes
            }
        }

        /*function search_box( $text, $input_id ) {
            if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {

                //return;
            }

            $input_id = $input_id . '-search-input';

            if ( ! empty( $_REQUEST['orderby'] ) ) {
                echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
            }
            if ( ! empty( $_REQUEST['order'] ) ) {
                echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
            }
            if ( ! empty( $_REQUEST['post_mime_type'] ) ) {
                echo '<input type="hidden" name="post_mime_type" value="' . esc_attr( $_REQUEST['post_mime_type'] ) . '" />';
            }
            if ( ! empty( $_REQUEST['detached'] ) ) {
                echo '<input type="hidden" name="detached" value="' . esc_attr( $_REQUEST['detached'] ) . '" />';
            }
            ?>
                <p class="search-box">
                    <label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo $text; ?>:</label>
                    <input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>" />
                        <?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
                </p>
            <?php
        }*/

        function column_type($item){
            return $item['import_export'].' '.$item['type'];
        }
        function column_file($item){
            //Build row actions
            if($item['import_export'] == 'Export'){
                $file_url   = TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_URL . "ietc_uploads/import-export-tag-category/export/".$item['file'];
                $returnString   = sprintf('<a href="%s" target="_blank">Download CSV</a>',$file_url);

            } else {
                $file_url       = TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_URL . "ietc_uploads/import-export-tag-category/import/".$item['file'];
                $logFileUrl     = TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_URL . "ietc_uploads/import-export-tag-category/logs/".$item['logfile'];

                $returnString   = sprintf('<a href="%s" target="_blank">Download CSV</a> | <a href="%s" target="_blank">Download log</a>',$file_url, $logFileUrl);
            }

            //Return the title contents
            return $returnString;
            /* return sprintf('%1$s %2$s',
                sprintf('<a href="%s" target="_blank">Download CSV</a>',$file_url),
                $this->row_actions($actions)
            ); */
        }

        function column_userid($item){
            //Build row actions
            $actions = array(
                // 'edit'      => sprintf('<a href="?page=%s&action=%s&edit=%s">Edit</a>',$_REQUEST['page'],'edit',$item['id']),
                'delete'    => sprintf('<a href="?page=%s&action=%s&delete=%s" class="ietc-delete-confirm">Delete</a>',$_REQUEST['page'],'id',$item['id']),
            );
            //Return the title contents
            return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
                /*$1%s*/ $item['userid'],
                /*$2%s*/ $item['id'],
                /*$3%s*/ $this->row_actions($actions)
            );
        }

        function column_cb($item){
            return sprintf(
                '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
                /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
            );
        }

        function get_columns(){
            $columns = array(
                // 'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
                'userid'    => 'User',
                'type'      => 'Type',
                'site_name' => 'Network source',
                'file'      => 'Download File',
                'inserted_date'  => 'Date',
            );
            return $columns;
        }

        function get_sortable_columns() {
            $sortable_columns = array(
                'userid'         => array('userid',false),     //true means it's already sorted
                // 'inserted_date'     => array('inserted_date',true)
            );
            return $sortable_columns;
        }

        function get_bulk_actions() {
        /* $actions = array(
                'delete'    => 'Delete'
            );
            return $actions; */
        }

        function process_bulk_action() {
            //Detect when a bulk action is being triggered...
        }

        protected function extra_tablenav( $which ) {

        if ( 'top' === $which ) {
            global $wpdb;
                $userLoginNames = $wpdb->get_results("SELECT ID,user_login FROM {$wpdb->prefix}users ORDER BY user_login ASC", ARRAY_A);
                $networkSources = $wpdb->get_results("SELECT blog_id,user_login FROM {$wpdb->prefix}users ORDER BY user_login ASC", ARRAY_A);

            ?>
              <input type='hidden' class='wpBaseurl' value="<?php echo get_bloginfo('wpurl');?>">
                <div class="alignleft actions bulkactions">
                        <select name="user-filter" class="ewc-filter-cat userfiltercls">
                            <option value="">Filter by User</option>
                            <?php
                                foreach($userLoginNames as $userLogin){
                            ?>
                                <option <?php echo filter_input(INPUT_GET, "filtuserid", FILTER_SANITIZE_STRIPPED)==$userLogin['ID']?' selected ':' '?> value="<?php echo $userLogin['ID']?>"><?php echo $userLogin['user_login']?></option>
                            <?php
                                }
                            ?>
                        </select>

                        <select name="type-filter" class="ewc-filter-cat typefiltercls">
                            <option value="">Filter by Type</option>

                                <option <?php echo filter_input(INPUT_GET, "filttypeid", FILTER_SANITIZE_STRIPPED)==1?' selected ':' '?> value="1">Import Category</option>
                                <option <?php echo filter_input(INPUT_GET, "filttypeid", FILTER_SANITIZE_STRIPPED)==2?' selected ':' '?> value="2">Export Category</option>
                                <option <?php echo filter_input(INPUT_GET, "filttypeid", FILTER_SANITIZE_STRIPPED)==3?' selected ':' '?> value="3">Import Tag</option>
                                <option <?php echo filter_input(INPUT_GET, "filttypeid", FILTER_SANITIZE_STRIPPED)==4?' selected ':' '?> value="4">Export Tag</option>

                        </select>

                         <select name="networksource-filter" class="ewc-filter-cat networksourcecls">
                                 <option value="">Filter by Network Source</option>
                                 <?php
                                    foreach(get_sites() as $getNetworkS){
                                 ?>
                                     <option <?php echo filter_input(INPUT_GET, "filtnetworkid", FILTER_SANITIZE_STRIPPED)==$getNetworkS->blog_id?' selected ':' '?> value="<?php echo $getNetworkS->blog_id;?>"><?php echo get_blog_details($getNetworkS->blog_id)->blogname?></option>
                                 <?php
                                    }
                                 ?>
                         </select>

                 </div>
            <?php
        }
    }


        function prepare_items() {
            global $wpdb; //This is used only if making any database queries

            /**
             * First, lets decide how many records per page to show
             */
            $per_page = 10;

            if(filter_input(INPUT_GET, "paged", FILTER_SANITIZE_STRIPPED)) {
                $page = filter_input(INPUT_GET, "paged", FILTER_SANITIZE_STRIPPED);
            }
            else{
                $page = '1';
            }
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();

            $this->_column_headers = array($columns, $hidden, $sortable);

            /**
             * Optional. You can handle your bulk actions however you see fit. In this
             * case, we'll handle them within our package just to keep things clean.
             */
            $this->process_bulk_action();

            $data = $this->get_log_data($per_page, $page );

            function usort_reorder($a,$b){

                $orderby = (!empty(filter_input(INPUT_GET, "orderby", FILTER_SANITIZE_STRIPPED))) ? filter_input(INPUT_GET, "orderby", FILTER_SANITIZE_STRIPPED) : 'id'; //If no sort, default to title
                $order = (!empty(filter_input(INPUT_GET, "order", FILTER_SANITIZE_STRIPPED))) ? filter_input(INPUT_GET, "order", FILTER_SANITIZE_STRIPPED) : 'asc'; //If no order, default to asc
                $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
                return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
            }
            // usort($data, 'usort_reorder');
            $current_page = $this->get_pagenum();

            $filter_by_user_type_site="";
            $sql = "SELECT * FROM {$wpdb->prefix}ietc_log WHERE 1 ";

            if(isset($_GET['s'])) {
                $search=$_GET['s'];
                $search = trim($search);
                 $filter_by_user_type_site.=" AND title LIKE '%$search%' ";
            }

            if(!empty(filter_input(INPUT_GET, "filtuserid", FILTER_SANITIZE_STRIPPED))) {
               $getUserid=filter_input(INPUT_GET, "filtuserid", FILTER_SANITIZE_STRIPPED);
               $filter_by_user_type_site.=" AND userid=$getUserid ";
            }

            if(!empty(filter_input(INPUT_GET, "filttypeid", FILTER_SANITIZE_STRIPPED))) {

               if(filter_input(INPUT_GET, "filttypeid", FILTER_SANITIZE_STRIPPED)==1){
                    $filter_by_user_type_site.=" AND type='category' AND import_export=2";
                }else if(filter_input(INPUT_GET, "filttypeid", FILTER_SANITIZE_STRIPPED)==2){
                     $filter_by_user_type_site.=" AND type='category' AND import_export=1";
                }else if(filter_input(INPUT_GET, "filttypeid", FILTER_SANITIZE_STRIPPED)==3){
                     $filter_by_user_type_site.=" AND type='post_tag' AND import_export=2";
                }else{
                     $filter_by_user_type_site.=" AND type='tag' AND import_export=1";
                }
            }

            if(!empty(filter_input(INPUT_GET, "filtnetworkid", FILTER_SANITIZE_STRIPPED))) {
              $getNetworkid=filter_input(INPUT_GET, "filtnetworkid", FILTER_SANITIZE_STRIPPED);
              $filter_by_user_type_site.=" AND blog_id=$getNetworkid ";

            }
            $sql.= $filter_by_user_type_site;

            $result = $wpdb->get_results( $sql);
            $total_items = count($result);
            $this->items = $data;

            $this->set_pagination_args( array(
                'total_items' => $total_items,                  //WE have to calculate the total number of items
                'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
                'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
            ) );
            $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        }
}
?>
