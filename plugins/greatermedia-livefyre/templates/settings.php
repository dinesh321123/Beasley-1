<div class="wrap">
<h2>LiveFyre Settings</h2>
<div class="updated" style="display:none" id="settings-message">
	<p></p>
</div>

<form name="form" action="options.php" method="post" onsubmit="return false;">
	<!--
	<h3 class="title">Network Settings</h3>
	-->
	<?php wp_nonce_field( 'change_livefyre_settings', 'change_livefyre_settings_nonce', false, true ) ?>

	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label for="network_name">Network Name</label></th>
				<td>
					<input
						name="network_name"
						type="text" id="network_name"
						value="<?php echo esc_attr( $network_name ); ?>"
						class="regular-text"
						style="width:40em">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="network_key">Network Key</label></th>
				<td>
					<input
						name="network_key"
						type="text" id="network_key"
						value="<?php echo esc_attr( $network_key ); ?>"
						class="regular-text"
						style="width:40em">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="site_id">Site ID</label></th>
				<td>
					<input
						name="site_id"
						type="text" id="site_id"
						value="<?php echo esc_attr( $site_id ); ?>"
						class="regular-text"
						style="width:40em">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="site_key">Site Key</label></th>
				<td>
					<input
						name="site_key"
						type="text" id="site_key"
						value="<?php echo esc_attr( $site_key ); ?>"
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
