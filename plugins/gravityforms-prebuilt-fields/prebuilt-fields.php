<?php
/*
Plugin Name: Gravity Forms Prebuilt Fields
Plugin URI: http://www.gravityforms.com
Description: Provides a single point for defining reusable field values
Version: 0.1
Author: 10up, Thaicloud
Author URI: http://www.10up.com
*/


add_action( 'init', 'GFPrebuiltFields::get_instance' );

class GFPrebuiltFields {

	private static $instance;

	public function __construct() {
		if ( ! self::$instance ) {
			wp_die( 'Please see the ::get_instance() method' );
		}
	}

	public static function get_instance() {
		if ( ! is_a( self::$instance, __CLASS__ ) ) {
			self::$instance = true;
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	// Initialize plugin
	protected function init() {

		// Only continue if Gravity Forms is enabled
		if ( ! self::is_gravityforms_supported() ) {
			return;
		}

		if ( is_admin() ) {
			// Create the admin menu for prebuilding Fields
			add_filter( "gform_addon_navigation", array( $this, 'prebuilt_fields_menu' ) );

			// Pre-populate GF fields, on edit screen, based on mapping settings (admin)
			add_filter( 'gform_admin_pre_render', array( $this, 'populate_presets' ) );

			// Enqueue js only on edit screen
			if ( self::is_prebuilt_page() ) {
				wp_enqueue_script( 'prebuilt-fields', plugins_url( null, __FILE__ ) . "/js/prebuilt-fields.js", array( 'jquery' ) );
			}

		} else {
			// Pre-populate GF fields based on mapping settings (frontend)
			add_filter( 'gform_pre_render', array( $this, 'populate_presets' ) );
		}
	}

	// Make sure Gravity Forms is installed
	public function is_gravityforms_supported() {
		return class_exists( "GFCommon" );
	}

	// Creates admin menu item under Forms > Prebuilt Fields
	public function prebuilt_fields_menu( $menus ) {
		$menus[] = array( "name" => "gf_prebuilt_fields", "label" => __( "Demographic Fields", "gravityformsprebuilt" ), "callback" => array( "GFPrebuiltFields", "gf_prebuilt_fields" ), "permission" => 'administrator' );
		return $menus;
	}

	// Boolean - check if on the gf_prebuilt_fields page
	public function is_prebuilt_page() {
		$current_page = trim( strtolower( RGForms::get( "page" ) ) );
		return in_array( $current_page, array( "gf_prebuilt_fields" ) );
	}

	// GF Method for displaying list / edit screen: Prebuilt form fields
	public function gf_prebuilt_fields() {
		$view = rgget( "view" );
		if ( $view == "edit" ) {
			self::edit_prebuilt_fields( rgget( "id" ) );
		} else {
			self::list_prebuilt_fields();
		}
	}

	// Display main admin page for predefining Gravity Form fields
	public function list_prebuilt_fields() {
		if ( ! self::is_gravityforms_supported() ) {
			die( 'Gravity Forms must be enabled.' );
		}

		if ( rgpost( 'action' ) == "delete" ) {
			check_admin_referer( "list_action", "gf_prebuilt_fields_list" );
			$id = esc_html( $_POST["action_argument"] );
			self::gf_prebuilt_field_delete_entry( esc_html( $id ) );
			?>
			<div class="updated fade" style="padding:6px"><?php _e( "Deleted.", "gravityformsprebuilt" ) ?></div>
		<?php
		} else {
			if ( ! empty( $_POST["bulk_action"] ) ) {
				check_admin_referer( "list_action", "gf_prebuilt_fields_list" );
				$selected_feeds = $_POST["feed"];
				if ( is_array( $selected_feeds ) ) {
					foreach ( $selected_feeds as $feed_id ) {
						self::gf_prebuilt_field_delete_entry( esc_html( $feed_id ) );
					}
				} ?>
				<div class="updated fade" style="padding:6px"><?php _e( "Deleted.", "gravityformsprebuilt" ) ?></div>
			<?php
			}
		} ?>
		<div class="wrap">
			<h2><?php
				_e( "Demographic Fields", "gravityformsprebuilt" ); ?>
				<a class="button add-new-h2" href="admin.php?page=gf_prebuilt_fields&view=edit&id=0"><?php _e( "Add New", "gravityformsprebuilt" ) ?></a>
			</h2>

			<form id="feed_form" method="post">
				<?php wp_nonce_field( 'list_action', 'gf_prebuilt_fields_list' ) ?>
				<input type="hidden" id="action" name="action" />
				<input type="hidden" id="action_argument" name="action_argument" />

				<div class="tablenav">
					<div class="alignleft actions" style="padding:8px 0 7px 0;">
						<label class="hidden" for="bulk_action"><?php _e( "Bulk action", "gravityformsprebuilt" ) ?></label>
						<select name="bulk_action" id="bulk_action">
							<option value=''> <?php _e( "Bulk action", "gravityformsprebuilt" ) ?> </option>
							<option value='delete'><?php _e( "Delete", "gravityformsprebuilt" ) ?></option>
						</select>
						<?php
						echo '<input type="submit" class="button" value="' . __( "Apply", "gravityformsprebuilt" ) . '" onclick="if( jQuery(\'#bulk_action\').val() == \'delete\' && !confirm(\'' . __( "Delete selected feeds? ", "gravityformsprebuilt" ) . __( "\'Cancel\' to stop, \'OK\' to delete.", "gravityformsprebuilt" ) . '\')) { return false; } return true;"/>'; ?>
					</div>
				</div>
				<table class="widefat fixed">
					<thead>
					<tr>
						<th scope="col" id="cb" class="manage-column column-cb check-column" style="">
							<input type="checkbox" /></th>
						<th scope="col" id="active" class="manage-column check-column"></th>
						<th scope="col" class="manage-column"><?php _e( "Gigya Field Name", "gravityformsprebuilt" ) ?></th>
						<th scope="col" class="manage-column"><?php _e( "Field Type", "gravityformsprebuilt" ) ?></th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<th scope="col" id="cb" class="manage-column column-cb check-column" style="">
							<input type="checkbox" /></th>
						<th scope="col" id="active" class="manage-column check-column"></th>
						<th scope="col" class="manage-column"><?php _e( "Gigya Field Name", "gravityformsprebuilt" ) ?></th>
						<th scope="col" class="manage-column"><?php _e( "Field Type", "gravityformsprebuilt" ) ?></th>
					</tr>
					</tfoot>

					<tbody class="list:user user-list">
					<?php

					$gf_prebuilt_fields = get_site_option( "gf_prebuilt_fields" );

					if ( empty( $gf_prebuilt_fields ) ){
						echo '<tr class="no-items"><td colspan="4">There are currently no prebuilt field settings- please <a href="admin.php?page=gf_prebuilt_fields&view=edit&id=0">add some</a>.</td></tr>';
					}else {
						foreach ( $gf_prebuilt_fields as $id => $field ) {
							?>
							<tr class='author-self status-inherit'>
								<th scope="row" class="check-column">
									<input type="checkbox" name="feed[]" value="<?php echo esc_html( $id ) ?>" /></th>
								<td></td>
								<td class="column-title">
									<a href="admin.php?page=gf_prebuilt_fields&view=edit&id=<?php echo esc_html( $id ) ?>" title="<?php _e( "Edit", "gravityformsprebuilt" ) ?>"><?php echo esc_html( $id ) ?></a>

									<div class="row-actions">
                                        <span class="edit">
                                        <a title="<?php _e( "Edit", "gravityformsprebuilt" ) ?>" href="admin.php?page=gf_prebuilt_fields&view=edit&id=<?php echo esc_html( $id ) ?>"><?php _e( "Edit", "gravityformsprebuilt" ) ?></a>
                                        |
                                        </span>
                                        <span class="trash">
                                        <a title="<?php _e( "Delete", "gravityformsprebuilt" ) ?>" href="javascript: if(confirm('<?php _e( "Delete this feed? ", "gravityformsprebuilt" ) ?> <?php _e( "\'Cancel\' to stop, \'OK\' to delete.", "gravityformsprebuilt" ) ?>')){ jQuery('#action_argument').val('<?php echo esc_html( $id ); ?>'); jQuery('#action').val('delete'); jQuery('#feed_form')[0].submit(); }"><?php _e( "Delete", "gravityformsprebuilt" ) ?></a>
                                        </span>
									</div>
								</td>
								<td class="eloqua-form-name">
									<?php
									echo esc_html( $field['type'] );
									?>
								</td>
							</tr>
						<?php
						}
					}
					?>
					</tbody>
				</table>
			</form>
		</div>
	<?php
	}

