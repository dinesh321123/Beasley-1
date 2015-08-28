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
			
			<div class="gmcltWX_narrowColumn">
				<div class="gmcltWX_loading">
					<p>Loading...</p>
					<img src="/wp-content/themes/wbt/images/WBTajaxLoader.gif">
				</div>
				<div class="gmcltWX_search">
					<input type="text" name="gmcltWX_search" id="gmcltWX_search" placeholder="Search for location..."><input type="submit" id="gmcltWX_searchsubmit" value="Search">
				</div>
				<div id="gmcltWX_narrowColumnContent"></div>
				<div style="text-align: center; padding-top: 10px;">
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-lists', 'desktop' ); ?>
				</div>
			</div>
			
			<div class="gmcltWX_wideColumn">
				<div id="gmcltWX_forecastFullContent"></div>
				<div id="gmcltWX_forecastContent"></div>
				<div id="radarMap-canvas"></div>
			</div>
		</section>
		
	
	</section>
	</article>
</div>

<script type="text/javascript" src="/wp-content/themes/wbt/assets/js/vendor/handlebars-v3.0.3.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>

<script id="currentConditions-template" type="text/x-handlebars-template">
	<h2>{{location}}, {{state}}</h2>
	<div class="gmcltWX_narrowColumnOne">
		<div class="gmcltWX_narrowColumnIcon">
			<img src="/wp-content/themes/wbt/images/wx/{{currentConditions.graphicCode}}.png">
		</div>
		<div class="gmcltWX_narrowColumnTemp">
			{{currentConditions.temperature}}&deg;
		</div>
		<div class="gmcltWX_data">
			<h4>{{currentConditions.sky}} {{currentConditions.weather}}</h4>
			<p style="margin-top: 0;"><strong>Wind moving from the {{currentConditions.windDirection}} at {{currentConditions.windSpeed}} mph</strong></p>
			<p class="gmcltWX_dataSmall">Current as of {{currentConditions.updateTime}} Eastern</p>
		</div>
	</div>
	<div class="gmcltWX_narrowColumnTwo">
		Feels Like: <strong>{{currentConditions.feelslike}}&deg; F</strong><br />
		Dew Point: <strong>{{currentConditions.dewpoint}}&deg; F</strong><br />
		Relative Humidity: <strong>{{currentConditions.relativeHumidity}}%</strong><br />
		Barometric Pressure: <strong>{{currentConditions.pressure}}&quot;</strong><br />
		Sunrise: <strong>{{forecast.0.sunrise}}</strong><br />
		UV Index: <strong>{{forecast.0.uvIdx}}, {{forecast.0.uvDes}}</strong><br />
		Sunset: <strong>{{forecast.0.sunset}}</strong><br />
	</div>
</script>

<script id="forecastFull-template" type="text/x-handlebars-template">
	{{#each forecast}}
		{{#if nightName}}
			<div class="gmcltWX_forecastDay gmcltWX_forecastFull">
				{{#unless eveningOnly}}
				<div class="gmcltWX_forecastIcon">
					<img src="/wp-content/themes/wbt/images/wx/{{sky24}}.png">
					High <strong>{{hiTmpF}}&deg;</strong><br />
					Low <strong>{{loTmpF}}&deg;</strong>
				</div>
				{{/unless}}
				<div class="gmcltWX_forecastText">
					{{#unless eveningOnly}}
					<h4 class="gmcltWX_forecastDayHeader">{{dayName}}</h4>
					<p>{{dayForecast}}</p>
					{{/unless}}
					<h4 class="gmcltWX_forecastDayHeader">{{nightName}}</h4>
					<p>{{nightForecast}}</p>
				</div>
				<div style="clear: both;"></div>
			</div>
		{{/if}}
	{{/each}}
</script>

<script id="forecast-template" type="text/x-handlebars-template">
	{{#each forecast}}
		{{#unless nightName}}
			<div class="gmcltWX_forecastDay gmcltWX_forecastShort{{#if firstDay}} gmcltWX_first{{/if}}{{#if lastDay}} gmcltWX_last{{/if}}">
				<h4 class="gmcltWX_forecastDayHeader gmcltWX_desktop">{{dayName}}</h4>
				<div class="gmcltWX_forecastIconShort">
					<img src="/wp-content/themes/wbt/images/wx/{{sky24}}.png">
					High <strong>{{hiTmpF}}&deg;</strong><br />
					Low <strong>{{loTmpF}}&deg;</strong><br />
						
				</div>
				<div class="gmcltWX_forecastText">
					<h4 class="gmcltWX_forecastDayHeader gmcltWX_mobile">{{dayName}}</h4>
					<p>{{dayForecast}}</p>
				</div>
				<div style="clear: both;"></div>
			</div>
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




		