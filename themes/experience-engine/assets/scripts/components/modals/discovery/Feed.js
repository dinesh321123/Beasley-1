import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

import LazyImage from '../../content/embeds/LazyImage';
import SvgIcon from '../../SvgIcon';

class Feed extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;
		self.handleAdd = self.handleAdd.bind( self );
		self.handleRemove = self.handleRemove.bind( self );
	}

	handleAdd() {
		const self = this;
		const { id } = self.props;

		self.setState( { loading: true } );
		self.props.onAdd( id );
	}

	handleRemove() {
		const self = this;
		const { id } = self.props;

		self.setState( { loading: true } );
		self.props.onRemove( id );
	}

	render() {
		const self = this;
		const { id, title, picture, type, added } = self.props;

		const placholder = `${id}-thumbnail`;
		const image = ( picture.original || picture.large || {} ).url;
		const lazyImage = image
			? <LazyImage placeholder={placholder} src={image} width="300" height="300" alt={title} />
			: false;

		const button = added
			? <button onClick={self.handleRemove} aria-label={`Remove ${title} from your feed`}><span>&#45;</span></button>
			: <button onClick={self.handleAdd} aria-label={`Add ${title} to your feed`}><span>&#43;</span></button>;

		return (
			<div className={`${type} post-tile`}>
				<div className="post-thumbnail">
					<div id={placholder} className="placeholder placeholder-lazyimage">
						{lazyImage}
						{button}
					</div>
				</div>

				<div className="post-title">
					<h3>{title}</h3>
				</div>

				<div className="feed-item-type">
					{ type && <p className="type"><SvgIcon type={type} />{type}</p> }
				</div>
			</div>
		);
	}

}

Feed.propTypes = {
	id: PropTypes.string.isRequired,
	title: PropTypes.string.isRequired,
	picture: PropTypes.shape( {} ),
	type: PropTypes.string.isRequired,
	added: PropTypes.bool.isRequired,
	onAdd: PropTypes.func.isRequired,
	onRemove: PropTypes.func.isRequired,
};

Feed.defaultProps = {
	picture: {},
};

export default Feed;
