<script>
	window.ooyalaResponsiveVideoPlayers = [];
	window.OoyalaPlayers = <?php echo json_encode($GLOBALS['ooyala_players']);?>;

	// Initialize Ooyala players once the DOM is ready
	jQuery(function() {
		_(OoyalaPlayers).each(function(player) {
			ooyalaResponsiveVideoPlayers.push(
				OO.Player.create(player.div_id, player.ooyala_video,{})
			);
		});
	});
</script>