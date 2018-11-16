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

if ( ! function_exists( 'ee_the_contest_dates' ) ) :
	function ee_the_contest_dates( $contest = null ) {
		$contest = get_post( $contest );
		if ( ! is_a( $contest, '\WP_Post' ) ) {
			return;
		}

		$now = current_time( 'timestamp' );
		$starts = ee_get_contest_meta( $contest, 'starts' );
		$ends = ee_get_contest_meta( $contest, 'ends' );

		$label = false;
		if ( $starts > 0 && $now < $starts ) {
			$label = 'Starts ' . ee_get_date( $starts );
		} elseif ( $ends > 0 ) {
			if ( $ends < $now ) {
				$label = 'Ended';
			} else {
				$label = 'Ends ' . ee_get_date( $ends );
			}
		}

		if ( $label ) {
			echo '<div class="contest-dates">', esc_html( $label ), '</div>';
		}
	}
endif;
