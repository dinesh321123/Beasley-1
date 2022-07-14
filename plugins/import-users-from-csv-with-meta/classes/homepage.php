<?php
if ( ! defined( 'ABSPATH' ) )
    exit;

class ACUI_Homepage{
	function __construct(){
	}

    function hooks(){
        add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ), 10, 1 );
		add_action( 'acui_homepage_start', array( $this, 'maybe_remove_old_csv' ) );
        add_action( 'wp_ajax_acui_delete_attachment', array( $this, 'delete_attachment' ) );
		add_action( 'wp_ajax_acui_bulk_delete_attachment', array( $this, 'bulk_delete_attachment' ) );
		add_action( 'wp_ajax_acui_delete_users_assign_posts_data', array( $this, 'delete_users_assign_posts_data' ) );
    }

    function load_scripts( $hook ){
        if( $hook != 'tools_page_acui' || ( isset( $_GET['tab'] ) && $_GET['tab'] != 'homepage' ) )
            return;

        wp_enqueue_style( 'select2-css', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' );
        wp_enqueue_script( 'select2-js', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js' );
    }

	static function admin_gui(){
		$last_roles_used = empty( get_option( 'acui_last_roles_used' ) ) ? array( 'subscriber' ) : get_option( 'acui_last_roles_used' );
?>
	<div class="wrap acui">

		<?php do_action( 'acui_homepage_start' ); ?>

		<div>
			<h2><?php _e( 'Export users and customers','import-users-from-csv-with-meta' ); ?></h2>
		</div>

		<div style="clear:both;"></div>

	</div>
	<script type="text/javascript">
	jQuery( document ).ready( function( $ ){
		check_delete_users_checked();

        $( '#uploadfile_btn' ).click( function(){
            if( $( '#uploadfile' ).val() == "" && $( '#upload_file' ).is( ':visible' ) ) {
                alert("<?php _e( 'Please choose a file', 'import-users-from-csv-with-meta' ); ?>");
                return false;
            }

            if( $( '#path_to_file' ).val() == "" && $( '#introduce_path' ).is( ':visible' ) ) {
                alert("<?php _e( 'Please enter a path to the file', 'import-users-from-csv-with-meta' ); ?>");
                return false;
            }
        } );

		$( '.acui-checkbox.roles[value="no_role"]' ).click( function(){
			var checked = $( this ).is(':checked');
			if( checked ) {
				if( !confirm( '<?php _e( 'Are you sure you want to disables roles from this users?', 'import-users-from-csv-with-meta' ); ?>' ) ){
					$( this ).removeAttr( 'checked' );
					return;
				}
				else{
					$( '.acui-checkbox.roles' ).not( '.acui-checkbox.roles[value="no_role"]' ).each( function(){
						$( this ).removeAttr( 'checked' );
					} )
				}
			}
		} );

		$( '.acui-checkbox.roles' ).click( function(){
			if( $( this ).val() != 'no_role' && $( this ).val() != '' )
				$( '.acui-checkbox.roles[value="no_role"]' ).removeAttr( 'checked' );
		} );

		$( '#delete_users' ).on( 'click', function() {
			check_delete_users_checked();
		});

		$( '.delete_attachment' ).click( function(){
			var answer = confirm( "<?php _e( 'Are you sure to delete this file?', 'import-users-from-csv-with-meta' ); ?>" );
			if( answer ){
				var data = {
					'action': 'acui_delete_attachment',
					'attach_id': $( this ).attr( "attach_id" ),
					'security': '<?php echo wp_create_nonce( "codection-security" ); ?>'
				};

				$.post(ajaxurl, data, function(response) {
					if( response != 1 )
						alert( response );
					else{
						alert( "<?php _e( 'File successfully deleted', 'import-users-from-csv-with-meta' ); ?>" );
						document.location.reload();
					}
				});
			}
		});

		$( '#bulk_delete_attachment' ).click( function(){
			var answer = confirm( "<?php _e( 'Are you sure to delete ALL CSV files uploaded? There can be CSV files from other plugins.', 'import-users-from-csv-with-meta' ); ?>" );
			if( answer ){
				var data = {
					'action': 'acui_bulk_delete_attachment',
					'security': '<?php echo wp_create_nonce( "codection-security" ); ?>'
				};

				$.post(ajaxurl, data, function(response) {
					if( response != 1 )
						alert( "<?php _e( 'There were problems deleting the files, please check files permissions', 'import-users-from-csv-with-meta' ); ?>" );
					else{
						alert( "<?php _e( 'Files successfully deleted', 'import-users-from-csv-with-meta' ); ?>" );
						document.location.reload();
					}
				});
			}
		});

		$( '.toggle_upload_path' ).click( function( e ){
			e.preventDefault();
			$( '#upload_file,#introduce_path' ).toggle();
		} );

		$( '#vote_us' ).click( function(){
			var win=window.open( 'http://wordpress.org/support/view/plugin-reviews/import-users-from-csv-with-meta?free-counter?rate=5#postform', '_blank');
			win.focus();
		} );

        $( '#change_role_not_present_role' ).select2();

        $( '#delete_users_assign_posts' ).select2({
            ajax: {
                url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
                cache: true,
                dataType: 'json',
                minimumInputLength: 3,
                allowClear: true,
                placeholder: { id: '', title: '<?php _e( 'Delete posts of deleted users without assigning to any user', 'import-users-from-csv-with-meta' )  ?>' },
                data: function( params ) {
                    if (params.term.trim().length < 3)
                        throw false;

                    var query = {
                        search: params.term,
                        _wpnonce: '<?php echo wp_create_nonce( 'codection-security' ); ?>',
                        action: 'acui_delete_users_assign_posts_data',
                    }

                    return query;
                }
            }
        });

		function check_delete_users_checked(){
			if( $( '#delete_users' ).is( ':checked' ) ){
                $( '#delete_users_assign_posts' ).prop( 'disabled', false );
				$( '#change_role_not_present' ).prop( 'disabled', true );
				$( '#change_role_not_present_role' ).prop( 'disabled', true );
			} else {
                $( '#delete_users_assign_posts' ).prop( 'disabled', true );
				$( '#change_role_not_present' ).prop( 'disabled', false );
				$( '#change_role_not_present_role' ).prop( 'disabled', false );
			}
		}
	} );
	</script>
	<?php
	}

	function maybe_remove_old_csv(){
		$args_old_csv = array( 'post_type'=> 'attachment', 'post_mime_type' => 'text/csv', 'post_status' => 'inherit', 'posts_per_page' => -1 );
		$old_csv_files = new WP_Query( $args_old_csv );

		if( $old_csv_files->found_posts > 0 ): ?>
		<div class="postbox">
		    <div title="<?php _e( 'Click to open/close', 'import-users-from-csv-with-meta' ); ?>" class="handlediv">
		      <br>
		    </div>

		    <h3 class="hndle"><span>&nbsp;&nbsp;&nbsp;<?php _e( 'Old CSV files uploaded', 'import-users-from-csv-with-meta' ); ?></span></h3>

		    <div class="inside" style="display: block;">
		    	<p><?php _e( 'For security reasons you should delete this files, probably they would be visible in the Internet if a bot or someone discover the URL. You can delete each file or maybe you want delete all CSV files you have uploaded:', 'import-users-from-csv-with-meta' ); ?></p>
		    	<input type="button" value="<?php _e( 'Delete all CSV files uploaded', 'import-users-from-csv-with-meta' ); ?>" id="bulk_delete_attachment" style="float:right;" />
		    	<ul>
		    		<?php while($old_csv_files->have_posts()) :
		    			$old_csv_files->the_post();

		    			if( get_the_date() == "" )
		    				$date = "undefined";
		    			else
		    				$date = get_the_date();
		    		?>
		    		<li><a href="<?php echo wp_get_attachment_url( get_the_ID() ); ?>"><?php the_title(); ?></a> <?php _e( 'uploaded on', 'import-users-from-csv-with-meta' ) . ' ' . $date; ?> <input type="button" value="<?php _e( 'Delete', 'import-users-from-csv-with-meta' ); ?>" class="delete_attachment" attach_id="<?php the_ID(); ?>" /></li>
		    		<?php endwhile; ?>
		    		<?php wp_reset_postdata(); ?>
		    	</ul>
		        <div style="clear:both;"></div>
		    </div>
		</div>
		<?php endif;
	}

    function delete_attachment() {
		check_ajax_referer( 'codection-security', 'security' );

		if( ! current_user_can( apply_filters( 'acui_capability', 'create_users' ) ) )
            wp_die( __( 'Only users who are able to create users can delete CSV attachments.', 'import-users-from-csv-with-meta' ) );

		$attach_id = absint( $_POST['attach_id'] );
		$mime_type  = (string) get_post_mime_type( $attach_id );

		if( $mime_type != 'text/csv' )
			_e('This plugin only can delete the type of file it manages, CSV files.', 'import-users-from-csv-with-meta' );

		$result = wp_delete_attachment( $attach_id, true );

		if( $result === false )
			_e( 'There were problems deleting the file, please check file permissions', 'import-users-from-csv-with-meta' );
		else
			echo 1;

		wp_die();
	}

	function bulk_delete_attachment(){
		check_ajax_referer( 'codection-security', 'security' );

		if( ! current_user_can( apply_filters( 'acui_capability', 'create_users' ) ) )
        wp_die( __( 'Only users who are able to create users can bulk delete CSV attachments.', 'import-users-from-csv-with-meta' ) );

		$args_old_csv = array( 'post_type'=> 'attachment', 'post_mime_type' => 'text/csv', 'post_status' => 'inherit', 'posts_per_page' => -1 );
		$old_csv_files = new WP_Query( $args_old_csv );
		$result = 1;

		while($old_csv_files->have_posts()) :
			$old_csv_files->the_post();

			$mime_type  = (string) get_post_mime_type( get_the_ID() );
			if( $mime_type != 'text/csv' )
				wp_die( __('This plugin only can delete the type of file it manages, CSV files.', 'import-users-from-csv-with-meta' ) );

			if( wp_delete_attachment( get_the_ID(), true ) === false )
				$result = 0;
		endwhile;

		wp_reset_postdata();

		echo $result;

		wp_die();
	}

    function delete_users_assign_posts_data(){
        check_ajax_referer( 'codection-security', 'security' );

		if( ! current_user_can( apply_filters( 'acui_capability', 'create_users' ) ) )
            wp_die( __( 'Only users who are able to create users can manage this option.', 'import-users-from-csv-with-meta' ) );

        $results = array( array( 'id' => '', 'value' => __( 'Delete posts of deleted users without assigning to any user', 'import-users-from-csv-with-meta' ) ) );
        $search = sanitize_text_field( $_GET['search'] );

        if( strlen( $search ) >= 3 ){
            $blogusers = get_users( array( 'fields' => array( 'ID', 'display_name' ), 'search' => '*' . $search . '*' ) );

            foreach ( $blogusers as $bloguser ) {
                $results[] = array( 'id' => $bloguser->ID, 'text' => $bloguser->display_name );
            }
        }

        echo json_encode( array( 'results' => $results, 'more' => 'false' ) );

        wp_die();
    }
}

$acui_homepage = new ACUI_Homepage();
$acui_homepage->hooks();
