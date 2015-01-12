<?php
/**
 * Gallery Post Thumbnails
 * 
 * Uses the first image in a gallery as the post's thumbnail if an actual 
 * thumbnail has not been set. 
 */

namespace Greater_Media\Gallery_Post_Thumbnails;

require __DIR__ . '/src/class-thumbnail-filter.php'; 
require __DIR__ . '/src/functions.php'; 

new Thumbnail_Filter(); 
