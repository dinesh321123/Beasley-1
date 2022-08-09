<?php
/**
 * Class ImportExportTagCategory
 */
class ImportExportTagCategory {
	function __construct()
	{
		$this->init();
	}
	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function init() {
		add_action( 'init', array( __CLASS__, 'ietc_init' ), 0 );
		add_action( 'admin_init', array( __CLASS__, 'ietc_imp_exp_init' ) );
		add_action('network_admin_notices', array( __CLASS__, 'ietc_general_admin_notice' ) ) ;

		add_action('network_admin_menu', array( __CLASS__, 'ietc_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'ietc_enqueue_scripts' ) );

		add_action( 'wp_ajax_ietc_export_tag_category', array( __CLASS__, 'ietc_export_tag_category' ) );
		add_action( 'wp_ajax_nopriv_ietc_export_tag_category', array( __CLASS__, 'ietc_export_tag_category' ) );

		add_action( 'wp_ajax_ietc_export_users_station', array( __CLASS__, 'ietc_export_users_station' ) );
		add_action( 'wp_ajax_nopriv_ietc_export_users_station', array( __CLASS__, 'ietc_export_users_station' ) );

		add_action( 'wp_ajax_ietc_export_users_posts', array( __CLASS__, 'ietc_export_users_posts' ) );
		add_action( 'wp_ajax_nopriv_ietc_export_users_posts', array( __CLASS__, 'ietc_export_users_posts' ) );

		add_action( 'wp_ajax_ietc_import_tag_category', array( __CLASS__, 'ietc_import_tag_category' ) );
		add_action( 'wp_ajax_nopriv_ietc_import_tag_category', array( __CLASS__, 'ietc_import_tag_category' ) );
	}
	public static function ietc_init() {
		$roles = [ 'administrator' ];

		foreach ( $roles as $role ) {
			$role_obj = get_role( $role );

			if ( is_a( $role_obj, \WP_Role::class ) ) {
				$role_obj->add_cap( 'manage_export_user_data', false );
			}
		}
	}

	public static function ietc_admin_menu() {
		add_menu_page(
			__('Import Export tag & category', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN),
			__('Import Export tag & category', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN),
		   'manage_options',
		   'ietc_logs',
		   array( __CLASS__, 'ietc_logs_form' ),
		   'dashicons-admin-multisite'
		 );
		add_submenu_page(
			'ietc_logs',
			__('Logs', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN),
			__('Logs', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN),
			'manage_options',
			'ietc_logs',
			array( __CLASS__, 'ietc_logs_form' )
		);
		add_submenu_page(
			'ietc_logs',
			__('Import tag & category', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN),
			__('Import tag & category', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN),
			'manage_options',
			'ietc_import',
			array( __CLASS__, 'ietc_import_form' )
		);
		add_submenu_page(
			'ietc_logs',
			__('Export tag & category', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN),
			__('Export tag & category', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN),
			'manage_options',
			'ietc_export',
			array( __CLASS__, 'ietc_export_form' )
		);
	   add_submenu_page(
		   'ietc_logs',
		   __('Export user posts', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN),
		   __('Export user posts', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN),
		   'manage_export_user_data',
		   'ietc_export_users',
		   array( __CLASS__, 'ietc_export_users_form' )
	   );
   }

	public static function ietc_logs_form() {
		if (is_file( TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH . 'includes/import-export-tag-category/ietc-logs.php')) {
			include_once TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH . 'includes/import-export-tag-category/ietc-logs.php';
		}
	}

	public static function ietc_export_form() {
		if (is_file( TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH . 'includes/import-export-tag-category/ietc-export.php')) {
			include_once TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH . 'includes/import-export-tag-category/ietc-export.php';
		}
	}

	public static function ietc_export_users_form() {
		if (is_file( TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH . 'includes/import-export-tag-category/ietc-export-user.php')) {
			include_once TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH . 'includes/import-export-tag-category/ietc-export-user.php';
		}
	}
	public static function ietc_import_form() {
		if (is_file( TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH . 'includes/import-export-tag-category/ietc-import.php')) {
			include_once TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH . 'includes/import-export-tag-category/ietc-import.php';
		}
	}

	public static function ietc_enqueue_scripts() {
		global $typenow, $pagenow;
		$admin_page = filter_input(INPUT_GET, "page", FILTER_SANITIZE_STRIPPED) != "" ? filter_input(INPUT_GET, "page", FILTER_SANITIZE_STRIPPED) : "" ;
		if ( in_array( $admin_page, array( 'ietc_export', 'ietc_logs', 'ietc_import', 'ietc_export_users' ) ) && in_array( $pagenow, array( 'admin.php' ) ) ) {
			$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
			//Add the Select2 CSS file
			wp_enqueue_style( 'general-settings-select2css', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_URL .'assets/css/select2.min.css', array(),TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_VERSION, 'all');
			wp_enqueue_script( 'general-settings-select2js', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_URL .'assets/js/select2.min.js', 'jquery', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_VERSION);

			wp_enqueue_script( 'import-export-tag-category', TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_URL ."assets/js/import-export-tag-category$postfix.js", array('jquery'), TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_VERSION);
			wp_localize_script( 'import-export-tag-category', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
			wp_enqueue_media();
			wp_enqueue_editor();
		}
	}

   public static function ietc_import_tag_category() {
	   $csvFileName			=	date('YmdHis').'-'.$_FILES['csv_file']['name'];
	   $csvFileTemp			=	$_FILES['csv_file']['tmp_name'];
	   $blog_id				=	filter_input(INPUT_POST, "network_source", FILTER_SANITIZE_STRIPPED);
	   $blog_type			=	filter_input(INPUT_POST, "network_type", FILTER_SANITIZE_STRIPPED);
	   $blog_type_compare	=	filter_input(INPUT_POST, "network_type", FILTER_SANITIZE_STRIPPED) == 'post_tag' ? 'tag' : filter_input(INPUT_POST, "network_type", FILTER_SANITIZE_STRIPPED);
	   $network_name		=	filter_input(INPUT_POST, "network_name", FILTER_SANITIZE_STRIPPED);
	   $user_id				=	get_current_user_id();

	   // echo "<pre>", print_r($_FILES['csv_file']) ;
		// echo " ---------  ", print_r($_REQUEST), "</pre>";

		$csvTargetFile = TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH. 'ietc_uploads/import-export-tag-category/import/'. basename($csvFileName);
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
	   $filter_network_name		=	str_replace(' ', '', $network_name);
	   $logFileName				= 	$filter_network_name.'-'.date('YmdHis').'.txt';
	   $date					= 	date('Y-m-d H:i:s');
	   $logFile					= 	fopen(TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH . "ietc_uploads/import-export-tag-category/logs/".$logFileName, "w");
	   $logFileURL				= 	TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_URL . "ietc_uploads/import-export-tag-category/logs/".$logFileName;
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
		$blog_id		= filter_input( INPUT_POST, 'network_source', FILTER_SANITIZE_STRIPPED);
		$network_type	= filter_input( INPUT_POST, 'network_type', FILTER_SANITIZE_STRIPPED);
		$network_name	= filter_input( INPUT_POST, 'network_name', FILTER_SANITIZE_STRIPPED);
		$user_id		= get_current_user_id();
		switch_to_blog( $blog_id );
			// Create Export file
			$todayDate	= date('YmdHis');
			$date		= date('Y-m-d H:i:s');
			//echo get_temp_dir(); exit;
			$string = str_replace(' ', '', $network_name);
			$file_name 	= $string.'-'.$todayDate.'.csv';
			$fileDirPath= fopen(TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH . "ietc_uploads/import-export-tag-category/export/".$file_name, "w");
			$file_url	= TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_URL . "ietc_uploads/import-export-tag-category/export/".$file_name;
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

	public function get_export_user_data_posttype_list() {
		$result	= (array) apply_filters( 'export-user-data-post-types', array( 'post', 'gmr_gallery', 'listicle_cpt', 'affiliate_marketing' )  );
		return $result;
	}

	public function get_user_list() {
		global $wpdb;
		$result	= $wpdb->get_results(sprintf('SELECT * FROM '. $wpdb->prefix .'users'));
		return $result;
	}

	public function ietc_export_users_posts() {
		global $wpdb;
		$blog_id		= 0;
		$network_name	= 'All';
		$term_type		= 'user posts';
		$user_id		= get_current_user_id();
		$input_type		= filter_input( INPUT_POST, 'input_type', FILTER_SANITIZE_STRIPPED);
		$export_from	= filter_input( INPUT_POST, 'export_from', FILTER_SANITIZE_STRIPPED);
		$export_to		= filter_input( INPUT_POST, 'export_to', FILTER_SANITIZE_STRIPPED);
		// $users		= $wpdb->get_results(sprintf('SELECT * FROM '. $wpdb->prefix .'users'));
		$users			= self::get_user_list();

		// Create User Export file
		$todayDate		= date('YmdHis');
		$date			= date('Y-m-d H:i:s');
		$file_name		= 'User-'.$todayDate.'.csv';
		$fileDirPath	= fopen(TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH . "ietc_uploads/import-export-tag-category/export/".$file_name, "w");
		$file_url		= TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_URL . "ietc_uploads/import-export-tag-category/export/".$file_name;

		if ($input_type == 'post_list'){
			fputcsv($fileDirPath, array('Station Name', 'User Login', 'Display Name', 'Email', 'Post title', 'Permalink', 'Post date'));
			$record_count			= 0;
			echo "<pre>", print_r($users), "</pre>";
			foreach($users as $user){
				$getBlogsdetials	= get_blogs_of_user( array( 'fields' => array( 'ID' ) ) );
				foreach( $getBlogsdetials as $id ) {
		    		switch_to_blog( $id->userblog_id );
					$station_name	= get_bloginfo();
					$from_date		= date('Y-m-d', strtotime('-1 day', strtotime($export_from)));
					$to_date		= date('Y-m-d', strtotime('+1 day', strtotime($export_to)));

					$export_query_string	= array(
						'author'		=> $user->ID,
						'post_type'		=> ImportExportTagCategory::get_export_user_data_posttype_list(),
						'post_status'	=> 'publish',
						'date_query'	=> array(
							'column'	=> 'post_date',
							'after'		=> $from_date,
							'before'	=> $to_date
						)
					);
					/* $export_query_string = array(
						'author' => $user->ID,
						'post_type' => 'post',
						'post_status' => 'publish'
					); */

					$query_posts		= new WP_Query( $export_query_string );
					echo $user->ID, " --- ", $query_posts->post_count, "<br>";
					echo "<pre>", print_r($query_posts->posts), "</pre>";

					while ( $query_posts->have_posts() ) {
						$query_posts->the_post();

						$title		= get_the_title();
						$permalink	= get_the_permalink();
						$post_date	= get_the_date();
						$file_row	= array( $station_name, $user->user_login, $user->display_name, $user->user_email, $title, $permalink, $post_date );
						// echo "<pre>", print_r($file_row);
						fputcsv($fileDirPath, $file_row);
						$record_count++;
					}	// End While
					wp_reset_postdata();
					restore_current_blog();
				}	//End blog Foreach
			}	//End User Foreach
			echo 'Count - ', $record_count;
			if (isset($record_count) && $record_count == 0) {
				$file_row	= array( 'No records found during this period');
				fputcsv($fileDirPath, $file_row);
			}
			fclose($fileDirPath);
		} else {
			echo "In else condition from User Post list";
			exit;
		}

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


		$result = array( 'message' => ' Users file successfully Exported for Posts lists', 'file_path' => $file_url, 'network_name' => $network_name, 'log_id' => $lastid );
		wp_send_json_success( $result );
		exit;
	}


   public function ietc_export_users_station() {
	   global $wpdb;
	   $blog_id			= 0;
	   $network_name	= 'All';
	   $term_type		= 'user station';
	   $user_id			= get_current_user_id();
	   $input_type		= filter_input( INPUT_POST, 'input_type', FILTER_SANITIZE_STRIPPED);
	   // $users			= $wpdb->get_results(sprintf('SELECT * FROM '. $wpdb->prefix .'users'));
	   $users			= ImportExportTagCategory::get_user_list();

	   // Create User Export file
	   $todayDate		= date('YmdHis');
	   $date			= date('Y-m-d H:i:s');
	   $file_name		= 'User-'.$todayDate.'.csv';
	   $fileDirPath		= fopen(TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH . "ietc_uploads/import-export-tag-category/export/".$file_name, "w");
	   $file_url		= TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_URL . "ietc_uploads/import-export-tag-category/export/".$file_name;

	   if($input_type == 'station_list'){
		   fputcsv($fileDirPath, array('Username', 'Email', 'Name', 'Role', 'Last login', 'Active Status', 'Station Name'));
		   foreach($users as $user){
			   $userdata			= get_userdata( $user->ID );
			   $stationName			= array();
			   $roles				= implode(', ', $userdata->roles );
			   $lastLoginmeta		= get_user_meta($user->ID, 'bbgi_user_last_login_meta', true);
			   $lastlogin			= isset($lastLoginmeta) && $lastLoginmeta != "" ? date('Y-m-d H:i:s', $lastLoginmeta) : 'Never' ;
			   $activeStatusMeta	= get_user_meta($user->ID, 'bbgi_is_user_disabled', true);
			   $activestatus		= isset($activeStatusMeta) && $activeStatusMeta === "0" ? 'Yes' : 'No' ;
			   $getBlogsdetials 	= get_blogs_of_user( $user->ID );

			   if(count($getBlogsdetials) > 0 ){
				   foreach($getBlogsdetials as $getBlogsdetial){
					   $stationName[] = $getBlogsdetial->domain;
				   }
			   }
			   $stationName			= implode(' || ', $stationName);

			   $file_row = array( $userdata->user_login, $userdata->user_email, $userdata->display_name, $roles, $lastlogin, $activestatus, $stationName );
			   // echo "<pre>", print_r($file_row);
			   fputcsv($fileDirPath, $file_row);
		   }
		   fclose($fileDirPath);
	   } else {
		   echo "In else condition for User Station list"; exit;
	   }

	   $wpdb->insert(
		   $wpdb->base_prefix . 'ietc_log',
		   array(
			   'blog_id'		=> $blog_id,
			   'userid'			=> $user_id,
			   'type'			=> $term_type,
			   'import_export'	=> '1',
			   'file'			=> $file_name,
			   'inserted_date'	=> $date,
			   'updated_date'	=> $date,
		   )
	   );
	   $lastid = $wpdb->insert_id;

	   $result = array( 'message' => ' Users file successfully Exported for Station lists', 'file_path' => $file_url, 'network_name' => $network_name, 'log_id' => $lastid );
	   wp_send_json_success( $result );
	   exit;
   }
   public static function ietc_imp_exp_init(){
		if(filter_input( INPUT_POST, 'list_publish', FILTER_SANITIZE_STRIPPED) != '')
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
				$target_file = TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH. 'ietc_uploads/import-export-tag-category/import/'. basename($_FILES["fileToUpload"]["name"]);
				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
					// echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
				}
				else {
					echo "Sorry, there was an error uploading your file.";
				}
			}

			// This code for insert data in import export table
			global $wpdb;

			$title = filter_input( INPUT_POST, 'title', FILTER_SANITIZE_STRIPPED);
			$site = filter_input( INPUT_POST, 'site', FILTER_SANITIZE_STRIPPED);
			$des = filter_input( INPUT_POST, 'des', FILTER_SANITIZE_STRIPPED);
			$file = filter_input( INPUT_POST, 'file', FILTER_SANITIZE_STRIPPED);
			$type = filter_input( INPUT_POST, 'type', FILTER_SANITIZE_STRIPPED);

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

		if(filter_input( INPUT_POST, 'list_update', FILTER_SANITIZE_STRIPPED) != '')
		{
			 global $wpdb;
			 $title		= filter_input( INPUT_POST, 'title', FILTER_SANITIZE_STRIPPED);
			 $site		= filter_input( INPUT_POST, 'site', FILTER_SANITIZE_STRIPPED);
			 $des		= filter_input( INPUT_POST, 'des', FILTER_SANITIZE_STRIPPED);
			 $id		= filter_input( INPUT_POST, 'edit', FILTER_SANITIZE_STRIPPED);
			 $taxonomy_type = filter_input( INPUT_POST, 'taxonomy_type', FILTER_SANITIZE_STRIPPED);

			 if($des == ''){
				  $des = ' ';
			 }
			 $update_sql = "UPDATE " .$wpdb->base_prefix . 'ietc SET description="'.$des.'", title = "'.$title .'", site = '.$site.', taxonomy_type = "'.$taxonomy_type .'" WHERE id='.$id;
			 $wpdb->query($wpdb->prepare($update_sql));

			// echo $update_sql;
			// exit;
			 wp_redirect(site_url('wp-admin/network/admin.php?page=ietc_page&action=edit&edit='.filter_input(INPUT_GET, 'edit', FILTER_SANITIZE_STRIPPED).'&msg=success'));
			 exit;

		}

	   $del_id = filter_input( INPUT_GET, 'delete', FILTER_SANITIZE_STRIPPED);

		if( isset($del_id) && $del_id != '' ){
			global $wpdb;
			$sqlQuery	=	"SELECT * FROM {$wpdb->prefix}ietc_log where id = ".$del_id;
			$sqlData	=	$wpdb->get_results( $sqlQuery );
			if( !empty($sqlData[0]) ){
				// 1 for export and 2 for import
				$folderPath		=	isset($sqlData[0]->import_export) && $sqlData[0]->import_export == 1 ? 'export' : 'import' ;
				$fileName		=	$sqlData[0]->file;
				$logsName		=	isset($sqlData[0]->import_export) && $sqlData[0]->import_export == 2 ? $sqlData[0]->logfile : '' ;
				$logsPath		=	TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH. 'ietc_uploads/import-export-tag-category/logs/'.$logsName;
				$file_path		=	TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH. 'ietc_uploads/import-export-tag-category/' . $folderPath . '/'.$fileName;  // path of the file which need to be deleted.

				if(file_exists($file_path)) {
					wp_delete_file($file_path);
				}
				if( file_exists($logsPath) ) {
					wp_delete_file( $logsPath );
				}
				$del_sql	= "DELETE FROM {$wpdb->prefix}ietc_log WHERE id = $del_id";
				$result		= $wpdb->get_results( $del_sql);
				wp_redirect(site_url('wp-admin/network/admin.php?page=ietc_logs&msg=delete'));
				exit;
			} else {
				wp_redirect(site_url('wp-admin/network/admin.php?page=ietc_logs&msg=error_delete'));
				exit;
			}
		}
   }

   public static function ietc_general_admin_notice(){
	   global $pagenow;

		 if (empty(filter_input( INPUT_GET, 'msg', FILTER_SANITIZE_STRIPPED))) {
				  return;
			   }

		if ( $pagenow == 'admin.php' ) {

			/* $error_class = $_GET['msg'] == 'success' ? 'notice notice-success is-dismissible' : 'error' ;
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
		   } */


			if(filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRIPPED)== 'ietc_logs'){
				if(filter_input( INPUT_GET, 'msg', FILTER_SANITIZE_STRIPPED) == 'delete'){
					$error_class = 'notice notice-success is-dismissible';
					$error_message = 'Item deleted.';
				}
			}
			if(filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRIPPED) == 'ietc_logs'){
				if(filter_input( INPUT_GET, 'msg', FILTER_SANITIZE_STRIPPED) == 'error_delete'){
					$error_class = 'notice notice-success is-dismissible';
					$error_message = 'Sorry, there was an error delete item.';
				}
			}

			 echo '<div class="'. $error_class .'">
				   <p>'. $error_message .'</p>
			  </div>';
		}
   }
   	public static function ietc_activation() {
		global $wpdb;
		$charset_collate	= $wpdb->get_charset_collate();
		$logtable 			= $wpdb->prefix . 'ietc_log';  // table name
		if($wpdb->get_var("show tables like '$logtable'") != $logtable) {
			$sql = "CREATE TABLE IF NOT EXISTS $logtable (
				id int(11) NOT NULL AUTO_INCREMENT,
				blog_id int(11) NOT NULL,
				userid int(11) NOT NULL,
				type varchar(50) NOT NULL,
				import_export int(4) NOT NULL,
				file varchar(60) NOT NULL,
				logfile varchar(60) NULL,
				inserted_date datetime NOT NULL,
				updated_date datetime NOT NULL,
				PRIMARY KEY (id) ) $charset_collate;";
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
				// add_option( 'test_db_version', $test_db_version );
			}
	   }
}

// ImportExportTagCategory::init();
new ImportExportTagCategory();
