<div class="wrap">
<h2>Member Query Settings</h2>
<div class="updated" style="display:none" id="member-query-status-message">
	<p></p>
</div>

<form name="form" action="options.php" method="post" onsubmit="return false;">
	<h3 class="title">Gigya Settings</h3>
	<?php wp_nonce_field( 'change_gigya_settings', 'change_gigya_settings_nonce', false, true ) ?>

	<table class="form-table permalink-structure">
		<tbody>
			<tr>
				<th scope="row"><label for="gigya_api_key">API Key</label></th>
				<td>
					<input
						name="gigya_api_key"
						type="text" id="gigya_api_key"
						value="<?php echo esc_attr( $gigya_api_key ); ?>"
						class="regular-text"
						style="width:40em">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="gigya_secret_key">Secret Key</label></th>
				<td>
					<input
						name="gigya_secret_key"
						type="text" id="gigya_secret_key"
						value="<?php echo esc_attr( $gigya_secret_key ); ?>"
						class="regular-text"
						style="width:40em">
				</td>
			</tr>
		</tbody>
	</table>

	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
	</p>
</form>

</div>
