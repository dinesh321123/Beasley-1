<?php
// Workaround for limitation in the YARPP plugin. YARPP doesn't understand
// parent and child themes and will always try to look for this file in the 
// active theme.

include get_template_directory() . '/partials/related-posts.php';
