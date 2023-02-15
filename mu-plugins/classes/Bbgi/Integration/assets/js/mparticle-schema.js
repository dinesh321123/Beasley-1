window.mParticleSchema = {
	"version_description": "",
	"version_document": {
		"data_points": [
			{
				"description": "Logging Page/Screen Views via logPageView() or Screen",
				"match": {
					"type": "screen_view",
					"criteria": {
						"screen_name": "Page View"
					}
				},
				"validator": {
					"type": "json_schema",
					"definition": {
						"properties": {
							"data": {
								"additionalProperties": true,
								"properties": {
									"custom_attributes": {
										"additionalProperties": false,
										"description": "",
										"properties": {
											"primary_category": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"primary_category_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"show_name": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"show_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"tags": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"content_type": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"enum": [
													"article",
													"listicle",
													"gallery",
													"null"
												]
											},
											"view_type": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"enum": [
													"primary",
													"embedded_content",
													"null"
												]
											},
											"embedded_content_title": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"embedded_content_type": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"embedded_content_path": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"embedded_content_post_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"embedded_content_wp_author": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"embedded_content_primary_author": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"embedded_content_secondary_author": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"daypart": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"post_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"wp_author": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"primary_author": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"secondary_author": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"ad_block_enabled": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"ad_tags_enabled": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"consent_cookie": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"event_day_of_the_week": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"event_hour_of_the_day": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"prebid_enabled": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"platform": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"publish_date": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "date"
											},
											"publish_day_of_the_week": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"publish_hour_of_the_day": {
												"description": "",
												"type": [
													"number",
													"null"
												]
											},
											"publish_month": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"publish_time_of_day": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "time"
											},
											"publish_timestamp_local": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "date-time"
											},
											"publish_timestamp_UTC": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "date-time"
											},
											"publish_year": {
												"description": "",
												"type": [
													"number",
													"null"
												]
											},
											"section_name": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"video_count": {
												"description": "",
												"type": [
													"number",
													"null"
												]
											},
											"word_count": {
												"description": "",
												"type": [
													"number",
													"null"
												]
											},
											"categories_stringified": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"tags_stringified": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"referrer": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"UTM": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"title": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"domain": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "hostname"
											}
										},
										"required": [],
										"type": "object"
									}
								},
								"required": [],
								"type": "object"
							}
						}
					}
				}
			},
			{
				"description": "user clicks on any lnk",
				"match": {
					"type": "custom_event",
					"criteria": {
						"event_name": "Link Clicked",
						"custom_event_type": "other"
					}
				},
				"validator": {
					"type": "json_schema",
					"definition": {
						"properties": {
							"data": {
								"additionalProperties": true,
								"properties": {
									"custom_attributes": {
										"additionalProperties": false,
										"description": "",
										"properties": {
											"container_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"link_name": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"link_text": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"link_url": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "uri"
											},
											"link_type": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"module_type": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"module_name": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"module_position": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"module_element_num": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"screen_position": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"title": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"domain": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "hostname"
											}
										},
										"required": [],
										"type": "object"
									}
								},
								"required": [],
								"type": "object"
							}
						}
					}
				}
			},
			{
				"description": "",
				"match": {
					"type": "custom_event",
					"criteria": {
						"event_name": "Searched For",
						"custom_event_type": "other"
					}
				},
				"validator": {
					"type": "json_schema",
					"definition": {
						"properties": {
							"data": {
								"additionalProperties": true,
								"properties": {
									"custom_attributes": {
										"additionalProperties": false,
										"description": "",
										"properties": {
											"search_term": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"search_num_results": {
												"description": "",
												"type": [
													"number",
													"null"
												]
											},
											"page_url": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "uri"
											},
											"call_sign": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"call_sign_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"title": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"domain": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "hostname"
											}
										},
										"required": [],
										"type": "object"
									}
								},
								"required": [],
								"type": "object"
							}
						}
					}
				}
			},
			{
				"description": "",
				"match": {
					"type": "custom_event",
					"criteria": {
						"event_name": "Searched Result Clicked",
						"custom_event_type": "other"
					}
				},
				"validator": {
					"type": "json_schema",
					"definition": {
						"properties": {
							"data": {
								"additionalProperties": true,
								"properties": {
									"custom_attributes": {
										"additionalProperties": false,
										"description": "",
										"properties": {
											"search_term": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"search_term_selected": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"search_term_position": {
												"description": "",
												"type": [
													"number",
													"null"
												]
											},
											"domain": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "hostname"
											},
											"page_url": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "uri"
											},
											"call_sign": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											}
										},
										"required": [],
										"type": "object"
									}
								},
								"required": [],
								"type": "object"
							}
						}
					}
				}
			},
			{
				"description": "",
				"match": {
					"type": "custom_event",
					"criteria": {
						"event_name": "Form Submitted",
						"custom_event_type": "other"
					}
				},
				"validator": {
					"type": "json_schema",
					"definition": {
						"properties": {
							"data": {
								"additionalProperties": true,
								"properties": {
									"custom_attributes": {
										"additionalProperties": false,
										"description": "",
										"properties": {
											"domain": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "hostname"
											},
											"page_url": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "uri"
											},
											"call_sign": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"container_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"module_type": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"module_name": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"module_position": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"module_screen_position": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"form_type": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"enum": [
													"newsletter_signup",
													"contest_entry",
													"null"
												]
											},
											"form_name": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"form_position": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"form_destination": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											}
										},
										"required": [],
										"type": "object"
									}
								},
								"required": [],
								"type": "object"
							}
						}
					}
				}
			},
			{
				"description": "",
				"match": {
					"type": "custom_event",
					"criteria": {
						"event_name": "MediaSessionCreate",
						"custom_event_type": "media"
					}
				},
				"validator": {
					"type": "json_schema",
					"definition": {
						"properties": {
							"data": {
								"additionalProperties": true,
								"properties": {
									"custom_attributes": {
										"additionalProperties": false,
										"description": "",
										"properties": {
											"sdk": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"listener_daypart": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"content_asset_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"domain": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "hostname"
											}
										},
										"required": [],
										"type": "object"
									}
								},
								"required": [],
								"type": "object"
							}
						}
					}
				}
			},
			{
				"description": "",
				"match": {
					"type": "custom_event",
					"criteria": {
						"event_name": "MediaSessionStart",
						"custom_event_type": "media"
					}
				},
				"validator": {
					"type": "json_schema",
					"definition": {
						"properties": {
							"data": {
								"additionalProperties": true,
								"properties": {
									"custom_attributes": {
										"additionalProperties": false,
										"description": "",
										"properties": {
											"content_asset_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"domain": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "hostname"
											}
										},
										"required": [],
										"type": "object"
									}
								},
								"required": [],
								"type": "object"
							}
						}
					}
				}
			},
			{
				"description": "",
				"match": {
					"type": "custom_event",
					"criteria": {
						"event_name": "Play",
						"custom_event_type": "media"
					}
				},
				"validator": {
					"type": "json_schema",
					"definition": {
						"properties": {
							"data": {
								"additionalProperties": true,
								"properties": {
									"custom_attributes": {
										"additionalProperties": false,
										"description": "",
										"properties": {
											"currentPlayheadPosition": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"percentage_viewed": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"live_time_viewed": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"content_asset_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"domain": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "hostname"
											}
										},
										"required": [],
										"type": "object"
									}
								},
								"required": [],
								"type": "object"
							}
						}
					}
				}
			},
			{
				"description": "",
				"match": {
					"type": "custom_event",
					"criteria": {
						"event_name": "Pause",
						"custom_event_type": "media"
					}
				},
				"validator": {
					"type": "json_schema",
					"definition": {
						"properties": {
							"data": {
								"additionalProperties": true,
								"properties": {
									"custom_attributes": {
										"additionalProperties": false,
										"description": "",
										"properties": {
											"currentPlayheadPosition": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"content_asset_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"domain": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "hostname"
											}
										},
										"required": [],
										"type": "object"
									}
								},
								"required": [],
								"type": "object"
							}
						}
					}
				}
			},
			{
				"description": "",
				"match": {
					"type": "custom_event",
					"criteria": {
						"event_name": "MediaContentEnd",
						"custom_event_type": "media"
					}
				},
				"validator": {
					"type": "json_schema",
					"definition": {
						"properties": {
							"data": {
								"additionalProperties": true,
								"properties": {
									"custom_attributes": {
										"additionalProperties": false,
										"description": "",
										"properties": {
											"currentPlayheadPosition": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"content_asset_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"domain": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "hostname"
											}
										},
										"required": [],
										"type": "object"
									}
								},
								"required": [],
								"type": "object"
							}
						}
					}
				}
			},
			{
				"description": "",
				"match": {
					"type": "custom_event",
					"criteria": {
						"event_name": "MediaSessionEnd",
						"custom_event_type": "media"
					}
				},
				"validator": {
					"type": "json_schema",
					"definition": {
						"properties": {
							"data": {
								"additionalProperties": true,
								"properties": {
									"custom_attributes": {
										"additionalProperties": false,
										"description": "",
										"properties": {
											"currentPlayheadPosition": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"content_asset_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"domain": {
												"description": "",
												"type": [
													"string",
													"null"
												],
												"format": "hostname"
											}
										},
										"required": [],
										"type": "object"
									}
								},
								"required": [],
								"type": "object"
							}
						}
					}
				}
			},
			{
				"description": "User Identities",
				"match": {
					"type": "user_identities",
					"criteria": {}
				},
				"validator": {
					"type": "json_schema",
					"definition": {
						"additionalProperties": true,
						"properties": {
							"customerid": {
								"description": "Known identity ",
								"type": "string",
								"pattern": "^[a-zA-Z0-9_]*$"
							},
							"email": {
								"description": "Known identity ",
								"type": "string",
								"format": "email"
							},
							"ios_idfv": {
								"description": "Anonymous identity",
								"type": "string"
							},
							"ios_idfa": {
								"description": "Anonymous identity",
								"type": "string"
							},
							"android_aaid": {
								"description": "Anonymous identity",
								"type": "string"
							},
							"android_uuid": {
								"description": "Anonymous identity",
								"type": "string"
							},
							"device_application_stamp": {
								"description": "Anonymous identity",
								"type": "string"
							}
						},
						"required": [],
						"type": "object"
					}
				}
			},
			{
				"description": "User Attributes",
				"match": {
					"type": "user_attributes",
					"criteria": {}
				},
				"validator": {
					"type": "json_schema",
					"definition": {
						"additionalProperties": true,
						"properties": {
							"my string attribute (enum validation)": {
								"description": "An example string attribute using enum validation.",
								"type": "string",
								"enum": [
									"two seater",
									"three seater",
									"sectional"
								]
							},
							"my string attribute (regex validation)": {
								"description": "An example string attribute using regex validation.",
								"type": "string",
								"pattern": "^[a-zA-Z0-9_]*$"
							},
							"my numeric attribute": {
								"description": "An example numeric attribute using range validation.",
								"type": "number",
								"minimum": 0,
								"maximum": 100
							},
							"my boolean attribute": {
								"description": "An example boolean attribute.",
								"type": "boolean"
							}
						},
						"required": [],
						"type": "object"
					}
				}
			}
		]
	}
};
