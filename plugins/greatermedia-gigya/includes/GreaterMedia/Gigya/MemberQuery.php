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
		'list'    => 'list',
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
		'in'                       => 'in',
		'not in'                   => 'not in',
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
	public $storeName = 'actions';

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
				error_log( 'Failed to parse constraints: ' . $content );
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
		$this->save( $json );
	}

	public function save( $json ) {
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
	 * TODO: DEPRECATE
	 *
	 * @access public
	 * @param bool $count Optionally Whether to return an aggregate query
	 * @param int $limit Optional row limit to apply to the query
	 * @return string
	 */
	public function to_gql( $count = false, $limit = null ) {
		$constraints = $this->get_constraints();
		if ( count( $constraints ) === 0 ) {
			return '';
		}

		$query       = 'select * from accounts where ';
		$query .= $this->clause_for( $constraints );

		if ( $count ) {
			$query = str_replace( '*', 'count(*)', $query );
		}

		if ( is_int( $limit ) ) {
			$query .= " limit $limit";
		}

		return $query;
	}

	/**
	 * Converts the current constraints into an associative array of
	 * subqueries.
	 *
	 * @return array Associative array of subqueries keyed on store_type
	 */
	public function to_subqueries() {
		$groups = $this->group_constraints( $this->get_constraints() );
		$subqueries = array();

		foreach ( $groups as $store_type => $constraints ) {
			$subquery = $this->constraints_to_query( $constraints, $store_type );
			$subqueries[] = array(
				'store_type' => $store_type,
				'query'      => $subquery,
			);
		}

		return $subqueries;
	}

	public function get_subquery_conjunction() {
		$groups = $this->group_constraints( $this->get_constraints() );

		if ( count( $groups ) <= 1 ) {
			return 'any';
		}

		if ( array_key_exists( 'profile', $groups ) ) {
			$total      = count( $groups['profile'] );
			$constraint = $groups['profile'][ $total - 1 ];

			return $constraint['conjunction'];
		} else {
			return 'or';
		}
	}

	/**
	 * Converts an array of constraints into an array grouped by their
	 * corresponding store_type.
	 *
	 * @param $constraints Array of constraints to classify
	 * @return array Associative array on store_type
	 */
	public function group_constraints( $constraints ) {
		$groups = array();

		foreach ( $constraints as $constraint ) {
			$store_type            = $this->get_constraint_store_type( $constraint );
			if ( ! array_key_exists( $store_type, $groups ) ) {
				$groups[ $store_type ] = array();
			}

			$groups[ $store_type ][] = $constraint;
		}

		return $groups;
	}

	/**
	 * Returns the storage type for the specified constraint.
	 *
	 * Currently valid store types are, 'profile' or 'data_store'.
	 *
	 * @access public
	 * @param $constraint The constraint object
	 * @return string
	 */
	public function get_constraint_store_type( $constraint ) {
		$type     = $constraint['type'];
		$typeList = explode( ':', $type );
		$mainType = $typeList[0];

		switch ( $mainType ) {
			case 'system':
				return 'profile';

			case 'profile':
			case 'data':
				return 'profile';

			case 'record':
			case 'action':
				return 'data_store';

			default:
				throw new \Exception( "Unknown constraint type: {$mainType}" );
		}
	}

	/**
	 * Converts specified constraints to a GQL query.
	 *
	 * @param array $constraints The list of constraints to serialize to GQL.
	 * @param string $type The store type name
	 * @param bool $count Optionally Whether to return an aggregate query
	 * @param int $limit Optional row limit to apply to the query
	 */
	public function constraints_to_query( $constraints, $store_type, $count = false, $limit = null ) {
		if ( count( $constraints ) === 0 ) {
			return '';
		}

		$store_name = $this->store_name_for_type( $store_type );
		$query      = "select * from {$store_name} where ";

		$query .= $this->clause_for( $constraints );

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
		$query = '';
		$total = count( $constraints );

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

			case 'action':
				$subType = $typeList[1];

				if ( $subType === 'comment_date' ) {
					return $this->clause_for_comment_date_constraint( $constraint );
				} else if ( $subType === 'social_share' ) {
					return $this->clause_for_social_share_constraint( $constraint );
				} else {
					return $this->clause_for_action_constraint( $constraint );
				}

			case 'profile':
				$subType = $typeList[1];

				if ( $subType === 'likes' ) {
					return $this->clause_for_likes_constraint( $constraint );
				} else if ( $subType === 'favorites' ) {
					return $this->clause_for_favorites_constraint( $constraint );
				} else if ( $subType === 'zip' ) {
					return $this->clause_for_zip_code_constraint( $constraint );
				} else {
					return $this->clause_for_profile_constraint( $constraint );
				}

			case 'data':
				$subType = $typeList[1];

				if ( preg_match( '/_status$/', $subType ) === 1 ) {
					return $this->clause_for_status_constraint( $constraint );
				} else if ( preg_match( '/_list/', $subType ) === 1 ) {
					return $this->clause_for_list_constraint( $constraint );
				} else if ( $subType === 'optout' ) {
					return $this->clause_for_optout_constraint( $constraint );
				} else if ( $subType === 'subscribedToList' ) {
					return $this->clause_for_subscribed_to_list_constraint( $constraint );
				} else if ( $subType === 'email_engagement_tally' ) {
					return $this->clause_for_email_engagement_tally_constraint( $constraint );
				} else if ( $subType === 'email_engagement' ) {
					return $this->clause_for_email_engagement_constraint( $constraint );
				} else {
					return $this->clause_for_data_constraint( $constraint );
				}

			case 'system':
				return $this->clause_for_system_constraint( $constraint );

			default:
				throw new \Exception( 'Unknown Constraint Type: ' . $mainType );
		}
	}

	/**
	 * Generates the GQL clause for an action constraint.
	 *
	 * @access public
	 * @param array $constraint The record constraint object
	 * @return string
	 */
	public function clause_for_action_constraint( $constraint ) {
		$type          = $constraint['type'];
		$value         = $constraint['value'];
		$valueType     = $constraint['valueType'];
		$operator      = $constraint['operator'];
		$actionTypeID  = $constraint['actionTypeID'];
		$actionFieldID = $constraint['actionFieldID'];
		$query         = '';

		$query .= $this->data_store_field_name_for( 'actionType' );
		$query .= ' ';
		$query .= $this->operator_for( '=' );
		$query .= ' ';
		$query .= $this->value_for( $type );

		$query .= ' and ';

		$query .= $this->data_store_field_name_for( 'actionTypeID', 'integer' );
		$query .= ' ';
		$query .= $this->operator_for( '=' );
		$query .= ' ';
		$query .= $this->value_for( $actionTypeID, 'integer' );

		$query .= ' and ';

		$query .= $this->data_store_field_name_for( 'actionFieldID', 'string' );
		$query .= ' ';
		$query .= $this->operator_for( '=' );
		$query .= ' ';
		$query .= $this->value_for( $actionFieldID, 'string' );

		$query .= ' and ';

		error_log( 'valueType: $valueType' );
		$query .= $this->data_store_field_name_for( 'actionValue', $valueType );
		$query .= ' ';
		$query .= $this->operator_for( $operator );
		$query .= ' ';
		$query .= $this->value_for( $value, $valueType );

		return $query;
	}

	/**
	 * Generates the GQL clause for a record constraint.
	 *
	 * TODO: DEPRECATE
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

		$query .= $this->field_name_for( 'actionType', 'none' );
		$query .= ' ';
		$query .= $this->operator_for( '=' );
		$query .= ' ';
		$query .= $this->value_for( $this->get_action_type_name( $type ) );

		$query .= ' and ';

		$query .= $this->field_name_for( 'actionID', 'none' );
		$query .= ' ';
		$query .= $this->operator_for( '=' );
		$query .= ' ';
		$query .= $this->value_for( $entryTypeID, 'string' );

		$query .= ' and ';

		$query .= $this->field_name_for( 'actionData.name', 'none' );
		$query .= ' ';
		$query .= $this->operator_for( '=' );
		$query .= ' ';
		$query .= $this->value_for( $entryFieldID, 'string' );

		$query .= ' and ';

		/*
		if ( $operator === 'contains' ) {
			$operator = 'equals';
		} else if ( $operator === 'not contains' ) {
			$operator = 'not equals';
		}
		 */

		if ( $valueType === 'string' ) {
			$valueType = 'text';
		}

		$query .= $this->field_name_for( 'actionData.value', $valueType );
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

	public function clause_for_zip_code_constraint( $constraint ) {
		$type      = $constraint['type'];
		$typeParts = explode( ':', $type );
		$value     = $constraint['value'];
		$valueType = $constraint['valueType'];
		$operator  = $constraint['operator'];
		$query     = '';

		if ( $operator === 'equals' ) {
			$operator = 'in';
		} else if ( $operator === 'not equals' ) {
			$operator = 'not in';
		}

		$query .= 'profile.' . $typeParts[1];
		$query .= ' ';
		$query .= $this->operator_for( $operator );
		$query .= ' ';
		$query .= $this->get_zip_code_list( $value );

		return $query;
	}

	function get_zip_code_list( $input ) {
		$input = trim( $input );
		$input = trim( $input, ',' );

		$zip_codes = explode( ',', $input );
		$formatted = array();

		foreach ( $zip_codes as $zip_code ) {
			$zip_code = trim( $zip_code );

			if ( ! empty( $zip_code ) ) {
				$zip_code = "'{$zip_code}'";
				$formatted[] = $zip_code;
			}
		}

		$zip_codes_list = implode( ', ', $formatted );

		return "({$zip_codes_list})";
	}


	public function clause_for_data_constraint( $constraint ) {
		$type      = $constraint['type'];
		$typeParts = explode( ':', $type );
		$value     = $constraint['value'];
		$valueType = $constraint['valueType'];
		$operator  = $constraint['operator'];
		$query     = '';

		$query .= 'data.' . $typeParts[1];
		$query .= ' ';
		$query .= $this->operator_for( $operator );
		$query .= ' ';
		$query .= $this->value_for( $value, $valueType );

		return $query;
	}

	public function clause_for_status_constraint( $constraint ) {
		$type          = $constraint['type'];
		$typeParts     = explode( ':', $type );
		$value         = $constraint['value'];
		$valueType     = $constraint['valueType'];
		$operator      = $constraint['operator'];
		$dependent_type = str_replace( '_status', '_count', $typeParts[1] );

		if ( $operator === 'equals' && $value ) {
			$query = "data.{$dependent_type} > 0";
		} else {
			$query = "data.{$dependent_type} = 0 or data.{$dependent_type} is null";
		}

		return $query;
	}


	public function clause_for_comment_status_constraint( $constraint ) {
		$type      = $constraint['type'];
		$typeParts = explode( ':', $type );
		$value     = $constraint['value'];
		$valueType = $constraint['valueType'];
		$operator  = $constraint['operator'];

		if ( $operator === 'equals' && $value ) {
			$query = 'data.comment_count > 0';
		} else {
			$query = 'data.comment_count = 0 or data.comment_count is null';
		}

		return $query;
	}

	public function clause_for_social_share_status_constraint( $constraint ) {
		$type      = $constraint['type'];
		$typeParts = explode( ':', $type );
		$value     = $constraint['value'];
		$valueType = $constraint['valueType'];
		$operator  = $constraint['operator'];

		if ( $operator === 'equals' && $value ) {
			$query = 'data.social_share_count > 0';
		} else {
			$query = 'data.social_share_count = 0 or data.social_share_count is null';
		}

		return $query;
	}

	public function clause_for_optout_constraint( $constraint ) {
		$type      = $constraint['type'];
		$typeParts = explode( ':', $type );
		$value     = $constraint['value'];
		$valueType = $constraint['valueType'];
		$operator  = $constraint['operator'];

		if ( $operator === 'equals' && $value ) {
			$query = 'data.optout = true';
		} else {
			$query = 'data.optout != true or data.optout is null';
		}

		return $query;
	}

	public function clause_for_list_constraint( $constraint ) {
		$type      = $constraint['type'];
		$typeParts = explode( ':', $type );
		$value     = $constraint['value'];
		$valueType = $constraint['valueType'];
		$operator  = $constraint['operator'];

		if ( empty( $operator ) ) {
			$operator = 'contains';
		}

		$query     = '';

		$query .= 'data.' . $typeParts[1];
		$query .= ' ';
		$query .= $this->operator_for( $operator );
		$query .= ' ';
		$query .= $this->value_for( $value, 'string' );

		return $query;
	}

	public function clause_for_subscribed_to_list_constraint( $constraint ) {
		$type      = $constraint['type'];
		$typeParts = explode( ':', $type );
		$value     = $constraint['value'];
		$valueType = $constraint['valueType'];
		$operator  = $constraint['operator'];
		$query     = '';

		$query .= 'data.' . $typeParts[1];
		$query .= ' ';
		$query .= $this->operator_for( $operator );
		$query .= ' ';
		$query .= $this->value_for( $value, 'string' );

		return $query;
	}

	public function clause_for_comment_date_constraint( $constraint ) {
		$type          = $constraint['type'];
		$value         = $constraint['value'];
		$valueType     = $constraint['valueType'];
		$operator      = $constraint['operator'];
		$query         = '';

		$query .= $this->data_store_field_name_for( 'actionType', 'none' );
		$query .= ' ';
		$query .= $this->operator_for( '=' );
		$query .= ' ';
		$query .= $this->value_for( 'action:comment' );

		$query .= ' and ';

		$query .= $this->data_store_field_name_for( 'actionData.name', 'none' );
		$query .= ' ';
		$query .= $this->operator_for( 'equals' );
		$query .= ' ';
		$query .= $this->value_for( 'timestamp', 'string' );

		$query .= ' and ';

		$query .= $this->data_store_field_name_for( 'actionData.value', 'integer' );
		$query .= ' ';
		$query .= $this->operator_for( $operator );
		$query .= ' ';
		$query .= $this->value_for( $value, 'epoch' );

		return $query;
	}

	public function clause_for_social_share_constraint( $constraint ) {
		$type      = $constraint['type'];
		$value     = $constraint['value'];
		$valueType = $constraint['valueType'];
		$operator  = $constraint['operator'];
		$query     = '';

		$query .= $this->data_store_field_name_for( 'actionType', 'none' );
		$query .= ' ';
		$query .= $this->operator_for( '=' );
		$query .= ' ';
		$query .= $this->value_for( 'action:social_share' );

		$query .= ' and ';

		$query .= $this->data_store_field_name_for( 'actionData.name', 'none' );
		$query .= ' ';
		$query .= $this->operator_for( 'equals' );
		$query .= ' ';
		$query .= $this->value_for( 'url', 'string' );

		$query .= ' and ';

		$query .= $this->data_store_field_name_for( 'actionData.value', 'string' );
		$query .= ' ';
		$query .= $this->operator_for( $operator );
		$query .= ' ';
		$query .= $this->value_for( $value, 'string' );

		return $query;
	}

	public function clause_for_email_engagement_tally_constraint( $constraint ) {
		$type       = $constraint['type'];
		$value      = $constraint['value'];
		$valueType  = $constraint['valueType'];
		$operator   = $constraint['operator'];
		$event_name = $constraint['event_name'];
		$query      = '';

		if ( $value !== 0 ) {
			$query .= "data.email_{$event_name}_count";
			$query .= ' ';
			$query .= $this->operator_for( $operator );
			$query .= ' ';
			$query .= $this->value_for( $value, 'integer' );
		} else {
			if ( $operator === 'equals' ) {
				$query .= "data.email_{$event_name}_count is null";
			} else {
				$query .= "data.email_{$event_name}_count != 0";
			}
		}

		return $query;
	}

	public function clause_for_email_engagement_constraint( $constraint ) {
		$type      = $constraint['type'];
		$value     = $constraint['value'];
		$valueType = $constraint['valueType'];
		$operator  = $constraint['operator'];
		$event_name = $constraint['event_name'];
		$query     = '';

		if ( $value !== 'any' ) {
			$query .= "data.email_{$event_name}_list";
			$query .= ' ';
			$query .= $this->operator_for( $operator );
			$query .= ' ';
			$query .= $this->value_for( $value, 'string' );
		} else {
			if ( $operator === 'contains' ) {
				$query .= "data.email_{$event_name}_count > 0";
			} else {
				$query .= "data.email_{$event_name}_count is null";
			}
		}

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
			$query = "profile.likes.category contains '{$category}' and ";
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
			// TODO: Figure out why categories don't match exactly
			$query .= "profile.favorites.{$favoriteType}.category contains '{$category}' and ";
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

			return $date->getTimestamp() * 1000;
		} elseif ( $valueType === 'epoch' ) {
			$date = \DateTime::createFromFormat(
				'm/d/Y', $value, new \DateTimeZone( 'UTC' )
			);

			return $date->getTimestamp();
		} elseif ( $valueType === 'list' ) {
			// This should escape surrounding double quotes in the full SQL query as well as encapsulate the $value in double quotes
			// In theory we should get something like \"Who Says You Can't Go Home - Bon Jovi\"
			// A minimalized query would look something like.
			/*
			SELECT *
			FROM actions
			WHERE data.actions.actionType = 'action:survey'
			AND data.actions.actionID = '2306'
			AND data.actions.actionData.name = 'c13766'
			AND data.actions.actionData.value_list
			CONTAINS \"Who Says You Can't Go Home - BON JOVI\"
			ORDER BY UID START 0 LIMIT 5
			*/
			return "\\" . "\"{$value}\"" . "\\";
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
	 * Returns the suffixed name for a field based on it's valueType.
	 *
	 * @access public
	 * @param string $field The name of the field
	 * @param string $valueType The data type of the field
	 * @return string
	 */
	public function data_store_field_name_for( $field, $valueType = 'string' ) {
		return 'data.actions.' . $field . $this->suffix_for( $valueType );
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
		if ( $valueType === 'none' ) {
			return '';
		} else if ( array_key_exists( $valueType, self::$suffixes ) ) {
			return '_' . self::$suffixes[ $valueType ];
		} else {
			return '_s';
		}
	}

	/**
	 * Returns the storage name for corresponding store type.
	 *
	 * Valid store types are 'profile' or 'data_store'.
	 *
	 * @param $store_type The type name of the store
	 * @return string The storage table name
	 */
	public function store_name_for_type( $store_type ) {
		if ( $store_type === 'profile' ) {
			return 'accounts';
		} else if ( $store_type === 'data_store' ) {
			return 'actions';
		} else {
			throw new \Exception( "Unknown Gigya storage type name: {$type}" );
		}
	}

	public function get_action_type_name( $old_type_name ) {
		switch ( $old_type_name ) {
			case 'record:contest':
				return 'action:contest';

			case 'record:survey':
				return 'action:survey';

			default:
				return $old_type_name;
		}
	}

}
