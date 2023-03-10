
window.isWhiz = () => {
	const searchParams = new URLSearchParams(window.location.search?.toLowerCase());
	return searchParams.has('whiz')
};
