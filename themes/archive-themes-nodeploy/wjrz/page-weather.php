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
        if ( has_post_thumbnail() && ! bbgi_post_has_gallery() && ! has_post_format( 'video' ) && ! has_post_format( 'audio' )  ): ?>
            <div class="article__thumbnail" style='background-image: url(<?php gm_post_thumbnail_url( 'gm-article-thumbnail' ); ?>)'>
                <?php bbgi_the_image_attribution(); ?>
            </div>
        <?php endif; ?>

        <section class="content">

            <article id="post-<?php the_ID(); ?>" <?php post_class( 'article cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
                <header class="article__header">

                    <time class="article__date" datetime="<?php echo get_the_time(); ?>"><?php the_date('F j, Y'); ?></time>
                    <h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>
                    <?php get_template_part( 'partials/social-share' ); ?>

                </header>

                <section class="article__content" itemprop="articleBody">
                    <?php the_content(); ?>

                    <a href="http://www.accuweather.com/en/us/manahawkin-nj/08050/sun-sand-current-weather/2192064" class="aw-widget-legal">
                    <!--
                    By accessing and/or using this code snippet, you agree to AccuWeather’s terms and conditions (in English) which can be found at http://www.accuweather.com/en/free-weather-widgets/terms and AccuWeather’s Privacy Statement (in English) which can be found at http://www.accuweather.com/en/privacy.
                    -->
                    </a><div id="awtd1406746145996" class="aw-widget-36hour"  data-locationkey="2192064" data-unit="f" data-language="en-us" data-useip="false" data-uid="awtd1406746145996" data-editlocation="true" data-lifestyle="sun-sand"></div><script type="text/javascript" src="https://oap.accuweather.com/launch.js"></script>

                    <hr />

                    <center>
                    <p><strong>Check out the latest ski reports from SnoCountry</strong>:</p><iframe src="https://www.snocountry.com/widget/widget_us_states.php?pettabs=1&region=us&size=small" frameborder="0" height="435" width="250" scrolling="no"><br />
                    <p>Your browser does not support iframes.</p><br />
                    </iframe></center>
                </section>

                <?php get_template_part( 'partials/article-footer' ); ?>

                <?php if ( function_exists( 'related_posts' ) ): ?>
                    <?php related_posts( array( 'template' => 'partials/related-posts.php' ) ); ?>
                <?php endif; ?>

            </article>

        </section>

		<?php get_sidebar(); ?>

    </div>

<?php endwhile;


get_footer();
