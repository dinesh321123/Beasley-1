<?php

add_action( 'category_add_form_fields', 'gmr_render_add_form_fields' );
add_action( 'category_edit_form_fields', 'gmr_render_edit_form_fields' );

add_action( 'edited_category', 'gmr_handle_category_edit' );
add_action( 'delete_term', 'gmr_handle_term_delete', 10, 3 );

function gmr_render_add_form_fields() {
	?><div class="form-field">
		<label for="gmr_category_layout">Category Layout</label>
		<select id="gmr_category_layout" name="gmr_category_layout">
			<option value="">Default</option>
			<option value="top3">Top 3 Video Gallery</option>
		</select>
		<p>Select a layout which will be used to display category archive.</p>
	</div><?php
}

function gmr_render_edit_form_fields( $tag ) {
	$layout = get_option( 'gmr_category_' . $tag->term_id . '_layout' );

	?><tr class="form-field">
		<th valign="top" scope="row">
			<label for="gmr_category_layout">Category Layout</label>
		</th>
		<td>
			<select id="gmr_category_layout" name="gmr_category_layout">
				<option value="">
					Default
				</option>
				<option value="top3"<?php selected( 'top3', $layout ); ?>>
					Top 3 Video Gallery
				</option>
			</select>
			<p class="description">Select a layout which will be used to display category archive.</p>
		</td>
	</tr><?php
}

function gmr_handle_category_edit( $term_id ) {
	if ( 'category' != $_POST['taxonomy'] || ! current_user_can( 'manage_categories' ) ) {
		return;
	}

	// Save or clear
	$options = array(
		'gmr_category_%d_layout' => filter_input( INPUT_POST, 'gmr_category_layout' ),
	);

	foreach ( $options as $key => $value ) {
		$key = sprintf( $key, $term_id );
		if ( $value ) {
			update_option( $key, $value );
		} else {
			delete_option( $key );
		}
	}
}

function gmr_handle_term_delete( $term_id, $tt_id, $taxonomy ) {
	if ( 'category' != $taxonomy ) {
		return;
	}

	delete_option( 'gmr_category_' . $term_id . '_layout' );
}