<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaContestEntryEmbeddedForm extends GreaterMediaContestEntry {

	public function render_preview() {

		$html = '<div class="contest_entry">';

		$html .= '<p>' . date_i18n( 'm/d/Y h:ia', strtotime( $this->post->post_date ) ) . ' ' . wp_kses_data( $this->entrant_name ) . '</p>';

		$entry_data = json_decode( $this->entry_reference );
		if ( ! empty( $entry_data ) ) {

			$html .= '<table>';

			foreach ( $entry_data as $key => $value ) {

				$html .= '<tr>';
				$html .= '<th>' . wp_kses_data( $key ) . '</th>';
				$html .= '<td>' . wp_kses_data( $value ) . '</td>';
				$html .= '</tr>';

			}

			$html .= '</table>';

		}

		$html .= '</div>';

		echo $html;

	}

}