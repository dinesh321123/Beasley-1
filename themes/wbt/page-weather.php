<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header();

// 	get_template_part( 'partials/article', 'page' );
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
			<div class="gmcltWX_current">
				<h2>Charlotte, NC</h2>
				<div class="gmcltWX_currentOne">
					<div class="gmcltWX_currentIcon">
						<img src="/wp-content/themes/wbt/images/wx/66.png">
					</div>
					<div class="gmcltWX_currentTemp">
						94&deg;
					</div>
					<div class="gmcltWX_data">
						<h4>Broken Clouds</h4>
						<p><strong>Wind moving from the Southeast at 8 mph</strong></p>
						<p class="gmcltWX_dataSmall">Current as of 5:52 PM EDT</p>
					</div>
				</div>
				<div class="gmcltWX_currentTwo">
					Feels Like: <strong>96&deg; F</strong><br />
					Dew Point: <strong>65&deg; F</strong><br />
					Humidity: <strong>38%</strong><br />
					Barometric Pressure: <strong>29.98&quote;</strong><br />
					Sunrise: <strong>6:28am</strong><br />
					UV Index: <strong>10, Very High</strong><br />
					Sunset: <strong>8:30pm</strong><br />
				</div>
				<?php do_action( 'acm_tag_gmr_variant', 'mrec-lists', 'desktop' ); ?>
			</div>
			
			<div class="gmcltWX_forecast">
				<div class="gmcltWX_forecastDay gmcltWX_forecastExpanded">
					<div class="gmcltWX_forecastIcon">
						<img src="/wp-content/themes/wbt/images/wx/66.png">
						High <strong>94&deg;</strong><br />
						Low <strong>71&deg;</strong>
					</div>
					<div class="gmcltWX_forecastText">
						<h4 class="gmcltWX_forecastDayHeader">Monday</h4>
						<p>
							Sun and clouds mixed. A stray shower or thunderstorm is possible. High 94F. Winds light and variable.
						</p>
						<h4 class="gmcltWX_forecastDayHeader">Monday Night</h4>
						<p>
							Cloudy skies early, then partly cloudy after midnight. A stray shower or thunderstorm is possible. Low 71F. Winds light and variable.
						</p>
					</div>
					<div style="clear: both;"></div>
				</div>
				
				<div class="gmcltWX_forecastDay gmcltWX_forecastExpanded">
					<div class="gmcltWX_forecastIcon">
						<img src="/wp-content/themes/wbt/images/wx/84.png">
						High <strong>92&deg;</strong><br />
						Low <strong>70&deg;</strong>
					</div>
					<div class="gmcltWX_forecastText">
						<h4 class="gmcltWX_forecastDayHeader">Tuesday</h4>
						<p>
							Partly cloudy in the morning followed by scattered thunderstorms in the afternoon. High 92F. Winds light and variable. Chance of rain 60%.
						</p>
						<h4 class="gmcltWX_forecastDayHeader">Tuesday Night</h4>
						<p>
							Variable clouds with scattered thunderstorms. Low around 70F. Winds light and variable. Chance of rain 50%.
						</p>
					</div>
					<div style="clear: both;"></div>
				</div>
				
				<div class="gmcltWX_forecastDay gmcltWX_forecastShort gmcltWX_first">
					<h4 class="gmcltWX_forecastDayHeader gmcltWX_desktop">Wednesday</h4>
					<div class="gmcltWX_forecastIconShort">
						<img src="/wp-content/themes/wbt/images/wx/84.png">
						High <strong>91&deg;</strong><br />
						Low <strong>72&deg;</strong><br />
							
					</div>
					<div class="gmcltWX_forecastText">
						<h4 class="gmcltWX_forecastDayHeader gmcltWX_mobile">Wednesday</h4>
						<p>
							Showers and thunderstorms late. Highs in the low 90s and lows in the low 70s.
						</p>
					</div>
					<div style="clear: both;"></div>
				</div>
				
				<div class="gmcltWX_forecastDay gmcltWX_forecastShort">
					<h4 class="gmcltWX_forecastDayHeader gmcltWX_desktop">Thursday</h4>
					<div class="gmcltWX_forecastIconShort">
						<img src="/wp-content/themes/wbt/images/wx/84.png">
						High <strong>94&deg;</strong><br />
						Low <strong>70&deg;</strong>
					</div>
					<div class="gmcltWX_forecastText">
						<h4 class="gmcltWX_forecastDayHeader gmcltWX_mobile">Thursday</h4>
						<p>
							Afternoon showers and thunderstorms. Highs in the mid 90s and lows in the low 70s.
						</p>
					</div>
					<div style="clear: both;"></div>
				</div>
				
				<div class="gmcltWX_forecastDay gmcltWX_forecastShort">
					<h4 class="gmcltWX_forecastDayHeader gmcltWX_desktop">Friday</h4>
					<div class="gmcltWX_forecastIconShort">
						<img src="/wp-content/themes/wbt/images/wx/84.png">
						High <strong>91&deg;</strong><br />
						Low <strong>70&deg;</strong>
					</div>
					<div class="gmcltWX_forecastText">
						<h4 class="gmcltWX_forecastDayHeader gmcltWX_mobile">Friday</h4>
						<p>
							Scattered thunderstorms. Highs in the low 90s and lows in the low 70s.
						</p>
					</div>
					<div style="clear: both;"></div>
				</div>
				
				<div class="gmcltWX_forecastDay gmcltWX_forecastShort">
					<h4 class="gmcltWX_forecastDayHeader gmcltWX_desktop">Saturday</h4>
					<div class="gmcltWX_forecastIconShort">
						<img src="/wp-content/themes/wbt/images/wx/65.png">
						High <strong>91&deg;</strong><br />
						Low <strong>69&deg;</strong>
					</div>
					<div class="gmcltWX_forecastText">
						<h4 class="gmcltWX_forecastDayHeader gmcltWX_mobile">Saturday</h4>
						<p>
							More sun than clouds. Highs in the low 90s and lows in the upper 60s.
						</p>
					</div>
					<div style="clear: both;"></div>
				</div>
				
				<div class="gmcltWX_forecastDay gmcltWX_forecastShort gmcltWX_last">
					<h4 class="gmcltWX_forecastDayHeader gmcltWX_desktop">Sunday</h4>
					<div class="gmcltWX_forecastIconShort">
						<img src="/wp-content/themes/wbt/images/wx/84.png">
						High <strong>88&deg;</strong><br />
						Low <strong>68&deg;</strong>
					</div>
					<div class="gmcltWX_forecastText">
						<h4 class="gmcltWX_forecastDayHeader gmcltWX_mobile">Sunday</h4>
						<p>
							A few thunderstorms possible. Highs in the upper 80s and lows in the upper 60s.
						</p>
					</div>
					<div style="clear: both;"></div>
				</div>
				
			</div>
		</section>
		
	
	</section>
	</article>
</div>

<?php get_footer(); ?>




		