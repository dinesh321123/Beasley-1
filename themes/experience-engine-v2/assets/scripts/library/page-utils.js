const getPageStatStack = () => {
	let { PageStatsArray } = window;
	if (!PageStatsArray) {
		window.PageStatsArray = [];
		PageStatsArray = window.PageStatsArray;
	}
	return PageStatsArray;
};

const getLastPageStat = () => {
	const pageStatsArray = getPageStatStack();
	if (pageStatsArray && pageStatsArray.length > 0) {
		return pageStatsArray[pageStatsArray.length - 1];
	}

	return null;
};

const processLastPageStatAndRemove = () => {
	const pageStatsArray = getPageStatStack();
	const lastPageStat = pageStatsArray.pop();
	console.log(`Back button detected, scrolling to ${lastPageStat.scrollPos}`);
	if (lastPageStat && lastPageStat.scrollPos) {
		window.scrollTo(window.scrollX, lastPageStat.scrollPos);
	}

	// set lastContentTopMargin holder for DFP to reference in ad-utils.js
	if (lastPageStat && lastPageStat.contentTopMargin) {
		window.lastContentTopMargin = lastPageStat.contentTopMargin;
	} else {
		window.lastContentTopMargin = 44;
	}
};

const processNewPageStatAndAdd = pageUrl => {
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
	window.scrollTo(window.scrollX, 0);
};

export default function doNewPageProcessing(lastUrl, newUrl) {
	const lastPageStat = getLastPageStat();

	// If we are going back a page
	if (lastPageStat && lastPageStat.pageUrl === lastUrl) {
		processLastPageStatAndRemove();
	} else {
		processNewPageStatAndAdd(newUrl);
	}
}
