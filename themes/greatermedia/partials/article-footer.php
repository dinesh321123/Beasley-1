<?php

$the_id = get_the_ID();
$post_taxonomies = get_post_taxonomies();

if ( (in_array( 'category', $post_taxonomies ) && has_category() ) ||  ( in_array( 'post_tag', $post_taxonomies ) && has_tag() ) || ( class_exists( 'ShowsCPT', false ) && in_array( ShowsCPT::SHOW_TAXONOMY, $post_taxonomies ) && has_term( '', ShowsCPT::SHOW_TAXONOMY ) ) ) : ?>

<footer class="article__footer">

	<?php if ( in_array( 'category', $post_taxonomies ) && has_category() ) :

		get_template_part( 'partials/article', 'categories' );

	endif; ?>

	<?php if ( in_array( 'post_tag', $post_taxonomies ) && has_tag() ) :

		get_template_part( 'partials/article', 'tags' );

	endif; ?>

	<?php if ( class_exists( 'ShowsCPT', false ) && in_array( ShowsCPT::SHOW_TAXONOMY, $post_taxonomies ) && has_term( '', ShowsCPT::SHOW_TAXONOMY ) ) :

		get_template_part( 'partials/article', 'shows' );

	endif; ?>
</footer>
<?php endif; ?>