import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

import Dfp from '../content/embeds/Dfp';

class Sponsor extends PureComponent {
	constructor(props) {
		super(props);

		this.state = { render: this.getRender() };

		this.onResize = this.handleResize.bind(this);
		this.onRef = this.handleSlotRef.bind(this);
	}

	componentDidMount() {
		window.addEventListener('resize', this.onResize);
	}

	componentWillUnmount() {
		window.removeEventListener('resize', this.onResize);
	}

	getRender() {
		const { minWidth, maxWidth } = this.props;

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

		return false;
	}

	handleResize() {
		window.requestAnimationFrame(() => {
			const render = this.getRender();
			if (render !== this.state.render) {
				this.setState({ render });
			}
		});
	}

	handleSlotRef(dfp) {
		if (dfp) {
			setTimeout(dfp.refreshSlot.bind(dfp), 50);
		}
	}

	render() {
		const { render } = this.state;
		if (!render) {
			return false;
		}

		// backward compatibility with the legacy theme to make sure that everything keeps working correctly
		// this id is also compared in /assets/scripts/components/content/embeds/Dfp.js
		const id = 'div-gpt-ad-1487117572008-0';
		const { unitId, unitName } = window.bbgiconfig.dfp.player;
		const { className, style } = this.props;

		// we use createElement to make sure we don't add empty spaces here, thus DFP can properly collapse it when nothing to show here
		return React.createElement('div', { id, className, style }, [
			<Dfp
				key="sponsor"
				ref={this.onRef}
				placeholder={id}
				unitId={unitId}
				unitName={unitName}
				isLazyLoadingEnabled="off"
			/>,
		]);
	}
}

Sponsor.propTypes = {
	className: PropTypes.string.isRequired,
	minWidth: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
	maxWidth: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
	style: PropTypes.shape({}),
};

Sponsor.defaultProps = {
	minWidth: 0,
	maxWidth: 0,
	style: {},
};

export default Sponsor;
