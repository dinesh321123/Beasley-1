<?php
/**
 * Weather template file
 */

get_header();

while ( have_posts() ) : the_post(); ?>

    <div class="container">

        <?php
        /**
         * This runs a check to determine if the post has a thumbnail, and that it's not a gallery or video post format.
         */
        if ( has_post_thumbnail() && ! \Greater_Media\Fallback_Thumbnails\post_has_gallery() && ! has_post_format( 'video' ) && ! has_post_format( 'audio' )  ): ?>
            <div class="article__thumbnail" style='background-image: url(<?php gm_post_thumbnail_url( 'gm-article-thumbnail' ); ?>)'>
                <?php

                    $image_attr = image_attribution();

                    if ( ! empty( $image_attr ) ) {
                        echo $image_attr;
                    }

                ?>
            </div>
        <?php endif; ?>

        <section class="content">

            <article id="post-<?php the_ID(); ?>" <?php post_class( 'article cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

                <div class="ad__inline--right desktop">
                    <?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'desktop', array( 'min_width' => 1024 ) ); ?>
                </div>

                <header class="article__header">

                    <time class="article__date" datetime="<?php echo get_the_time(); ?>"><?php the_date('F j, Y'); ?></time>
                    <h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>
                    <?php get_template_part( 'partials/social-share' ); ?>

                </header>

                <section class="article__content" itemprop="articleBody">

                    <?php the_content(); ?>


                    <a href="http://www.accuweather.com/en/us/new-brunswick-nj/08901/current-weather/329549" class="aw-widget-legal">
<!--
By accessing and/or using this code snippet, you agree to AccuWeather’s terms and conditions (in English) which can be found at http://www.accuweather.com/en/free-weather-widgets/terms and AccuWeather’s Privacy Statement (in English) which can be found at http://www.accuweather.com/en/privacy.
-->
</a><div id="awtd1385567990060" class="aw-widget-36hour"  data-locationkey="329549" data-unit="f" data-language="en-us" data-useip="false" data-uid="awtd1385567990060" data-editlocation="true"></div><script type="text/javascript" src="http://oap.accuweather.com/launch.js"></script>
<hr />
<strong>Your Central Jersey Forecast</strong>
<br /><iframe src="http://www.jerseysgreatest.com/wx/wmgqweather.php" frameborder="0" scrolling="yes" width=“100%" height=“500"></iframe></div>


                </section>

                <?php get_template_part( 'partials/article-footer' ); ?>

                <div class="ad__inline--right mobile">
                    <?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'mobile', array( 'max_width' => 1023 ) ); ?>
                </div>

                <?php if ( post_type_supports( get_post_type(), 'comments' ) ) { // If comments are open or we have at least one comment, load up the comment template. ?>
                    <div class='article__comments'>
                        <?php comments_template(); ?>
                    </div>
                <?php } ?>


                <?php if ( function_exists( 'related_posts' ) ): ?>
                    <?php related_posts( array( 'template' => 'partials/related-posts.php' ) ); ?>
                <?php endif; ?>

            </article>

        </section>

    </div>

<?php endwhile;


get_footer();
