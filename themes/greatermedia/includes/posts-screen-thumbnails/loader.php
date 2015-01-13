<?php
/**
 * Posts Screen Thumbnails
 * 
 * Adds a column to the posts screen to show each post's thumbnail. Works with
 * custom post types as well. 
 */

namespace Greater_Media\Posts_Screen_Thumbnails;

require __DIR__ . '/src/class-thumbnail-column.php'; 
require __DIR__ . '/src/class-thumbnail-size.php'; 

new Thumbnail_Column( 'gmr_gallery' ); 
new Thumbnail_Size(); 
