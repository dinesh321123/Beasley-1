/* eslint-disable sort-keys */
import { getStorage } from '../../../library/local-storage';

// Set livePlayerLocalStorage to utility helper object
// namespaced with liveplayer. Will return an
// object with a get and set using the namespace
// as a prefix shortcut
export default getStorage( 'liveplayer' );
