import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

class Embedly extends PureComponent {

	constructor( props ) {
		super( props );
	}

	render() {

		return (
			<div className="embedly-wrapper">
				<blockquote className="embedly-card" data-card-controls="1" data-card-align="center" data-card-theme="light">
					<h4><a href={this.props.url}>{this.props.title}</a></h4>
					<p>{this.props.description}</p>
				</blockquote>
				<script async src="//cdn.embedly.com/widgets/platform.js" charset="UTF-8"></script>
			</div>
		);
	}

}

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
