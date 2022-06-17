import React, { useRef } from 'react';
import ReactDOM from 'react-dom';
import { useSelector, useDispatch } from 'react-redux';
import Dfp from '../components/content/embeds/Dfp';
import ErrorBoundary from '../components/ErrorBoundary';
import {
	dropdownAdHidden,
	dropdownAdRefreshed,
} from '../redux/actions/dropdownad';

const DropDownAd = () => {
	const dispatch = useDispatch();
	const shouldRefresh = useSelector(
		state => state.dropdownad.shouldRefreshDropdownAd,
	);
	const shouldHide = useSelector(
		state => state.dropdownad.shouldHideDropdownAd,
	);
	const initialAdWasShown = useSelector(
		state => state.dropdownad.initialDropdownAdWasShown,
	);

	const dropDropDownAdRef = useRef(null);
	const container = document.getElementById('drop-down-container');
	const [pageURL] = document.location.href;
	// this id is also compared in /assets/scripts/components/content/embeds/Dfp.js
	const id = 'div-drop-down-slot';

	const { unitId, unitName } = window.bbgiconfig.dfp.dropdown;

	if (shouldRefresh && dropDropDownAdRef.current) {
		if (initialAdWasShown) {
			dropDropDownAdRef.current.refreshSlot();
		} else {
			dropDropDownAdRef.current.showSlot();
		}
		dispatch(dropdownAdRefreshed());
	}

	if (shouldHide && dropDropDownAdRef.current) {
		dropDropDownAdRef.current.hideSlot();
		dispatch(dropdownAdHidden());
	}

	const children = (
		<ErrorBoundary>
			<Dfp
				key={`drop-down-ad-${pageURL}`}
				ref={dropDropDownAdRef}
				placeholder={id}
				unitId={unitId}
				unitName={unitName}
				shouldMapSizes={false}
				pageURL={pageURL}
			/>
		</ErrorBoundary>
	);

	return ReactDOM.createPortal(children, container);
};

export default DropDownAd;
