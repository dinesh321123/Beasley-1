import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

import Dfp from '../content/embeds/Dfp';

class PlayerAd extends PureComponent {
	constructor(props) {
		super(props);

		this.getWhetherShouldRender = this.getWhetherShouldRender.bind(this);
		this.state = { shouldRender: this.getWhetherShouldRender() };

		this.onResize = this.handleResize.bind(this);
		this.onRef = this.handleSlotRef.bind(this);
	}

	componentDidMount() {
		window.addEventListener('resize', this.onResize);
	}

	componentWillUnmount() {
		window.removeEventListener('resize', this.onResize);
	}

	getWhetherShouldRender() {
		const { minWidth, maxWidth } = this.props;
		const { unitId } = window.bbgiconfig.dfp.player;

		if (unitId) {
			if (
				minWidth > 0 &&
				window.matchMedia(`(min-width: ${minWidth}px)`).matches
			) {
				return true;
			}

			if (
				maxWidth > 0 &&
				window.matchMedia(`(max-width: ${maxWidth}px)`).matches
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
				this.setState({ shouldRender });
			}
		});
	}

	handleSlotRef(dfp) {
		if (dfp) {
			setTimeout(dfp.refreshSlot.bind(dfp), 50);
		}
	}

	render() {
		const { shouldMapSizes } = this.props;
		const { shouldRender } = this.state;
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
				key="player-ad"
				ref={this.onRef}
				placeholder={id}
				unitId={unitId}
				unitName={unitName}
				shouldMapSizes={shouldMapSizes}
			/>,
		]);
	}
}

PlayerAd.propTypes = {
	className: PropTypes.string.isRequired,
	minWidth: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
	maxWidth: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
	style: PropTypes.shape({}),
	shouldMapSizes: PropTypes.bool,
};

PlayerAd.defaultProps = {
	minWidth: 0,
	maxWidth: 0,
	style: {},
	shouldMapSizes: true,
};

export default PlayerAd;
