import {
	loadAssets,
	unloadScripts,
} from '../../../library/dom';

/**
 * @function manageScripts
 *
 * @param {*} load
 * @param {*} unload
 */
export default function manageScripts( load, unload ) {

	// remove scripts loaded on the previous page
	unloadScripts( Object.keys( unload ) );

	// a workaround to make sure Facebook embeds work properly
	delete window.FB;
	window.FB = null;

	// load scripts for the new page
	loadAssets( Object.keys( load ) );
}
