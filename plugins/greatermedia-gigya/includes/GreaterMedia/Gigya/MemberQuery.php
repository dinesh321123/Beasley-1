<?php

namespace GreaterMedia\Gigya;

/**
 * MemberQuery represents the content of the MemberQueryPostType. It is
 * a json representation of the constraints, query and direct query that
 * make up an individual MemberQueryPostType.
 *
 * The MemberQuery JSON is stored in postmeta corresponding to it's
 * parent MemberQuery CPT.
 *
 * @package GreaterMedia\Gigya
 */
class MemberQuery {

	/**
	 * Mapping of data types to their Gigya suffixes
	 *
	 * @access public
	 * @var array
	 */
	static public $suffixes = array(
		'integer' => 'i',
		'float'   => 'f',
		'string'  => 's',
		'text'    => 't',
		'boolean' => 'b',
		'date'    => 'd',
	);

	/**
	 * Mapping of operators labels to their Gigya operator symbols.
	 *
	 * @access public
	 * @var array
	 */
	static public $operators = array(
		'equals'                   => '=',
		'not equals'               => '!=',
		'greater than'             => '>',
		'greater than or equal to' => '>=',
		'less than'                => '<',
		'less than or equal to'    => '<=',
	);

	/**
	 * The id of the post to which the current MemberQuery belongs.
	 *
	 * @access public
	 * @var integer
	 */
	public $post_id = null;

	/**
	 * The properties that make up a MemberQuery.
	 *
	 * @access public
	 * @var array
	 */
	public $properties;

	/**
	 * Name of Gigya Storage 'table'. All items are normalized and
	 * stored as entry items in this table.
	 *
	 * @access public
	 * @var string
	 */
	public $storeName = 'entries';

	/**
	 * Stores the post object corresponding to the member query and
	 * parse it's post_content into the MemberQuery properties.
	 */
	public function __construct( $post_id, $constraints = null ) {
		if ( ! is_null( $post_id ) ) {
			$this->post_id = $post_id;
			$this->properties = $this->parse( $this->content_for( $post_id ) );
		} else {
			$this->properties = $this->parse( $constraints );
		}
	}

	/**
	 * Fetches the raw JSON content for a post using postmeta lookups.
	 *
	 * @access public
	 * @param integer $post
	 * @return string The json representation of a MemberQuery.
	 */
	public function content_for( $post_id ) {
		return get_post_meta( $post_id, 'member_query_constraints', true );
	}

	/**
	 * Parses the JSON representation of a MemberQuery into a PHP array.
	 *
	 * @access public
	 * @param string The JSON to parse
	 * @return array
	 */
	public function parse( $content ) {
		if ( $content !== '' ) {
			$json = json_decode( $content, true );

			if ( ! is_array( $json ) ) {
				$json = array();
			}
		} else {
			$json = array();
		}

		return array(
			'constraints' => $json,
		);
	}

	/**
	 * Builds the MemberQuery from POST data.
	 *
	 * @access public
	 * @return string JSON built from the POST data.
	 */
	public function build() {
		if ( array_key_exists( 'constraints', $_POST ) ) {
			// TODO: Should check if constraints is actually json
			return sanitize_text_field( $_POST['constraints'] );
		} else {
			return '[]';
		}
	}

	/**
	 * Builds the MemberQuery JSON from POST and saves to the current
	 * post_id's postmeta.
	 *
	 * Post Meta key is 'member_query_constraints'
	 *
	 * @access public
	 * @return void
	 */
	public function build_and_save() {
		$json = $this->build();
		update_post_meta( $this->post_id, 'member_query_constraints', $json );
	}

	/**
	 * Returns the parsed constraints for the current member query.
	 *
	 * @access public
	 * @return array
	 */
	public function get_constraints() {
		return $this->properties['constraints'];
	}

	/**
	 * Returns the generated GQL query. Currently this generates the GQL
	 * everytime.
	 *
	 * TODO: Look into whether we should cache the generated query in
	 * postmeta.
	 *
	 * @access public
	 * @return string
	 */
	public function get_query() {
		return $this->to_gql();
	}

	/**
	 * Converts the MemberQuery back to it's JSON representation.
	 *
	 * @access public
	 * @return string JSON representation of the current MemberQuery.
	 */
	public function to_json() {
		return json_encode( $this->properties );
	}

