/* GA CONFIG DATA EMITTED FROM PHP
		$data = [
			'google_analytics_v3_enabled' => $google_analytics_v3_enabled,
			'google_analytics'        	  => $google_analytics_ua,
			'google_analytics_v4_enabled' => $google_analytics_v4_enabled,
			'google_analytics_v4'	  	  => $google_analytics_ua_v4,
			'google_uid_dimension'    	  => absint( get_option( self::OPTION_UA_UID ) ),
			'google_author_dimension' 	  => absint( get_option( self::OPTION_UA_AUTHOR ) ),
			'title'                   	  => wp_title( '&raquo;', false ),
			'url'					  	  => esc_url( home_url( $_SERVER['REQUEST_URI'] ) ),
			'shows'                   	  => '',
			'category'                	  => '',
			'author'                  	  => 'non-author',
		];
*/


class beasleyAnalytics {
	analyticsProviderArray = [];

	constructor() {
		this.loadBeasleyConfigData(window.bbgiAnalyticsConfig);
	}

	loadBeasleyConfigData = (beasleyAnalyticsConfigData) => {
		// guard to prevent multiple initial loads
		if (this.analyticsProviderArray.length > 0) {
			return;
		}

		if (beasleyAnalyticsConfigData.google_analytics_v3_enabled && beasleyAnalyticsConfigData.google_analytics) {
			this.analyticsProviderArray.push(new beasleyAnalyticsGaV3Provider(beasleyAnalyticsConfigData));
		}
		if (beasleyAnalyticsConfigData.google_analytics_v4_enabled && beasleyAnalyticsConfigData.google_analytics_v4) {
			this.analyticsProviderArray.push(new beasleyAnalyticsGaV4Provider(beasleyAnalyticsConfigData));
		}
	}

	createAnalytics() {
		this.analyticsProviderArray.map(provider => provider.createAnalytics.apply(provider, arguments));
	}

	requireAnalytics() {
		this.analyticsProviderArray.map(provider => provider.requireAnalytics.apply(provider, arguments));
	}

	setAnalytics() {
		this.analyticsProviderArray.map(provider => provider.setAnalytics.apply(provider, arguments));
	}

	sendEvent() {
		this.analyticsProviderArray.map(provider => provider.sendEvent.apply(provider, arguments));
	}
}

class beasleyAnalyticsBaseProvider {
	constructor(typeString, idString) {
		this.analyticType = typeString;
		this.idString = idString;

		this.debugLog(`Constructor - id: ${this.idString}`);
	}

	debugLog(message) {
		if (location.search.indexOf('gadebug=1') !== -1) {
			console.log(`Beasley Analytics ${this.analyticType} - ${message}`);
		}
	}

	createAnalytics() {
		this.debugLog(`createAnalytics() - ${JSON.stringify(arguments)}`);
	}

	requireAnalytics() {
		this.debugLog(`requireAnalytics() - ${JSON.stringify(arguments)}`);
	}

	setAnalytics() {
		this.debugLog(`setAnalytics() - ${JSON.stringify(arguments)}`);
	}

	sendEvent() {
		this.debugLog(`sendEvent() - ${JSON.stringify(arguments)}`);
	}
}

class beasleyAnalyticsGaV3Provider extends beasleyAnalyticsBaseProvider {
	static typeString = 'GA_V3';

	constructor(bbgiAnalyticsConfig) {
		super(beasleyAnalyticsGaV3Provider.typeString, bbgiAnalyticsConfig.google_analytics);
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	}

	createAnalytics() {
		super.createAnalytics.apply(this, arguments);
		this.doGaCall('create', arguments);
	}

	requireAnalytics() {
		super.requireAnalytics.apply(this, arguments);
		this.doGaCall('require', arguments);
	}

	setAnalytics() {
		super.setAnalytics.apply(this, arguments);
		this.doGaCall('set', arguments);
	}

	sendEvent() {
		super.sendEvent.apply(this, arguments);
		this.doGaCall('send', arguments);
	}

	doGaCall(gaFunctionName, argumentArray) {
		const extendedArgs = [...argumentArray];
		extendedArgs.unshift(gaFunctionName);
		ga.apply(this, extendedArgs);
	}
}

class beasleyAnalyticsGaV4Provider extends beasleyAnalyticsBaseProvider {
	static typeString = 'GA_V4';

	constructor(bbgiAnalyticsConfig) {
		super(beasleyAnalyticsGaV4Provider.typeString, bbgiAnalyticsConfig.google_analytics_v4);
		// <!-- Google tag (gtag.js) -->
		// <script async src="https://www.googletagmanager.com/gtag/js?id=G-2EPYVQB125"></script> -->
		const gaV4script = document.createElement('script');
		gaV4script.type = 'text/javascript';
		gaV4script.src = 'https://www.googletagmanager.com/gtag/js?id=' + bbgiAnalyticsConfig.google_analytics_v4;
		gaV4script.async = true;
		document.head.appendChild(gaV4script);
	}

	// Category, Action, Label, Value not in GA4 - there are prdefine and you can add custom
	// set event params - https://developers.google.com/analytics/devguides/collection/ga4/event-parameters?client_type=gtag

	// https://support.google.com/analytics/answer/11403294?hl=en#zippy=%2Cgoogle-tag-manager-websites
	// If you manually send page_view events, make sure Enhanced measurement is configured correctly to avoid double counting pageviews on history state changes. Typically, this means disabling Page changes based on browser history events under the advanced settings of the Page views section.

	gtag() {
		window.dataLayer = window.dataLayer || [];
		dataLayer.push(arguments);
	}

	createAnalytics() {
		// Call Super to log, but really we ignore the arguments since they were specific for V3
		super.createAnalytics.apply(this, arguments);

		this.gtag('js', new Date());
		this.gtag('config', window.bbgiAnalyticsConfig.google_analytics_v4);
	}

	requireAnalytics() {
		super.requireAnalytics.apply(this, arguments);
	}

	setAnalytics() {
		super.setAnalytics.apply(this, arguments);
		this.gtag('set', arguments);
	}

	sendEvent() {
		super.sendEvent.apply(this, arguments);
		this.gtag('send', arguments);
	}
}

console.log('ga_enqueue_scripts loaded');

