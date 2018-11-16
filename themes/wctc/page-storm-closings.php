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

                    <p style="text-align: center;"><strong>Is your organization not a member of 1450 WCTC's storm closings system yet? <a href="http://www.centraljerseysnow.com/members/signupform.cfm"><br />SIGN UP NOW!</a></strong></p>
					<!-- @todo : Update to HTTPS when supports -->
                    <iframe src="http://www.centraljerseysnow.com/members/closings/index.cfm" width="590" height="3000" scrolling="auto" frameborder="0"></iframe>
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
