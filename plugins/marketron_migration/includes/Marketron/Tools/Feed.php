<?php

namespace Marketron\Tools;

class Feed extends BaseTool {

	public $articles;
	public $article_fields = array(
		'Title',
		'Slug',
		'SlugDate',
		'ArticleText',
		'ExcerptText',
		'PrimaryMediaReference',
		'FeaturedImageFilepath',
		'FeaturedImageCaption',
		'FeaturedImageAttribute',
		'FeaturedAudioFilepath',
		'UTCStartDateTime',
		'LastModifiedUTCDateTime',
		'ArticleViews',
	);

	public $category_fields = array(
		'Category',
		'Slug',
	);

	public $slug_history_fields = array(
		'ArticleHistoricalSlug',
	);

	function get_name() {
		return 'feed';
	}

	function get_data_filename() {
		return 'Feeds.XML';
	}

	function parse( $xml_element ) {
		$this->articles = $this->parse_collection(
			'parse_article', $xml_element->Articles->Article
		);
		//print_r( $this->articles );
	}

	function parse_article( $article_element ) {
		$article = $this->parse_fields(
			$article_element, $this->article_fields
		);

		if ( isset( $article_element->Authors->Author ) ) {
			$article['authors'] = $this->parse_collection(
				'parse_author',
				$article_element->Authors->Author
			);
		}

		if ( isset( $article_element->Feeds->Feed->FeedCategories->FeedCategory ) ) {
			$article['categories'] = $this->parse_collection(
				'parse_feed_category',
				$article_element->Feeds->Feed->FeedCategories->FeedCategory
			);
		}

		if ( isset( $article_element->SlugHistoryItems->SlugHistoryItem ) ) {
			$article['slug_history_items'] = $this->parse_collection(
				'parse_slug_history_item',
				$article_element->SlugHistoryItems->SlugHistoryItem
			);
		}

		return $article;
	}

	function parse_author( $element ) {
		return false;
	}

	function parse_feed_category( $element ) {
		return $this->parse_fields(
			$element, $this->category_fields
		);
	}

	function parse_slug_history_item( $element ) {
		$item = $this->parse_fields(
			$element, $this->slug_history_fields
		);

		if ( preg_match( '/\/$/', $item['ArticleHistoricalSlug'] ) ) {
			return false;
		} else {
			return $item;
		}
	}

}