	/**
	 * Returns the GQL to execute for this query. If a direct query is
	 * present it is used, else the generated query is returned.
	 *
	 * @access public
	 * @param bool $count Optionally Whether to return an aggregate query
	 * @param int $limit Optional row limit to apply to the query
	 * @return string
	 */
	public function to_gql( $count = false, $limit = null ) {
		$query  = 'select * from accounts where ';
		$query .= $this->clause_for( $this->get_constraints() );

		if ( $count ) {
			$query = str_replace( '*', 'count(*)', $query );
		}

		if ( is_int( $limit ) ) {
			$query .= " limit $limit";
		}

		return $query;
	}

	/**
	 * Builds the GQL for a list of constraints.
	 *
	 * @access public
	 * @param array $constraints The list of constraint array
	 * @return string
	 */
	public function clause_for( $constraints ) {
		$query       = '';
		$total       = count( $constraints );

		for ( $i = 0; $i < $total; $i++ ) {
			$constraint = $constraints[ $i ];
			$query .= $this->clause_for_constraint( $constraint );

			if ( $i < $total - 1 ) {
				$query .= ' ' . $constraint['conjunction'] . ' ';
			}
		}

		return $query;
	}

	/**
	 * Builds the GQL clause for a constraint based on it's type.
	 *
	 * Currently supports,
	 *
	 * 1. Profile Constraints - profile:foo
	 * 2. Record Constraints - record:foo
	 *
	 * @access public
	 * @param array $constraint The constraint array
	 * @return string
	 */
	public function clause_for_constraint( $constraint ) {
		$type     = $constraint['type'];
		$typeList = explode( ':', $type );
		$mainType = $typeList[0];

		switch ( $mainType ) {
			case 'record':
				return $this->clause_for_record_constraint( $constraint );

			case 'profile':
				$subType = $typeList[1];

				if ( $subType === 'likes' ) {
					return $this->clause_for_likes_constraint( $constraint );
				} else if ( $subType === 'favorites' ) {
					return $this->clause_for_favorites_constraint( $constraint );
				} else {
					return $this->clause_for_profile_constraint( $constraint );
				}

			case 'system':
				return $this->clause_for_system_constraint( $constraint );

			default:
				throw new \Exception( 'Unknown Constraint Type: ' . $mainType );
		}
	}

	/**
	 * Generates the GQL clause for a record constraint.
	 *
	 * @access public
	 * @param array $constraint The record constraint object
	 * @return string
	 */
	public function clause_for_record_constraint( $constraint ) {
		$type         = $constraint['type'];
		$value        = $constraint['value'];
		$valueType    = $constraint['valueType'];
		$operator     = $constraint['operator'];
		$entryTypeID  = $constraint['entryTypeID'];
		$entryFieldID = $constraint['entryFieldID'];
		$query        = '';

		$query .= $this->field_name_for( 'entryType' );
		$query .= ' ';
		$query .= $this->operator_for( '=' );
		$query .= ' ';
		$query .= $this->value_for( $type );

		$query .= ' and ';

		$query .= $this->field_name_for( 'entryTypeID', 'integer' );
		$query .= ' ';
		$query .= $this->operator_for( '=' );
		$query .= ' ';
		$query .= $this->value_for( $entryTypeID, 'integer' );

		$query .= ' and ';

		$query .= $this->field_name_for( 'entryFieldID', 'string' );
		$query .= ' ';
		$query .= $this->operator_for( '=' );
		$query .= ' ';
		$query .= $this->value_for( $entryFieldID, 'string' );

		$query .= ' and ';

		$query .= $this->field_name_for( 'entryValue', $valueType );
		$query .= ' ';
		$query .= $this->operator_for( $operator );
		$query .= ' ';
		$query .= $this->value_for( $value, $valueType );

		return $query;
	}

	/**
	 * Generates the GQL clause for a single constraint specified.
	 *
	 * @access public
	 * @param array $constraint Generates the GQL specified.
	 * @return string
	 */
	public function clause_for_profile_constraint( $constraint ) {
		$type      = $constraint['type'];
		$typeParts = explode( ':', $type );
		$value     = $constraint['value'];
		$valueType = $constraint['valueType'];
		$operator  = $constraint['operator'];
		$query     = '';

		$query .= 'profile.' . $typeParts[1];
		$query .= ' ';
		$query .= $this->operator_for( $operator );
		$query .= ' ';
		$query .= $this->value_for( $value, $valueType );

		return $query;
	}

