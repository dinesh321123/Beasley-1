import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import LazyImage from '../../content/embeds/LazyImage';
import SvgIcon from '../../SvgIcon';
import trapHOC from '@10up/react-focus-trap-hoc';

import { updateNotice } from '../../../redux/actions/screen';

class Feed extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;
		self.handleAdd = self.handleAdd.bind( self );
		self.handleRemove = self.handleRemove.bind( self );
		self.hideNotice = self.hideNotice.bind( self );
	}

	hideNotice() {
		setTimeout( () => {
			this.props.updateNotice( {
				message: this.props.notice.message,
				isOpen: false,
			} );
		}, 2000 );
	}

	handleAdd() {
		const self = this;
		const { id, title } = self.props;

		self.setState( { loading: true } );
		self.props.onAdd( id );

		self.props.updateNotice( {
			message: `<span class="title">${title}</span> has been added to your homepage`,
			isOpen: true
		} );

		self.hideNotice();
	}

	handleRemove() {
		const self = this;
		const { id, title } = self.props;

		self.setState( { loading: true } );
		self.props.onRemove( id );

		self.props.updateNotice( {
			message: `<span class="title">${title}</span> has been removed from your homepage`,
			isOpen: true
		} );

		self.hideNotice();
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
	updateNotice: PropTypes.func.isRequired,
	notice: PropTypes.object.isRequired,
};

Feed.defaultProps = {
	picture: {},
};

function mapStateToProps( { screen } ) {
	return {
		notice: screen.notice
	};
}

function mapDispatchToProps( dispatch ) {
	return bindActionCreators( {
		updateNotice,
	}, dispatch );
}

export default connect( mapStateToProps, mapDispatchToProps )( trapHOC()( Feed ) );
