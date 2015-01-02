var AVAILABLE_CONSTRAINTS = [


	/* System Fields */
	{
		type: 'system:createdTimestamp',
		valueType: 'date',
		value: '01/01/2012',
		operator: 'greater than',
	},
	{
		type: 'system:lastLoginTimestamp',
		valueType: 'date',
		value: '01/01/2014',
		operator: 'greater than',
	},
	{
		type: 'system:isActive',
		valueType: 'boolean',
		value: true,
	},
	{
		type: 'system:isRegistered',
		valueType: 'boolean',
		value: true,
	},
	{
		type: 'system:isVerified',
		valueType: 'boolean',
		value: true,
	},
	{
		type: 'data:optout',
		valueType: 'boolean',
		value: true,
	},
	{
		type: 'data:subscribedToList',
		valueType: 'enum',
		operator: 'contains',
	},

	/* Profile fields */
	{
		type: 'profile:gender',
		valueType: 'string',
		value: 'm'
	},
	{
		type: 'profile:age',
		valueType: 'integer',
		value: 25
	},
	{
		type: 'profile:birthDay',
		valueType: 'integer',
		value: 1,
	},
	{
		type: 'profile:birthMonth',
		valueType: 'integer',
		value: 1
	},
	{
		type: 'profile:birthYear',
		valueType: 'integer',
		value: 1990
	},
	{
		type: 'profile:state',
		valueType: 'string',
		value: 'New York'
	},
	{
		type: 'profile:city',
		valueType: 'string'
	},
	{
		type: 'profile:country',
		valueType: 'string',
		value: 'United States'
	},
	{
		type: 'profile:zip',
		valueType: 'string',
		value: '01001'
	},

	/*
	{
		type: 'profile:timezone',
		valueType: 'string',
		value: 'America/New_York',
	},
	*/

	// Facebook
	{
		type: 'profile:likes',
		valueType: 'string',
		category: 'Any Category',
		value: ''
	},
	{
		type: 'profile:favorites',
		valueType: 'string',
		category: 'Any Category',
		value: ''
	},
	{
		type: 'data:listeningFrequency',
		valueType: 'string',
		value: '0',
	},
	{
		type: 'data:listeningLoyalty',
		valueType: 'string',
		value: '0',
	},

	/* Contests */
	{
		type: 'record:contest',
		valueType: 'string',
		entryTypeID: -1,
		entryFieldID: -1
	},

	{
		type: 'data:comment_count',
		valueType: 'integer',
		value: 0,
	},
	{
		type: 'data:comment_status',
		valueType: 'boolean',
		value: true,
	},
	{
		type: 'action:comment_date',
		valueType: 'date',
		value: '01/01/2014',
		operator: 'greater than',
	},
];

/* Constraint Meta */

