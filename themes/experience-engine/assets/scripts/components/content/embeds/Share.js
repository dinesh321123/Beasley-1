import React from 'react';

const getUrl = () => encodeURIComponent( window.location.href );
const getTitle = () => encodeURIComponent( document.title );

const handleFacebookClick = () => {
	const url = `https://www.facebook.com/sharer/sharer.php?u=${getUrl()}&t=${getTitle()}`;
	window.open( url, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600' );
};

const handleTwitterClick = () => {
	const url = `https://twitter.com/share?url=${getUrl()}&text=${getTitle()}`;
	window.open( url, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600' );
};

const Share = () => (
	<div className="share">
		<span>Share</span>
		<button className="facebook" onClick={handleFacebookClick} aria-label="Share this episode on Facebook">
			<svg xmlns="http://www.w3.org/2000/svg" width="8" height="17">
				<path d="M4.78 16.224H1.911v-7.65H0V5.938l1.912-.001-.003-1.553c0-2.151.583-3.46 3.117-3.46h2.11v2.637H5.816c-.987 0-1.034.368-1.034 1.056l-.004 1.32H7.15l-.28 2.636H4.781l-.002 7.65z"/>
			</svg>
		</button>
		<button className="twitter" onClick={handleTwitterClick} aria-label="Share this episode on Twitter">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="14" fill="none">
				<path d="M15.13 2.38a6.207 6.207 0 0 1-1.783.489 3.114 3.114 0 0 0 1.365-1.718c-.6.356-1.264.614-1.971.754a3.104 3.104 0 0 0-5.29 2.831 8.813 8.813 0 0 1-6.398-3.244 3.103 3.103 0 0 0 .96 4.144 3.091 3.091 0 0 1-1.405-.388v.04a3.106 3.106 0 0 0 2.49 3.043 3.11 3.11 0 0 1-1.402.053 3.107 3.107 0 0 0 2.9 2.156A6.227 6.227 0 0 1 0 11.825a8.785 8.785 0 0 0 4.758 1.395c5.71 0 8.832-4.73 8.832-8.832a8.92 8.92 0 0 0-.009-.401A6.305 6.305 0 0 0 15.13 2.38z"/>
			</svg>
		</button>
	</div>
);

export default Share;
