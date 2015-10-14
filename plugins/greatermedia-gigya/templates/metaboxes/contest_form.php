<?php ?>

<div class="contest-form-heading">
	<h4>Select Existing Form:</h4>
</div>

<select id="contest-form-select" style="width: 100%" name="contest_form_id">
	<?php foreach ( $this->data['forms'] as $form ) {

		$label    = esc_attr( $form->title );
		$value    = esc_html( $form->id );
		$selected = selected( $this->data['contest_form_id'], $form->id, false );

		echo "<option value='$value' $selected>$label</option>";

	} ?>
</select>

<div class="contest-form-divider">
	<p>or</p>
</div>

<div class="contest-form-new">
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=gf_new_form' ) )?>" target="_blank" class="button button-secondary">Create New Form</a>
</div>



