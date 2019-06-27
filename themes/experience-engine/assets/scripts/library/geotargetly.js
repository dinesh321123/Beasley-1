history.pushState = ( f => function pushState(){
	var ret = f.apply( this, arguments );
	window.dispatchEvent( new Event( 'pushState' ) );
	window.dispatchEvent( new Event( 'locationchange' ) );
	return ret;
} )( history.pushState );

history.replaceState = ( f => function replaceState(){
	var ret = f.apply( this, arguments );
	window.dispatchEvent( new Event( 'replaceState' ) );
	window.dispatchEvent( new Event( 'locationchange' ) );
	return ret;
} )( history.replaceState );

window.addEventListener( 'popstate',()=>{
	window.dispatchEvent( new Event( 'locationchange' ) );
} );

if ( window.bbgiconfig.geotargetly ) {
	window.addEventListener( 'locationchange', function() {
		if ( window.geotargetly ) {
			try {
				window.geotargetly( document, 'script', 'style', 'head' );
			} catch ( e ) {
				// no-op
			}
		}
	} );
}
