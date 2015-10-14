<div class="login-restricted-content-view" style="position: relative;border: 1px dotted rgb(99, 191, 60);padding:1em .5em;">
	<header style="display: inline-block;position: absolute;color: rgb(99, 191, 60);top: -.75em;left: .5em;padding: 0 .25em; background-color: #fff;font-size:.8em;">
		<div class="dashicons dashicons-admin-network"></div>
		<%= GreaterMediaLoginRestrictedContent.strings['Must be:'] %> <% if ('logged-in' === status) { %><%= GreaterMediaLoginRestrictedContent.strings['logged in'] %><% } else { %><%= GreaterMediaLoginRestrictedContent.strings['logged out'] %><% }  %>
	</header>
	<div class="content">
		<%= content %>
	</div>
</div>