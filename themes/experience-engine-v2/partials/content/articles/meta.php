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
                $contest_is_singular = is_singular( 'contest' );

                // Co author Checking
                $primary_author = get_field( 'primary_author_cpt', $post );
                $primary_author = $primary_author ? $primary_author : $post->post_author;
                $secondary_author = get_field( 'secondary_author_cpt', $post );

                $primary_author_name = $primary_author ? get_the_author_meta( 'display_name', $primary_author ) : '';
                $secondary_author_name = $secondary_author ? get_the_author_meta( 'display_name', $secondary_author) : '';
            ?>
            <?php if ( ! $contest_is_singular ) : ?>
                <span class="author-avatar hide-avatar">
                    <?php if ( is_singular() ) : ?>
                        <?php
                            $avatar = get_avatar( get_the_author_meta( 'ID' ), 40 );
                            if ( $avatar ) {
                                echo $avatar;
                            } else {
                                echo '<img class="avatar avatar-40 photo" src="https://2.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=96&d=mm&r=g" height="40" width="40" alt="Placeholder Shilloutte User Image">';
                            }
                        ?>
                    <?php endif; ?>
                </span>
                <div class="flex-property">
                    <div class="author-meta-name">
                        <?php
                            if($secondary_author_name) { ?>
                                <span style='color:rgba(68, 68, 68, 0.6);'>By </span>
                                <author><a href="<?php echo esc_url( home_url( '/authors/'.$primary_author ) ); ?>" title="<?php echo $primary_author_name; ?>">
                                    <?php echo $primary_author_name; ?>
                                </a></author>
                                <span style='color:rgba(68, 68, 68, 0.6);'> and </span>
                                <author><a href="<?php echo esc_url( home_url( '/authors/'.$secondary_author ) ); ?>" title="<?php echo $secondary_author_name; ?>" >
                                    <?php echo $secondary_author_name; ?>
                                </a></author>
                            <?php } else { ?>
                                <!-- // the_author_meta( 'display_name', $primary_author); -->
                                <span style='color:rgba(68, 68, 68, 0.6);'>By </span>
                                <author><a href="<?php echo esc_url( home_url( '/authors/'.$primary_author ) ); ?>" title="<?php echo $primary_author_name; ?>">
                                    <?php echo $primary_author_name; ?>
                                </a></author>
                            <?php }
                        ?>
                    </div>
                    
                    <?php if(in_array('date',$args['show'])){ // check tags argument passed in temeplate for show tags or date  ?>
                        <div class="author-meta-date">
                            <time><?php ee_the_date(); ?></time>
                        </div> 
                    <?php }?>
                </div>
                
                <?php 
                    if(in_array('date',$args['show'])){
                        ee_the_sponsored_by_div(get_the_id(), !$contest_is_singular);
                    }
                ?>

            <?php endif; ?>
                    
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