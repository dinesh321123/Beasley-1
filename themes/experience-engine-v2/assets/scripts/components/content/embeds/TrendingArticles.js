import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import LoadingAjaxContent from '../../LoadingAjaxContent';
import { slugify } from '../../../library';

// Component for rendering individual trending posts
const TrendingPost = ({ id, url, title, primary_image, showImage }) => {
	const targetUrl = `https://${url}`;

	return (
		<div id={`post-${id}`} className={['post-tile post'].join(' ')}>
			{showImage && (
				<a href={targetUrl} id={`thumbnail-${id}`}>
					<img
						data-crop="false"
						data-placeholder={`thumbnail-${id}`}
						src={primary_image}
						width="100%"
						className="img-box"
						alt={title || ''}
					/>
				</a>
			)}
			{!showImage && (
				<div className="post-title">
					<p>
						<a href={targetUrl}>{title}</a>
					</p>
				</div>
			)}
		</div>
	);
};

TrendingPost.propTypes = {
	id: PropTypes.string.isRequired,
	url: PropTypes.string.isRequired,
	title: PropTypes.string.isRequired,
	primary_image: PropTypes.string.isRequired,
	showImage: PropTypes.bool.isRequired,
};

// Component for rendering trending articles
const TrendingArticles = ({
	posttype,
	posttitle,
	categories,
	url,
	location,
	has_shortcode,
}) => {
	const [postsEndpointURL, setPostsEndpointURL] = useState('');
	const [TrendingArticles, setTrendingArticles] = useState([]);
	const [loading, setLoading] = useState(false);
	const [testName, setTestName] = useState('not defined');
	const { bbgiconfig } = window;
	const endpointURL = `${bbgiconfig.eeapi}publishers/${bbgiconfig.publisher.id}/recommendations?related=true`;

	// Fetch the endpoint URL for fetching trending posts
	useEffect(() => {
		async function fetchPostsEndpoint() {
			try {
				setLoading(true);
				const result = await fetch(endpointURL).then(r => r.json());
				if (!result.url) {
					setLoading(false);
				} else {
					setTestName(result.testname);
					let transformedURL = result.url;
					transformedURL = transformedURL.replace('{url}', url);
					setPostsEndpointURL(transformedURL);
				}
			} catch (e) {
				setLoading(false);
				setPostsEndpointURL('');
			}
		}

		fetchPostsEndpoint();
	}, [setLoading, setPostsEndpointURL, endpointURL, setTestName]);

	// Fetch the trending posts using the postsEndpointURL
	useEffect(() => {
		async function fetchPosts() {
			const normalizeUrl = urlToNormalize => {
				return urlToNormalize.replace('https://', '').replace('http://', '');
			};

			if (postsEndpointURL) {
				try {
					const result = await fetch(postsEndpointURL).then(r => r.json());
					if (
						// If We Did Not Pull From Parsley
						postsEndpointURL.toLowerCase().indexOf('api.parsely.com') === -1
					) {
						setTrendingArticles(result.data);
					} else if (result.data) {
						setTrendingArticles(
							result.data.map(trendingPost => {
								return {
									id: slugify(trendingPost.url ? trendingPost.url : ''),
									url: trendingPost.url ? normalizeUrl(trendingPost.url) : '',
									title: trendingPost.title,
									primary_image: trendingPost.image_url
										? trendingPost.image_url.replace('-150x150', '')
										: '',
									published: trendingPost.pub_date,
									test_name: testName,
								};
							}),
						);
					}
					setLoading(false);
				} catch (e) {
					setLoading(false);
					setTrendingArticles([]);
				}
			}
		}

		fetchPosts();
	}, [postsEndpointURL, setLoading, setTrendingArticles]);

	// Render loading state while fetching trending articles
	if (loading) {
		return <LoadingAjaxContent displayText="Loading Trending Articles..." />;
	}

	// Render trending articles if available
	if (TrendingArticles && TrendingArticles.length > 0) {
		const deduplicate = trendingPost => {
			const normalizedUrl = url.replace('https://', '').replace('http://', '');
			return (
				posttitle !== trendingPost.title && normalizedUrl !== trendingPost.url
			);
		};

		// Render trending articles when shortcode is present
		if (has_shortcode) {
			if (location === 'embed_custom') {
				return (
					<div className={`post-trending-articles content-wrap ${location}`}>
						<h2 className="section-head">
							<span>{bbgiconfig.trending_article_title}</span>
						</h2>
						<div className="archive-tiles -list">
							{TrendingArticles.filter(deduplicate).map(
								(trendingPost, index) => (
									<TrendingPost
										key={trendingPost.id}
										id={trendingPost.id}
										url={trendingPost.url}
										title={trendingPost.title}
										primary_image={trendingPost.primary_image}
										showImage={index === 0}
									/>
								),
							)}
						</div>
					</div>
				);
			}
			return null;
		}

		// Render trending articles when shortcode is not present
		if (bbgiconfig.isTrendingPostRender[location] === false) {
			bbgiconfig.isTrendingPostRender[location] = true;
			return (
				<div className={`post-trending-articles content-wrap ${location}`}>
					<h2 className="section-head">
						<span>{bbgiconfig.trending_article_title}</span>
					</h2>
					<div className="archive-tiles -list">
						{TrendingArticles.filter(deduplicate).map((trendingPost, index) => (
							<TrendingPost
								key={trendingPost.id}
								id={trendingPost.id}
								url={trendingPost.url}
								title={trendingPost.title}
								primary_image={trendingPost.primary_image}
								showImage={index === 0}
							/>
						))}
					</div>
				</div>
			);
		}
	}

	return null;
};

TrendingArticles.propTypes = {
	posttype: PropTypes.string,
	categories: PropTypes.string,
	posttitle: PropTypes.string,
	url: PropTypes.string,
	location: PropTypes.string,
	has_shortcode: PropTypes.bool,
};

TrendingArticles.defaultProps = {
	posttype: 'all',
	categories: '',
	posttitle: '',
	url: '/',
	location: '',
	has_shortcode: '',
};

export default TrendingArticles;
