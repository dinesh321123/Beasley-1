import { fixMegaSubMenuWidth } from '../../../library';

export default function setPlayerVisibility() {
	const { streams } = window.bbgiconfig;
	if (streams.length === 0) {
		const listenLiveClass = document.getElementsByClassName(
			'primary-mega-topbar',
		);
		if (listenLiveClass && listenLiveClass.length) {
			listenLiveClass[0].classList.add('no-ll');
		}
		const downloadApp = document.querySelector('.download');
		downloadApp.style.display = 'none';

		fixMegaSubMenuWidth();
	}
}
