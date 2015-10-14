<?php

class ContestEntryEmbeddedForm extends GreaterMediaContestEntry {

	private static $_profile = null;

	public $entrant_email;
	public $entrant_gender;
	public $entrant_zip;
	public $entrant_birth_year;
	public $entrant_birth_date;

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

	public function save() {
		parent::save();

		$post_id = $this->post_id();

		update_post_meta( $post_id, 'entrant_email', $this->entrant_email );
		update_post_meta( $post_id, 'entrant_gender', $this->entrant_gender );
		update_post_meta( $post_id, 'entrant_zip', $this->entrant_zip );
		update_post_meta( $post_id, 'entrant_birth_year', $this->entrant_birth_year );
		update_post_meta( $post_id, 'entrant_birth_date', $this->entrant_birth_date );
	}

	/**
	 * Factory method to create a new contest entry for a given set of data
	 *
	 * @param int $contest_id Post ID of the related contest
	 * @param string $entry_reference ID or link to the source of the entry
	 * @return GreaterMediaContestEntry
	 */
	public static function create_for_data( $contest_id, $entry_reference ) {
		$entry = new ContestEntryEmbeddedForm( null, $contest_id );

		$entry->_fill_entrant_info();
		$entry->entry_source = self::ENTRY_SOURCE_EMBEDDED_FORM;
		$entry->entry_reference = $entry_reference;

		return $entry;
	}

	protected function _fill_entrant_info() {
		if ( function_exists( 'is_gigya_user_logged_in' ) && is_gigya_user_logged_in() ) {
			$this->entrant_reference = get_gigya_user_id();
			$this->entrant_name = trim( sprintf( '%s %s', self::_get_user_field( 'firstName' ), self::_get_user_field( 'lastName' ) ) );
			$this->entrant_email = self::_get_user_field( 'email' );
			$gender = self::_get_user_field( 'gender' );
			$this->entrant_gender = $gender != 'u' ? $gender : null;
			$this->entrant_zip = self::_get_user_field( 'zip' );
			$this->entrant_birth_year = self::_get_user_field( 'birthYear' );
			$this->entrant_birth_date = strtotime( sprintf(
				'%s-%02s-%02s',
				self::_get_user_field( 'birthYear' ),
				self::_get_user_field( 'birthMonth', '01' ),
				self::_get_user_field( 'birthDay', '01' )
			) );

			return;
		}

		$submitted_by = trim( strip_tags( filter_input( INPUT_POST, 'userinfo_submitted_by' ) ) );
		if ( empty( $submitted_by ) ) {
			$submitted_by = 'Anonymous Listener';
		}

		$birth_year = null;
		$dob = strtotime( filter_input( INPUT_POST, 'userinfo_dob' ) );
		if ( $dob ) {
			$birth_year = date( 'Y', $dob );
		}

		$this->entrant_name = $submitted_by;
		$this->entrant_email = filter_input( INPUT_POST, 'userinfo_email', FILTER_VALIDATE_EMAIL );
		$this->entrant_gender = null;
		$this->entrant_zip = trim( strip_tags( filter_input( INPUT_POST, 'userinfo_zip' ) ) );
		$this->entrant_birth_year = $birth_year;
		$this->entrant_birth_date = $dob;
	}

	private static function _get_user_field( $field, $default = null ) {
		if ( is_null( self::$_profile ) ) {
			self::$_profile = get_gigya_user_profile();
		}

		return ! empty( self::$_profile[ $field ] ) ? self::$_profile[ $field ] : $default;
	}

}