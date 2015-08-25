<?php

namespace GreaterMedia\OoyalaPlaylists;

class ShortcodeHandler
{
	public function __construct()
	{
		add_shortcode( 'ooyala_playlist', array( $this, 'handle_shortcode' ) );
	}

	public function handle_shortcode( $atts, $content = null )
	{
		$atts = shortcode_atts( array(
			'player_id' => '',
			'playlist_ids' => '',
			'video_id' => '',
			'autoplay' => false,
			'loop' => false,
			'height' => 300,
			'width' => '',
			'use_first_video_from_playlist' => true
		), $atts );

		$uniqid = uniqid();

		ob_start();

		?>
		<div class="ooyala ooyala-playlist" id="playerContainer-<?php echo esc_attr( $uniqid ); ?>" style="overflow:hidden;"></div>
		<script type="text/javascript" src="https://player.ooyala.com/v3/<?php echo esc_attr( $atts['player_id'] ); ?>"></script>
		<script type="text/javascript">
		var ooyalaPlayer;

		OO.ready(function() {
		    var playerConfiguration = {
		        playlistsPlugin: {"data":[<?php echo '"' . implode( '","', explode( ',', esc_attr( $atts['playlist_ids'] ))) . '"' ?>]},
		        autoplay: <?php echo esc_attr( $atts['autoplay'] ); ?>,
		        loop: <?php echo esc_attr( $atts['loop'] ); ?>,
		        height: <?php echo esc_attr( $atts['height'] ) ? esc_attr( $atts['height'] ) : "''"; ?>,
		        width: <?php echo esc_attr( $atts['width'] ) ? esc_attr( $atts['width'] ) : "''"; ?>,
		        useFirstVideoFromPlaylist: <?php echo esc_attr( $atts['use_first_video_from_playlist'] ); ?>
		    };

		    ooyalaPlayer = OO.Player.create('playerContainer-<?php echo esc_attr( $uniqid ); ?>', '<?php echo esc_attr( $atts['video_id'] ); ?>', playerConfiguration);
		});
		</script>
		<?php

		return ob_get_clean();
	}
}
