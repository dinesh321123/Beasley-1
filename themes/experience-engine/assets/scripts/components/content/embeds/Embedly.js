import React, { useEffect, useRef } from 'react';
import PropTypes from 'prop-types';

const Embedly = ( { url, title, description } ) => {
	const embedRef = useRef( null );

	useEffect( () => {
		if ( window.embedly && embedRef && embedRef.current ) {
			window.embedly( 'card', embedRef.current );
		}
	}, [url] );

	return (
		<div className="embedly-wrapper">
			<blockquote ref={embedRef} className="embedly-card" data-card-controls="1" data-card-align="center" data-card-theme="light">
				<h4><a href={url}>{title}</a></h4>
				<p>{description}</p>
			</blockquote>
		</div>
	);
};


Embedly.propTypes = {
	url: PropTypes.string,
	title: PropTypes.string,
	description: PropTypes.string,
};

Embedly.defaultProps = {
	url: '',
	title: '',
	description: '',
};

export default Embedly;
