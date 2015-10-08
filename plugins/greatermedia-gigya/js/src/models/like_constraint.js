var LikeConstraint = Constraint.extend({

	defaults        : {
		type        : 'profile:likes',
		operator    : 'equals',
		conjunction : 'and',
		valueType   : 'string',
		value       : '',
		category    : 'Any Category',
	},

	getCategories: function() {
		return FACEBOOK_CATEGORIES;
	}

});

FACEBOOK_CATEGORIES = [
	{ label: 'Actor/Director' },
	{ label: 'Movie' },
	{ label: 'Producer' },
	{ label: 'Writer' },
	{ label: 'Studio' },
	{ label: 'Movie Theater' },
	{ label: 'TV/Movie Award' },
	{ label: 'Fictional Character' },
	{ label: 'Movie Character' },
	{ label: 'Album' },
	{ label: 'Song' },
	{ label: 'Musician/Band' },
	{ label: 'Music Video' },
	{ label: 'Concert Tour' },
	{ label: 'Concert Venue' },
	{ label: 'Radio Station' },
	{ label: 'Record Label' },
	{ label: 'Music Award' },
	{ label: 'Music Chart' },
	{ label: 'Book' },
	{ label: 'Author' },
	{ label: 'Book Store' },
	{ label: 'Library' },
	{ label: 'Magazine' },
	{ label: 'Book Series' },
	{ label: 'TV Show' },
	{ label: 'TV Network' },
	{ label: 'TV Channel' },
	{ label: 'Athlete' },
	{ label: 'Artist' },
	{ label: 'Public Figure' },
	{ label: 'Journalist' },
	{ label: 'News Personality' },
	{ label: 'Chef' },
	{ label: 'Lawyer' },
	{ label: 'Doctor' },
	{ label: 'Business Person' },
	{ label: 'Comedian' },
	{ label: 'Entertainer' },
	{ label: 'Teacher' },
	{ label: 'Dancer' },
	{ label: 'Designer' },
	{ label: 'Photographer' },
	{ label: 'Entrepreneur' },
	{ label: 'Politician' },
	{ label: 'Government Official' },
	{ label: 'Sports League' },
	{ label: 'Professional Sports Team' },
	{ label: 'Coach' },
	{ label: 'Amateur Sports Team' },
	{ label: 'School Sports Team' },
	{ label: 'Restaurant/Cafe' },
	{ label: 'Bar' },
	{ label: 'Club' },
	{ label: 'Company' },
	{ label: 'Product/Service' },
	{ label: 'Website' },
	{ label: 'Cars' },
	{ label: 'Bags/Luggage' },
	{ label: 'Camera/Photo' },
	{ label: 'Clothing' },
	{ label: 'Computers' },
	{ label: 'Software' },
	{ label: 'Office Supplies' },
	{ label: 'Electronics' },
	{ label: 'Health/Beauty' },
	{ label: 'Appliances' },
	{ label: 'Building Materials' },
	{ label: 'Commercial Equipment' },
	{ label: 'Home Decor' },
	{ label: 'Furniture' },
	{ label: 'Household Supplies' },
	{ label: 'Kitchen/Cooking' },
	{ label: 'Patio/Garden' },
	{ label: 'Tools/Equipment' },
	{ label: 'Wine/Spirits' },
	{ label: 'Jewelry/Watches' },
	{ label: 'Pet Supplies' },
	{ label: 'Outdoor Gear/Sporting Goods' },
	{ label: 'Baby Goods/Kids Goods' },
	{ label: 'Media/News/Publishing' },
	{ label: 'Bank/Financial Institution' },
	{ label: 'Non-Governmental Organization (NGO)' },
	{ label: 'Insurance Company' },
	{ label: 'Small Business' },
	{ label: 'Energy/Utility' },
	{ label: 'Retail and Consumer Merchandise' },
	{ label: 'Automobiles and Parts' },
	{ label: 'Industrials' },
	{ label: 'Transport/Freight' },
	{ label: 'Health/Medical/Pharmaceuticals' },
	{ label: 'Aerospace/Defense' },
	{ label: 'Mining/Materials' },
	{ label: 'Farming/Agriculture' },
	{ label: 'Chemicals' },
	{ label: 'Consulting/Business Services' },
	{ label: 'Legal/Law' },
	{ label: 'Education' },
	{ label: 'Engineering/Construction' },
	{ label: 'Food/Beverages' },
	{ label: 'Telecommunication' },
	{ label: 'Biotechnology' },
	{ label: 'Computers/Technology' },
	{ label: 'Internet/Software' },
	{ label: 'Travel/Leisure' },
	{ label: 'Community Organization' },
	{ label: 'Political Organization' },
	{ label: 'Vitamins/Supplements' },
	{ label: 'Drugs' },
	{ label: 'Church/Religious Organization' },
	{ label: 'Phone/Tablet' },
	{ label: 'Games/Toys' },
	{ label: 'App Page' },
	{ label: 'Video Game' },
	{ label: 'Board Game' },
	{ label: 'Local Business' },
	{ label: 'Hotel' },
	{ label: 'Landmark' },
	{ label: 'Airport' },
	{ label: 'Sports Venue' },
	{ label: 'Arts/Entertainment/Nightlife' },
	{ label: 'Automotive' },
	{ label: 'Spas/Beauty/Personal Care' },
	{ label: 'Event Planning/Event Services' },
	{ label: 'Bank/Financial Services' },
	{ label: 'Food/Grocery' },
	{ label: 'Health/Medical/Pharmacy' },
	{ label: 'Home Improvement' },
	{ label: 'Pet Services' },
	{ label: 'Professional Services' },
	{ label: 'Business Services' },
	{ label: 'Community/Government' },
	{ label: 'Real Estate' },
	{ label: 'Shopping/Retail' },
	{ label: 'Public Places' },
	{ label: 'Attractions/Things to Do' },
	{ label: 'Sports/Recreation/Activities' },
	{ label: 'Tours/Sightseeing' },
	{ label: 'Transportation' },
	{ label: 'Hospital/Clinic' },
	{ label: 'Museum/Art Gallery' },
	{ label: 'Organization' },
	{ label: 'School' },
	{ label: 'University' },
	{ label: 'Non-Profit Organization' },
	{ label: 'Government Organization' },
	{ label: 'Cause' },
	{ label: 'Political Party' },
	{ label: 'Pet' },
	{ label: 'Middle School' },
];

(function() {
	var i = 0;
	var n = FACEBOOK_CATEGORIES.length;
	var category;

	for (i = 0; i < n; i++) {
		category = FACEBOOK_CATEGORIES[i];
		if (!category.value) {
			category.value = category.label;
		}
	}

	FACEBOOK_CATEGORIES = _.sortBy(FACEBOOK_CATEGORIES, 'label');
	FACEBOOK_CATEGORIES.unshift({
		label: 'Any Category', value: 'Any Category'
	});

}());
