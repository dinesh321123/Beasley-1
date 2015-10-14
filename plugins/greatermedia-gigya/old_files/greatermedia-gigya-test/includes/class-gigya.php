<?php
class GMI_Gigya {

	public static function hooks() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'gigya_script' ) );
	}

	public static function gigya_script() {
		?>
		<!-- socialize.js script should only be included once -->

		<script type='text/javascript' src='http://cdn.gigya.com/JS/socialize.js?apiKey=3_e_T7jWO0Vjsd9y0WJcjnsN6KaFUBv6r3VxMKqbitvw-qKfmaUWysQKa1fra5MTb6'>
			{
				enabledProviders: 'facebook,twitter,linkedin,yahoo,messenger'
			}
		</script>

<?php
	}
}