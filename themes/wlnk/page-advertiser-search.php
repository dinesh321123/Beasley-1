<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header();
?>

<div class="container">
	<section class="content">
		<article id="post-309" class="article cf post-309 page type-page status-publish hentry" role="article" itemscope="" itemtype="http://schema.org/BlogPosting">
		<header class="article__header">
			<h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>
			<?php get_template_part( 'partials/social-share' ); ?>
		</header>
		<section class="article__content" itemprop="articleBody">
			<?php the_content(); ?>


			<div class="gmclt_wideColumn left">
				<div id="gmclt_categoryDropdown"></div>
				<div class="gmclt_searchBar">
					<input type="text" name="gmclt_advertiserSearch" id="gmclt_advertiserSearch" placeholder="search for an advertiser or product..." value=""><input type="submit" id="gmclt_searchSubmit" value="Search">
				</div>
				<div class="gmclt_searching">
					<p>Searching...</p>
					<img src="/wp-content/themes/wbt/images/WBTajaxLoader.gif">
				</div>
				<div id="gmclt_wideColumnContent"></div>
			</div>

			<div class="gmclt_narrowColumn">

				<div id="gmclt_narrowColumnContent"></div>
				<div class="gmclt_adDiv">
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-lists', 'desktop' ); ?>
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-lists', 'mobile' ); ?>
				</div>
			</div>


		</section>


	</section>
	</article>
</div>

<script id="error-template" type="text/x-handlebars-template">
	<h2>Sorry!</h2>
	<p>An error has occurred while searching our advertisers. Please refresh the page and try again.</p>
</script>

<script id="category-template" type="text/x-handlebars-template">
	<select id="gmclt_categorySelect">
		<option value="0">Choose a category...</option>
		{{#each this}}
			<option value="{{categoryId}}">{{categoryName}}</option>
		{{/each}}
	</select>
	OR
</script>

<script id="searchResults-template" type="text/x-handlebars-template">
	{{#if message}}<p>{{{message}}}</p>{{/if}}
	<div class="gmclt_advertiserList">
	{{#each results}}

	<article class="entry">
		<section class="entry2__meta">
			<h2 class="entry2__title" itemprop="headline">{{advertiserName}} {{analytics analyticsId}}</h2>
			{{#if advertiserLogo}}<img src="{{advertiserLogo}}" align="right">{{/if}}
			<p>{{{advertiserDescription}}}</p>
			{{#if advertiserUrl}}
				On the web: <a href="{{advertiserUrl}}" onclick="ga('send', {'hitType': 'event', 'eventCategory': 'Advertising', 'eventAction': 'Clickthrough', 'eventLabel': '{{analyticsId}}'})" target="_blank">{{advertiserDisplayUrl}}</a>
			{{/if}}
			{{#if advertiserPhone}}
				<br />Phone: {{advertiserPhone}}
			{{/if}}

			{{#if advertiserLocations}}
			<div>
				{{#if advertiserMapUrl}}
					<div class="gmclt_right">
						<img class="gmclt_locationMap" src="{{advertiserMapUrl}}">
					</div>
				{{/if}}
				<div class="gmclt_left">
					{{#each advertiserLocations}}
						<div class="gmclt_topBottomPadding">
							<div class="gmclt_mapPin" style="background-position:0 {{locationPinImagePosition}}px;"></div>
							<div class="gmclt_left">
								{{locationAddressOne}}{{#if locationAddressTwo}}, {{locationAddressTwo}}{{/if}}<br />
								{{locationCity}}, {{locationState}} {{locationZipcode}}<br />
								{{#if locationPhoneNumber}}Phone: {{locationPhoneNumber}}<br />{{/if}}
								{{#if locationDescription}}{{locationDescription}}<br />{{/if}}
							</div>
							<div class="gmclt_clear"></div>
						</div>
					{{/each}}
				</div>

				<div class="gmclt_clear"></div>
			<div>
			{{/if}}

		</section>

		{{#each advertiserSpots}}
			<div class="podcast-player podcast-player--compact">
				<div class="podcast__play mp3-{{spotHash}}">
					<div class="podcast__cover">
						<button class="podcast__btn--play" data-mp3-src="{{spotFileName}}" data-mp3-title="{{../advertiserName}} Commercial #{{inc @index}}" data-mp3-artist=" " data-mp3-hash="{{spotHash}}"></button>
						<button class="podcast__btn--pause"></button>
					</div>
					<span class="podcast__runtime"></span>
				</div>
				<div class="podcast__meta">
					<h3 class="podcast__title">Commercial #{{inc @index}}</h3>
					<div id="audio__time" class="audio__time">
						<div id="audio__progress-bar" class="audio__progress-bar">
							<span id="audio__progress" class="audio__progress"></span>
						</div>
						<div id="audio__time--elapsed" class="audio__time--elapsed"></div>
						<div id="audio__time--remaining" class="audio__time--remaining"></div>
					</div>
					<a href="{{spotFileName}}" download="{{spotFileName}}" class="podcast__download--fallback">Download Commercial</a>
				</div>

			</div>
		{{/each}}


	</article>

	{{/each}}
	</div>
</script>

<script type="text/javascript">
	jQuery(document).ready(function(){
		GMCLT.AdIndex.init();
	});
</script>

<?php get_footer(); ?>