	// Edit / Add New Predefined Field
	public function edit_prebuilt_fields() {
		// Grab current GF form ID - from POST var or GET var
		$id = ! empty( $_POST["gf_prebuilt_fields_name"] ) ? $_POST["gf_prebuilt_fields_name"] : esc_html( $_GET["id"] );
		?>
		<div class="wrap">
		<h2><?php _e( "Demographic Field", "gravityformsprebuilt" ) ?></h2>

		<form method="post" action="">
			<input type="hidden" name="eloqua_setting_id" id="eloqua_setting_id" value="<?php echo absint( $id ); ?>" />

			<?php
			$gf_prebuilt_fields = get_site_option( "gf_prebuilt_fields" );
			// If form has been submitted
			if ( rgpost( "gf_prebuilt_fields_name" ) ) {

				$gf_prebuilt_fields_name        = rgpost( "gf_prebuilt_fields_name" ); // Array of mapped fields
				$gf_prebuilt_fields_type        = rgpost( "gf_prebuilt_fields_type" );
				$gf_prebuilt_fields_value       = rgpost( "gf_prebuilt_fields_text" );
				$gf_prebuilt_fields_array_label = rgpost( "gf_prebuilt_fields_array_label" );
				$gf_prebuilt_fields_array_value = rgpost( "gf_prebuilt_fields_array_value" );

				if ( $gf_prebuilt_fields_type == 'dropdown' || $gf_prebuilt_fields_type == 'checkbox' ) {

					// Combine array values into required format for GF field parse
					$gf_prebuilt_fields_value = array();
					foreach ( $gf_prebuilt_fields_array_label as $key => $label ) {
						array_push( $gf_prebuilt_fields_value, array(
							'text'  => sanitize_text_field( $label ),
							'value' => sanitize_text_field( $gf_prebuilt_fields_array_value[$key] )
						) );
					}

					$gf_prebuilt_fields[sanitize_text_field( $gf_prebuilt_fields_name )] = array(
						'type'  => sanitize_text_field( $gf_prebuilt_fields_type ),
						'value' => $gf_prebuilt_fields_value
					);

				} else {
					$gf_prebuilt_fields[sanitize_text_field( $gf_prebuilt_fields_name )] = array(
						'type'  => sanitize_text_field( $gf_prebuilt_fields_type ),
						'value' => sanitize_text_field( $gf_prebuilt_fields_value )
					);
				}

				update_site_option( "gf_prebuilt_fields", $gf_prebuilt_fields );

			} else if ( ! empty( $id ) && ! empty( $gf_prebuilt_fields ) ) {
				$gf_prebuilt_fields_name  = $id;
				$gf_prebuilt_fields_type  = $gf_prebuilt_fields[esc_html( $id )]['type'];
				$gf_prebuilt_fields_value = $gf_prebuilt_fields[esc_html( $id )]['value'];
			}
			?>

			<table class="form-table" id="gf_prebuilt_fields">
				<tbody>
				<tr>
					<th scope="row">
						<label for="gf_prebuilt_fields_name" class="left_header"><?php _e( "Gigya Field Name", "gravityformsprebuilt" ); ?> </label>
					</th>
					<td>
						<input type="text" name="gf_prebuilt_fields_name" id="gf_prebuilt_fields_name" <?php if ( !empty( $gf_prebuilt_fields_name ) ) {
							echo " value='" . esc_attr( $gf_prebuilt_fields_name ) . "' ";
						} ?> size="50" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="gf_prebuilt_fields_type" class="left_header"><?php _e( "Field Type", "gravityformsprebuilt" ); ?> </label>
					</th>
					<td>
						<select id="gf_prebuilt_fields_type" name="gf_prebuilt_fields_type">
							<option value="text" <?php if ( $gf_prebuilt_fields_type == 'text' ) {
								echo 'selected';
							} ?>><?php _e( "Text Field", "gravityformsprebuilt" ); ?> </option>
							<option value="dropdown"<?php if ( $gf_prebuilt_fields_type == 'dropdown' ) {
								echo 'selected';
							} ?>><?php _e( "Dropdown List", "gravityformsprebuilt" ); ?> </option>
							<option value="checkbox" <?php if ( $gf_prebuilt_fields_type == 'checkbox' ) {
								echo 'selected';
							} ?>><?php _e( "Checkbox", "gravityformsprebuilt" ); ?> </option>
						</select>
					</td>
				</tr>
				<tr id="gf_prebuilt_fields_text_group" <?php if ( $gf_prebuilt_fields_type != 'text' && ! empty( $gf_prebuilt_fields_type ) ) {
					echo 'class="hidden"';
				} ?>>
					<th scope="row">
						<label for="gf_prebuilt_fields_text" class="left_header"><?php _e( "Field Value", "gravityformsprebuilt" ); ?> </label>
					</th>
					<td>
						<input type="text" name="gf_prebuilt_fields_text" id="gf_prebuilt_fields_text" <?php if ( $gf_prebuilt_fields_value ) {
							echo " value='" . esc_attr( $gf_prebuilt_fields_value ) . "' ";
						} ?> size="50" />
					</td>
				</tr>
				<tr id="gf_prebuilt_fields_array_group" <?php if ( $gf_prebuilt_fields_type == 'text' || empty( $gf_prebuilt_fields_type ) ) {
					echo 'class="hidden"';
				} ?>>
					<th scope="row">
						<label for="gf_prebuilt_fields_array_label" class="left_header"><?php _e( "Field Values", "gravityformsprebuilt" ); ?> </label>
					</th>
					<td>
						<?php
						if ( is_array( $gf_prebuilt_fields_value ) ) {
							foreach ( $gf_prebuilt_fields_value as $values ) {
								?>
								<div>
									<input type="text" placeholder="Name" name="gf_prebuilt_fields_array_label[]" id="gf_prebuilt_fields_array_label" <?php echo " value='" . esc_attr( $values['text'] ) . "' "; ?> size="25" />
									<input type="text" placeholder="Value" name="gf_prebuilt_fields_array_value[]" id="gf_prebuilt_fields_array_value" <?php echo " value='" . esc_attr( $values['value'] ) . "' "; ?> size="25" />
									<a class="add-item">Add</a> |
									<a class="rm-item">Remove</a>
								</div>
							<?php
							}
						} else {
							?>
							<div>
								<input type="text" placeholder="Name" name="gf_prebuilt_fields_array_label[]" id="gf_prebuilt_fields_array_label" size="25" />
								<input type="text" placeholder="Value" name="gf_prebuilt_fields_array_value[]" id="gf_prebuilt_fields_array_value" size="25" />
								<a class="add-item">Add</a> |
								<a class="rm-item">Remove</a>
							</div>
						<?php
						} ?>

					</td>
				</tr>
				</tbody>
			</table>
			<p>
				<input type="submit" name="gf_prebuilt_fields_submit" class="button-primary" value="<?php _e( "Save Settings", "gravityformsprebuilt" ) ?>" />
			</p>
		</form>
		</div><?php
	}

