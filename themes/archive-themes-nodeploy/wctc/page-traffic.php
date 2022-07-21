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
                    <iframe src="https://www.sigalert.com/Custom/Map.asp?partner=WCTC-AM&lat=40.32424&lon=-74.43478&z=2&th=brown&ap=left&sp=p&urqs=1" width="574" height="665" style="border:1px solid #000000" frameborder="0" scrolling="auto" marginwidth="0" marginheight="0" allowtransparency="true"></iframe>
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
