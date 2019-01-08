import React from 'react';
import PropTypes from 'prop-types';

import LazyImage from '../../content/embeds/LazyImage';

function Feed( { id, title, picture, type } ) {
	const placholder = `${id}-thumbnail`;
	const image = ( picture.original || picture.large || {} ).url;
	const lazyImage = image
		? <LazyImage placeholder={placholder} src={image} width="300" height="300" alt={title} />
		: false;

	return (
		<div className={`${type} post-tile`}>
			<div className="post-thumbnail">
				<div id={placholder} className="placeholder placeholder-lazyimage">
					{lazyImage}
				</div>
			</div>

			<div className="post-title">
				<h3>{title}</h3>
			</div>
		</div>
	);
}

Feed.propTypes = {
	id: PropTypes.string.isRequired,
	title: PropTypes.string.isRequired,
	picture: PropTypes.shape( {} ),
	type: PropTypes.string.isRequired,
};

Feed.defaultProps = {
	picture: {},
};

export default Feed;
