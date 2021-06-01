export default function stnvideorender() {
	if (window.stnvideos) {
		console.log(window.stnvideos);
		if (window.stnvideos.prevent) {
			// do nothing
		} else if (window.stnvideos.override) {
			window.stnvideos.override.render();
		} else if (window.stnvideos.default) {
			window.stnvideos.default.render();
		}
	}
	delete window.stnvideos;
}
