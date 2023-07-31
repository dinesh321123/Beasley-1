<?php
/**
 * Template: articles-meta.php
 *
 * Description: This template file is responsible for displaying the meta information of an article in WordPress.
 * It includes author information, post date, and social share buttons.
 */
?>

<div class="articles-meta">

    <div class="post-meta">
        <div class="author-meta">
            <?php
            // Check if the current page is a singular contest
            $contest_is_singular = is_singular('contest');

            // Co author Checking
            $primary_author = get_field('primary_author_cpt', $post);
            $primary_author = $primary_author ? $primary_author : $post->post_author;
            $secondary_author = get_field('secondary_author_cpt', $post);

            // Retrieve primary and secondary author names
            $primary_author_name = $primary_author ? get_the_author_meta('display_name', $primary_author) : '';
            $secondary_author_name = $secondary_author ? get_the_author_meta('display_name', $secondary_author) : '';
            ?>

            <?php if (!$contest_is_singular) : ?>
                <span class="author-avatar hide-avatar">
                    <?php if (is_singular()) : ?>
                        <?php
                        // Retrieve the avatar for the current author
                        $avatar = get_avatar(get_the_author_meta('ID'), 40);
                        if ($avatar) {
                            echo $avatar;
                        } else {
                            // If no avatar is found, display a placeholder image
                            echo '<img class="avatar avatar-40 photo" src="https://2.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=96&d=mm&r=g" height="40" width="40" alt="Placeholder Shilloutte User Image">';
                        }
                        ?>
                    <?php endif; ?>
                </span>

                <p class="author-meta-name">
                    <strong>
                        <?php
                        // Display the author names
                        if ($secondary_author_name) {
                            printf(
                                '<span style="color: %1$s;">%2$s</span><span style="color: %5$s;" > <a href="%6$s" >%3$s</a></span> <span style="color: %1$s;">and</span><span style="color: %5$s;" > <a href="%7$s" > %4$s </a></span>',
                                'rgba(68, 68, 68, 0.6)',
                                esc_html__('By', 'text-domain'),
                                $primary_author_name,
                                $secondary_author_name,
                                '#cc1D23',
                                esc_url( home_url( '/authors/'.$primary_author ) ),
                                esc_url( home_url( '/authors/'.$secondary_author ) )
                            );
                        } else {
                            // If no secondary author, display the primary author's name
                            // printf(
                            //     '%1$s | @%2$s',
                            //     esc_html__('Author name', 'text-domain'),
                            //     get_the_author_meta('display_name', $primary_author)
                            // );

                            printf(
                                '%1$s | <span style="color: %2$s;"><a href="%4$s">@%3$s</a></span>',
                                esc_html__('Author name', 'text-domain'),
                                '#cc1D23',                               
                                $primary_author_name,
                                esc_url( home_url( '/authors/'.$primary_author ) )
                            );
                        }
                        ?>
                    </strong>
                </p>
            <?php endif; ?>

            <?php
            // Get the post date and calculate the time ago
            $post_date = get_the_date('F jS', get_the_ID());
            $time_ago = human_time_diff(get_the_time('U'), current_time('timestamp'));

            // Format the time ago with a maximum limit of 48 hours
            if ($time_ago < 48) {
                $formatted_time_ago = sprintf(_n('%s hour ago', '%s ago', $time_ago, 'text-domain'), $time_ago);
                $post_date_display = $post_date . ' (' . $formatted_time_ago . ')';
            } else {
                // If more than 48 hours, display only the post date
                $post_date_display = $post_date;
            } 
            ?>
            <?php if(in_array('date',$args['show'])){ // check tags argument passed in temeplate for show tags or date  ?>
                <div class="post-date">
                    <?php echo esc_html($post_date_display); ?>
                </div>
                <?php ee_the_sponsored_by_div(get_the_id(), !$contest_is_singular); ?>
            <?php }?>
            
            <?php if(in_array('category',$args['show'])){ ?>
                <?php if ( has_category() ) : ?>
                    <div class="meta-category-container">
                        <?php
                        $exclude_list	=	array( "must-haves-chr-ac", "stacker", "must-haves-rock", "must-haves-urban", "must-haves-sports", "must-haves-news-talk", "must-haves-country", "must-haves-adult-hits", "must-haves-classic-hits", "must-haves-classic-rock", "must-haves-praise", "must-haves-urban-urban-ac" );
                        $cate_details	=	get_the_category( $post->ID );
                        if(count($cate_details) > 0) {
                            echo '<span> Category: </span><ul class="post-categories">';
                            foreach ($cate_details as $category) {
                                if( !in_array( $category->slug, $exclude_list) ) {
                                    echo "<li><a href = '" . get_category_link($category->term_id) . "' rel='category tag' style='padding-left: 5px; text-transform:capitalize;'>" . $category->cat_name . "</a></li>";
                                }
                            }
                            echo "</ul>";
                        }
                        ?>
                        <?php // the_category(); ?>
                    </div>
                <?php endif; ?>
            <?php } ?>

            <?php if(in_array('tags',$args['show'])){ ?>
                <?php if ( has_tag() ) : ?>
                    <div class="post-tags">
                        <?php the_tags( '<div class="post-tag-label">Tags: </div><div class="post-tag-items">', ',', '</div>' ); ?>
                    </div>
                <?php endif; ?>   
            <?php } ?>
        </div>
    </div>

    <div class="share-wrap-icons">
        <?php ee_the_share_buttons(get_permalink(), get_the_title()); ?>
    </div>
</div>