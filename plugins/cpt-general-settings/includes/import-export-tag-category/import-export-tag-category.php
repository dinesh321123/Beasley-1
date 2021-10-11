<?php
/**
 * Class ImportExportTagCategory
 */
class ImportExportTagCategory {
	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'ietc_imp_exp_init' ) );
		add_action('network_admin_notices', array( __CLASS__, 'ietc_general_admin_notice' ) ) ;

		add_action('network_admin_menu', array( __CLASS__, 'ietc_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'ietc_enqueue_scripts' ) );
		/* Remove ietc_import_data, wp_ajax_ietc_export_data */ 
		add_action( 'wp_ajax_ietc_import_data', array( __CLASS__, 'ietc_import_data' ) );
		add_action( 'wp_ajax_nopriv_ietc_import_data', array( __CLASS__, 'ietc_import_data' ) );
   
		add_action( 'wp_ajax_ietc_export_data', array( __CLASS__, 'ietc_export_data' ) );
		add_action( 'wp_ajax_nopriv_ietc_export_data', array( __CLASS__, 'ietc_export_data' ) );

		add_action( 'wp_ajax_ietc_export_tag_category', array( __CLASS__, 'ietc_export_tag_category' ) );
		add_action( 'wp_ajax_nopriv_ietc_export_tag_category', array( __CLASS__, 'ietc_export_tag_category' ) );

		add_action( 'wp_ajax_ietc_import_tag_category', array( __CLASS__, 'ietc_import_tag_category' ) );
		add_action( 'wp_ajax_nopriv_ietc_import_tag_category', array( __CLASS__, 'ietc_import_tag_category' ) );

	}
   
   public static function ietc_admin_menu() {
		add_menu_page(
			__('Import tag & category', GENERAL_SETTINGS_CPT_TEXT_DOMAIN),
			__('Import tag & category', GENERAL_SETTINGS_CPT_TEXT_DOMAIN),
		   'manage_options',
		   'ietc_page',
		   array( __CLASS__, 'ietc_listing_page' ),
		   'dashicons-admin-multisite'
		 );

		add_submenu_page(
			'ietc_page', 
			__('Add New', GENERAL_SETTINGS_CPT_TEXT_DOMAIN), 
			__('Add New', GENERAL_SETTINGS_CPT_TEXT_DOMAIN), 
			'activate_plugins', 
			'ietc_add_new', 
			array( __CLASS__, 'ietc_add_new_form' ) 
		);

		add_submenu_page(
			'ietc_page', 
			__('Logs', GENERAL_SETTINGS_CPT_TEXT_DOMAIN), 
			__('Logs', GENERAL_SETTINGS_CPT_TEXT_DOMAIN), 
			'manage_options', 
			'ietc_logs', 
			array( __CLASS__, 'ietc_logs_form' ) 
		);
		add_submenu_page(
			'ietc_page', 
			__('Import tag & category', GENERAL_SETTINGS_CPT_TEXT_DOMAIN), 
			__('Import tag & category', GENERAL_SETTINGS_CPT_TEXT_DOMAIN), 
			'manage_options', 
			'ietc_import', 
			array( __CLASS__, 'ietc_import_form' ) 
		);
		add_submenu_page(
			'ietc_page', 
			__('Export tag & category', GENERAL_SETTINGS_CPT_TEXT_DOMAIN), 
			__('Export tag & category', GENERAL_SETTINGS_CPT_TEXT_DOMAIN), 
			'manage_options', 
			'ietc_export', 
			array( __CLASS__, 'ietc_export_form' ) 
		);
   }

   public static function ietc_listing_page() {
	  	// echo plugin_dir_path( __FILE__ ) . 'main.php'; exit;
		// require plugin_dir_path( __FILE__ ) . 'includes/import-export-tag-category/main.php';
		// echo GENERAL_SETTINGS_CPT_DIR_PATH . 'includes/import-export-tag-category/main.php'; exit;
		if (is_file( GENERAL_SETTINGS_CPT_DIR_PATH . 'includes/import-export-tag-category/main.php')) {
		   include_once GENERAL_SETTINGS_CPT_DIR_PATH . 'includes/import-export-tag-category/main.php';
		}
	}
   
	public static function ietc_add_new_form(){
		require plugin_dir_path( __FILE__ ) . 'add-new.php';
	}

	public static function ietc_logs_form() {
		if (is_file( GENERAL_SETTINGS_CPT_DIR_PATH . 'includes/import-export-tag-category/ietc-logs.php')) {
			include_once GENERAL_SETTINGS_CPT_DIR_PATH . 'includes/import-export-tag-category/ietc-logs.php';
		}
	}

	public static function ietc_export_form() {
		require plugin_dir_path( __FILE__ ) . 'ietc-export.php';
	}
	public static function ietc_import_form() {
		require plugin_dir_path( __FILE__ ) . 'ietc-import.php';
	}
   
	public static function ietc_enqueue_scripts() {
		global $typenow, $pagenow;
		$admin_page = isset( $_GET['page'] ) && $_GET['page'] != "" ? $_GET['page'] : "" ;
		if ( in_array( $admin_page, array( 'ietc_export', 'ietc_add_new', 'ietc_import' ) ) && in_array( $pagenow, array( 'admin.php' ) ) ) {
			//Add the Select2 CSS file
			wp_enqueue_style( 'general-settings-select2css', GENERAL_SETTINGS_CPT_URL .'assets/css/select2.min.css', array(),GENERAL_SETTINGS_CPT_VERSION, 'all');
			//Add the Select2 JavaScript file
			wp_enqueue_script( 'general-settings-select2js', GENERAL_SETTINGS_CPT_URL .'assets/js/select2.min.js', 'jquery', GENERAL_SETTINGS_CPT_VERSION);

			wp_enqueue_script( 'import-export-tag-category', GENERAL_SETTINGS_CPT_URL ."assets/js/import-export-tag-category.js", array('jquery'));
			wp_localize_script( 'import-export-tag-category', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
			wp_enqueue_media();
			wp_enqueue_editor();
		}
	}
	
	public static function ietc_import_data() {
		$file = $_POST['file']; 
		$blog_id = $_POST['site_id'];
		$list_id = $_POST['list_id'];
		$taxonomy_type = $_POST['taxonomy_type'];
		
   
		$file_path = GENERAL_SETTINGS_CPT_DIR_PATH.'uploads/import-export-tag-category/import/'. $file;
		
		if (file_exists($file_path) && !empty($file)) {
			$message = "The file $filename exists";
		} else {
			$response = "The file $filename does not exist";
			wp_send_json_error( $response );
			exit;
		}
		
		switch_to_blog( $blog_id );      
		$row = 0;
		$result = array();
		$log_data = array();
		if (($handle = fopen($file_path, "r")) !== FALSE) {
		  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			 
			 $num = count($data);
			 if($row != '0'){
				   
				  $name = $data[0];
				  $slug = $data[1];
				  $description = $data[2];
				  
				  $result = wp_insert_term(              
					   $name,               
					   $taxonomy_type,               
					   array(               
							'description' => $description,  
							'slug' => $slug,
					   )               
				  );
   
				  if ( is_wp_error($result) ) {
					   $message = $result->get_error_message();                              
				  }
				  else {
					   $message = 'Created ID - '.$result['term_id'];   
				  }
				//  echo '<br>';              
				  $log_data[$row] = array('type' => $taxonomy_type, 'name' => $name, 'message' => $message); 
				  //echo $row. '. '.$type .'----'.$name.'----'. $message;
			 }
			$row++;         
		}
			// Create Log file
			$todayDate  = date('YmdHis');
			$date = date('Y-m-d H:i:s');
			$myfile = fopen(GENERAL_SETTINGS_CPT_DIR_PATH . "/uploads/import-export-tag-category/logs/".$todayDate.'.txt', "w");
			fwrite($myfile, json_encode($log_data)); /** Once the data is written it will be saved in the path given */
			fclose($myfile); 

			global $wpdb;  

				$title = $_POST['title'];
				$site = $_POST['site'];
				$des = $_POST['des'];
				$file = $_POST['file'];

				$wpdb->insert(
					$wpdb->base_prefix . 'ietc_import_log',
					array(
							'id'        => $list_id,
							'log_filename'   => $todayDate.'.txt',
							'inserted_date'   => $date,                          
							)
					);                 
			fclose($handle);
		}
   
		restore_current_blog();
		$result = array( 'message' => 'File successfully imported', 'log_data' => $log_data );
		wp_send_json_success( $result );
		exit;          
   }

   public static function ietc_import_tag_category() {
	   // date('YmdHis').''.
	   $csvFileName			=	date('YmdHis').'-'.$_FILES['csv_file']['name'];
	   $csvFileTemp			=	$_FILES['csv_file']['tmp_name'];
	   $blog_id				=	$_POST['network_source'];
	   $blog_type			=	$_POST['network_type'];
	   $blog_type_compare	=	isset($_POST['network_type']) && $_POST['network_type'] == 'post_tag' ? 'tag' : $_POST['network_type'];
	   $network_name		=	$_POST['network_name'];
	   $user_id				=	get_current_user_id();
		
	   // echo "<pre>", print_r($_FILES['csv_file']) ;
		// echo " ---------  ", print_r($_REQUEST), "</pre>";

		$csvTargetFile = GENERAL_SETTINGS_CPT_DIR_PATH. '/uploads/import-export-tag-category/import/'. basename($csvFileName);
		if (move_uploaded_file($csvFileTemp, $csvTargetFile)) {
			// echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
		}
		else {
			$result = array( 'error' => 'Sorry, there was an error uploading your file.' );
			wp_send_json_error( $result );
			exit;
		}
		switch_to_blog( $blog_id );		
		$row			= 1;
		$importCount	=	0;
		if (($handle = fopen($csvTargetFile, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$num = count($data);
				if( $row != '1' ) {
					$csvBlog_id		= $data[0];
					$csvType		= $data[1];
					$csvName		= $data[2];
					$csvSlug		= $data[3];
					$csvDescription	= $data[4];

					// Valid Blog id import in selected blog
					// validateCsvData($data);
					if($blog_id == $csvBlog_id) {
						if($csvType == $blog_type_compare) {
							$result = wp_insert_term($csvName, $blog_type,
								array(
									'description' => $csvDescription,
									'slug' => $csvSlug,
								)
							);
							if ( ! is_wp_error($result) ) { $importCount++;	}
							$message = is_wp_error($result) ? $result->get_error_message() : ' Data inserted and new term ID: '.$result['term_id'];
							// $message = 'Data inserted';
						} else {
							$message = 'Type not similar';
						}
					} else {
						$message = 'Blog ID not similar';
					}
					//echo $row, ' ---  ', $message, "<br>";
					$log_data[$row] = array('CSV row' => $row,'type' => $blog_type, 'name' => $csvName, 'message' => $message, 'data' => $data);
				}
				$row++;         
			}
			fclose($handle);
			}
		restore_current_blog();
		// Create Log file
		$logFileName	= 	$network_name.'-'.date('YmdHis').'txt';
		$date			= 	date('Y-m-d H:i:s');
		$logFile		= 	fopen(GENERAL_SETTINGS_CPT_DIR_PATH . "/uploads/import-export-tag-category/logs/".$logFileName, "w");
		$logFileURL		= 	GENERAL_SETTINGS_CPT_DIR_PATH . "/uploads/import-export-tag-category/logs/".$logFileName;
		fwrite($logFile, json_encode($log_data)); /** Once the data is written it will be saved in the path given */
		fclose($logFile);

		global $wpdb;
		$wpdb->insert(
			$wpdb->base_prefix . 'ietc_log',
			array(
				'blog_id'		=> $blog_id,
				'userid'		=> $user_id,
				'type'			=> $blog_type,
				'import_export'	=> '2',
				'file'			=> $csvFileName,
				'logfile'		=> $logFileName,
				'inserted_date'	=> $date,
				'updated_date'	=> $date,
				)
			);
		$lastid = $wpdb->insert_id;
		// $result = array( 'message' => $network_name. ' - File successfully Import', 'log_file_path' => $logFileURL, 'network_name' => $network_name, 'log_id' => $lastid );
		$result = array( 'message' => $importCount. ' records import in '. $network_name, 'log_file_path' => $logFileURL, 'network_name' => $network_name, 'log_id' => $lastid );
		wp_send_json_success( $result );
		exit;
	}

   public static function ietc_export_tag_category() {
		$blog_id		= $_POST['network_source'];
		$network_type	= $_POST['network_type'];
		$network_name	= $_POST['network_name'];
		$user_id		= get_current_user_id();


		switch_to_blog( $blog_id );  
			// Create Export file
			$todayDate	= date('YmdHis');
			$date		= date('Y-m-d H:i:s');
			//echo get_temp_dir(); exit;
			$file_name 	= $network_name.'-'.$todayDate.'.csv';
			$fileDirPath= fopen(GENERAL_SETTINGS_CPT_DIR_PATH . "/uploads/import-export-tag-category/export/".$file_name, "w");
			$file_url	= GENERAL_SETTINGS_CPT_URL . "/uploads/import-export-tag-category/export/".$file_name;
			// $fileDirPath = fopen(get_temp_dir() . $todayDate.'.csv', "w");
			// $file_url = get_temp_dir() . $todayDate.'.csv';
			
			// $categories = get_categories($args);
			$terms_array = get_terms( array(
				'taxonomy' => $network_type,
				'hide_empty' => false,
			) );
			// echo "<pre>", print_r($terms), "</pre>";
			$term_type = "";
			if( $network_type === 'category' ) {
				$term_type = 'category';
				fputcsv($fileDirPath, array('blog_id', 'Type', 'Category ID', 'Category Name', 'Category Slug', 'Description'));
			} else {
				$term_type = 'tag';
				fputcsv($fileDirPath, array('blog_id', 'Type', 'Tag ID', 'Tag Name', 'Tag Slug', 'Description'));
			}

			foreach($terms_array as $terms) {
				$file_row = array($blog_id, $term_type, $terms->term_id, $terms->name, $terms->slug, $terms->description);
				fputcsv($fileDirPath, $file_row);
			}
		fclose($fileDirPath);
		restore_current_blog();

		global $wpdb;  
		$wpdb->insert(
			$wpdb->base_prefix . 'ietc_log',
			array(
				'blog_id'		=> $blog_id,
				'userid'		=> $user_id,
				'type'			=> $term_type,
				'import_export'	=> '1',
				'file'			=> $file_name,
				'inserted_date'	=> $date,
				'updated_date'	=> $date,
				)
			);
		$lastid = $wpdb->insert_id;
		$result = array( 'message' => $network_name. ' - File successfully Exported', 'file_path' => $file_url, 'network_name' => $network_name, 'log_id' => $lastid );
		wp_send_json_success( $result );
		exit;
   }
   
   public static function ietc_export_data(){
   
		$blog_id = $_POST['site_id'];
		$list_id = $_POST['list_id'];
   
		switch_to_blog( $blog_id );  
   
		$args = array(
			'hide_empty'      => false,
		);
   
		// Create Export file
			 $todayDate  = date('YmdHis');
			 $date = date('Y-m-d H:i:s');
			 $myfile = fopen(GENERAL_SETTINGS_CPT_DIR_PATH . "/uploads/import-export-tag-category/export/".$todayDate.'.csv', "w");
			 $file_url = GENERAL_SETTINGS_CPT_URL . "/uploads/import-export-tag-category/export/".$todayDate.'.csv';
			 $categories = get_categories($args); 
			 fputcsv($myfile, array('Category Name', 'Category Slug', 'Description'));    
			 foreach($categories as $category) {
				  
				  $file_row = array($category->name , $category->name, $category->description);
				  
				  fputcsv($myfile, $file_row);
			 }
			 fclose($myfile);  
   
			global $wpdb;  

			$title = $_POST['title'];
			$site = $_POST['site'];
			$des = $_POST['des'];
			$file = $_POST['file'];
   
			$wpdb->insert(
				$wpdb->base_prefix . 'ietc_export_log',
				array(
					'id'        => $list_id,
					'log_filename'   => $todayDate.'.csv',
					'inserted_date'   => $date,                          
					)
				);
		restore_current_blog();
		$result = array( 'message' => 'File successfully Exported', 'file_path' => $file_url );
		wp_send_json_success( $result );
		exit;
   }
   public static function ietc_imp_exp_init(){
		if(isset($_POST['list_publish']) && $_POST['list_publish'] != '')
		{
			$name_file = $_FILES['fileToUpload']['name'];
			$tmp_name = $_FILES['fileToUpload']['tmp_name'];
			$file_type = $_FILES["fileToUpload"]["type"];
			$allowedExts = array("csv");
			$extension = end(explode(".", $_FILES["fileToUpload"]["name"]));
			// This is for file upload in plugins uploads folder
			if (file_exists("upload/" . $_FILES["file"]["name"])) {
				echo $_FILES["fileToUpload"]["name"] . " already exists. ";
			} else
			{
				$target_file = GENERAL_SETTINGS_CPT_DIR_PATH. '/uploads/import-export-tag-category/import/'. basename($_FILES["fileToUpload"]["name"]);
				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
					// echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
				}
				else {
					echo "Sorry, there was an error uploading your file.";
				}
			}

			// This code for insert data in import export table
			global $wpdb;  
   
			$title = $_POST['title'];
			$site = $_POST['site'];
			$des = $_POST['des'];
			$file = $_POST['file'];
			$type = $_POST['type'];
   
			$wpdb->insert(
				$wpdb->base_prefix . 'ietc',
				array(
					'title'        => $title,
					'site'        => $site,
					'description'   => $des,
					'file'   => basename($_FILES["fileToUpload"]["name"]),
					'taxonomy_type' => $type,
					)
				);
				$lastid = $wpdb->insert_id;
				if($lastid){
					wp_redirect(site_url('wp-admin/network/admin.php?page=ietc_page&action=edit&edit='.$lastid.'&msg=added'));
					// wp_redirect(site_url('wp-admin/network/admin.php?page=ietc_page&msg=success'));
					exit;            
				}
				else{
					wp_redirect(site_url('wp-admin/network/admin.php?page=ietc_add_new&msg=error'));
					exit;            
				}
			}
   
		if(isset($_POST['list_update']) && $_POST['list_update'] != '')
		{
			 global $wpdb;
			 $title = $_POST['title'];          
			 $site = $_POST['site'];
			 $des = $_POST['des'];
			 $id = $_GET['edit'];
			 $taxonomy_type = $_POST['taxonomy_type'];
   
			 if($des == ''){
				  $des = ' ';
			 }
			 $update_sql = "UPDATE " .$wpdb->base_prefix . 'ietc SET description="'.$des.'", title = "'.$title .'", site = '.$site.', taxonomy_type = "'.$taxonomy_type .'" WHERE id='.$id;
			 $wpdb->query($wpdb->prepare($update_sql));
			 
			// echo $update_sql;
			// exit;
			 wp_redirect(site_url('wp-admin/network/admin.php?page=ietc_page&action=edit&edit='.$_GET['edit'].'&msg=success'));
			 exit;
   
		}
   
		if( isset($_GET['action']) && $_GET['action'] == 'delete' ) {
			echo "Remove"; exit;
			global $wpdb;
			$id = $_GET['id'];

			echo $sql = "SELECT * FROM {$wpdb->prefix}ietc_log WHERE id=".$id;
			exit;
			$result = $wpdb->get_results( $sql);
			
			$delete_sql = "DELETE FROM {$wpdb->prefix}ietc where id = ".$id;        

			$result = $wpdb->query( $delete_sql );
			$delete_msg = array('msg'=> 'delete');
			
			wp_redirect(site_url('wp-admin/network/admin.php?page=ietc_page&msg=delete'));
			exit;
		   }
   }
   
   public static function ietc_general_admin_notice(){
	   global $pagenow;
		 if ( ! isset( $_GET['msg'] ) ) {
				  return;
			   }
   
		if ( $pagenow == 'admin.php' ) {      
   
			 $error_class = $_GET['msg'] == 'success' ? 'notice notice-success is-dismissible' : 'error' ;
			 $error_message = $_GET['msg'] == 'success' ? 'New Record Insert successfully' : 'there is issue in add new' ;
			 
			 if(isset($_GET['page']) && $_GET['page'] == 'ietc_page'){
				  if(isset($_GET['action']) && $_GET['action'] == 'edit'){
					   $error_class = $_GET['msg'] == 'success' ? 'notice notice-success is-dismissible' : 'error' ;
					   $error_message = $_GET['msg'] == 'success' ? 'List updated.' : 'there is issue in add new' ;
				  }
			   
			 }
			if(isset($_GET['page']) && $_GET['page'] == 'ietc_page'){               
				if(isset($_GET['msg']) && $_GET['msg'] == 'added'){
					 $error_class = 'notice notice-success is-dismissible';
					 $error_message = 'New Record Insert successfully.';
				}            
		   }
			 if(isset($_GET['page']) && $_GET['page'] == 'ietc_page'){               
				  if(isset($_GET['msg']) && $_GET['msg'] == 'delete'){
					   $error_class = 'notice notice-success is-dismissible';
					   $error_message = 'Item deleted.';
				  }            
			 }
   
			 echo '<div class="'. $error_class .'">
				   <p>'. $error_message .'</p>
			  </div>';
		}
   }
   	public static function ietc_activation() {
		/* global $wpdb;
		$import_table = $wpdb->prefix . 'ietc_import_log';  // table name
		$charset_collate = $wpdb->get_charset_collate();
		//Check to see if the table exists already, if not, then create it
		$logtable = $wpdb->prefix . 'ietc_log';  // table name
		if($wpdb->get_var( "show tables like '$logtable'" ) != $logtable ) {
			$sql = "CREATE TABLE IF NOT EXISTS $logtable (
					id int(11) NOT NULL AUTO_INCREMENT,
					blog_id int(11) NOT NULL,
					userid int(11) NOT NULL,
					type varchar(250) NOT NULL,
					import_export int(4) NOT NULL,
					file varchar(250) NOT NULL,
					logfile varchar(250) NULL,
					inserted_date varchar(250) NOT NULL,
					updated_date varchar(250) NOT NULL,
					PRIMARY KEY (id)
				) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;";
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
				add_option( 'test_db_version', $test_db_version );
			} */

	   //Check to see if the table exists already, if not, then create it
	  /*  if($wpdb->get_var( "show tables like '$import_table'" ) != $import_table ) {
		   $sql = "CREATE TABLE $import_table (
			   log_id int(11) NOT NULL auto_increment,
			   id varchar(15) NOT NULL,
			   log_filename varchar(60) NOT NULL,
			   inserted_date varchar(200) NOT NULL,
			   UNIQUE KEY id (log_id)
			   ) $charset_collate;";
			   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			   dbDelta( $sql );
			add_option( 'test_db_version', $test_db_version );
		}
		//Check to see if the table exists already, if not, then create it
		$export_table = $wpdb->prefix . 'ietc_export_log';  // table name
		if($wpdb->get_var( "show tables like '$export_table'" ) != $export_table ) 
		{
			$sql = "CREATE TABLE $export_table (
				log_id int(11) NOT NULL auto_increment,
				id varchar(15) NOT NULL,
				log_filename varchar(100) NOT NULL,
				inserted_date varchar(100) NOT NULL,               
				UNIQUE KEY id (log_id)
				) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		add_option( 'test_db_version', $test_db_version );
		}

		//Check to see if the table exists already, if not, then create it
		$main_table = $wpdb->prefix . 'ietc';  // table name
		if($wpdb->get_var( "show tables like '$main_table'" ) != $main_table ) {
			$sql = "CREATE TABLE IF NOT EXISTS $main_table (
					id int(11) NOT NULL AUTO_INCREMENT,
					title varchar(250) NOT NULL,
					site varchar(250) NOT NULL,
					description varchar(250) NOT NULL,
					file varchar(250) NOT NULL,
					taxonomy_type varchar(250) NOT NULL,
					inserted_date varchar(250) NOT NULL,
					updated_date varchar(250) NOT NULL,
					PRIMARY KEY (id)
				) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;";
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
				add_option( 'test_db_version', $test_db_version );
			} */
	}
}

ImportExportTagCategory::init();
