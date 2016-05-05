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
		<article id="post-242" class="article cf post-242 page type-page status-publish hentry" role="article" itemscope="" itemtype="http://schema.org/BlogPosting">
		<header class="article__header">
			<h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>
			<?php get_template_part( 'partials/social-share' ); ?>
		</header>
		<section class="article__content" itemprop="articleBody">
			<?php the_content(); ?>

			<div class="gmclt_narrowColumn">
				<div class="gmclt_wxLoading">
					<p>Loading...</p>
<<<<<<< HEAD
					<img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>images/WBTajaxLoader.gif">
=======
					<img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>images/WLNKajaxLoader.gif">
>>>>>>> feature/wlnk-v2
				</div>
				<div class="gmclt_wxSearch">
					<input type="text" name="gmclt_wxSearch" id="gmclt_wxSearch" placeholder="Search for location..."><input type="submit" id="gmclt_wxSearchsubmit" value="Search">
				</div>
				<div id="gmclt_narrowColumnContent"></div>
				<div class="gmclt_adDiv">
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-lists', 'desktop' ); ?>
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-lists', 'mobile' ); ?>
				</div>
			</div>

			<div class="gmclt_wideColumn right">
				<div id="gmcltWX_forecastFullContent"></div>
				<div id="gmcltWX_forecastContent"></div>
				<div id="gmclt_radarMapCanvas"></div>
			</div>
		</section>


	</section>
	</article>
</div>

<script id="currentConditions-template" type="text/x-handlebars-template">
	<h2>{{location}}, {{state}}</h2>
	<div class="gmclt_narrowColumnOne">
		<div class="gmclt_narrowColumnIcon">
			<img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>images/wxv2/60/wx_{{currentConditions.IconCode}}.png">
		</div>
		<div class="gmclt_narrowColumnTemp">
			{{currentConditions.TempF}}&deg;
		</div>
		<div class="gmclt_wxData">
			<h4>{{currentConditions.Sky}} {{currentConditions.Weather}}</h4>
			<p><strong>Wind moving from the {{currentConditions.WindCardinal}} at {{currentConditions.WindSpeed}} mph</strong></p>
			<p class="gmclt_wxDataSmall">Current as of {{currentConditions.LastLocalReportTime}} Eastern</p>
		</div>
	</div>
	<div class="gmclt_narrowColumnTwo">
		Feels Like: <strong>{{currentConditions.FeelsLikeF}}&deg; F</strong><br />
		Dew Point: <strong>{{currentConditions.DewPointF}}&deg; F</strong><br />
		Relative Humidity: <strong>{{currentConditions.RelativeHumidity}}%</strong><br />
		Barometric Pressure: <strong>{{currentConditions.Pressure}}&quot;</strong><br />
		Sunrise: <strong>{{forecast.0.Sunrise}}</strong><br />
		UV Index: <strong>{{forecast.0.UVIndex}}, {{forecast.0.UVDescription}}</strong><br />
		Sunset: <strong>{{forecast.0.Sunset}}</strong><br />
	</div>
</script>

<script id="forecastFull-template" type="text/x-handlebars-template">
	{{#each forecast}}
		{{#if PhraseNight}}
			<div class="gmclt_wxForecastDay gmcltWX_forecastFull">
				<div>
					{{#unless EveningOnly}}
						<div class="gmclt_wxForecastIcon">
							<img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>images/wxv2/60/wx_{{IconCode}}.png">
							High <strong>{{HiTempF}}&deg;</strong>
						</div>
						<div class="gmclt_wxForecastText">
							<h4 class="gmclt_wxForecastDayHeader">{{DayName}}</h4>
							<p>{{PhraseDay}}</p>
						</div>
					{{/unless}}
				</div>
				<div class="gmclt_clear"></div>
				<div>
					<div class="gmclt_wxForecastIcon">
						<img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>images/wxv2/60/wx_{{IconCodeNight}}.png">
						Low <strong>{{LowTempF}}&deg;</strong>
					</div>
					<div class="gmclt_wxForecastText">
						<h4 class="gmclt_wxForecastDayHeader">{{DayName}} Night</h4>
						<p>{{PhraseNight}}</p>
					</div>
				</div>
				<div class="gmclt_clear"></div>
			</div>

		{{/if}}
	{{/each}}
</script>

<script id="forecast-template" type="text/x-handlebars-template">
	{{#each forecast}}
		{{#unless PhraseNight}}
			{{#if DisplayDay}}
				<div class="gmclt_wxForecastDay gmclt_wxForecastShort{{#if firstDay}} gmclt_wxFirst{{/if}}{{#if lastDay}} gmclt_wxLast{{/if}}">
					<h4 class="gmclt_wxForecastDayHeader gmclt_wxDesktop">{{DayName}}</h4>
					<div class="gmclt_wxForecastIconShort">
						<img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>images/wxv2/60/wx_{{IconCode}}.png">
						High <strong>{{HiTempF}}&deg;</strong><br />
						Low <strong>{{LowTempF}}&deg;</strong><br />

					</div>
					<div class="gmclt_wxForecastText">
						<h4 class="gmclt_wxForecastDayHeader gmclt_wxMobile">{{DayName}}</h4>
						<p>{{ShortPhrase}}</p>
					</div>
					<div class="gmclt_clear"></div>
				</div>
			{{/if}}
		{{/unless}}
	{{/each}}
</script>

<script id="searchResults-template" type="text/x-handlebars-template">
	<h2>Search Results</h2>
	<p>{{{message}}}</p>
	{{#each results}}
		<a href="javascript:void(0)" onclick="GMCLT.Weather.populateWeatherData('{{locationId}}')">{{location}}</a><br />
	{{/each}}
</script>

<script id="error-template" type="text/x-handlebars-template">
	<h2>Sorry!</h2>
	<p>An error has occurred while loading weather information. Please refresh the page and try again.</p>
</script>

<script type="text/javascript">
jQuery(document).ready(function(){
	GMCLT.Weather.init();

});
</script>

<?php get_footer(); ?>
