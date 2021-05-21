import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import LazyImage from './LazyImage';
import LoadingAjaxContent from '../../LoadingAjaxContent';
import { slugify } from '../../../library';

const RelatedPost = ({
	id,
	url,
	title,
	primary_image,
	published,
	test_name,
}) => {
	const date = new Date(published);
	const targetUrl = `https://${url}`;

	function handleClick(e) {
		e.preventDefault();

		window.ga('send', {
			hitType: 'event',
			eventCategory: 'YouMightAlsoLike',
			eventAction: `click test ${test_name}`,
			eventLabel: `${targetUrl}`,
			hitCallback: () => {
				window.location.href = targetUrl;
			},
		});
	}

	return (
		<div id={`post-${id}`} className={['post-tile post'].join(' ')}>
			<div className="post-thumbnail">
				<a href={targetUrl} onClick={handleClick} id={`thumbnail-${id}`}>
					<LazyImage
						crop={false}
						placeholder={`thumbnail-${id}`}
						src={primary_image}
						width={188}
						height={141}
						alt={title || ''}
					/>
				</a>
			</div>
			<div className="post-details">
				<div className="post-date">
					{date.toLocaleDateString('en-US', {
						day: 'numeric',
						month: 'long',
						year: 'numeric',
					})}
				</div>
				<div className="post-title">
					<h3>
						<a href={targetUrl} onClick={handleClick}>
							{title}
						</a>
					</h3>
				</div>
			</div>
		</div>
	);
};

RelatedPost.propTypes = {
	id: PropTypes.string.isRequired,
	url: PropTypes.string.isRequired,
	title: PropTypes.string.isRequired,
	primary_image: PropTypes.string.isRequired,
	published: PropTypes.string.isRequired,
	test_name: PropTypes.string.isRequired,
};

const RelatedPosts = ({ posttype, posttitle, categories, url }) => {
	const [postsEndpointURL, setPostsEndpointURL] = useState('');
	const [relatedPosts, setRelatedPosts] = useState([]);
	const [loading, setLoading] = useState(false);
	const [testName, setTestName] = useState('not defined');
	const { bbgiconfig } = window;

	const endpointURL = `${bbgiconfig.eeapi}publishers/${
		bbgiconfig.publisher.id
	}/recommendations?categories=${categories ||
		''}&posttype=${posttype}&url=${encodeURIComponent(url)}`;

	useEffect(() => {
		async function fetchPostsEndpoint() {
			try {
				setLoading(true);
				const result = await fetch(endpointURL).then(r => r.json());
				setTestName(result.testname);

				window.ga('send', {
					hitType: 'event',
					eventCategory: 'YouMightAlsoLike',
					eventAction: `displayed`,
					eventLabel: `test ${result.testname}`,
				});

				setPostsEndpointURL(result.url);
			} catch (e) {
				setLoading(false);
				setPostsEndpointURL('');
			}
		}

		fetchPostsEndpoint();
	}, [setLoading, setPostsEndpointURL, endpointURL, setTestName]);

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
						setRelatedPosts(result.data);
					} else if (result.data) {
						setRelatedPosts(
							result.data.map(relatedPost => {
								return {
									id: slugify(relatedPost.url ? relatedPost.url : ''),
									url: relatedPost.url ? normalizeUrl(relatedPost.url) : '',
									title: relatedPost.title,
									primary_image: relatedPost.image_url
										? relatedPost.image_url.replace('-150x150', '')
										: '',
									published: relatedPost.pub_date,
									test_name: relatedPost.tags,
								};
							}),
						);
					}
					setLoading(false);
				} catch (e) {
					setLoading(false);
					setRelatedPosts([]);
				}
			}
		}

		fetchPosts();
	}, [postsEndpointURL, setLoading, setRelatedPosts]);

	if (loading) {
		return <LoadingAjaxContent displayText="Loading Related Posts..." />;
	}

	if (relatedPosts && relatedPosts.length > 0) {
		const deduplicate = relatedPost => {
			const normalizedUrl = url.replace('https://', '').replace('http://', '');

			return (
				posttitle !== relatedPost.title && normalizedUrl !== relatedPost.url
			);
		};
		return (
			<div className="related-articles content-wrap">
				<h2 className="section-head">
					<span>{bbgiconfig.related_article_title}</span>
				</h2>

				<div className="archive-tiles -list">
					{relatedPosts.filter(deduplicate).map(relatedPost => (
						<RelatedPost
							key={relatedPost.id}
							id={relatedPost.id}
							url={relatedPost.url}
							title={relatedPost.title}
							primary_image={relatedPost.primary_image}
							published={relatedPost.published}
							test_name={testName}
						/>
					))}
				</div>
			</div>
		);
	}

	return null;
};

RelatedPosts.propTypes = {
	posttype: PropTypes.string,
	categories: PropTypes.string,
	posttitle: PropTypes.string,
	url: PropTypes.string,
};

RelatedPosts.defaultProps = {
	posttype: 'all',
	categories: '',
	posttitle: '',
	url: '/',
};

export default RelatedPosts;