	/**
	 * Generates the GQL clause for a likes constraint specified.
	 *
	 * @access public
	 * @param array $constraint Generates the GQL specified.
	 * @return string
	 */
	public function clause_for_likes_constraint( $constraint ) {
		$type      = $constraint['type'];
		$typeParts = explode( ':', $type );
		$value     = $constraint['value'];
		$valueType = $constraint['valueType'];
		$operator  = $constraint['operator'];
		$category  = $constraint['category'];
		$query     = '';

		if ( $category !== 'Any Category' ) {
			$query = "profile.likes.category = '{$category}' and ";
		}

		$query .= 'profile.likes.name';
		$query .= ' ';
		$query .= $this->operator_for( $operator );
		$query .= ' ';
		$query .= $this->value_for( $value, $valueType );

		return $query;
	}

	/**
	 * Generates the GQL clause for a single favorites constraint specified.
	 *
	 * @access public
	 * @param array $constraint Generates the GQL specified.
	 * @return string
	 */
	public function clause_for_favorites_constraint( $constraint ) {
		$type         = $constraint['type'];
		$typeParts    = explode( ':', $type );
		$value        = $constraint['value'];
		$valueType    = $constraint['valueType'];
		$operator     = $constraint['operator'];
		$favoriteType = $constraint['favoriteType'];
		$category     = $constraint['category'];
		$query        = '';

		if ( $category !== 'Any Category' ) {
			$query .= "profile.favorites.{$favoriteType}.category = '{$category}' and ";
		}

		$query .= "profile.favorites.{$favoriteType}.name";
		$query .= ' ';
		$query .= $this->operator_for( $operator );
		$query .= ' ';
		$query .= $this->value_for( $value, $valueType );

		return $query;
	}

	/**
	 * Generates the GQL clause for the system constraint specified.
	 *
	 * @access public
	 * @param array $constraint The system constraint to find the clause for.
	 * @return string
	 */
	public function clause_for_system_constraint( $constraint ) {
		$type      = $constraint['type'];
		$typeParts = explode( ':', $type );
		$value     = $constraint['value'];
		$valueType = $constraint['valueType'];
		$operator  = $constraint['operator'];
		$query     = '';

		$query .= $typeParts[1];
		$query .= ' ';
		$query .= $this->operator_for( $operator );
		$query .= ' ';
		$query .= $this->value_for( $value, $valueType );

		return $query;
	}

	/**
	 * Quotes values for insertion into GQL.
	 *
	 * @access public
	 * @param string $value The actual data value
	 * @param string $valueType The data type of the value
	 * @return mixed
	 */
	public function value_for( $value, $valueType = 'string' ) {
		if ( $valueType === 'string' || $valueType === 'text' ) {
			// TODO: Custom escaping
			return "'{$value}'";
		} elseif ( $valueType === 'boolean' ) {
			$value = (bool)$value;
			return $value ? 'true' : 'false';
		} elseif ( $valueType === 'date' ) {
			$date = \DateTime::createFromFormat(
				'm/d/Y', $value, new \DateTimeZone( 'UTC' )
			);

			return $date->getTimestamp();
		} else {
			return $value;
		}
	}

	/**
	 * Returns the Gigya operator from the operator label.
	 *
	 * Uses the same name as the operator if not found in map.
	 *
	 * @access public
	 * @param string $operatorName Label of the operator
	 * @return string
	 */
	public function operator_for( $operatorName ) {
		if ( array_key_exists( $operatorName, self::$operators ) ) {
			return self::$operators[ $operatorName ];
		} else {
			return $operatorName;
		}
	}

	/**
	 * Returns the suffixed name for a field based on it's valueType.
	 *
	 * @access public
	 * @param string $field The name of the field
	 * @param string $valueType The data type of the field
	 * @return string
	 */
	public function field_name_for( $field, $valueType = 'string' ) {
		return 'data.' . $this->storeName . '.' . $field . $this->suffix_for( $valueType );
	}

	/**
	 * Returns the gigya suffix for the specified valueType. This is
	 * used by Gigya to determine the data type of the field.
	 *
	 * Data Types supported are,
	 * 1. integer
	 * 2. float
	 * 3. string
	 * 4. boolean
	 * 5. text
	 * 6. date
	 *
	 * @access public
	 * @param string $valueType Data type name
	 * @return string
	 */
	public function suffix_for( $valueType ) {
		if ( array_key_exists( $valueType, self::$suffixes ) ) {
			return '_' . self::$suffixes[ $valueType ];
		} else {
			return '_s';
		}
	}

}
