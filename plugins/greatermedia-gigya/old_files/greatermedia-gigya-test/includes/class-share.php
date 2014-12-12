<?php
class GMI_Gigya_Share {

	public static function display_share_buttons() {
			global $post;
			?>
			<script type="text/javascript">
				var act = new gigya.socialize.UserAction();
				act.setUserMessage("This is the user message");
				act.setTitle("<?php the_title(); ?>");
				act.setLinkBack("<?php the_permalink(); ?>");
				act.setDescription("<?php echo wptexturize( $post->post_excerpt ); ?>");
				act.addActionLink("Read this post", "<?php the_permalink(); ?>");
				var showShareBarUI_params =
				{
					containerID : 'share-bar-div',
					shareButtons: 'Facebook-Like,googleplus-share,Twitter-Tweet,Share',
					userAction  : act
				}
			</script>
			<style type="text/css">
				.entry-content td { padding: 0; }
				.entry-content table { margin: 0 }
				.fb-like iframe {
					max-width: none;
					width: auto;
				}
			</style>
			<div id="share-bar-div"></div>
			<script type="text/javascript">
				gigya.socialize.showShareBarUI(showShareBarUI_params);
			</script>
<?php
	}
}