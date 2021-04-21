import { useEffect } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

import * as screenActions from '../redux/actions/screen';

/**
 * Renders an empty component that listens to the SearchForm input.
 *
 * This component does not mount or render anything into the dom.
 *
 * @param {object} props
 */
const SearchForm = ({ fetchPage }) => {
	const onSearchSubmit = e => {
		const { target } = e;

		e.preventDefault();

		const url = target.getAttribute('action') || '/';
		const formData = new FormData(target);
		const search = formData.get('s');
		if (search && search.length) {
			fetchPage(`${url}?s=${encodeURIComponent(search)}`);
			target.querySelector('input[name="s"]').value = '';
		}
	};

	useEffect(() => {
		const searchForm = document.querySelector('.search-form');

		if (searchForm) {
			searchForm.addEventListener('submit', onSearchSubmit);
		}

		return () => {
			if (searchForm) {
				searchForm.removeEventListener('submit', onSearchSubmit);
			}
		};
	}, []);

	return null;
};

SearchForm.propTypes = {
	fetchPage: PropTypes.func.isRequired,
};

export default connect(null, { fetchPage: screenActions.fetchPage })(
	SearchForm,
);
