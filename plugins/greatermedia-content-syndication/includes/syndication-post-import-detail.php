<?php

class SyndicationTerms
{
	private $terms = [];

	private function buildObjectWithWPTermSlugProperty($slug) {
		$newTerm = new stdClass();
		$newTerm->slug = $slug;
		return $newTerm;
	}

	public function addSlug($slug) {
		$this->terms[$slug] = true;
	}

	public function removeSlug($slug) {
		unset($this->terms[$slug]);
	}

	public function getWpCompatibleTerms() {
		return array_map(array($this, 'buildObjectWithWPTermSlugProperty'),$this->slugs());
	}

	public function slugs(): array {
		return array_keys($this->terms);
	}
}


class SyndicationPostImportDetail
{

	/**
	 * @var string
	 */
	public $status = null;
	/**
	 * @var string
	 */
	public $post_type = null;
	private $categories = null;
	private $shows = null;
	private $tags = null;

	function __construct() {
		$this->categories = new SyndicationTerms();
		$this->shows = new SyndicationTerms();
		$this->tags = new SyndicationTerms();
	}

	/**
	 * @var string
	 */
	public $slug = '';

	/**
	 * @var int
	 */
	public $post_id = 0;

	public function getCategories(): SyndicationTerms {
		return $this->categories;
	}

	public function getShows(): SyndicationTerms {
		return $this->shows;
	}

	public function getTags(): SyndicationTerms {
		return $this->tags;
	}

}





