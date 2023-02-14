/* GA CONFIG DATA EMITTED FROM PHP
		$data = [
			'google_analytics_v3_enabled' => $google_analytics_v3_enabled,
			'google_analytics'        	  => $google_analytics_ua,
			'mparticle_enabled' 		  => $mparticle_enabled,
			'mparticle_key'	  	  		  => $mparticle_key,
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
		if (beasleyAnalyticsConfigData.mparticle_enabled && beasleyAnalyticsConfigData.mparticle_key) {
			this.analyticsProviderArray.push(new beasleyAnalyticsMParticleProvider(beasleyAnalyticsConfigData));
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

	setAnalyticsForMParticle() {
		const provider = this.analyticsProviderArray.filter(provider => provider.analyticType === beasleyAnalyticsMParticleProvider.typeString);
		if (provider) {
			provider.setAnalytics.apply(provider, arguments);
		}
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

class beasleyAnalyticsMParticleProvider extends beasleyAnalyticsBaseProvider {
	static typeString = 'MPARTICLE';

	static GAtoMParticleFieldNameMap = {
		contentGroup1: 'show_name',
		contentGroup2: 'primary_category',
		dimension2: 'primary_author',
	}
	static cleanKeyValuePairs = {
		title: '',
		domain: '',
		call_sign: '',
		call_sign_id: '',
		beasley_event_id: '',
		primary_category: '',
		primary_category_id: '',
		show_name: '',
		show_id: '',
		tags: '',
		content_type: '',
		view_type: '',
		embedded_content_title: '',
		embedded_content_type: '',
		embedded_content_path: '',
		embedded_content_post_id: '',
		embedded_content_wp_author: '',
		embedded_content_primary_author: '',
		embedded_content_secondary_author: '',
		daypart: '',
		post_id: '',
		wp_author: '',
		primary_author: '',
		secondary_author: '',
		ad_block_enabled: '',
		ad_tags_enabled: '',
		consent_cookie: '',
		event_day_of_the_week: '',
		event_hour_of_the_day: '',
		prebid_enabled: '',
		platform: '',
		publish_date: '',
		publish_day_of_the_week: '',
		publish_hour_of_the_day: '',
		publish_month: '',
		publish_time_of_day: '',
		publish_timestamp_local: '',
		publish_timestamp_UTC: '',
		publish_year: '',
		section_name: '',
		video_count: '',
		word_count: '',
		categories_stringified: '',
		tags_stringified: '',
		referrer: '',
		UTM: '',
	};

	keyValuePairs;

	constructor(bbgiAnalyticsConfig) {
		super(beasleyAnalyticsMParticleProvider.typeString, bbgiAnalyticsConfig.mparticle_key);

		// Configures the SDK. Note the settings below for isDevelopmentMode
		// and logLevel.
		window.mParticle = {
			config: {
				appName: 'beasley_web',
				appVersion: '1',
				isDevelopmentMode: true,
				logLevel: 'verbose',
			},
		};
		(
			function(t){window.mParticle=window.mParticle||{};window.mParticle.EventType={Unknown:0,Navigation:1,Location:2,Search:3,Transaction:4,UserContent:5,UserPreference:6,Social:7,Other:8};window.mParticle.eCommerce={Cart:{}};window.mParticle.Identity={};window.mParticle.config=window.mParticle.config||{};window.mParticle.config.rq=[];window.mParticle.config.snippetVersion=2.3;window.mParticle.ready=function(t){window.mParticle.config.rq.push(t)};var e=["endSession","logError","logBaseEvent","logEvent","logForm","logLink","logPageView","setSessionAttribute","setAppName","setAppVersion","setOptOut","setPosition","startNewSession","startTrackingLocation","stopTrackingLocation"];var o=["setCurrencyCode","logCheckout"];var i=["identify","login","logout","modify"];e.forEach(function(t){window.mParticle[t]=n(t)});o.forEach(function(t){window.mParticle.eCommerce[t]=n(t,"eCommerce")});i.forEach(function(t){window.mParticle.Identity[t]=n(t,"Identity")});function n(e,o){return function(){if(o){e=o+"."+e}var t=Array.prototype.slice.call(arguments);t.unshift(e);window.mParticle.config.rq.push(t)}}var dpId,dpV,config=window.mParticle.config,env=config.isDevelopmentMode?1:0,dbUrl="?env="+env,dataPlan=window.mParticle.config.dataPlan;dataPlan&&(dpId=dataPlan.planId,dpV=dataPlan.planVersion,dpId&&(dpV&&(dpV<1||dpV>1e3)&&(dpV=null),dbUrl+="&plan_id="+dpId+(dpV?"&plan_version="+dpV:"")));var mp=document.createElement("script");mp.type="text/javascript";mp.async=true;mp.src=("https:"==document.location.protocol?"https://jssdkcdns":"http://jssdkcdn")+".mparticle.com/js/v2/"+t+"/mparticle.js" + dbUrl;var c=document.getElementsByTagName("script")[0];c.parentNode.insertBefore(mp,c)}
		)
			// Insert your API key below
			(bbgiAnalyticsConfig.mparticle_key);
	}

	clearKVPairs() {
		this.keyValuePairs = {...beasleyAnalyticsMParticleProvider.cleanKeyValuePairs};
	}

	createAnalytics() {
		// Call Super to log, but really we ignore the arguments since they were specific for GA V3
		super.createAnalytics.apply(this, arguments);
		this.clearKVPairs();
	}

	requireAnalytics() {
		super.requireAnalytics.apply(this, arguments);
	}

	setAnalytics() {
		super.setAnalytics.apply(this, arguments);

		if (arguments && arguments.length == 2) {
			if (Object.keys(this.keyValuePairs).includes(arguments[0])) {
				this.keyValuePairs[arguments[0]] = arguments[1];
			} else if (beasleyAnalyticsMParticleProvider.GAtoMParticleFieldNameMap[arguments[0]]) {
				const mparticleFieldName = beasleyAnalyticsMParticleProvider.GAtoMParticleFieldNameMap[arguments[0]];
				console.log(`Mapped GA Field Name '${arguments[0]} To MParticle Field Name Of '${mparticleFieldName}'` );
				this.keyValuePairs[mparticleFieldName] = arguments[1];
			} else {
				console.log(`MParticle Params Ignoring ${arguments[0]} of ${arguments[1]}`);
			}
		} else {
			console.log('Attempt to set MParticle Key Value Pair With Arguments NOT Of Length 2');
		}
	}

	sendEvent() {
		super.sendEvent.apply(this, arguments);

		if (arguments && arguments[0] && arguments[0].hitType === 'pageview') {
			window.mParticle.logPageView(
				'Page View',
				{page: window.location.toString()},
				this.keyValuePairs
			);
		} else {
			console.log('NOT A PAGEVIEW');
		}

		this.clearKVPairs();
	}
}

console.log('ga_enqueue_scripts loaded');

