<?php
/**
 * Template Name:User Myaccount
 *
 */
get_header(); ?>
	<div id="primary" class="content-area">
		<div class="container">
			<div class="site-content" id="content">
				<main id="main" class="site-main" role="main">
					<div class="row padding-">
						<div class="col-lg-12 ">
							<h1><?php echo esc_html( get_the_title() ); ?></h1>
							<p><?php echo esc_html("Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.");?></p>
							<button type="button"><?php echo esc_html("Click Me!"); ?></button>
						</div>
					</div>
				</main>
			</div>
		</div>
	</div>
<?php get_footer(); ?>