<?php
/**
 * Template: articles-meta.php
 *
 * Description: This template file is responsible for displaying the meta information of an article in WordPress.
 * It includes author information, post date, and social share buttons.
 */
?>

<div class="articles-meta">

    <div class="post-meta footer-meta">
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
                                '<span style="color: %1$s;">%2$s</span><span style="color: %5$s;" > %3$s</span> <span style="color: %1$s;">and</span><span style="color: %5$s;" > %4$s</span>',
                                'rgba(68, 68, 68, 0.6)',
                                esc_html__('By', 'text-domain'),
                                $primary_author_name,
                                $secondary_author_name,
                                '#cc1D23'
                            );
                        } else {
                            // If no secondary author, display the primary author's name
                            printf(
                                '%1$s | <span style="color: %2$s;">@%3$s</span>',
                                esc_html__('Author name', 'text-domain'),
                                '#cc1D23',                               
                                $primary_author_name,
                            );
                           
                        }
                        ?>
                    </strong>
                </p>
            <?php endif; ?>
             
            <?php if ( has_tag() ) : ?>
                <div class="post-tags">
                    <?php the_tags( '<div class="post-tag-label">Tags: </div><div class="post-tag-items">', ',', '</div>' ); ?>
                </div>
            <?php endif; ?>         
            
        </div>
    </div>

    <div class="share-wrap-icons">
        <?php ee_the_share_buttons(get_permalink(), get_the_title()); ?>
    </div>

    <?php ee_the_sponsored_by_div(get_the_id(), !$contest_is_singular); ?>
</div>