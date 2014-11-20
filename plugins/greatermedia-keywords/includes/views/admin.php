<?php
/**
 * Created by Eduard
 * Date: 20.11.2014
 */

$posts = get_posts(
	array(
		'post_type'         =>  $this->supported_post_types,
		'posts_per_page'    =>  -1,
		'post_status'       =>  'publish'
	)
);

$options = wp_cache_get( $this->plugin_slug . '_option_name', "keywords" );
if( !$options ) {
	$options = get_option( $this->plugin_slug . '_option_name' );
}

?>
<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<table class="form-table">
		<tr>
			<td>
				<h3>Keyword</h3>
			</td>
			<td>
				<h3>Linked Content</h3>
			</td>
		</tr>
		<?php
		if( !empty( $options ) ):
			foreach ( $options as $key => $value ): ?>
				<tr>
					<td><?php echo esc_html( $key ); ?></td>
					<td><?php echo esc_html( $value['post_title'] ); ?></td>
					<td><a data-postid="<?php echo esc_attr( $value['post_id'] ); ?>" id="submitdelete" class="submitdelete" href="#">delete</a></td>
				</tr>
		<?php endforeach;
		endif;
		?>
	</table>
	<h3>Add new keyword</h3>
	<form method="post" action="">
		<table class="form-table">
			<tr>
				<td>
					<label for="keyword">Keyword</label>
					<br />
					<input type="text" id="keyword" name="keyword" size="25" />
				</td>
				<td>
					<label for="linked_content">Linked Content</label>
					<br />
					<select id="linked_content" name="linked_content">
						<?php foreach ($posts as $post) : ?>
							<option value="<?php echo esc_attr( $post->ID ) . ',' . esc_attr( $post->post_title ); ?>">
								<?php echo esc_html( $post->post_title ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
				<td>
					<input type="submit" value="Add" class="button"/>
				</td>
			</tr>
		</table>
		<input type="hidden" name="save_keyword_settings" value="Y" />
	</form>

</div>