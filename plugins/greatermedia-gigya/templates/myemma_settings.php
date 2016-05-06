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
					<td><%- webhookItem.webhook_id %></td>
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
				<th class="row-title">Status</th>
				<th class="row-title">Actions</th>
			</tr>
		</thead>
		<tbody>
			<% _.each(groups, function(groupItem, index) { %>
				<tr>
					<td>
						<a target="_blank" href="<%- view.toEmmaGroupURL( groupItem.group_id ) %>">
							<%- groupItem.group_id %>
						</a>
					</td>
					<td><%- groupItem.group_name %></td>
					<td><%- groupItem.field_key %></td>
					<td><%- groupItem.group_active === undefined || !!groupItem.group_active ? 'Active' : 'Inactive' %></td>
					<td>
						<% if (groupItem.group_id) { %>
						<a data-group="<%- groupItem.group_id %>" title="Edit Group" href="#" class="edit-group-link"><i alt="f119" class="dashicons dashicons-welcome-write-blog"></i></a>
						<a data-group="<%- groupItem.group_id %>" title="Remove Group" href="#" class="remove-group-link"><i alt="f153" class="dashicons dashicons-no"></i></a>
						<% } %>
					</td>
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
			<div class="emma-groups">
				<h3>MyEmma Groups</h3>
				<div class="status">
					<p></p>
				</div>
				<table class="widefat">
					<thead>
						<tr>
							<th class="row-title">MyEmma Group ID</th>
							<th class="row-title">MyEmma Group Name</th>
							<th class="row-title">Gigya Field Key</th>
						</tr>
					</thead>
				</table>

				<input type="button" value="Add Group" class="button button-primary add-group-button" />
			</div>

			<div class="new-group-content">
				<input type="button" value="Back" class="button button-secondary back-button" />
				<h3 class="editor-title">New MyEmma Group</h3>
				<div class="status">
					<p></p>
				</div>

				<label for="emma_group_name">MyEmma Group Name (Name of the Audience Group in MyEmma)</label>
				<input type="text" name="emma_group_name" value="" id="emma_group_name" />

				<label for="emma_group_id">MyEmma Group ID (Leave blank to create a new group)</label>
				<input type="text" name="emma_group_id" value="" id="emma_group_id" />

				<label for="gigya_field_key">Gigya Field Key (Must only contain letters and numbers)</label>
				<input type="text" name="gigya_field_key" value="" id="gigya_field_key" />

				<label for="emma_group_description" class="emma_group_description_label">MyEmma Group Description (Label shown to Members)</label>
				<textarea name="emma_group_description" value="" id="emma_group_description"></textarea>

				<label for="emma_group_active" class="emma_group_active_label">
				<input type="checkbox" name="emma_group_active" value="" id="emma_group_active" />Active (Only Active Groups are shown to Members)</label>

				<label for="emma_group_opt_in" class="emma_group_opt_in_label">
				<input type="checkbox" name="emma_group_opt_in" value="" id="emma_group_opt_in" />Opt-in by default?</label>

				<input type="submit" value="Create" class="button button-primary create-group-button" />
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
