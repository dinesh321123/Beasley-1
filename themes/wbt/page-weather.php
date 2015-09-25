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
					<img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>images/WBTajaxLoader.gif">
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
			<img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>images/wx/{{currentConditions.graphicCode}}.png">
		</div>
		<div class="gmclt_narrowColumnTemp">
			{{currentConditions.temperature}}&deg;
		</div>
		<div class="gmclt_wxData">
			<h4>{{currentConditions.sky}} {{currentConditions.weather}}</h4>
			<p><strong>Wind moving from the {{currentConditions.windDirection}} at {{currentConditions.windSpeed}} mph</strong></p>
			<p class="gmclt_wxDataSmall">Current as of {{currentConditions.updateTime}} Eastern</p>
		</div>
	</div>
	<div class="gmclt_narrowColumnTwo">
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
			<div class="gmclt_wxForecastDay gmcltWX_forecastFull">
				{{#unless eveningOnly}}
				<div class="gmclt_wxForecastIcon">
					<img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>images/wx/{{sky24}}.png">
					High <strong>{{hiTmpF}}&deg;</strong><br />
					Low <strong>{{loTmpF}}&deg;</strong>
				</div>
				{{/unless}}
				<div class="gmclt_wxForecastText">
					{{#unless eveningOnly}}
					<h4 class="gmclt_wxForecastDayHeader">{{dayName}}</h4>
					<p>{{dayForecast}}</p>
					{{/unless}}
					<h4 class="gmclt_wxForecastDayHeader">{{nightName}}</h4>
					<p>{{nightForecast}}</p>
				</div>
				<div class="gmclt_clear"></div>
			</div>
		{{/if}}
	{{/each}}
</script>

<script id="forecast-template" type="text/x-handlebars-template">
	{{#each forecast}}
		{{#unless nightName}}
			<div class="gmclt_wxForecastDay gmclt_wxForecastShort{{#if firstDay}} gmclt_wxFirst{{/if}}{{#if lastDay}} gmclt_wxLast{{/if}}">
				<h4 class="gmclt_wxForecastDayHeader gmclt_wxDesktop">{{dayName}}</h4>
				<div class="gmclt_wxForecastIconShort">
					<img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>images/wx/{{sky24}}.png">
					High <strong>{{hiTmpF}}&deg;</strong><br />
					Low <strong>{{loTmpF}}&deg;</strong><br />
						
				</div>
				<div class="gmclt_wxForecastText">
					<h4 class="gmclt_wxForecastDayHeader gmclt_wxMobile">{{dayName}}</h4>
					<p>{{dayForecast}}</p>
				</div>
				<div class="gmclt_clear"></div>
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




		