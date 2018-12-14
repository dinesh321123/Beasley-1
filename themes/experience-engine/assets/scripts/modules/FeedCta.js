import React from 'react';
import ReactDOM from 'react-dom';

const renderFeedCta = (
	<div className="content-wrap">
		<div className="meta">
			{/* @TODO :: How will we pull in the station logo here? */}
			<img src="https://placehold.it/117x69" alt="station logo" />
			{/* @TODO:: How are we going to control this content dynamically? Or maybe we're not at all. */}
			<div className="copy">
				<h3>Personalize your feed</h3>
				<p>Subscribe to content tailored just for you</p>
			</div>

		</div>
		<div className="action">
			<svg className="waveform">
				<rect style={{ animationDelay: '.2s' }} width="7" height="42" x="198" y="41" rx="3.5" />
				<rect style={{ animationDelay: '.4s' }} width="7" height="42" x="246" y="41" rx="3.5" />
				<rect style={{ animationDelay: '.6s' }} width="7" height="72" x="210" y="26" rx="3.5" />
				<rect style={{ animationDelay: '.8s' }} width="7" height="72" x="234" y="26" rx="3.5" />
				<rect style={{ animationDelay: '1s' }} width="7" height="72" x="258" y="26" rx="3.5" />
				<rect style={{ animationDelay: '1.2s' }} width="7" height="42" x="270" y="41" rx="3.5" />
				<rect style={{ animationDelay: '1.4s' }} width="7" height="42" x="294" y="41" rx="3.5" />
				<rect style={{ animationDelay: '1.6s' }} width="7" height="19" x="306" y="52" rx="3.5" />
				<rect style={{ animationDelay: '1.8s' }} width="7" height="9" x="318" y="57" rx="3.5" />
				<rect style={{ animationDelay: '.2s' }} width="7" height="9" x="330" y="57" rx="3.5" />
				<rect style={{ animationDelay: '.4s' }} width="7" height="5" x="342" y="59" rx="2.5" />
				<rect style={{ animationDelay: '.6s' }} width="7" height="9" x="354" y="57" rx="3.5" />
				<rect style={{ animationDelay: '.8s' }} width="7" height="5" x="366" y="59" rx="2.5" />
				<rect style={{ animationDelay: '1"s' }} width="7" height="9" x="378" y="57" rx="3.5" />
				<rect style={{ animationDelay: '1.2s' }} width="7" height="5" x="138" y="59" rx="2.5" />
				<rect style={{ animationDelay: '1.4s' }} width="7" height="9" x="150" y="57" rx="3.5" />
				<rect style={{ animationDelay: '1.6s' }} width="7" height="5" x="162" y="59" rx="2.5" />
				<rect style={{ animationDelay: '1.8s' }} width="7" height="9" x="174" y="57" rx="3.5" />
				<rect style={{ animationDelay: '.2s' }} width="7" height="19" x="186" y="52" rx="3.5" />
				<rect style={{ animationDelay: '.4s' }} width="7" height="72" x="282" y="26" rx="3.5" />
				<rect style={{ animationDelay: '.6s' }} width="7" height="123" x="222" rx="3.5" />
				<rect style={{ animationDelay: '.8s' }} width="7" height="42" rx="3.5" y="43" x="79" />
				<rect style={{ animationDelay: '1"s' }} width="7" height="42" rx="3.5" y="43" x="55" />
				<rect style={{ animationDelay: '1.2s' }} width="7" height="19" rx="3.5" y="52" x="43" />
				<rect style={{ animationDelay: '1.4s' }} width="7" height="9" rx="3.5" y="57" x="31" />
				<rect style={{ animationDelay: '1.6s' }} width="7" height="9" rx="3.5" y="57" x="19" />
				<rect style={{ animationDelay: '1.8s' }} width="7" height="5" rx="2.5" y="59.179104" x="7" />
				<rect style={{ animationDelay: '.2s' }} width="7" height="72" rx="3.5" y="28" x="67" />
			</svg>
			{/* @TODO :: Button needs associated method to add station to users 'subscribed' list */}
			<button className="btn">
				Customize Your Feed
			</button>
		</div>
	</div>
);

const FeedCta = () => {
	const container = document.getElementById( 'feed-cta' );
	const component = renderFeedCta;

	return ReactDOM.createPortal( component, container );
};

export default FeedCta;
