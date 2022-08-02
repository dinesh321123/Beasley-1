let nextPageScrollPos = 0;

const getPageStatStack = () => {
	let { PageStatsArray } = window;
	if (!PageStatsArray) {
		window.PageStatsArray = [];
		PageStatsArray = window.PageStatsArray;
	}
	return PageStatsArray;
};

const getPriorPageStat = () => {
	const pageStatsArray = getPageStatStack();
	if (pageStatsArray && pageStatsArray.length > 0) {
		return pageStatsArray[pageStatsArray.length - 1];
	}

	return null;
};

const removeTailAndProcessPrior = () => {
	const pageStatsArray = getPageStatStack();
	const priorPageStat = pageStatsArray.pop();

	console.log(
		`Back button detected, scrolling to ${priorPageStat.scrollPos} on ${priorPageStat.pageUrl}`,
	);

	// set lastContentTopMargin holder for DFP to reference in ad-utils.js
	if (priorPageStat && priorPageStat.contentTopMargin) {
		window.lastContentTopMargin = priorPageStat.contentTopMargin;
	} else {
		window.lastContentTopMargin = 44;
	}

	if (priorPageStat && priorPageStat.scrollPos) {
		// window.scrollTo(window.scrollX, priorPageStat.scrollPos);
		nextPageScrollPos = priorPageStat.scrollPos;
	} else {
		nextPageScrollPos = 0;
	}
};

const processNewPageStatAndAddTail = pageUrl => {
	console.log(`Detected New Page - Adding ${pageUrl}`);
	if (!pageUrl) {
		throw Error('NULL Url Param in addNewPageStat()');
	}

	const pageStatsArray = getPageStatStack();
	let contentTopMargin = null;
	const contentElement = document.getElementById('inner-content');
	if (contentElement) {
		const contentStyle = window.getComputedStyle(contentElement);
		contentTopMargin = parseInt(contentStyle.marginTop, 10);
	}

	pageStatsArray.push({ pageUrl, scrollPos: window.scrollY, contentTopMargin });
	if (pageStatsArray.length > 5) {
		pageStatsArray.shift();
	}

	window.lastContentTopMargin = 0; // Turn Off DFP Adjustment
	// window.scrollTo(window.scrollX, 0);
	nextPageScrollPos = 0;
};

export function doUpdatePageStack(leavingPageUrl, newPageUrl) {
	console.log(`leavingPageUrl: ${leavingPageUrl}`);
	console.log(`newPageUrl: ${newPageUrl}`);
	const priorPageStat = getPriorPageStat();

	if (priorPageStat) {
		console.log(`Prior URL: ${priorPageStat.pageUrl}`);
	} else {
		console.log('No Prior Page Stat');
	}

	// If we are going back a page
	if (priorPageStat && priorPageStat.pageUrl === newPageUrl) {
		removeTailAndProcessPrior(priorPageStat);
	} else {
		processNewPageStatAndAddTail(leavingPageUrl);
	}
}

export function doPageStackScroll(leavingPageUrl, newPageUrl) {
	window.scrollTo(window.scrollX, nextPageScrollPos);
}
