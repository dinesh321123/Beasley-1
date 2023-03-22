
window.isWhiz = () => {
	const searchParams = new URLSearchParams(window.location.search?.toLowerCase());
	return searchParams.has('whiz')
};

window.getDayPart = (hourOfDay) => {
	const morning = 'Morning Drive'; // 6am to 10am
	const midday = 'Midday'; // 10am to 3pm
	const afternoon = 'Afternoon Drive'; // 3pm to 7pm
	const evening = 'Evening'; // 7pm to 12am
	const overnight = 'Overnight'; // 12am to 6am
	const dayPartArray = [
		overnight, // 0
		overnight, // 1
		overnight, // 2
		overnight, // 3
		overnight, // 4
		overnight, // 5
		morning, // 6
		morning, // 7
		morning, // 8
		morning, // 9
		midday, // 10
		midday, // 11
		midday, // 12
		midday, // 13
		midday, // 14
		afternoon, // 15
		afternoon, // 16
		afternoon, // 17
		afternoon, // 18
		evening, // 19
		evening, // 20
		evening, // 21
		evening, // 22
		evening, // 23
	];

	return dayPartArray[hourOfDay];
}

// createUUID() copied from https://www.arungudelli.com/tutorial/javascript/how-to-create-uuid-guid-in-javascript-with-examples/
// NOT WELL TESTED
window.createUUID = () => {
	return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
		(c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
	)
}
