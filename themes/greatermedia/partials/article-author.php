<?php
/**
 * Partial to display author associated with a post
 *
 * @package Greater Media
 * @since   0.1.0
 */

$the_id = get_the_ID();

?>
<div class="article__author">
    <p>Posted by <span class="author"><?php the_author($the_id); ?></span></p>
</div>
