<div class="timed-content-view" style="position: relative;border: 1px dotted rgb(155, 49, 214);padding:1em .5em;">
	<header style="display: inline-block;position: absolute;color: rgb(155, 49, 214);top: -.75em;left: .5em;padding: 0 .25em; background-color: #fff;font-size:.8em;">
		<div class="dashicons dashicons-clock"></div>
	<% if (undefined !== show) { %>Show: <%= show %><% }  %>
	<% if (undefined !== show && undefined !== hide) { %>;<% } %>
	<% if (undefined !== hide) { %>Hide: <%= hide %><% }  %>
	</header>
	<div class="content"><%= content %></div>
</div>