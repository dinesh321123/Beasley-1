<?php
/**
 * Posts Screen Thumbnails
 * 
 * Adds a column to the posts screen to show each post's thumbnail. Works with
 * custom post types as well. 
 */

namespace Greater_Media\Posts_Screen_Thumbnails;

class Thumbnail_Column {

	private $_post_types; 
	
	private $_thumbnail_size;

	private $_column_name; 
	
	/**
	 * Register the thumbnail column for a post type. 
	 * 
	 * @param string|array $post_types Post type, array of post types, or "all"
	 * @param array|string $thumbnail_size
	 */
	public function __construct( $post_types = 'all', $thumbnail_size = 'greater_media/thumbnail_column' )
	{
		if ( 'all' == $post_types ) {
			$this->_post_types = $post_types; 
		} else {
			$this->_post_types = (array) $post_types; 
		}
		
		$this->_thumbnail_size = $thumbnail_size; 
		$this->_column_name = 'greater_media_thumbnail';  
		
		add_filter( 'manage_posts_columns', array( $this, 'filter_columns' ), 10, 2 );
		add_action( 'manage_posts_custom_column', array( $this, 'do_custom_column' ), 10, 2 );
		add_action( 'admin_head', array( $this, 'admin_head' ) ); 
	}

	/**
	 * Add the custom column to the columns list. 
	 * 
	 * @param array $columns
	 * @return array 
	 */
	public function filter_columns( $columns, $post_type )
	{
		// Make sure this is a post type we're handling. 
		if ( ! $this->_check_post_type( $post_type ) ) {
			return $columns; 
		}		
		
		// Put the thumbnail right after the checkbox.
		$columns = array_merge(
			array_slice( $columns, 0, 1 ),
			array( $this->_column_name => 'Thumbnail' ),
			array_slice( $columns, 1 )
		);

		return $columns;
	}

	/**
	 * Render the custom column. 
	 * 
	 * @param string $column_name
	 * @param int $post_id
	 */
	public function do_custom_column( $column_name, $post_id )
	{
		// Make sure this is our column. 
		if ( $this->_column_name != $column_name ) {
			return;
		}
		
		// Make sure we have a thumbnail. 
		if ( ! has_post_thumbnail( $post_id ) ) {
			return; 
		}
		
		if (current_user_can('edit_post', $post_id)) {
			echo "<a href='", get_edit_post_link( $post_id ), "'>", get_the_post_thumbnail( $post_id, $this->_thumbnail_size ), "</a>";
		} else {
			echo get_the_post_thumbnail( $post_id, $this->_thumbnail_size );
		}
	}
	
	/**
	 * Add some CSS to the page head that sets the column width to match the 
	 * thumbnail width. 
	 */
	public function admin_head() 
	{
		global $_wp_additional_image_sizes; 
		
		if ( is_array( $this->_thumbnail_size ) ) {
			$width = $this->_thumbnail_size[0];
			$height = $this->_thumbnail_size[1];
		} elseif ( isset( $_wp_additional_image_sizes[ $this->_thumbnail_size ] ) ) {
			$width = $_wp_additional_image_sizes[ $this->_thumbnail_size ]['width']; 
			$height = $_wp_additional_image_sizes[ $this->_thumbnail_size ]['height']; 
		} else {
			return; 
		}
		
		?>
		<style type='text/css'>
			.column-<?php echo sanitize_html_class( $this->_column_name ); ?> { width: <?php echo (int) $width; ?>px; }
			.column-<?php echo sanitize_html_class( $this->_column_name ); ?> img { width: <?php echo (int) $width; ?>px; height: <?php echo (int) $height; ?>px; }
		</style>
		<?php 
	}
	
	/**
	 * Check that the post type is registered as one we are handling. 
	 * 
	 * @param string $type
	 * @return boolean
	 */
	protected function _check_post_type( $type ) 
	{
		return ( 'all' == $this->_post_types || in_array( $type, $this->_post_types ) );
	} 
}
