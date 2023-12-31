import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import trapHOC from '@10up/react-focus-trap-hoc';
import LazyImage from '../../content/embeds/LazyImage';
import SvgIcon from '../../SvgIcon';

import { updateNotice } from '../../../redux/actions/screen';

class Feed extends PureComponent {
	constructor(props) {
		super(props);

		this.handleAdd = this.handleAdd.bind(this);
		this.handleRemove = this.handleRemove.bind(this);
		this.hideNotice = this.hideNotice.bind(this);
	}

	hideNotice() {
		setTimeout(() => {
			this.props.updateNotice({
				message: this.props.notice.message,
				isOpen: false,
			});
		}, 2000);
	}

	handleAdd() {
		const { id, title } = this.props;

		this.props.onAdd(id);

		this.props.updateNotice({
			message: `<span class="title">${title}</span> has been added to your homepage`,
			isOpen: true,
		});

		this.hideNotice();
	}

	handleRemove() {
		const { id, title } = this.props;

		this.props.onRemove(id);

		this.props.updateNotice({
			message: `<span class="title">${title}</span> has been removed from your homepage`,
			isOpen: true,
		});

		this.hideNotice();
	}

	render() {
		const { id, title, picture, type, added, pageNum } = this.props;

		const placholder = `${id}-thumbnail`;
		const image = (picture.original || picture.large || {}).url;
		const lazyImage = image ? (
			<LazyImage
				placeholder={placholder}
				src={image}
				width="300"
				height="300"
				alt={title}
			/>
		) : (
			false
		);

		const button = added ? (
			<button
				onClick={this.handleRemove}
				aria-label={`Remove ${title} from your feed`}
				type="button"
			>
				<span>&#45;</span>
			</button>
		) : (
			<button
				onClick={this.handleAdd}
				aria-label={`Add ${title} to your feed`}
				type="button"
			>
				<span>&#43;</span>
			</button>
		);

		const tileClass = added ? '-added' : '';

		return (
			<div className={`${type} post-tile ${tileClass}`} data-pagenum={pageNum}>
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
					{type && (
						<p className="type">
							<SvgIcon type={type} />
							{type}
						</p>
					)}
				</div>
			</div>
		);
	}
}

Feed.propTypes = {
	id: PropTypes.string.isRequired,
	title: PropTypes.string.isRequired,
	picture: PropTypes.shape({
		original: PropTypes.shape({}),
		large: PropTypes.shape({}),
	}),
	type: PropTypes.string.isRequired,
	added: PropTypes.bool.isRequired,
	onAdd: PropTypes.func.isRequired,
	onRemove: PropTypes.func.isRequired,
	updateNotice: PropTypes.func.isRequired,
	notice: PropTypes.shape({
		message: PropTypes.string,
	}).isRequired,
	pageNum: PropTypes.number,
};

Feed.defaultProps = {
	picture: {},
	pageNum: 1,
};

function mapStateToProps({ screen }) {
	return {
		notice: screen.notice,
	};
}

function mapDispatchToProps(dispatch) {
	return bindActionCreators(
		{
			updateNotice,
		},
		dispatch,
	);
}

export default connect(mapStateToProps, mapDispatchToProps)(trapHOC()(Feed));
