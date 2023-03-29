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


class BeasleyAnalytics {
	BeasleyAnalyticsMParticleProvider = BeasleyAnalyticsMParticleProvider;

	analyticsProviderArray = [];

	static confirmLoaded() {
		console.log('Beasley Analytics Loaded');
	}

	constructor() {
		console.log('Constructing BeasleyAnalytics');
		this.loadBeasleyConfigData(window.bbgiAnalyticsConfig);
	}

	loadBeasleyConfigData = (beasleyAnalyticsConfigData) => {
		// guard to prevent multiple initial loads
		if (this.analyticsProviderArray.length > 0) {
			return;
		}

		if (beasleyAnalyticsConfigData.google_analytics_v3_enabled && beasleyAnalyticsConfigData.google_analytics) {
			this.analyticsProviderArray.push(new BeasleyAnalyticsGaV3Provider(beasleyAnalyticsConfigData));
		}
		if (beasleyAnalyticsConfigData.mparticle_enabled && beasleyAnalyticsConfigData.mparticle_key) {
			this.analyticsProviderArray.push(new BeasleyAnalyticsMParticleProvider(beasleyAnalyticsConfigData));
		}
	}

	createAnalytics() {
		this.analyticsProviderArray.map(provider => provider.createAnalytics.apply(provider, arguments));
	}

	requireAnalytics() {
		this.analyticsProviderArray.map(provider => provider.requireAnalytics.apply(provider, arguments));
	}

	setAnalytics() {
		this.analyticsProviderArray
			.filter(provider => provider.analyticType !== BeasleyAnalyticsMParticleProvider.typeString) // No Longer Set MParticle - Google Analytics Only
			.map(provider => provider.setAnalytics.apply(provider, arguments));
	}

	initializeMParticle() {
		const provider = this.analyticsProviderArray.find(provider => provider.analyticType === BeasleyAnalyticsMParticleProvider.typeString);
		if (provider) {
			provider.initialize.apply(provider, arguments);
		}
	}

	setAnalyticsForMParticle() {
		const provider = this.analyticsProviderArray.find(provider => provider.analyticType === BeasleyAnalyticsMParticleProvider.typeString);
		if (provider) {
			provider.setAnalytics.apply(provider, arguments);
		}
	}

	setMediaAnalyticsForMParticle() {
		const provider = this.analyticsProviderArray.find(provider => provider.analyticType === BeasleyAnalyticsMParticleProvider.typeString);
		if (provider) {
			provider.setMediaAnalytics.apply(provider, arguments);
		}
	}

	sendEvent() {
		// NO LONGER FORWARD EVENT TO MPARTICLE - MParticle Events Are Now Called Independently
		this.analyticsProviderArray.filter(provider => provider.analyticType !== BeasleyAnalyticsMParticleProvider.typeString)
			.map(provider => provider.sendEvent.apply(provider, arguments));
	}

	sendMParticleEvent(eventName) {
		const provider = this.analyticsProviderArray.find(provider => provider.analyticType === BeasleyAnalyticsMParticleProvider.typeString);
		if (provider) {
			provider.sendEventByName.apply(provider, arguments);
		}
	}

	sendMParticleLinkClickEvent(event) {
		const provider = this.analyticsProviderArray.find(provider => provider.analyticType === BeasleyAnalyticsMParticleProvider.typeString);
		if (provider) {
			provider.sendClickEvent.apply(provider, arguments);
		}
	}


	getMParticleMediaEventObject(eventName) {
		const provider = this.analyticsProviderArray.find(provider => provider.analyticType === BeasleyAnalyticsMParticleProvider.typeString);
		if (provider) {
			return provider.getMediaEventObject.apply(provider, arguments);
		}
	}

	fireLazyMParticlePageViewsForElementsWithMeta(elementList) {
		const provider = this.analyticsProviderArray.find(provider => provider.analyticType === BeasleyAnalyticsMParticleProvider.typeString);
		if (provider) {
			provider.fireLazyPageViewsForElementsWithMeta.apply(provider, arguments);
		}
	}

}

class BeasleyAnalyticsBaseProvider {
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

class BeasleyAnalyticsGaV3Provider extends BeasleyAnalyticsBaseProvider {
	static typeString = 'GA_V3';

