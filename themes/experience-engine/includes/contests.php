<?php

if ( ! function_exists( 'ee_get_contest_meta' ) ) :
	function ee_get_contest_meta( $contest, $meta_key ) {
		$contest = get_post( $contest );
		if ( ! is_a( $contest, '\WP_Post' ) ) {
			return false;
		}

		switch ( $meta_key ) {
			case 'starts':
				return get_post_meta( $contest->ID, 'contest-start', true );
			case 'ends':
				return get_post_meta( $contest->ID, 'contest-end', true );
		}

		return false;
	}
endif;

if ( ! function_exists( 'ee_the_contest_dates_if_enabled' ) ) :
	function ee_the_contest_dates_if_enabled( $contest = null ) {
		$contest_show_dates_setting = get_option( 'contest_show_dates_setting', 'hide' );
		if  ( $contest_show_dates_setting === 'show' ) {
			ee_the_contest_dates($contest);
		}
	}
endif;

if ( ! function_exists( 'ee_the_contest_dates' ) ) :
	function ee_the_contest_dates( $contest = null ) {
		$contest = get_post( $contest );
		if ( ! is_a( $contest, '\WP_Post' ) || ! $contest->ID ) {
			return;
		}

		$now = current_time( 'timestamp', 1 );
		$starts = ee_get_contest_meta( $contest, 'starts' );
		$ends = ee_get_contest_meta( $contest, 'ends' );

		$label = false;
		if ( $starts > 0 && $now < $starts ) {
			$label = 'Starts ' . ee_get_date( $starts, 1 );
		} elseif ( $ends > 0 ) {
			if ( $ends < $now ) {
				$label = 'Ended';
			} else {
				$label = 'Ends ' . ee_get_date( $ends, 1 );
			}
		}

		if ( $label ) : ?>
			<div class="contest-dates">
				<span class="contest-dates-icon" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 488.152 488.152">
						<path d="M177.854 269.311c0-6.115-4.96-11.069-11.08-11.069h-38.665c-6.113 0-11.074 4.954-11.074 11.069v38.66c0 6.123 4.961 11.079 11.074 11.079h38.665c6.12 0 11.08-4.956 11.08-11.079v-38.66zM274.483 269.311c0-6.115-4.961-11.069-11.069-11.069h-38.67c-6.113 0-11.074 4.954-11.074 11.069v38.66c0 6.123 4.961 11.079 11.074 11.079h38.67c6.108 0 11.069-4.956 11.069-11.079v-38.66zM371.117 269.311c0-6.115-4.961-11.069-11.074-11.069h-38.665c-6.12 0-11.08 4.954-11.08 11.069v38.66c0 6.123 4.96 11.079 11.08 11.079h38.665c6.113 0 11.074-4.956 11.074-11.079v-38.66zM177.854 365.95c0-6.125-4.96-11.075-11.08-11.075h-38.665c-6.113 0-11.074 4.95-11.074 11.075v38.653c0 6.119 4.961 11.074 11.074 11.074h38.665c6.12 0 11.08-4.956 11.08-11.074V365.95zM274.483 365.95c0-6.125-4.961-11.075-11.069-11.075h-38.67c-6.113 0-11.074 4.95-11.074 11.075v38.653c0 6.119 4.961 11.074 11.074 11.074h38.67c6.108 0 11.069-4.956 11.069-11.074V365.95zM371.117 365.95c0-6.125-4.961-11.075-11.069-11.075h-38.67c-6.12 0-11.08 4.95-11.08 11.075v38.653c0 6.119 4.96 11.074 11.08 11.074h38.67c6.108 0 11.069-4.956 11.069-11.074V365.95z"/>
						<path d="M440.254 54.354v59.05c0 26.69-21.652 48.198-48.338 48.198h-30.493c-26.688 0-48.627-21.508-48.627-48.198V54.142h-137.44v59.262c0 26.69-21.938 48.198-48.622 48.198H96.235c-26.685 0-48.336-21.508-48.336-48.198v-59.05c-23.323.703-42.488 20.002-42.488 43.723v346.061c0 24.167 19.588 44.015 43.755 44.015h389.82c24.131 0 43.755-19.889 43.755-44.015V98.077c0-23.721-19.164-43.02-42.487-43.723zm-14.163 368.234c0 10.444-8.468 18.917-18.916 18.917H80.144c-10.448 0-18.916-8.473-18.916-18.917V243.835c0-10.448 8.467-18.921 18.916-18.921h327.03c10.448 0 18.916 8.473 18.916 18.921l.001 178.753z"/>
						<path d="M96.128 129.945h30.162c9.155 0 16.578-7.412 16.578-16.567V16.573C142.868 7.417 135.445 0 126.29 0H96.128C86.972 0 79.55 7.417 79.55 16.573v96.805c0 9.155 7.422 16.567 16.578 16.567zM361.035 129.945h30.162c9.149 0 16.572-7.412 16.572-16.567V16.573C407.77 7.417 400.347 0 391.197 0h-30.162c-9.154 0-16.577 7.417-16.577 16.573v96.805c0 9.155 7.423 16.567 16.577 16.567z"/>
					</svg>
				</span>
				<span><?php echo esc_html( $label ); ?></span>
			</div>
		<?php endif;
	}
endif;
