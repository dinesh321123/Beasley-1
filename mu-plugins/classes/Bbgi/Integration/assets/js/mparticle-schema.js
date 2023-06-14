window.mParticleSchema = {
	"version": 1,
	"data_plan_id": "beasley_web",
	"version_description": "",
	"activated_environment": "development",
	"created_on": "2023-05-11T17:09:53.597",
	"created_by": "mike.persico@bbgi.com",
	"last_modified_on": "2023-06-14T14:55:23.073",
	"last_modified_by": "mike.persico@bbgi.com",
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
											"content_type": {
												"description": "",
												"type": [
													"string",
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
											"embedded_content_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"embedded_content_is_nested": {
												"description": "",
												"type": [
													"boolean",
													"null"
												]
											},
											"embedded_content_item_title": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"embedded_content_item_type": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"embedded_content_item_path": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"embedded_content_item_post_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"embedded_content_item_wp_author": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"embedded_content_item_primary_author": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"embedded_content_item_secondary_author": {
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
											"title": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"page_url": {
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
											},
											"is_app": {
												"description": "",
												"type": [
													"boolean",
													"null"
												]
											},
											"station_formats": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_location": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"call_letters": {
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
											"beasley_event_id": {
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
											"page_url": {
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
											},
											"is_app": {
												"description": "",
												"type": [
													"boolean",
													"null"
												]
											},
											"station_formats": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_location": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"call_letters": {
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
											"beasley_event_id": {
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
											"title": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"page_url": {
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
											},
											"is_app": {
												"description": "",
												"type": [
													"boolean",
													"null"
												]
											},
											"station_formats": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_location": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"call_letters": {
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
											"beasley_event_id": {
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
											"title": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"page_url": {
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
											},
											"is_app": {
												"description": "",
												"type": [
													"boolean",
													"null"
												]
											},
											"station_formats": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_location": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"call_letters": {
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
											"beasley_event_id": {
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
											},
											"title": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"page_url": {
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
											},
											"is_app": {
												"description": "",
												"type": [
													"boolean",
													"null"
												]
											},
											"station_formats": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_location": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"call_letters": {
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
											"beasley_event_id": {
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
						"event_name": "Shared",
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
											"from_page_url": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"content_name": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"shared_to_service": {
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
											"page_url": {
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
											},
											"is_app": {
												"description": "",
												"type": [
													"boolean",
													"null"
												]
											},
											"station_formats": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_location": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"call_letters": {
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
											"beasley_event_id": {
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
						"event_name": "Downloaded Podcast",
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
											"podcast_name": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"episode_title": {
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
											"page_url": {
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
											},
											"is_app": {
												"description": "",
												"type": [
													"boolean",
													"null"
												]
											},
											"station_formats": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_location": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"call_letters": {
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
											"beasley_event_id": {
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
						"event_name": "Media Session Start",
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
											"content_title": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_duration": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_id": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_type": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"stream_type": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"media_session_id": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_asset_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"content_network": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"primary_category": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"primary_category_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"show_name": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"show_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"content_daypart": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"stream_call_letters": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"is_primary_stream": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"boolean",
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
											"is_app": {
												"description": "",
												"type": [
													"boolean",
													"null"
												]
											},
											"station_formats": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_location": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"call_letters": {
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
											"beasley_event_id": {
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
											"content_title": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_duration": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_id": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_type": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"stream_type": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"media_session_id": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_asset_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"content_network": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"primary_category": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"primary_category_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"show_name": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"show_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"content_daypart": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"stream_call_letters": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"is_primary_stream": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"boolean",
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
											"is_app": {
												"description": "",
												"type": [
													"boolean",
													"null"
												]
											},
											"station_formats": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_location": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"call_letters": {
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
											"beasley_event_id": {
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
											"content_title": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_duration": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_id": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_type": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"stream_type": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"media_session_id": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_asset_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"content_network": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"primary_category": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"primary_category_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"show_name": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"show_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"content_daypart": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"stream_call_letters": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"is_primary_stream": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"boolean",
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
											"is_app": {
												"description": "",
												"type": [
													"boolean",
													"null"
												]
											},
											"station_formats": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_location": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"call_letters": {
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
											"beasley_event_id": {
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
						"event_name": "Media Content End",
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
											"content_title": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_duration": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_id": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_type": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"stream_type": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"media_session_id": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_asset_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"content_network": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"primary_category": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"primary_category_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"show_name": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"show_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"content_daypart": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"stream_call_letters": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"is_primary_stream": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"boolean",
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
											"is_app": {
												"description": "",
												"type": [
													"boolean",
													"null"
												]
											},
											"station_formats": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_location": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"call_letters": {
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
											"beasley_event_id": {
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
						"event_name": "Media Session End",
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
											"content_title": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_duration": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_id": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_type": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"stream_type": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"media_session_id": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_asset_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"content_network": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"primary_category": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"primary_category_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"show_name": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"show_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"content_daypart": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"stream_call_letters": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"is_primary_stream": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"boolean",
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
											"is_app": {
												"description": "",
												"type": [
													"boolean",
													"null"
												]
											},
											"station_formats": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_location": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"call_letters": {
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
											"beasley_event_id": {
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
						"event_name": "Media Session Summary",
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
											"media_session_start_time": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"number",
													"null"
												]
											},
											"media_session_end_time": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"number",
													"null"
												]
											},
											"media_time_spent": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"number",
													"null"
												]
											},
											"media_content_time_spent": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"number",
													"null"
												]
											},
											"media_content_complete": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"boolean",
													"null"
												]
											},
											"media_session_segment_total": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"number",
													"null"
												]
											},
											"media_total_ad_time_spent": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"number",
													"null"
												]
											},
											"media_ad_time_spent_rate": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"number",
													"null"
												]
											},
											"media_session_ad_total": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"number",
													"null"
												]
											},
											"content_title": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_duration": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_id": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_type": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"stream_type": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"media_session_id": {
												"description": "MPARTICLE-FIELD-DO-NOT-POPULATE",
												"type": [
													"string",
													"null"
												]
											},
											"content_asset_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"content_network": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"primary_category": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"primary_category_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"show_name": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"show_id": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"content_daypart": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"stream_call_letters": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"string",
													"null"
												]
											},
											"is_primary_stream": {
												"description": "MEDIA-SPECIFIC",
												"type": [
													"boolean",
													"null"
												]
											},
											"domain": {
												"description": "",
												"format": "hostname",
												"type": [
													"string",
													"null"
												]
											},
											"is_app": {
												"description": "",
												"type": [
													"boolean",
													"null"
												]
											},
											"station_formats": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_location": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"call_letters": {
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
											"beasley_event_id": {
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
				"description": "User Identities",
				"match": {
					"type": "user_identities",
					"criteria": {}
				},
				"validator": {
					"type": "json_schema",
					"definition": {
						"additionalProperties": false,
						"properties": {
							"customerid": {
								"description": "Known identity ",
								"pattern": "^[a-zA-Z0-9_]*$",
								"type": "string"
							},
							"email": {
								"description": "Known identity ",
								"format": "email",
								"type": "string"
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
						"required": []
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
						"additionalProperties": false,
						"properties": {
							"$firstname": {
								"description": "",
								"type": "string"
							},
							"contests_tags": {
								"description": "",
								"items": {
									"type": "string"
								},
								"type": "array"
							},
							"dob": {
								"description": "",
								"format": "date",
								"pattern": "^\\d{4}-\\d{2}-\\d{2}$",
								"type": "string"
							},
							"$gender": {
								"description": "",
								"enum": [
									"M",
									"F",
									"O",
									"N",
									"P"
								],
								"type": "string"
							},
							"email_subscribe": {
								"description": "",
								"enum": [
									"opted_in",
									"unsubscribed",
									"subscribed"
								],
								"type": "string"
							},
							"list_wdmk_1059kiss_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wcsx_947wcsx_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"favorite_artists": {
								"description": "",
								"items": {
									"type": "string"
								},
								"type": "array"
							},
							"$lastname": {
								"description": "",
								"type": "string"
							},
							"$zip": {
								"description": "",
								"type": "string"
							},
							"$mobile": {
								"description": "",
								"type": "string"
							},
							"list_wmgc_1051bounce_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wmmr_933wmmr_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wmmr_big_friggin_deal": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wpen_975fanatic_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wpen_975fanatic_deals": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wrif_101wrif_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wmgq_magic983_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wdha_1055wdha_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wrat_wrat959_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wrat_rock_girl_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wrat_jersey_rock_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wjrz_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_espn_swfl_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wrxk_96krock_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_dave_chuck_daily_download": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wxkb_b1039_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wjpt_sunny1063_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_newplaya993_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_kklz_963kklz_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_koas_jammin1057_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_kvgs_1027vgs_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_kcye_1079coyotecountry_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_1057wror_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wklb_country1025_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wbos_rock929_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wben_957ben_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wben_big_deal": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wbqt_hot969_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wmgk_1029wmgk_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wmgk_1029wmgk_discount_deal": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wjbr_mix995_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wxkbhd2_bounceswfl_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wmtram_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wnks_kiss951_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wsoc_country1037_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wpeg_power98_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wbav_v1019_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wxtui_925xtu_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wxtu_925xtu_perks": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_whfs_moneytalk1010_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wlld_941wild_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wpbb_987shark_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wrbq_q105_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wqyk_995qyk_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wyuu_925maxima_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wyuuhd_1069playatampa_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wdrr_939bob_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wchz_hot955_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wgacam_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_whhd_hd983_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wkxc_kicks99_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wkqc_k1047_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wuks_1077jamz_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wzfx_foxy99_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_fox_sports_charlotte_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wflb_965bobfm_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wkml957_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wazz_sunny943_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wbz_sports_hub_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wazz_kiss_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wdmkhd2_praise_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wgus_sunny1027_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_fox_sports_nj_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_wcsxhd2_roar_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_must_haves_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_ktxe_alt1079_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_bamf_outlaws_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"list_houston_outlaws_newsletter": {
								"description": "email_list_item",
								"type": "boolean"
							},
							"contests_entered": {
								"description": "",
								"items": {
									"type": "string"
								},
								"type": "array"
							},
							"stations_visited": {
								"description": "",
								"items": {
									"type": "string"
								},
								"type": "array"
							},
							"$state": {
								"description": "",
								"type": "string"
							},
							"$country": {
								"description": "",
								"enum": [
									"AD",
									"AE",
									"AF",
									"AG",
									"AI",
									"AL",
									"AM",
									"AO",
									"AQ",
									"AR",
									"AS",
									"AT",
									"AU",
									"AW",
									"AX",
									"AZ",
									"BA",
									"BB",
									"BD",
									"BE",
									"BF",
									"BG",
									"BH",
									"BI",
									"BJ",
									"BL",
									"BM",
									"BN",
									"BO",
									"BQ",
									"BQ",
									"BR",
									"BS",
									"BT",
									"BV",
									"BW",
									"BY",
									"BZ",
									"CA",
									"CC",
									"CD",
									"CF",
									"CG",
									"CH",
									"CI",
									"CK",
									"CL",
									"CM",
									"CN",
									"CO",
									"CR",
									"CU",
									"CV",
									"CW",
									"CX",
									"CY",
									"CZ",
									"DE",
									"DJ",
									"DK",
									"DM",
									"DO",
									"DZ",
									"EC",
									"EE",
									"EG",
									"EH",
									"ER",
									"ES",
									"ET",
									"FI",
									"FJ",
									"FK",
									"FM",
									"FO",
									"FR",
									"GA",
									"GB",
									"GD",
									"GE",
									"GF",
									"GG",
									"GH",
									"GI",
									"GL",
									"GM",
									"GN",
									"GP",
									"GQ",
									"GR",
									"GS",
									"GT",
									"GU",
									"GW",
									"GY",
									"HK",
									"HM",
									"HN",
									"HR",
									"HT",
									"HU",
									"ID",
									"IE",
									"IL",
									"IM",
									"IN",
									"IO",
									"IQ",
									"IR",
									"IS",
									"IT",
									"JE",
									"JM",
									"JO",
									"JP",
									"KE",
									"KG",
									"KH",
									"KI",
									"KM",
									"KN",
									"KP",
									"KR",
									"KW",
									"KY",
									"KZ",
									"LA",
									"LB",
									"LC",
									"LI",
									"LK",
									"LR",
									"LS",
									"LT",
									"LU",
									"LV",
									"LY",
									"MA",
									"MC",
									"MD",
									"ME",
									"MF",
									"MG",
									"MH",
									"MK",
									"ML",
									"MM",
									"MN",
									"MO",
									"MP",
									"MQ",
									"MR",
									"MS",
									"MT",
									"MU",
									"MV",
									"MW",
									"MX",
									"MY",
									"MZ",
									"NA",
									"NC",
									"NE",
									"NF",
									"NG",
									"NI",
									"NL",
									"NO",
									"NP",
									"NR",
									"NU",
									"NZ",
									"OM",
									"PA",
									"PE",
									"PF",
									"PG",
									"PH",
									"PK",
									"PL",
									"PM",
									"PN",
									"PR",
									"PS",
									"PT",
									"PW",
									"PY",
									"QA",
									"RE",
									"RO",
									"RS",
									"RU",
									"RW",
									"SA",
									"SB",
									"SC",
									"SD",
									"SE",
									"SG",
									"SH",
									"SI",
									"SJ",
									"SK",
									"SL",
									"SM",
									"SN",
									"SO",
									"SR",
									"SS",
									"ST",
									"SV",
									"SX",
									"SY",
									"SZ",
									"TC",
									"TD",
									"TF",
									"TG",
									"TH",
									"TJ",
									"TK",
									"TL",
									"TM",
									"TN",
									"TO",
									"TR",
									"TT",
									"TV",
									"TW",
									"TZ",
									"UA",
									"UG",
									"UM",
									"US",
									"UY",
									"UZ",
									"VA",
									"VC",
									"VE",
									"VG",
									"VI",
									"VN",
									"VU",
									"WF",
									"WS",
									"YE",
									"YT",
									"ZA",
									"ZM",
									"ZW"
								],
								"type": "string"
							},
							"$city": {
								"description": "",
								"type": "string"
							},
							"$address": {
								"description": "",
								"type": "string"
							}
						},
						"required": [],
						"type": "object"
					}
				}
			},
			{
				"description": "",
				"match": {
					"type": "custom_event",
					"criteria": {
						"event_name": "Error",
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
											"beasley_event_id": {
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
											"call_letters": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_id": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_location": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"station_formats": {
												"description": "",
												"type": [
													"string",
													"null"
												]
											},
											"is_app": {
												"description": "",
												"type": [
													"boolean",
													"null"
												]
											},
											"domain": {
												"description": "",
												"format": "hostname",
												"type": [
													"string",
													"null"
												]
											},
											"error_code_number": {
												"description": "",
												"type": "number"
											},
											"error_message": {
												"description": "",
												"type": "string"
											},
											"error_code_name": {
												"description": "",
												"type": "string"
											},
											"error_class": {
												"description": "",
												"type": "string"
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
			}
		]
	}
};
