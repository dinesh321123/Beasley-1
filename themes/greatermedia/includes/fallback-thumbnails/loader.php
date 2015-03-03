<?php
/**
 * Fallback Thumbnails
 *
 * Provides fallback thumbnails for galleries, albums, and podcasts.
 */

namespace Greater_Media\Fallback_Thumbnails;

require __DIR__ . '/src/class-thumbnail-filter.php'; 
require __DIR__ . '/src/functions.php'; 

new Thumbnail_Filter(); 
