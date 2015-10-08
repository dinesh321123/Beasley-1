<div id='comments-div'></div>
<?php ?>
<script type='text/javascript'>
	var params = {
		categoryID : '<?php echo esc_js( GMI_Gigya_Comments::category_id() ); ?>',
		streamID   : '<?php echo esc_js( GMI_Gigya_Comments::stream_id() ); ?>',
		containerID: 'comments-div',
		version    : 2,
		cid        : ''
	};
	gigya.comments.showCommentsUI(params);
</script>