document.addEventListener("DOMContentLoaded", function() {
	const show_on_device_elements = document.getElementsByClassName(
		"show-on-device-client"
	);
	let userAgent = navigator.userAgent.toLowerCase();

	let isAndroid = /android/i.test(navigator.userAgent);

	if (! isAndroid) {
		userAgent += ' (ios)';
	}

	for (let i = 0; i < show_on_device_elements.length; i++) {
		const devicesList = show_on_device_elements[i].dataset.devices;
		const devices = devicesList.split(/[,\s]+/);

		for (let j = 0; j < devices.length; j++) {
			const device = devices[j];

			if (userAgent.indexOf(device.toLowerCase()) > -1) {
				show_on_device_elements[i].style.display = "inline";
			}
		}
	}
});
