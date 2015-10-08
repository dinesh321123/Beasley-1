<div class="age-restricted-content-view" style="position: relative;border: 1px dotted rgb(255, 0, 0);padding:1em .5em;">
	<header style="display: inline-block;position: absolute;color: rgb(255, 0, 0);top: -.75em;left: .5em;padding: 0 .25em; background-color: #fff;font-size:.8em;">
		<div class="dashicons dashicons-businessman"></div>
		<%= GreaterMediaAgeRestrictedContent.strings['Restricted to:'] %> <% if ('18plus' === status) { %><%= GreaterMediaAgeRestrictedContent.strings['18+'] %><% } else if ('21plus' === status) { %><%= GreaterMediaAgeRestrictedContent.strings['21+'] %><% }  %>
	</header>
	<div class="content">
		<%= content %>
	</div>
</div>