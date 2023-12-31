import React, { useRef } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import Dfp from '../content/embeds/Dfp';
import ErrorBoundary from '../ErrorBoundary';
import {
	dropdownAdHidden,
	dropdownAdRefreshed,
} from '../../redux/actions/dropdownad';

const DropDownAd = () => {
	const dispatch = useDispatch();
	const dropDropDownAdRef = useRef(null);
	const shouldRefresh = useSelector(
		state => state.dropdownad.shouldRefreshDropdownAd,
	);
	const shouldHide = useSelector(
		state => state.dropdownad.shouldHideDropdownAd,
	);
	const isListenLiveShowing = useSelector(
		state => state.screen.isListenLiveShowing,
	);

	if (!isListenLiveShowing && !dropDropDownAdRef.current) {
		console.log('NOT SHOWING DD AD');
		return false;
	}

	const [pageURL] = document.location.href;
	// this id is also compared in /assets/scripts/components/content/embeds/Dfp.js
	const { unitId, unitName } = window.bbgiconfig.dfp.dropdown;

	if (shouldRefresh) {
		if (dropDropDownAdRef.current) {
			console.log('Refreshing Dropdown');
			dropDropDownAdRef.current.refreshSlot();
		}
		dispatch(dropdownAdRefreshed());
	}

	if (shouldHide && dropDropDownAdRef.current) {
		dropDropDownAdRef.current.hideSlot();
		dispatch(dropdownAdHidden());
	}

	return (
		<ErrorBoundary>
			<div>
				<div
					id="drop-down-container"
					className="drop-down-container -ad -centered"
				>
					<div
						id="div-drop-down-slot"
						className="placeholder placeholder-dfp"
					/>
				</div>
			</div>
			<Dfp
				key="drop-down-dfp"
				ref={dropDropDownAdRef}
				placeholder="div-drop-down-slot"
				unitId={unitId}
				unitName={unitName}
				shouldMapSizes={false}
				pageURL={pageURL}
			/>
		</ErrorBoundary>
	);
};

export default DropDownAd;
