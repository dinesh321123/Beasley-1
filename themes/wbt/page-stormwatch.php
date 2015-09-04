<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header();
?>

<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400' rel='stylesheet' type='text/css'>

  <style type="text/css">
#alert-content h1 {
	font-family: 'Source Sans Pro', sans-serif;
	font-size: 24px;
	font-weight:400;
}

#alert-content h2 {
	font-family: 'Source Sans Pro', sans-serif;
	font-size: 18px;
	font-weight:400;
}

#alert-content p {
	font-family: 'Source Sans Pro', sans-serif;
	font-size: 14px;
	font-weight:300;
	margin: 0 0 10px 0;
}

#alert-content ul {
	margin: 10px 0 10px 50px;
	list-style-type: none;
}

#alert-content li {
	margin: 0 0 10px 0;
	font-family: 'Source Sans Pro', sans-serif;
	font-size: 14px;
	font-weight:300;
}

#alert-content hr {
    border: 0;
    height: 1px;
    background-image: -webkit-linear-gradient(left, rgba(0,0,0,0), rgba(0,0,0,0.75), rgba(0,0,0,0)); 
    background-image:    -moz-linear-gradient(left, rgba(0,0,0,0), rgba(0,0,0,0.75), rgba(0,0,0,0)); 
    background-image:     -ms-linear-gradient(left, rgba(0,0,0,0), rgba(0,0,0,0.75), rgba(0,0,0,0)); 
    background-image:      -o-linear-gradient(left, rgba(0,0,0,0), rgba(0,0,0,0.75), rgba(0,0,0,0)); 
    margin-bottom: 10px;
}

#search-box p {
	font-family: 'Source Sans Pro', sans-serif;
	font-size: 14px;
	font-weight:300;
	margin: 0 0 10px 0;
}
</style>
	
<div class="container">
	<section class="content">
		<article id="post-242" class="article cf post-242 page type-page status-publish hentry" role="article" itemscope="" itemtype="http://schema.org/BlogPosting">
		<header class="article__header">
			<h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>
			<?php get_template_part( 'partials/social-share' ); ?>
		</header>
		<section class="article__content" itemprop="articleBody">
			<?php the_content(); ?>
			
			Choose a state to view WBT Operation Storm Watch Alerts:<br />
				<select id="GMCLTstateSelect" name="GMCLTstateSelect">
					<option value="NC" selected="selected">NORTH CAROLINA</option>
					<option value="SC">SOUTH CAROLINA</option>
					<option value="NC">   --------   </option>
					<option value="AL">ALABAMA</option>
					<option value="AK">ALASKA</option>
					<option value="AZ">ARIZONA</option>
					<option value="AR">ARKANSAS</option>
					<option value="CA">CALIFORNIA</option>
					<option value="CO">COLORADO</option>
					<option value="CT">CONNECTICUT</option>
					<option value="DE">DELAWARE</option>
					<option value="DC">DISTRICT OF COLUMBIA</option>
					<option value="FL">FLORIDA</option>
					<option value="GA">GEORGIA</option>
					<option value="HI">HAWAII</option>
					<option value="ID">IDAHO</option>
					<option value="IL">ILLINOIS</option>
					<option value="IN">INDIANA</option>
					<option value="IA">IOWA</option>
					<option value="KS">KANSAS</option>
					<option value="KY">KENTUCKY</option>
					<option value="LA">LOUISIANA</option>
					<option value="ME">MAINE</option>
					<option value="MD">MARYLAND</option>
					<option value="MA">MASSACHUSETTS</option>
					<option value="MI">MICHIGAN</option>
					<option value="MN">MINNESOTA</option>
					<option value="MS">MISSISSIPPI</option>
					<option value="MO">MISSOURI</option>
					<option value="MT">MONTANA</option>
					<option value="NE">NEBRASKA</option>
					<option value="NV">NEVADA</option>
					<option value="NH">NEW HAMPSHIRE</option>
					<option value="NJ">NEW JERSEY</option>
					<option value="NM">NEW MEXICO</option>
					<option value="NY">NEW YORK</option>
					<option value="NC">NORTH CAROLINA</option>
					<option value="ND">NORTH DAKOTA</option>
					<option value="OH">OHIO</option>
					<option value="OK">OKLAHOMA</option>
					<option value="OR">OREGON</option>
					<option value="PA">PENNSYLVANIA</option>
					<option value="RI">RHODE ISLAND</option>
					<option value="SC">SOUTH CAROLINA</option>
					<option value="SD">SOUTH DAKOTA</option>
					<option value="TN">TENNESSEE</option>
					<option value="TX">TEXAS</option>
					<option value="UT">UTAH</option>
					<option value="VT">VERMONT</option>
					<option value="VA">VIRGINIA</option>
					<option value="WA">WASHINGTON</option>
					<option value="WV">WEST VIRGINIA</option>
					<option value="WI">WISCONSIN</option>
					<option value="WY">WYOMING</option>
				</select>
			<div class="gmcltWX_wideColumn">
				<div class="gmcltWX_mapLoading">
					<p>Loading...</p>
					<img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>images/WBTajaxLoader.gif">
				</div>
				<div id="stormwatchMap-canvas"></div>
			</div>
			
			<div class="gmcltWX_narrowColumn">
				
				<div id="gmcltWX_narrowColumnContent"></div>
				<div style="text-align: center; padding-top: 10px;">
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-lists', 'desktop' ); ?>
				</div>
			</div>
			
			
		</section>
		
	
	</section>
	</article>
</div>

<script type="text/javascript" src="<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>assets/js/vendor/handlebars-v3.0.3.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>


<script id="error-template" type="text/x-handlebars-template">
	<h2>Sorry!</h2>
	<p>An error has occurred while loading weather information. Please refresh the page and try again.</p>
</script>

<script id="alert-template" type="text/x-handlebars-template">
	<h3>WBT Operation Stormwatch Alerts for {{0.countyName}} County</h3>
	{{#each 0.alerts}}
		<h4>{{event}}</h4>
		<p>{{description}} - expires {{expires}}</p>
		<a href="{{web}}" target="_blank">see more at weather.gov</a>
	{{/each}}
</script>

<script type="text/javascript">
jQuery(document).ready(function(){
	GMCLT.Weather.stormwatchInit();
	
});
</script>

<?php get_footer(); ?>




		