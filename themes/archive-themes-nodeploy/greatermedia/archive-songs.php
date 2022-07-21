<?php
/**
 * Songs archive template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header(); ?>

	<div class="container">

		<section class="content">

			<h2 class="content__heading">
				Recently Played
				<?php $call_sign = get_query_var( GMR_LIVE_STREAM_CPT ); ?>
				<?php if ( ! empty( $call_sign ) && ! is_numeric( $call_sign ) ) : ?>
					<?php
					$stream_query = new WP_Query( array(
						'post_type'           => GMR_LIVE_STREAM_CPT,
						'meta_key'            => 'call_sign',
						'meta_value'          => $call_sign,
						'posts_per_page'      => 1,
						'ignore_sticky_posts' => 1,
						'no_found_rows'       => true,
						'fields'              => 'ids',
					) );
					if ( $stream_query->have_posts() ) : $stream_query->the_post();
						$description = get_post_meta( get_the_ID(), 'description', true );
						if ( !empty( $description ) ){
							echo 'on ' . esc_html( $description );
						}else{
							echo 'on ' . esc_html( $call_sign );
						}
					endif;
					wp_reset_postdata();
					?>
				<?php endif; ?>
			</h2>

			<!-- list songs -->
			<div class="songs__group songs__group--new-date">
			   <!--<div class="songs__group--date">May 2, 2018</div>
			   <ul class="songs__group--list">
			      <li class="song__item">
			         <div class="song__time">
			            08:10 AM
			         </div>
			         <div class="song__meta">
			            <span class="song__title">Refugee</span>
			            <span class="song__artist">
			            — Tom Petty			</span>
			         </div>
			      </li>
			   </ul>-->
			</div>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/moment.min.js"></script>
			<script>
				var currentDateOfLoop;
				jQuery.get( "https://nowplaying.bbgi.com/<?php echo esc_js($call_sign); ?>/list?limit=100&offset=0", function( data ) {
					_.each(data, function(song){
						var songDate = moment(song.timestamp * 1000);
						if (!currentDateOfLoop || (currentDateOfLoop.format('MM/DD/YY') != songDate.format('MM/DD/YY'))){
							currentDateOfLoop = songDate;
							jQuery('.songs__group').append('<div class="songs__group--date"></div><ul class="songs__group--list"></ul>');
							jQuery('.songs__group--date').last().text(currentDateOfLoop.format('MMM D, YYYY'));
						}
						jQuery('.songs__group--list').last().append('<li class="song__item"><div class="song__time"></div><div class="song__meta"><span class="song__title"></span><span class="song__artist"></span></div></li>');
						jQuery('.song__time').last().text(songDate.format('hh:mm A'));
						jQuery('.song__title').last().text(song.title);
						jQuery('.song__artist').last().text(' — ' + song.artist);
					});
				});
			</script>
			<!-- end list songs -->

		</section>

		<?php get_sidebar(); ?>

	</div>

<?php get_footer(); ?>
