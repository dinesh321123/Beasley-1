<script type="text/template" id="nav">
	<ul class="nav">
		<% _.each(screens, function(screenItem, index) { %>
			<li>
				<a href="#"
				   class="<%- screenItem.index === selectedIndex ? 'selected' : '' %>"
				   data-index="<%- screenItem.index %>">
					<%- screenItem.label %>
				</a>
			</li>
		<% }); %>
	</ul>
</script>

<script type="text/template" id="webhooks">
	<table class="widefat">
		<thead>
			<tr>
				<th class="row-title">ID</th>
				<th class="row-title">Event Name</th>
			</tr>
		</thead>
		<tbody>
			<% _.each(webhooks, function(webhookItem, index) { %>
				<tr>
					<td><%- webhookItem.webhook_id %></th>
					<td><%- webhookItem.event %></td>
				</tr>
			<% }); %>
		</tbody>
	</table>
</script>

<script type="text/template" id="groups">
	<table class="widefat">
		<thead>
			<tr>
				<th class="row-title">MyEmma Group ID</th>
				<th class="row-title">MyEmma Group Name</th>
				<th class="row-title">Gigya Field Key</th>
			</tr>
		</thead>
		<tbody>
			<% _.each(groups, function(groupItem, index) { %>
				<tr>
					<td><%- groupItem.group_id %></th>
					<td><%- groupItem.group_name %></td>
					<td><%- groupItem.field_key %></td>
				</tr>
			<% }); %>
		</tbody>
	</table>
</script>

<div class="wrap emma-settings">
	<h1>MyEmma Settings</h1>

	<div class="emma-settings-nav">
	</div>

	<ul class="emma-nav-content" style="display:none">
		<li data-index="0">
			<h3>MyEmma Groups</h3>

			<div class="emma-groups">
				<table class="widefat">
					<thead>
						<tr>
							<th class="row-title">MyEmma Group ID</th>
							<th class="row-title">MyEmma Group Name</th>
							<th class="row-title">Gigya Field Key</th>
						</tr>
					</thead>
				</table>
			</div>

			<hr />

			<div class="new-group-content">
				<h3>New MyEmma Group</h3>
				<div class="status">
					<p></p>
				</div>

				<label for="emma_group_name">MyEmma Group Name</label>
				<input type="text" name="emma_group_name" value="" id="emma_group_name" />

				<label for="emma_group_id">MyEmma Group ID (For mapping existing Groups, leave blank to create new group)</label>
				<input type="text" name="emma_group_id" value="" id="emma_group_id" />

				<label for="gigya_field_key">Gigya Field Key</label>
				<input type="text" name="gigya_field_key" value="" id="gigya_field_key" />

				<input type="submit" value="Create" class="button button-primary add-group-button" />
				<span class="spinner"></span>
			</div>
		</li>
		<li data-index="1">
			<h3>Web Hooks</h3>
			<div class="status updated">
				<p>Settings Saved</p>
			</div>

			<label for="auth_token">Auth Token</label>
			<input type="text" name="auth_token" value="foo" id="auth_token" />

			<h4>Active Webhooks</h4>
			<div class="active-webhooks">
				<table class="widefat">
					<thead>
						<tr>
							<th class="row-title">ID</th>
							<th class="row-title">Event Name</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>N/A</th>
							<td>Loading ...</td>
						</tr>
					</tbody>
				</table>
			</div>

			<input type="submit" value="Update Webhooks" class="button button-primary update-webhooks-button" />
			<span class="spinner"></span>
		</li>
		<li data-index="2">
			<h3>API Keys</h3>
			<div class="status">
				<p></p>
			</div>

			<p>Please enter your MyEmma API keys. Note: You must update all existing <strong>Group IDs</strong> and <strong>Webhooks</strong> when switching accounts.</p>

			<label for="account_id">Account ID</label>
			<input type="text" name="account_id" value="foo" id="account_id" />

			<label for="public_key">Public Key</label>
			<input type="text" name="public_key" value="public" id="public_key" />

			<label for="private_key">Private Key</label>
			<input type="text" name="private_key" value="private" id="private_key" />

			<input type="submit" value="Authorize" class="button button-primary authorize-button" />
			<span class="spinner"></span>
		</li>
	</ul>
</div>
