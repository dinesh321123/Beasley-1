import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import LazyImage from './LazyImage';

const RelatedPost = ( { id, url, title, primary_image, published } ) => {
	const date = new Date( published );
	const targetUrl = `https://${url}`;
	return (
		<div id={`post-${id}`} className={['post-tile post'].join( ' ' )}>
			<div className="post-thumbnail">
				<a href={targetUrl} id={`thumbnail-${id}`}>
					<LazyImage
						crop={ false }
						placeholder={`thumbnail-${id}`}
						src={primary_image}
						width={310}
						height={205}
						alt={title || ''} />
				</a>
			</div>
			<div className="post-details">
				<div className="post-date">
					{date.toLocaleDateString( 'en-US', {
						day: 'numeric',
						month: 'long',
						year: 'numeric',
					} )}
				</div>
				<div className="post-title">
					<h3>
						<a href={targetUrl}>
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
};

const RelatedPosts = ( { posttype, posttitle, categories, url } ) => {
	const [postsEndpointURL, setPostsEndpointURL] = useState( '' );
	const [relatedPosts, setRelatedPosts] = useState( [] );
	const [loading, setLoading] = useState( false );
	const { bbgiconfig } = window;

	const endpointURL =  `${bbgiconfig.eeapi}publishers/${bbgiconfig.publisher.id}/recommendations?categories=${categories || ''}&posttype=${posttype}&url=${encodeURIComponent( url )}`;

	useEffect( () => {
		async function fetchPostsEndpoint() {
			try {
				setLoading( true );
				const result = await fetch( endpointURL ).then( r => r.json() );
				let transformedURL = result.url;

				if ( 'undefined' !== typeof window.jstag && 'undefined' !== typeof window.jstag.ckieGet ) {
					const seerid = window.jstag.ckieGet( 'seerid' );
					if ( seerid ) {
						transformedURL = transformedURL.replace( '{userid}', seerid );
					}
				}

				setPostsEndpointURL( transformedURL );
			} catch( e ) {
				setLoading( false );
				setPostsEndpointURL( '' );
			}

		}

		fetchPostsEndpoint();
	}, [setLoading, setPostsEndpointURL, endpointURL] );

	useEffect( () => {
		async function fetchPosts() {
			if ( postsEndpointURL ) {
				try {
					const result = await fetch( postsEndpointURL ).then( r => r.json() );
					setRelatedPosts( result.data );
					setLoading( false );
				} catch ( e ) {
					setLoading( false );
					setRelatedPosts( [] );
				}

			}
		}

		fetchPosts();
	}, [postsEndpointURL, setLoading, setRelatedPosts] );

	if ( loading ) {
		return <div>Loading...</div>;
	}

	if ( relatedPosts && 0 < relatedPosts.length ) {
		const deduplicate = ( relatedPost ) => {
			const normalizedUrl = url.replace( 'https://', '' ).replace( 'http://', '' );

			return posttitle !== relatedPost.title && normalizedUrl !== relatedPost.url;
		};
		return (
			<div className="related-articles content-wrap">
				<h2 className="section-head"><span>You Might Also Like</span></h2>

				<div className="archive-tiles -list">
					{relatedPosts.filter( deduplicate ).map( relatedPost => <RelatedPost key={relatedPost.id} {...relatedPost} /> )}
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
	postid: PropTypes.number,
	url: PropTypes.string,
};

RelatedPosts.defaultProps = {
	posttype: 'all',
	categories: '',
	posttitle: '',
	postid: 0,
	url: '/',
};

export default RelatedPosts;