	// Delete a prebuilt field setting
	public function gf_prebuilt_field_delete_entry( $field_id ) {
		$gf_prebuilt_fields = get_site_option( "gf_prebuilt_fields" );
		$new_gf_prebuilt_fields = array();
		foreach ( $gf_prebuilt_fields as $key => $value ) {
			if ( $key != $field_id ) {
				$new_gf_prebuilt_fields[$key] = $gf_prebuilt_fields[$key];
			}
		}
		update_site_option( 'gf_prebuilt_fields', $new_gf_prebuilt_fields );
	}

	// Pre-populate field values based on Prebuilt Form Settings
	public function populate_presets( $form ) {

		$gf_prebuilt_fields = get_site_option( "gf_prebuilt_fields" );

		if ( ! empty( $gf_prebuilt_fields ) ) {

			foreach ( $form["fields"] as $key => $field ) {
				// Only override field output when allows Pre-populate option is set
				if ( $field['allowsPrepopulate'] ) {
					$name = $field['inputName'];
					$type = $field['type'];

					if ( $type == 'checkbox' ) { // Checkbox
						if ( $gf_prebuilt_fields[esc_html( $name )]['type'] == 'checkbox' ) {
							$choices = $gf_prebuilt_fields[esc_html( $name )]['value'];
							$inputs  = array();
							foreach ( $gf_prebuilt_fields[esc_html( $name )]['value'] as $i=>$entry ) {
								array_push( $inputs, array(
									'label' => $entry['text'],
									'id'    => $field['id'] . '.' . $i
								) );
							}
							$form["fields"][$key]['choices'] = $choices;
							$form["fields"][$key]['inputs']  = $inputs;
						}
					} elseif ( $type == 'select' ) { // Dropdown List
						if ( $gf_prebuilt_fields[esc_html( $name )]['type'] == 'dropdown' ) {
							$choices          = $gf_prebuilt_fields[esc_html( $name )]['value'];
							$form["fields"][$key]['choices'] = $choices;
						}
					} elseif ( in_array( $type, array( 'text', 'textarea', 'number', 'hidden', 'html', 'phone', 'website', 'email' ) ) ) { // String
						if ( $gf_prebuilt_fields[esc_html( $name )]['type'] == 'text' ) {
							$form["fields"][$key]['defaultValue'] = $gf_prebuilt_fields[esc_html( $name )]['value'];
						}
					}
				}
			}
		}

		return $form;
	}
}
