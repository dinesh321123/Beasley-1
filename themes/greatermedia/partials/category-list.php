<?php
$categories = get_the_category();
$cat_count = 0;

foreach( $categories as $category ) : $cat_count++; ?>
	<a class="entry__footer--category" href="<?php echo get_term_link( $category, 'category' ); ?>"><?php echo esc_html( $category->name ); ?><?php echo $cat_count > 1 ? ',&nbsp;' : ''; ?></a>
<?php endforeach; ?>