var AVAILABLE_CONSTRAINTS_META = [

	/* System Fields */
	{
		type: 'system:createdTimestamp',
		title: 'Creation Date',
	},
	{
		type: 'system:isActive',
		title: 'Active Status',
		choices: [
			{ label: 'Active', value: true },
			{ label: 'Inactive', value: false }
		]
	},
	{
		type: 'system:isRegistered',
		title: 'Registration Status',
		choices: [
			{ label: 'Registered', value: true },
			{ label: 'Not Registered', value: false }
		]
	},
	{
		type: 'system:lastLoginTimestamp',
		title: 'Last Logged In'
	},
	{
		type: 'system:isVerified',
		title: 'Verified Status',
		choices: [
			{ label: 'Verified', value: true },
			{ label: 'Not Verified', value: false },
		]
	},

	/* Profile Fields */
	{
		type: 'profile:likes',
		title: 'Facebook Likes'
	},
	{
		type: 'profile:favorites',
		title: 'Facebook Favorites'
	},
	{
		type: 'profile:birthYear',
		title: 'Birth Year'
	},
	{
		type: 'profile:country',
		title: 'Country',
		choices: [
			{ label: 'Afghanistan',                       value: 'Afghanistan'        } ,
			{ label: 'Albania',                           value: 'Albania'            } ,
			{ label: 'Algeria',                           value: 'Algeria'            } ,
			{ label: 'American Samoa',                    value: 'Samoa'              } ,
			{ label: 'Andorra',                           value: 'Andorra'            } ,
			{ label: 'Angola',                            value: 'Angola'             } ,
			{ label: 'Antigua and Barbuda',               value: 'Barbuda'            } ,
			{ label: 'Argentina',                         value: 'Argentina'          } ,
			{ label: 'Armenia',                           value: 'Armenia'            } ,
			{ label: 'Australia',                         value: 'Australia'          } ,
			{ label: 'Austria',                           value: 'Austria'            } ,
			{ label: 'Azerbaijan',                        value: 'Azerbaijan'         } ,
			{ label: 'Bahamas',                           value: 'Bahamas'            } ,
			{ label: 'Bahrain',                           value: 'Bahrain'            } ,
			{ label: 'Bangladesh',                        value: 'Bangladesh'         } ,
			{ label: 'Barbados',                          value: 'Barbados'           } ,
			{ label: 'Belarus',                           value: 'Belarus'            } ,
			{ label: 'Belgium',                           value: 'Belgium'            } ,
			{ label: 'Belize',                            value: 'Belize'             } ,
			{ label: 'Benin',                             value: 'Benin'              } ,
			{ label: 'Bermuda',                           value: 'Bermuda'            } ,
			{ label: 'Bhutan',                            value: 'Bhutan'             } ,
			{ label: 'Bolivia',                           value: 'Bolivia'            } ,
			{ label: 'Bosnia and Herzegovina',            value: 'Herzegovina'        } ,
			{ label: 'Botswana',                          value: 'Botswana'           } ,
			{ label: 'Brazil',                            value: 'Brazil'             } ,
			{ label: 'Brunei',                            value: 'Brunei'             } ,
			{ label: 'Bulgaria',                          value: 'Bulgaria'           } ,
			{ label: 'Burkina Faso',                      value: 'Faso'               } ,
			{ label: 'Burundi',                           value: 'Burundi'            } ,
			{ label: 'Cambodia',                          value: 'Cambodia'           } ,
			{ label: 'Cameroon',                          value: 'Cameroon'           } ,
			{ label: 'Canada',                            value: 'Canada'             } ,
			{ label: 'Cape Verde',                        value: 'Verde'              } ,
			{ label: 'Cayman Islands',                    value: 'Islands'            } ,
			{ label: 'Central African Republic',          value: 'Republic'           } ,
			{ label: 'Chad',                              value: 'Chad'               } ,
			{ label: 'Chile',                             value: 'Chile'              } ,
			{ label: 'China',                             value: 'China'              } ,
			{ label: 'Colombia',                          value: 'Colombia'           } ,
			{ label: 'Comoros',                           value: 'Comoros'            } ,
			{ label: 'Congo, Democratic Republic of the', value: 'the'                } ,
			{ label: 'Congo, Republic of the',            value: 'the'                } ,
			{ label: 'Costa Rica',                        value: 'Rica'               } ,
			{ label: 'Croatia',                           value: 'Croatia'            } ,
			{ label: 'Cuba',                              value: 'Cuba'               } ,
			{ label: 'Cyprus',                            value: 'Cyprus'             } ,
			{ label: 'Czech Republic',                    value: 'Republic'           } ,
			{ label: 'Denmark',                           value: 'Denmark'            } ,
			{ label: 'Djibouti',                          value: 'Djibouti'           } ,
			{ label: 'Dominica',                          value: 'Dominica'           } ,
			{ label: 'Dominican Republic',                value: 'Republic'           } ,
			{ label: 'East Timor',                        value: 'Timor'              } ,
			{ label: 'Ecuador',                           value: 'Ecuador'            } ,
			{ label: 'Egypt',                             value: 'Egypt'              } ,
			{ label: 'El Salvador',                       value: 'Salvador'           } ,
			{ label: 'Equatorial Guinea',                 value: 'Guinea'             } ,
			{ label: 'Eritrea',                           value: 'Eritrea'            } ,
			{ label: 'Estonia',                           value: 'Estonia'            } ,
			{ label: 'Ethiopia',                          value: 'Ethiopia'           } ,
			{ label: 'Fiji',                              value: 'Fiji'               } ,
			{ label: 'Finland',                           value: 'Finland'            } ,
			{ label: 'France',                            value: 'France'             } ,
			{ label: 'French Polynesia',                  value: 'Polynesia'          } ,
			{ label: 'Gabon',                             value: 'Gabon'              } ,
			{ label: 'Gambia',                            value: 'Gambia'             } ,
			{ label: 'Georgia',                           value: 'Georgia'            } ,
			{ label: 'Germany',                           value: 'Germany'            } ,
			{ label: 'Ghana',                             value: 'Ghana'              } ,
			{ label: 'Greece',                            value: 'Greece'             } ,
			{ label: 'Greenland',                         value: 'Greenland'          } ,
			{ label: 'Grenada',                           value: 'Grenada'            } ,
			{ label: 'Guam',                              value: 'Guam'               } ,
			{ label: 'Guatemala',                         value: 'Guatemala'          } ,
			{ label: 'Guinea',                            value: 'Guinea'             } ,
			{ label: 'Guinea-Bissau',                     value: 'Bissau'             } ,
			{ label: 'Guyana',                            value: 'Guyana'             } ,
			{ label: 'Haiti',                             value: 'Haiti'              } ,
			{ label: 'Honduras',                          value: 'Honduras'           } ,
			{ label: 'Hong Kong',                         value: 'Kong'               } ,
			{ label: 'Hungary',                           value: 'Hungary'            } ,
			{ label: 'Iceland',                           value: 'Iceland'            } ,
			{ label: 'India',                             value: 'India'              } ,
			{ label: 'Indonesia',                         value: 'Indonesia'          } ,
			{ label: 'Iran',                              value: 'Iran'               } ,
			{ label: 'Iraq',                              value: 'Iraq'               } ,
			{ label: 'Ireland',                           value: 'Ireland'            } ,
			{ label: 'Israel',                            value: 'Israel'             } ,
			{ label: 'Italy',                             value: 'Italy'              } ,
			{ label: 'Jamaica',                           value: 'Jamaica'            } ,
			{ label: 'Japan',                             value: 'Japan'              } ,
			{ label: 'Jordan',                            value: 'Jordan'             } ,
			{ label: 'Kazakhstan',                        value: 'Kazakhstan'         } ,
			{ label: 'Kenya',                             value: 'Kenya'              } ,
			{ label: 'Kiribati',                          value: 'Kiribati'           } ,
			{ label: 'North Korea',                       value: 'Korea'              } ,
			{ label: 'South Korea',                       value: 'Korea'              } ,
			{ label: 'Kosovo',                            value: 'Kosovo'             } ,
			{ label: 'Kuwait',                            value: 'Kuwait'             } ,
			{ label: 'Kyrgyzstan',                        value: 'Kyrgyzstan'         } ,
			{ label: 'Laos',                              value: 'Laos'               } ,
			{ label: 'Latvia',                            value: 'Latvia'             } ,
			{ label: 'Lebanon',                           value: 'Lebanon'            } ,
			{ label: 'Lesotho',                           value: 'Lesotho'            } ,
			{ label: 'Liberia',                           value: 'Liberia'            } ,
			{ label: 'Libya',                             value: 'Libya'              } ,
			{ label: 'Liechtenstein',                     value: 'Liechtenstein'      } ,
			{ label: 'Lithuania',                         value: 'Lithuania'          } ,
			{ label: 'Luxembourg',                        value: 'Luxembourg'         } ,
			{ label: 'Macedonia',                         value: 'Macedonia'          } ,
			{ label: 'Madagascar',                        value: 'Madagascar'         } ,
			{ label: 'Malawi',                            value: 'Malawi'             } ,
			{ label: 'Malaysia',                          value: 'Malaysia'           } ,
			{ label: 'Maldives',                          value: 'Maldives'           } ,
			{ label: 'Mali',                              value: 'Mali'               } ,
			{ label: 'Malta',                             value: 'Malta'              } ,
			{ label: 'Marshall Islands',                  value: 'Islands'            } ,
			{ label: 'Mauritania',                        value: 'Mauritania'         } ,
			{ label: 'Mauritius',                         value: 'Mauritius'          } ,
			{ label: 'Mexico',                            value: 'Mexico'             } ,
			{ label: 'Micronesia',                        value: 'Micronesia'         } ,
			{ label: 'Moldova',                           value: 'Moldova'            } ,
			{ label: 'Monaco',                            value: 'Monaco'             } ,
			{ label: 'Mongolia',                          value: 'Mongolia'           } ,
			{ label: 'Montenegro',                        value: 'Montenegro'         } ,
			{ label: 'Morocco',                           value: 'Morocco'            } ,
			{ label: 'Mozambique',                        value: 'Mozambique'         } ,
			{ label: 'Myanmar',                           value: 'Myanmar'            } ,
			{ label: 'Namibia',                           value: 'Namibia'            } ,
			{ label: 'Nauru',                             value: 'Nauru'              } ,
			{ label: 'Nepal',                             value: 'Nepal'              } ,
			{ label: 'Netherlands',                       value: 'Netherlands'        } ,
			{ label: 'New Zealand',                       value: 'Zealand'            } ,
			{ label: 'Nicaragua',                         value: 'Nicaragua'          } ,
			{ label: 'Niger',                             value: 'Niger'              } ,
			{ label: 'Nigeria',                           value: 'Nigeria'            } ,
			{ label: 'Norway',                            value: 'Norway'             } ,
			{ label: 'Northern Mariana Islands',          value: 'Islands'            } ,
			{ label: 'Oman',                              value: 'Oman'               } ,
			{ label: 'Pakistan',                          value: 'Pakistan'           } ,
			{ label: 'Palau',                             value: 'Palau'              } ,
			{ label: 'Palestine',                         value: 'Palestine'          } ,
			{ label: 'Panama',                            value: 'Panama'             } ,
			{ label: 'Papua New Guinea',                  value: 'Guinea'             } ,
			{ label: 'Paraguay',                          value: 'Paraguay'           } ,
			{ label: 'Peru',                              value: 'Peru'               } ,
			{ label: 'Philippines',                       value: 'Philippines'        } ,
			{ label: 'Poland',                            value: 'Poland'             } ,
			{ label: 'Portugal',                          value: 'Portugal'           } ,
			{ label: 'Puerto Rico',                       value: 'Rico'               } ,
			{ label: 'Qatar',                             value: 'Qatar'              } ,
			{ label: 'Romania',                           value: 'Romania'            } ,
			{ label: 'Russia',                            value: 'Russia'             } ,
			{ label: 'Rwanda',                            value: 'Rwanda'             } ,
			{ label: 'Saint Kitts and Nevis',             value: 'Nevis'              } ,
			{ label: 'Saint Lucia',                       value: 'Lucia'              } ,
			{ label: 'Saint Vincent and the Grenadines',  value: 'Grenadines'         } ,
			{ label: 'Samoa',                             value: 'Samoa'              } ,
			{ label: 'San Marino',                        value: 'Marino'             } ,
			{ label: 'Sao Tome and Principe',             value: 'Principe'           } ,
			{ label: 'Saudi Arabia',                      value: 'Arabia'             } ,
			{ label: 'Senegal',                           value: 'Senegal'            } ,
			{ label: 'Serbia and Montenegro',             value: 'Montenegro'         } ,
			{ label: 'Seychelles',                        value: 'Seychelles'         } ,
			{ label: 'Sierra Leone',                      value: 'Leone'              } ,
			{ label: 'Singapore',                         value: 'Singapore'          } ,
			{ label: 'Slovakia',                          value: 'Slovakia'           } ,
			{ label: 'Slovenia',                          value: 'Slovenia'           } ,
			{ label: 'Solomon Islands',                   value: 'Islands'            } ,
			{ label: 'Somalia',                           value: 'Somalia'            } ,
			{ label: 'South Africa',                      value: 'South Africa'       } ,
			{ label: 'Spain',                             value: 'Spain'              } ,
			{ label: 'Sri Lanka',                         value: 'Lanka'              } ,
			{ label: 'Sudan',                             value: 'Sudan'              } ,
			{ label: 'Sudan, South',                      value: 'South'              } ,
			{ label: 'Suriname',                          value: 'Suriname'           } ,
			{ label: 'Swaziland',                         value: 'Swaziland'          } ,
			{ label: 'Sweden',                            value: 'Sweden'             } ,
			{ label: 'Switzerland',                       value: 'Switzerland'        } ,
			{ label: 'Syria',                             value: 'Syria'              } ,
			{ label: 'Taiwan',                            value: 'Taiwan'             } ,
			{ label: 'Tajikistan',                        value: 'Tajikistan'         } ,
			{ label: 'Tanzania',                          value: 'Tanzania'           } ,
			{ label: 'Thailand',                          value: 'Thailand'           } ,
			{ label: 'Togo',                              value: 'Togo'               } ,
			{ label: 'Tonga',                             value: 'Tonga'              } ,
			{ label: 'Trinidad and Tobago',               value: 'Tobago'             } ,
			{ label: 'Tunisia',                           value: 'Tunisia'            } ,
			{ label: 'Turkey',                            value: 'Turkey'             } ,
			{ label: 'Turkmenistan',                      value: 'Turkmenistan'       } ,
			{ label: 'Tuvalu',                            value: 'Tuvalu'             } ,
			{ label: 'Uganda',                            value: 'Uganda'             } ,
			{ label: 'Ukraine',                           value: 'Ukraine'            } ,
			{ label: 'United Arab Emirates',              value: 'Emirates'           } ,
			{ label: 'United Kingdom',                    value: 'Kingdom'            } ,
			{ label: 'United States',                     value: 'United States'      } ,
			{ label: 'Uruguay',                           value: 'Uruguay'            } ,
			{ label: 'Uzbekistan',                        value: 'Uzbekistan'         } ,
			{ label: 'Vanuatu',                           value: 'Vanuatu'            } ,
			{ label: 'Vatican City',                      value: 'City'               } ,
			{ label: 'Venezuela',                         value: 'Venezuela'          } ,
			{ label: 'Vietnam',                           value: 'Vietnam'            } ,
			{ label: 'Virgin Islands, British',           value: 'British'            } ,
			{ label: 'Virgin Islands, U.S.',              value: 'Virgin Islands, US' } ,
			{ label: 'Yemen',                             value: 'Yemen'              } ,
			{ label: 'Zambia',                            value: 'Zambia'             } ,
			{ label: 'Zimbabwe',                          value: 'Zimbabwe'           } ,
		]
	},
	{
		type: 'profile:zip',
		title: 'Zip Code'
	},
	{
		type: 'profile:gender',
		title: 'Gender',
		choices: [
			{ label: 'Male', value: 'm' },
			{ label: 'Female', value: 'f' },
			{ label: 'Unknown', value: 'u' }
		]
	},
	{
		type: 'profile:age',
		title: 'Age',
	},
	{
		type: 'profile:birthDay',
		title: 'Birth Day'
	},
	{
		type: 'profile:birthMonth',
		title: 'Birth Month',
		choices: [
			{ label: 'January'   , value: 1  } ,
			{ label: 'February'  , value: 2  } ,
			{ label: 'March'     , value: 3  } ,
			{ label: 'April'     , value: 4  } ,
			{ label: 'May'       , value: 5  } ,
			{ label: 'June'      , value: 6  } ,
			{ label: 'July'      , value: 7  } ,
			{ label: 'August'    , value: 8  } ,
			{ label: 'September' , value: 9  } ,
			{ label: 'October'   , value: 10 } ,
			{ label: 'November'  , value: 11 } ,
			{ label: 'December'  , value: 12 } ,
		]
	},
	{
		type: 'profile:state',
		title: 'State',
		choices: [
			{ label: 'Alabama'               , value: 'AL' } ,
			{ label: 'Alaska'                , value: 'AK' } ,
			{ label: 'Arizona'               , value: 'AZ' } ,
			{ label: 'Arkansas'              , value: 'AR' } ,
			{ label: 'California'            , value: 'CA' } ,
			{ label: 'Colorado'              , value: 'CO' } ,
			{ label: 'Connecticut'           , value: 'CT' } ,
			{ label: 'Delaware'              , value: 'DE' } ,
			{ label: 'District of Columbia'  , value: 'DC' } ,
			{ label: 'Florida'               , value: 'FL' } ,
			{ label: 'Georgia'               , value: 'GA' } ,
			{ label: 'Hawaii'                , value: 'HI' } ,
			{ label: 'Idaho'                 , value: 'ID' } ,
			{ label: 'Illinois'              , value: 'IL' } ,
			{ label: 'Indiana'               , value: 'IN' } ,
			{ label: 'Iowa'                  , value: 'IA' } ,
			{ label: 'Kansas'                , value: 'KS' } ,
			{ label: 'Kentucky'              , value: 'KY' } ,
			{ label: 'Louisiana'             , value: 'LA' } ,
			{ label: 'Maine'                 , value: 'ME' } ,
			{ label: 'Maryland'              , value: 'MD' } ,
			{ label: 'Massachusetts'         , value: 'MA' } ,
			{ label: 'Michigan'              , value: 'MI' } ,
			{ label: 'Minnesota'             , value: 'MN' } ,
			{ label: 'Mississippi'           , value: 'MS' } ,
			{ label: 'Missouri'              , value: 'MO' } ,
			{ label: 'Montana'               , value: 'MT' } ,
			{ label: 'Nebraska'              , value: 'NE' } ,
			{ label: 'Nevada'                , value: 'NV' } ,
			{ label: 'New Hampshire'         , value: 'NH' } ,
			{ label: 'New Jersey'            , value: 'NJ' } ,
			{ label: 'New Mexico'            , value: 'NM' } ,
			{ label: 'New York'              , value: 'NY' } ,
			{ label: 'North Carolina'        , value: 'NC' } ,
			{ label: 'North Dakota'          , value: 'ND' } ,
			{ label: 'Ohio'                  , value: 'OH' } ,
			{ label: 'Oklahoma'              , value: 'OK' } ,
			{ label: 'Oregon'                , value: 'OR' } ,
			{ label: 'Pennsylvania'          , value: 'PA' } ,
			{ label: 'Rhode Island'          , value: 'RI' } ,
			{ label: 'South Carolina'        , value: 'SC' } ,
			{ label: 'South Dakota'          , value: 'SD' } ,
			{ label: 'Tennessee'             , value: 'TN' } ,
			{ label: 'Texas'                 , value: 'TX' } ,
			{ label: 'Utah'                  , value: 'UT' } ,
			{ label: 'Vermont'               , value: 'VT' } ,
			{ label: 'Virginia'              , value: 'VA' } ,
			{ label: 'Washington'            , value: 'WA' } ,
			{ label: 'West Virginia'         , value: 'WV' } ,
			{ label: 'Wisconsin'             , value: 'WI' } ,
			{ label: 'Wyoming'               , value: 'WY' } ,
			{ label: 'Armed Forces Americas' , value: 'AA' } ,
			{ label: 'Armed Forces Europe'   , value: 'AE' } ,
			{ label: 'Armed Forces Pacific'  , value: 'AP' } ,
		]
	},
	{
		type: 'profile:city',
		title: 'City'
	},
	{
		type: 'profile:timezone',
		title: 'Timezone'
	},

	/* Contests */
	{
		type: 'record:contest',
		title: 'Contest Entry'
	},

	{
		type: 'data:comment_count',
		title: 'Comment Count'
	},
	{
		type: 'data:comment_status',
		title: 'Comment Status',
		choices: [
			{ label: 'Has Commented', value: true },
			{ label: 'Has Not Commented', value: false }
		]
	},
	{
		type: 'action:comment_date',
		title: 'Comment Date'
	},
	{
		type: 'data:optout',
		title: 'Optout Status',
		choices: [
			{ label: 'Yes', value: true },
			{ label: 'No', value: false }
		]
	},
	{
		type: 'data:subscribedToList',
		title: 'Subscribed To List',
		choices: [
			{ label: 'VIP Newsletter', value: '2129171' },
			{ label: 'Birthday Greetings', value: '2131219' },
			{ label: "MMR's VIP Big Friggin' Deal", value: '2130195' },
		]
	},
	{
		type: 'data:listeningFrequency',
		title: 'Listening Frequency',
		choices: [
			{ label: 'Less than 1 hour', value: '0' },
			{ label: '1 to 3 hours', value: '1' },
			{ label: 'more than 3 hours', value: '2' }
		]
	},
	{
		type: 'data:listeningLoyalty',
		title: 'Listening Loyalty',
		choices: [
			{ label: '0%', value: '0' },
			{ label: '10%', value: '10' },
			{ label: '20%', value: '20' },
			{ label: '30%', value: '30' },
			{ label: '40%', value: '40' },
			{ label: '50%', value: '50' },
			{ label: '60%', value: '60' },
			{ label: '70%', value: '70' },
			{ label: '80%', value: '80' },
			{ label: '90%', value: '90' },
			{ label: '100%', value: '100' },
		]
	},
];

var AVAILABLE_CONSTRAINTS_META_MAP = {};

(function() {
	var integerChoicesFor = function(start, end) {
		var choices = [];
		var i;

		for (i = start; i <= end; i++) {
			choice = { label: i, value: i };
			choices.push(choice);
		}

		return choices;
	};

	var i;
	var constraints = AVAILABLE_CONSTRAINTS_META;
	var map         = AVAILABLE_CONSTRAINTS_META_MAP;
	var n           = constraints.length;

	for ( i = 0; i < n; i++ ) {
		constraint = constraints[i];
		map[constraint.type] = constraint;

		if (constraint.type === 'profile:birthDay') {
			constraint.choices = integerChoicesFor(1, 31);
		}
	}
})();