	constructor(bbgiAnalyticsConfig) {
		super(BeasleyAnalyticsGaV3Provider.typeString, bbgiAnalyticsConfig.google_analytics);
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

class BeasleyAnalyticsMParticleProvider extends BeasleyAnalyticsBaseProvider {
	static typeString = 'MPARTICLE';
	static settingsFuncName = 'SETTINGS_FUNC';
	static mediaSettingsFuncName = 'MEDIA_SETTINGS_FUNC';
	static eventFuncName = 'EVENT_FUNC';
	static fireLazyPageViewFuncName = 'FIRE_LAZY_PAGE_VIEW_FUNC';

	static GAtoMParticleFieldNameMap = {
		contentGroup1: 'show_name',
		contentGroup2: 'primary_category',
		dimension2: 'primary_author',
	}
	static mparticleEventNames = {
		pageView: 'Page View',
		linkClicked: 'Link Clicked',
		searchedFor: 'Searched For',
		searchedResultClicked: 'Searched Result Clicked',
		formSubmitted: 'Form Submitted',
		shared: 'Shared',
		downloadedPodcast: 'Downloaded Podcast',
		mediaSessionStart: 'Media Session Start',
		play: 'Play',
		pause: 'Pause',
		mediaContentEnd: 'Media Content End',
		mediaSessionEnd: 'Media Session End',
		mediaSessionSummary: 'Media Session Summary',
	};

	queuedArgs = [];
	isInitialized = false;
	keyValuePairsTemplate;
	mediaSpecificKeyValuePairsTemplate;
	keyValuePairs;
	mediaSpecificKeyValuePairs;
	customEventTypeLookupByName;
	lazyPageEventObserver;

	getCleanEventObject(eventName, isIgnoringBuiltInMparticleFields, isIncludingOnlyMediaSpecificFields) {
		const dataPoints = window.mParticleSchema?.version_document?.data_points;
		if (dataPoints) {
			const dataPoint = dataPoints.find( dp =>
				(dp?.match?.type === 'screen_view' && dp?.match?.criteria?.screen_name === eventName) ||
				(dp?.match?.criteria?.event_name === eventName) );
			if (dataPoint) {
				const dataPointProperties = dataPoint.validator?.definition?.properties?.data?.properties?.custom_attributes?.properties;
				if (dataPointProperties) {
					const filteredKeys = Object.keys(dataPointProperties).filter(key =>
						((!isIgnoringBuiltInMparticleFields) || dataPointProperties[key].description !== 'MPARTICLE-FIELD-DO-NOT-POPULATE') &&
						((!isIncludingOnlyMediaSpecificFields) || dataPointProperties[key].description === 'MEDIA-SPECIFIC'));
					if ( filteredKeys && filteredKeys.length > 0) {
						const kvArray = filteredKeys.map(filteredKey => ({[filteredKey]: null}));
						return Object.assign(...kvArray); // Return an object with each field assigned to ''
					}
				}
			}
		}

		console.log(`WARNING - Could not create Key Value Pairs for ${isIncludingOnlyMediaSpecificFields ? 'Media Specific' : ''} MParticle Event - '${eventName}'`);
		return null;
	};

	getAllEventFieldsObjects(isMediaSpecific) {
		let retval = {};
		Object.keys(BeasleyAnalyticsMParticleProvider.mparticleEventNames).forEach(eventNameKey => {
			const newEventFieldsObject = this.getCleanEventObject(BeasleyAnalyticsMParticleProvider.mparticleEventNames[eventNameKey], isMediaSpecific, isMediaSpecific);
			retval = {...retval, ...newEventFieldsObject};
		});

		return retval;
	}

	getCustomEventTypeValueForEventName(eventName) {
		const dataPoints = window.mParticleSchema?.version_document?.data_points;
		if (dataPoints) {
			const dataPoint = dataPoints.find( dp =>
				(dp?.match?.type === 'custom_event' && dp?.match?.criteria?.event_name === eventName));
			if (dataPoint) {
				const dataPointType = dataPoint.match?.criteria?.custom_event_type;
				if (dataPointType) {
					const mParticleEventType = Object.entries(window.mParticle.EventType).find( kvpair => kvpair[0].toLowerCase() === dataPointType.toLowerCase());
					if (mParticleEventType) {
						return mParticleEventType[1];
					} else {
						console.log(`ERROR - could not find an MParticle Custom Event Type matching text - '${dataPointType}'`);
						return window.mParticle.EventType.Unknown;
					}
				}
			}
		}

		console.log(`Could not find Custom Event Type For MParticle Event - '${eventName}'`);
		return null;
	}
	getAllCustomEventTypeLookupObject() {
		const entryArray = Object.keys(BeasleyAnalyticsMParticleProvider.mparticleEventNames).map(eventNameKey => {
			return [BeasleyAnalyticsMParticleProvider.mparticleEventNames[eventNameKey], this.getCustomEventTypeValueForEventName(BeasleyAnalyticsMParticleProvider.mparticleEventNames[eventNameKey])];
		});
		return Object.fromEntries(entryArray);
	}

	// When Running React We Use Use MParticle "Self Hosting" Within Bundle. For Mobile App Pages, we need to include via JS Snippet
	includeMParticleSnippetIfMobileApp() {
		if (! window.isWhiz()) {
			console.log(`NOT Including MParticle via js since no 'whiz' param found and assuming we are using bundle.`);
			return;
		}
		console.log(`Including MParticle via js since 'whiz' param found and assuming we are not using bundle.`);

		// Configures the SDK. Note the settings below for isDevelopmentMode
		// and logLevel.
		window.mParticle = {
			config: window.bbgiAnalyticsConfig.mParticleConfig,
		};
		(
			function (t) {
				window.mParticle = window.mParticle || {};
				window.mParticle.EventType = {
					Unknown: 0,
					Navigation: 1,
					Location: 2,
					Search: 3,
					Transaction: 4,
					UserContent: 5,
					UserPreference: 6,
					Social: 7,
					Other: 8
				};
				window.mParticle.eCommerce = {Cart: {}};
				window.mParticle.Identity = {};
				window.mParticle.config = window.mParticle.config || {};
				window.mParticle.config.rq = [];
				window.mParticle.config.snippetVersion = 2.3;
				window.mParticle.ready = function (t) {
					window.mParticle.config.rq.push(t)
				};
				var e = ["endSession", "logError", "logBaseEvent", "logEvent", "logForm", "logLink", "logPageView", "setSessionAttribute", "setAppName", "setAppVersion", "setOptOut", "setPosition", "startNewSession", "startTrackingLocation", "stopTrackingLocation"];
				var o = ["setCurrencyCode", "logCheckout"];
				var i = ["identify", "login", "logout", "modify"];
				e.forEach(function (t) {
					window.mParticle[t] = n(t)
				});
				o.forEach(function (t) {
					window.mParticle.eCommerce[t] = n(t, "eCommerce")
				});
				i.forEach(function (t) {
					window.mParticle.Identity[t] = n(t, "Identity")
				});

				function n(e, o) {
					return function () {
						if (o) {
							e = o + "." + e
						}
						var t = Array.prototype.slice.call(arguments);
						t.unshift(e);
						window.mParticle.config.rq.push(t)
					}
				}

				var dpId, dpV, config = window.mParticle.config, env = config.isDevelopmentMode ? 1 : 0,
					dbUrl = "?env=" + env, dataPlan = window.mParticle.config.dataPlan;
				dataPlan && (dpId = dataPlan.planId, dpV = dataPlan.planVersion, dpId && (dpV && (dpV < 1 || dpV > 1e3) && (dpV = null), dbUrl += "&plan_id=" + dpId + (dpV ? "&plan_version=" + dpV : "")));
				var mp = document.createElement("script");
				mp.type = "text/javascript";
				mp.async = true;
				mp.src = ("https:" == document.location.protocol ? "https://jssdkcdns" : "http://jssdkcdn") + ".mparticle.com/js/v2/" + t + "/mparticle.js" + dbUrl;
				var c = document.getElementsByTagName("script")[0];
				c.parentNode.insertBefore(mp, c)
			}
		)
			// Insert your API key below
			(bbgiAnalyticsConfig.mparticle_key);

		this.initialize();
	}

	constructor(bbgiAnalyticsConfig) {
		super(BeasleyAnalyticsMParticleProvider.typeString, bbgiAnalyticsConfig.mparticle_key);

		// For Mobile App Pages (ie Whiz) include MParticle via js
		this.includeMParticleSnippetIfMobileApp();
	}

	initialize() {
		super.debugLog('Initializing Beasley Analytics mParticle Variables.');
		window.mparticleEventNames = BeasleyAnalyticsMParticleProvider.mparticleEventNames;
		this.createKeyValuePairs();
		this.createMediaKeyValuePairs();
		this.customEventTypeLookupByName = this.getAllCustomEventTypeLookupObject();

		this.CompleteInitializationAndSetPerSessionKeys();
	}

	createKeyValuePairs() {
		this.keyValuePairsTemplate = this.getAllEventFieldsObjects(false);
		this.clearKeyValuePairs();
	}

	clearKeyValuePairs() {
		this.keyValuePairs = {...this.keyValuePairsTemplate};
	}

	createMediaKeyValuePairs() {
		this.mediaSpecificKeyValuePairsTemplate = this.getAllEventFieldsObjects(true);
		this.clearMediaKeyValuePairs();
	}

	clearMediaKeyValuePairs() {
		this.mediaSpecificKeyValuePairs = {...this.mediaSpecificKeyValuePairsTemplate};
	}

	CompleteInitializationAndSetPerSessionKeys = () => {
		// Set Global Fields In Callback After Ad Blocker Detection Completes And Empty Process Queue

		const handleAdBlockFunc = () => {
			adblockDetect((isBlockingAds) => {
				super.debugLog('Beasley Analytics mParticle Was Initialized. Now Processing...');
				this.isInitialized = true;

				this.setAnalytics('ad_block_enabled', isBlockingAds);
				this.setAnalytics('domain', window.location.hostname);
				this.setAnalytics('platform', 'Web');

				this.processAnyQueuedCalls();
				removeEventListener("DOMContentLoaded", handleAdBlockFunc);
			});
		};

		if (window.isWhiz() && document.readyState !== 'complete') {
			addEventListener("DOMContentLoaded", handleAdBlockFunc);
		} else {
			handleAdBlockFunc();
		}
	};

	processAnyQueuedCalls() {
		// Process Any Queued Event Args
		if (this.queuedArgs.length > 0) {
			this.queuedArgs.forEach(eventArg => {
				if (eventArg.funcName === BeasleyAnalyticsMParticleProvider.settingsFuncName) {
					this.setAnalytics.apply(this, eventArg.args);
				} else if (eventArg.funcName === BeasleyAnalyticsMParticleProvider.mediaSettingsFuncName) {
					this.setMediaAnalytics().apply(this, eventArg.args);
				}
				else if (eventArg.funcName === BeasleyAnalyticsMParticleProvider.eventFuncName) {
					this.sendEventByName.apply(this, eventArg.args);
				} else if (eventArg.funcName === BeasleyAnalyticsMParticleProvider.fireLazyPageViewFuncName) {
					this.fireLazyPageViewsForElementsWithMeta.apply(this, eventArg.args);
				}
			});
			this.queuedArgs = [];
		}
	}

	setPerEventKeys() {
		this.setAnalytics('beasley_event_id', window.createUUID());
		const currentDateTime = new Date();
		const hourOfDay = currentDateTime.getHours() || 0;
		this.setAnalytics('event_day_of_the_week', currentDateTime.toLocaleDateString(undefined, { weekday: 'long' }));
		this.setAnalytics('event_hour_of_the_day', hourOfDay.toString().padStart(2, '0'));
		this.setAnalytics('daypart', window.getDayPart(hourOfDay));
	}

	createAnalytics() {
		// Call Super to log, but really we ignore the arguments since they were specific for GA V3
		super.createAnalytics.apply(this, arguments);
	}

	requireAnalytics() {
		super.requireAnalytics.apply(this, arguments);
	}

	setAnalytics() {
		super.setAnalytics.apply(this, arguments);

		if (!this.isInitialized) {
			this.queuedArgs.push( {funcName: BeasleyAnalyticsMParticleProvider.settingsFuncName, args: arguments} );
			return;
		}

		if (arguments && arguments.length === 2) {
			if (Object.keys(this.keyValuePairs).includes(arguments[0])) {
				this.keyValuePairs[arguments[0]] = arguments[1];
			} else {
				console.error(`MParticle Params Ignoring ${arguments[0]} of ${arguments[1]}`);
			}
		} else {
			console.error(`Attempt to set MParticle Key Value Pair With Arguments NOT Of Length 2 - '${arguments}'`, arguments);
		}
	}

	setMediaAnalytics() {
		if (!this.isInitialized) {
			this.queuedArgs.push( {funcName: BeasleyAnalyticsMParticleProvider.mediaSettingsFuncName, args: arguments} );
			return;
		}

		if (arguments && arguments.length === 2) {
			if (Object.keys(this.mediaSpecificKeyValuePairs).includes(arguments[0])) {
				this.mediaSpecificKeyValuePairs[arguments[0]] = arguments[1];
			} else {
				console.error(`MParticle Media Params Ignoring ${arguments[0]} of ${arguments[1]}`);
			}
		} else {
			console.error(`Attempt to set MParticle Media Key Value Pair With Arguments NOT Of Length 2 - '${arguments}'`, arguments);
		}
	}

	sendEvent() {
		super.sendEvent.apply(this, arguments);

		if (arguments && arguments[0] && arguments[0].hitType === 'pageview') {
			this.sendEventByName(BeasleyAnalyticsMParticleProvider.mparticleEventNames.pageView);
		} else {
			console.log(`ATTEMPTED TO SEND A COMMON EVENT TO MPARTICLE WHICH IS NOT A PAGEVIEW - '${arguments[0]?.hitType}'`);
		}
	}

	getEventObject(eventName, isIgnoringBuiltInMparticleFields = false, isIncludingOnlyMediaSpecificFields = false) {
		const emptyEventObject = this.getCleanEventObject(eventName, isIgnoringBuiltInMparticleFields, isIncludingOnlyMediaSpecificFields);
		return Object.keys(emptyEventObject)
			.reduce((a, key) => ({ ...a, [key]: this.keyValuePairs[key]}), {});
	}

	getMediaEventObject(eventName) {
		const eventPopulatedWithCommonFields = this.getEventObject(eventName, true, false);
		const mediaSpecificKeysArray = Object.keys(this.mediaSpecificKeyValuePairs);
		return Object.keys(eventPopulatedWithCommonFields)
			.reduce((a, key) => ({ ...a, [key]: mediaSpecificKeysArray.includes(key) ? this.mediaSpecificKeyValuePairs[key] : eventPopulatedWithCommonFields[key]}), {});
	}

	sendEventByName(eventName) {
		super.sendEvent.apply(this, arguments);

		if (!this.isInitialized) {
			this.queuedArgs.push( {funcName: BeasleyAnalyticsMParticleProvider.eventFuncName, args: arguments} );
			return;
		}

		this.doSendEventByName(eventName);
	}

	doSendEventByName(eventName) {
		this.setPerEventKeys();

		// If The Event Is A Page View
		if (eventName === BeasleyAnalyticsMParticleProvider.mparticleEventNames.pageView) {
			const objectToSend = this.getEventObject(BeasleyAnalyticsMParticleProvider.mparticleEventNames.pageView);

			window.mParticle.logPageView(
				'Page View',
				objectToSend,
			);
		} else { // Event is a Custom Event
			const objectToSend = this.getEventObject(eventName);
			const customEventType = this.customEventTypeLookupByName[eventName];

			super.debugLog(`Beasley Analytics is queueing '${customEventType}' Event`);
			window.mParticle.logEvent(
				eventName,
				customEventType,
				objectToSend,
			);
		}
	}

	sendClickEvent(ev) {
		const clickSrcElement = ev.target;

		this.setAnalytics('container_id', '%s');
		this.setAnalytics('link_name', clickSrcElement.ariaLabel);
		this.setAnalytics('link_text', clickSrcElement.innerText);
		this.setAnalytics('link_url', clickSrcElement.href);

		// Fire Link Clicked If Not A Search Result
		if (!this.searchResultClick(clickSrcElement)) {
			this.sendEventByName(
				BeasleyAnalyticsMParticleProvider.mparticleEventNames.linkClicked,
			);
		}
	}

	searchResultClick(clickSrcElement) {
		let searchElementIndex = 0;

		let postElement;
		const grandParentElement = clickSrcElement?.parentElement?.parentElement;
		if ( grandParentElement?.parentElement?.classList?.contains('search-results') ) {
			postElement = grandParentElement;
		} else if ( grandParentElement?.parentElement?.parentElement?.parentElement?.classList?.contains('search-results') ) {
			postElement = grandParentElement.parentElement.parentElement;
		}

		if (postElement) {
			searchElementIndex = Array.from( postElement.parentElement.children ).indexOf(postElement) + 1;
			this.setAnalytics('search_term_position', searchElementIndex);
			this.sendEventByName(
				BeasleyAnalyticsMParticleProvider.mparticleEventNames.searchedResultClicked,
			);
		}

		return searchElementIndex;
	}

	fireLazyPageViewsForElementsWithMeta(elementList) {
		if (!this.isInitialized) {
			this.queuedArgs.push( {funcName: BeasleyAnalyticsMParticleProvider.fireLazyPageViewFuncName, args: arguments} );
			return;
		}
		this.doFireLazyPageViewsForElementsWithMeta(elementList);
	}

	doFireLazyPageViewsForElementsWithMeta(elementList) {
		const onIntersection = (entries) => {
			for (const entry of entries) {
				if (entry.isIntersecting) {
					super.debugLog(entry);
					Array.prototype.slice.call(entry.target.attributes).forEach((item) => {
						this.setAnalytics(item.name.replace('data-', ''), item.value);
					});
					this.sendEventByName(BeasleyAnalyticsMParticleProvider.mparticleEventNames.pageView);
					this.lazyPageEventObserver.unobserve(entry.target);
				}
			}
		};

		if (this.lazyPageEventObserver) {
			this.lazyPageEventObserver.disconnect();
		} else {
			this.lazyPageEventObserver = new IntersectionObserver(onIntersection);
		}

		if (elementList) {
			Array.from(elementList).forEach(el => this.lazyPageEventObserver.observe(el));
		}
	}
}

BeasleyAnalytics.confirmLoaded();

