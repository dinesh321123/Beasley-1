import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import Dfp from '../content/embeds/Dfp';

class PlayerAd extends PureComponent {
	constructor(props) {
		super(props);

		this.getWhetherShouldRender = this.getWhetherShouldRender.bind(this);
		this.state = {
			shouldRender: this.getWhetherShouldRender(),
			pageURL: window.location.href,
		};

		this.onResize = this.handleResize.bind(this);
		this.onSubtreeModified = this.handleSubtreeModified.bind(this);
	}

	componentDidMount() {
		window.addEventListener('resize', this.onResize);
		window.addEventListener('DOMSubtreeModified', this.onSubtreeModified);
	}

	componentWillUnmount() {
		window.removeEventListener('resize', this.onResize);
		window.removeEventListener('DOMSubtreeModified', this.onSubtreeModified);
	}

	getWhetherShouldRender() {
		const { minWidth } = this.props;
		const { unitId } = window.bbgiconfig.dfp.player;

		if (unitId) {
			if (
				minWidth > 0 &&
				window.matchMedia(`(min-width: ${minWidth}px)`).matches
			) {
				return true;
			}
		}

		return false;
	}

	handleResize() {
		window.requestAnimationFrame(() => {
			const shouldRender = this.getWhetherShouldRender();
			if (shouldRender !== this.state.shouldRender) {
				console.log(
					`Resetting Player Ad State Because Of Resize Change on ${window.location.href}`,
				);
				this.setState({ shouldRender });
			}
		});
	}

	handleSubtreeModified() {
		const currentPageURL = window.location.href;
		if (currentPageURL !== this.state.pageURL) {
			console.log(
				`Resetting Player Ad State Because Page Changed To ${currentPageURL}`,
			);
			this.setState({ pageURL: currentPageURL });
		}
	}

	render() {
		const { shouldMapSizes } = this.props;
		const { shouldRender, pageURL } = this.state;

		if (!shouldRender) {
			return false;
		}

		// backward compatibility with the legacy theme to make sure that everything keeps working correctly
		// this id is also compared in /assets/scripts/components/content/embeds/Dfp.js
		const id = 'div-gpt-ad-player-0';
		const { className, style } = this.props;
		const { unitId, unitName } = window.bbgiconfig.dfp.player;

		// we use createElement to make sure we don't add empty spaces here, thus DFP can properly collapse it when nothing to show here
		return React.createElement('div', { id, className, style }, [
			<Dfp
				key={`player-ad-${pageURL}`}
				ref={this.onRef}
				placeholder={id}
				unitId={unitId}
				unitName={unitName}
				shouldMapSizes={shouldMapSizes}
				pageURL={pageURL}
			/>,
		]);
	}
}

PlayerAd.propTypes = {
	className: PropTypes.string.isRequired,
	minWidth: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
	style: PropTypes.shape({}),
	shouldMapSizes: PropTypes.bool,
};

PlayerAd.defaultProps = {
	minWidth: 0,
	style: {},
	shouldMapSizes: true,
};

export default PlayerAd;
