<script>
	setTimeout(function() {
		window.ooyalaResponsiveVideoPlayers = [];
		window.OoyalaPlayers = <?php echo json_encode($GLOBALS['ooyala_players']);?>;

		// Initialize Ooyala players once the DOM is ready
		jQuery(function() {
			_(OoyalaPlayers).each(function(player) {
				var player_args = {};

				if (player.ad_set) {
					player_args['adSetCode'] = player.ad_set;
				}

				ooyalaResponsiveVideoPlayers.push(
					OO.Player.create(player.div_id, player.ooyala_video, player_args)
				);
			});
		});
	}, 2000);
</script>