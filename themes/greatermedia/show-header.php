<div class="show__header">
	<div class="show__cast">
		<img src="http://placehold.it/135x135&text=cast">
	</div>
	<nav class="show__nav">
		<a href="<?php the_permalink(); ?>"><h1 class="show__title"><?php the_title(); ?></h1></a>
		<ul>
			<?php \GreaterMedia\Shows\about_link_html( get_the_ID() ); ?>
			<?php \GreaterMedia\Shows\podcasts_link_html( get_the_ID() ); ?>
			<?php \GreaterMedia\Shows\albums_link_html( get_the_ID(), 'Galleries' ); ?>
		</ul>
	</nav>
	<div class="show__meta">
		<em>Weekdays</em>
		<em>5:30am - 10:30am</em>
		<a href="http://www.facebook.com/sharer/sharer.php?u=[URL]&title=[TITLE]" class="icon-facebook social-share-link"></a>
		<a href="http://twitter.com/home?status=[TITLE]+[URL]" class="icon-twitter social-share-link"></a>
		<a href="https://plus.google.com/share?url=[URL]" class="icon-google-plus social-share-link"></a>
	</div>
</div>