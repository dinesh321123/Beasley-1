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

	static getMParticleDevConfig(versionNo) {
		// WITHOUT CNAMES $jsonMParticleConfig = '{"isDevelopmentMode": true, "logLevel": "verbose", "dataPlan": {"planId": "beasley_web_beta_3", "planVersion": 1}}'
		return {
			isDevelopmentMode: true,
			logLevel: "verbose",
			dataPlan: {planId: "beasley_web", "planVersion": versionNo},
			v1SecureServiceUrl: "mparticle.bbgi.com/webevents/v1/JS/",
			v2SecureServiceUrl: "mparticle.bbgi.com/webevents/v2/JS/",
			v3SecureServiceUrl: "mparticle.bbgi.com/webevents/v3/JS/",
			configUrl: "mparticle.bbgi.com/tags/JS/v2/",
			identityUrl: "mparticle.bbgi.com/identity/v1/",
			aliasUrl: "mparticle.bbgi.com/webevents/v1/identity/",
			identityCallback: (result) => {
				// Do something once an identity call has been made.
				// For more information, see https://docs.mparticle.com/developers/sdk/web/idsync/#sdk-initialization-and-identify
				console.log('MPARTICLE IDENTITY CALLBACK: ', result);
			},
		};
	}

	static getMParticleProdConfig(versionNo) {
		return {
			isDevelopmentMode: false,
			logLevel: "verbose",
			dataPlan: {planId: "beasley_web", "planVersion": versionNo},
			v1SecureServiceUrl: "mparticle.bbgi.com/webevents/v1/JS/",
			v2SecureServiceUrl: "mparticle.bbgi.com/webevents/v2/JS/",
			v3SecureServiceUrl: "mparticle.bbgi.com/webevents/v3/JS/",
			configUrl: "mparticle.bbgi.com/tags/JS/v2/",
			identityUrl: "mparticle.bbgi.com/identity/v1/",
			aliasUrl: "mparticle.bbgi.com/webevents/v1/identity/",
			identityCallback: (result) => {
				// Do something once an identity call has been made.
				// For more information, see https://docs.mparticle.com/developers/sdk/web/idsync/#sdk-initialization-and-identify
				console.log('MPARTICLE IDENTITY CALLBACK: ', result);
			},
		};
	}

	static getMParticleConfig() {
		const isDevEnvironment = window.isDevEnvironment();

		console.log(`Returning mParticle config for ${isDevEnvironment ? 'Dev' : 'Prod'} Environment`);

		const versionNo = mParticlePlan?.version;
		console.log(`Using plan version ${versionNo} based on cached plan`);
		const retval = isDevEnvironment ? BeasleyAnalytics.getMParticleDevConfig(versionNo) : BeasleyAnalytics.getMParticleProdConfig(versionNo);

		// If window.firebase User Exists, Add mParticle identifyRequest
		if (window.firebase?.auth().currentUser) {
			console.log(`Augmenting mParticle Configuration with window.firebase User: ${window.firebase.auth().currentUser.email}`);
			retval.identifyRequest = {
				userIdentities: {
					customerid: window.firebase.auth().currentUser.uid,
					email: window.firebase.auth().currentUser.email.toLowerCase(),
				}
			};
		}

		return retval;
	}

	constructor() {
		console.log('Constructing BeasleyAnalytics');

		window.bbgiAnalyticsConfig.mParticleConfig = BeasleyAnalytics.getMParticleConfig();
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

	setNewsletterControlForMParticleAccount(contentElement) {
		const provider = this.analyticsProviderArray.find(provider => provider.analyticType === BeasleyAnalyticsMParticleProvider.typeString);
		if (provider) {
			provider.setNewsletterControlForMParticleAccount.apply(provider, arguments);
		}
	}

	setMParticleAppBoyForwarderForDOB() {
		const provider = this.analyticsProviderArray.find(provider => provider.analyticType === BeasleyAnalyticsMParticleProvider.typeString);
		if (provider) {
			provider.setAppBoyForwarderForDOB.apply(provider, arguments);
		}
	}

	setMParticleUserAttributeArray(userEmailAddress, emailIsMaskedMParticleVal, userAttributeArray) {
		const provider = this.analyticsProviderArray.find(provider => provider.analyticType === BeasleyAnalyticsMParticleProvider.typeString);
		if (provider) {
			provider.setMParticleUserAttributeArray.apply(provider, arguments);
		}
	}

	sendMParticleEvent(eventName) {
		const provider = this.analyticsProviderArray.find(provider => provider.analyticType === BeasleyAnalyticsMParticleProvider.typeString);
		if (provider) {
			provider.sendEventByName.apply(provider, arguments);
		}
	}

	sendMParticleErrorEvent(errorClass, errorNumber, errorName, source, message) {
		const provider = this.analyticsProviderArray.find(provider => provider.analyticType === BeasleyAnalyticsMParticleProvider.typeString);
		if (provider) {
			provider.sendErrorEvent.apply(provider, arguments);
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

	getMParticleAllMediaFields() {
		const provider = this.analyticsProviderArray.find(provider => provider.analyticType === BeasleyAnalyticsMParticleProvider.typeString);
		if (provider) {
			return provider.getAllMediaFields.apply(provider, arguments);
		}
	}

	setMParticlePerEventKeys() {
		const provider = this.analyticsProviderArray.find(provider => provider.analyticType === BeasleyAnalyticsMParticleProvider.typeString);
		if (provider) {
			return provider.setPerEventKeys.apply(provider, arguments);
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
		error: 'Error',
	};

	static mparticleErrorClasses = {
		General: 'General',
		mParticle: 'mParticle',
	}
	static mparticleErrorCodes = {
		ImproperJSONStringifiedArray: { number: 101, name:'Improper JSON Stringified Array' },
		UnableToSendPageViewEvent: { number: 102, name:'Unable To Send Page View Event' },
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
		const dataPoints = mParticlePlan?.dataPointArray;
		if (dataPoints) {
			const dataPoint = dataPoints.find( dp => dp.eventName === eventName );
			if (dataPoint) {
				const dataPointProperties = dataPoint.properties;
				if (dataPointProperties) {
					const filteredProps = dataPointProperties.filter(prop =>
						((!isIgnoringBuiltInMparticleFields) || prop.propertyDescription !== 'MPARTICLE-FIELD-DO-NOT-POPULATE') &&
						((!isIncludingOnlyMediaSpecificFields) || prop.propertyDescription === 'MEDIA-SPECIFIC' || prop.propertyDescription === 'MPARTICLE-FIELD-DO-NOT-POPULATE'));
					if ( filteredProps && filteredProps.length > 0) {
						const kvArray = filteredProps.map(filteredProp => ({[filteredProp.propertyName]: null}));
						return Object.assign(...kvArray); // Return an object with each field assigned to ''
					} else {
						if (!isIncludingOnlyMediaSpecificFields) {
							console.log(`WARNING - No Matching DataPoint Properties in Schema File. Could not create MParticle Event - '${eventName}'`);
						}
					}
				}
			} else {
				if (eventName !== 'Page View') {
					console.log(`WARNING - No Matching DataPoint in Schema File. Could not create MParticle Event - '${eventName}'`);
				}
			}
		} else {
			console.log(`WARNING - No DataPoints in Schema File. Could not create MParticle Event - '${eventName}'`);
		}

		return null;
	};

	getCustomEventTypeValueForEventName(eventName) {
		const dataPoints = mParticlePlan?.dataPointArray;
		if (dataPoints) {
			/* We need to filter because event names are not unique.
				For instance, there are 2 play events: one of type media and one of type other.
				This was done in order to share the play event between React pages, which have access to the
				media library, and Whiz pages which do not.
				We return first matched event type which means Media events must be above non-media events
				in the plan.
			 */
			const matchingDataPoints = dataPoints.filter( dp => dp.eventName === eventName);
			for (const dataPoint of matchingDataPoints) {
				const dataPointType = dataPoint.eventType;
				if (dataPointType) {
					const mParticleEventType = Object.entries(window.mParticle.EventType).find( kvpair => kvpair[0].toLowerCase() === dataPointType.toLowerCase());
					if (mParticleEventType) {
						return mParticleEventType[1];
					} else {
						console.log(`ERROR - could not find an MParticle Custom Event Type matching text - '${dataPointType}'`);
					}
				}
			}
			return window.mParticle.EventType.Unknown;
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
				// Without CNAMES mp.src = ("https:" == document.location.protocol ? "https://jssdkcdns" : "http://jssdkcdn") + ".mparticle.com/js/v2/" + t + "/mparticle.js" + dbUrl;
				mp.src = "https://mparticle.bbgi.com/tags/JS/v2/" + t + "/mparticle.js" + dbUrl;
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

		this.completeInitializationAndSetPerSessionKeys();
	}

	createKeyValuePairs() {
		this.keyValuePairsTemplate = mParticlePlan?.allFields;
		this.clearKeyValuePairs();
	}

	clearKeyValuePairs() {
		this.keyValuePairs = {...this.keyValuePairsTemplate};
	}

	createMediaKeyValuePairs() {
		this.mediaSpecificKeyValuePairsTemplate = mParticlePlan?.mediaFields;
		this.clearMediaKeyValuePairs();
	}

	clearMediaKeyValuePairs() {
		this.mediaSpecificKeyValuePairs = {...this.mediaSpecificKeyValuePairsTemplate};
	}

	completeInitializationAndSetPerSessionKeys = () => {
		// Set Global Fields In Callback After Ad Blocker Detection Completes And Empty Process Queue

		const handleAdBlockFunc = () => {
			adblockDetect((isBlockingAds) => {
				super.debugLog('Beasley Analytics mParticle Was Initialized. Now Processing...');
				this.isInitialized = true;

				this.setAnalytics('ad_block_enabled', isBlockingAds);
				this.setAnalytics('domain', window.location.hostname);
				this.setAnalytics('platform', 'Web');
				this.setAnalytics('is_app', window.isWhiz());
				this.setAnalytics('station_formats', window.bbgiconfig?.publisher?.genre?.join(', '));
				this.setAnalytics('station_location', window.bbgiconfig?.publisher?.location);
				this.setAnalytics('call_letters', window.getCallLetters());
				this.setAnalytics('station_id', window.bbgiconfig?.publisher?.AppId);
				this.setAnalytics('prebid_enabled', window.bbgiconfig?.prebid_enabled);

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

	getUnstrippedEventObject(eventName, isIgnoringBuiltInMparticleFields, isIncludingOnlyMediaSpecificFields) {
		const emptyEventObject = this.getCleanEventObject(eventName, isIgnoringBuiltInMparticleFields, isIncludingOnlyMediaSpecificFields);
		return Object.keys(emptyEventObject)
			.reduce((a, key) => ({ ...a, [key]: this.keyValuePairs[key]}), {});
	}

	getEventObject(eventName, isIgnoringBuiltInMparticleFields = false, isIncludingOnlyMediaSpecificFields = false) {
		const populatedObj = this.getUnstrippedEventObject(eventName, isIgnoringBuiltInMparticleFields, isIncludingOnlyMediaSpecificFields);
		return this.stripPlaceholdersFromObject(populatedObj);
	}

	getMediaEventObject(eventName) {
		const eventPopulatedWithCommonFields = this.getUnstrippedEventObject(eventName, true, false);
		const mediaSpecificKeysArray = Object.keys(this.mediaSpecificKeyValuePairs);
		const populatedObj = Object.keys(eventPopulatedWithCommonFields)
			.reduce((a, key) => ({ ...a, [key]: mediaSpecificKeysArray.includes(key) ? this.mediaSpecificKeyValuePairs[key] : eventPopulatedWithCommonFields[key]}), {});
		return this.stripPlaceholdersFromObject(populatedObj);
	}

	stripPlaceholdersFromObject(objToStrip) {
		return Object.keys(objToStrip)
			.reduce((a, key) => {
					const keyVal = objToStrip[key];
					const keyValString = keyVal ? keyVal.toString() : '';
					if ( keyVal === false ||
						( keyVal &&
						  keyValString.trim().toLowerCase() !== 'null' &&
						  !(keyValString.startsWith('?') && keyValString.endsWith('?'))
						)
					){
						return ({ ...a, [key]: keyVal})
					} else {
						return a;
					}
				},
				{}
			);
	}

	getAllMediaFields() {
		return this.mediaSpecificKeyValuePairs;
	}

	sendErrorEvent(errorClass, errorNumber, errorName, source, message) {
		this.setAnalytics('error_class', errorClass);
		this.setAnalytics('error_code_number', errorNumber);
		this.setAnalytics('error_code_name', errorName);
		this.setAnalytics('error_source', source);
		this.setAnalytics('error_message', message);
		this.sendEventByName(BeasleyAnalyticsMParticleProvider.mparticleEventNames.error);
	}

	sendEventByName(eventName) {
		super.sendEvent.apply(this, arguments);

		if (!this.isInitialized) {
			this.queuedArgs.push( {funcName: BeasleyAnalyticsMParticleProvider.eventFuncName, args: arguments} );
			return;
		}

		this.doSendEventByName(eventName);
	}

	isValidStringifiedJSONArray(str) {
		try {
			const stringifiedJSONArrayRegex =  /^\s*\[\s*"(?:[^"\\]|\\.)*"(?:\s*,\s*"(?:[^"\\]|\\.)*")*\s*\]\s*$/; // Regex pattern for stringified JSON array
			const trimmedStr = str.trim(); // Remove leading/trailing whitespace

			if (!stringifiedJSONArrayRegex.test(trimmedStr)) {
				return false; // String does not match the pattern
			}

			JSON.parse(trimmedStr); // Try parsing the stringified JSON array

			return true; // Parsing successful, valid stringified JSON array
		} catch (error) {
			return false; // Parsing failed, not a valid stringified JSON array
		}
	}

	validateStringFieldsWithSquareBracesAsValidStringifiedJSONArray(objectToValidate) {
		for(const key in objectToValidate){
			const val = objectToValidate[key];
			if ((typeof val === 'string' || val instanceof String) &&
				val.length > 1 &&
				val.indexOf('[') === 0 &&
				val.indexOf(']') === val.length - 1
			) {
				if ( ! this.isValidStringifiedJSONArray(val) ) {
					const message = `Invalid JSON on Beasley Event '${objectToValidate.beasley_event_id}' field name: '${key}' field value: '${val}'`;
					this.sendErrorEvent(
						BeasleyAnalyticsMParticleProvider.mparticleErrorClasses.mParticle,
						BeasleyAnalyticsMParticleProvider.mparticleErrorCodes.ImproperJSONStringifiedArray.number,
						BeasleyAnalyticsMParticleProvider.mparticleErrorCodes.ImproperJSONStringifiedArray.name,
						window.location.href,
						message
					);
				}
			}
		}
	}

	// doSendPageEvent interrogates the DOM and must be called only after entire page is loaded
	doSendPageEvent() {
		// Set ad_tags_enabled
		const adTagsEnabled = !!(
			document.body.parentElement.innerHTML.includes('dfp-slot') ||
			document.body.parentElement.innerHTML.includes('placeholder-dfp')
		);
		this.setAnalytics('ad_tags_enabled', adTagsEnabled);

		const objectToSend = this.getEventObject(BeasleyAnalyticsMParticleProvider.mparticleEventNames.pageView);

		this.validateStringFieldsWithSquareBracesAsValidStringifiedJSONArray(objectToSend);

		// Set embedded_content_is_nested
		if ( objectToSend.view_type === 'embedded_content' ) {
			objectToSend.embedded_content_is_nested = ( objectToSend.embedded_content_id === objectToSend.post_id );
		}

		console.log(`CATEGORIES STRINGIFIED -> ${objectToSend.categories_stringified}`);
		window.mParticle.logPageView(
			'Page View',
			objectToSend,
		);
	}

	doSendEventByName(eventName) {
		this.setPerEventKeys();

		// If The Event Is A Page View
		if (eventName === BeasleyAnalyticsMParticleProvider.mparticleEventNames.pageView) {
			this.doSendPageEvent();
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

	// We eventually need to compute fields for both Click Events and Form Submitted Events
	// Currently this function is called only for Click Events with a parameter of an A Tag
	populateMParticleModuleFields(domElement) {
		this.setAnalytics('container_id', '?container_id?');
		this.setAnalytics('module_type', '?module_type?');
		this.setAnalytics('module_name', '?module_name?');
		this.setAnalytics('module_position', '?module_position?');
		this.setAnalytics('module_element_num', '?module_element_num?');
		this.setAnalytics('screen_position', '?screen_position?'); // For Link Click Only
		this.setAnalytics('module_screen_position', '?module_screen_position?'); // For Form Submitted Only
	}

	sendClickEvent(targetElement) {
		// Find the A tag for only A tags, Lazy Images, SVG, or Path tags themselves
		if (targetElement?.tagName !== 'A' &&
			targetElement?.className !== 'lazy-image' &&
			targetElement?.tagName !== 'svg' &&
			targetElement?.tagName !== 'path') {
			return;
		}
 		while (targetElement && targetElement.tagName !== 'A') {
			targetElement = targetElement.parentElement;
		}
		if (!targetElement) {
			return;
		}

		// Fire Link Clicked If Not A Search Result And Not A Podcast Download
		if (!this.searchResultClick(targetElement) && !this.downloadPodcastClick(targetElement)) {
			this.setAnalytics('link_name', targetElement.ariaLabel);
			this.setAnalytics('link_text', targetElement.innerText);
			this.setAnalytics('link_url', targetElement.href);
			this.populateMParticleModuleFields(targetElement);
			this.sendEventByName(
				BeasleyAnalyticsMParticleProvider.mparticleEventNames.linkClicked,
			);
		}
	}

	// Return non-Zero Index If targetElement is a Search Result
	searchResultClick(targetElement) {
		let searchElementIndex = 0;
		let domElement = targetElement;

		while (domElement && !domElement.classList.contains('search-result')) {
			domElement = domElement.parentElement;
		}

		if (domElement) {
			searchElementIndex = Array.from( document.querySelectorAll('.search-result') ).indexOf(domElement) + 1;
			this.setAnalytics('search_term_position', searchElementIndex);
			this.setAnalytics('search_term_selected', domElement.attributes['data-search-result-slug']?.value);
			this.sendEventByName(
				BeasleyAnalyticsMParticleProvider.mparticleEventNames.searchedResultClicked,
			);
		}

		return searchElementIndex;
	}

	// Return whether targetElement is a Podcast Download link
	downloadPodcastClick(targetElement) {
		let retval = false;
		let domElement = targetElement;

		if (domElement.classList.contains('is-podcast-download-link')) {
			retval = true;
			this.setAnalytics('podcast_name', domElement.attributes['data-podcast-name']?.value);
			this.setAnalytics('episode_title', domElement.attributes['data-episode-title']?.value);
			this.sendEventByName(
				BeasleyAnalyticsMParticleProvider.mparticleEventNames.downloadedPodcast,
			);
		}

		return retval;
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

	maskEmail(email) {
		// Split the email address into username and domain parts
		const [username, domain] = email.split("@");

		// Determine the number of asterisks to show in the masked username
		const numAsterisks = Math.min(5, Math.max(0, username.length - 5));

		// Mask the username with asterisks, leaving only the first and last character visible
		const maskedUsername =
			username.length <= 1
				? username
				: `${username.charAt(0)}${"*".repeat(numAsterisks)}${username.charAt(username.length - 1)}`;

		// Combine the masked username and the domain to form the masked email address
		const maskedEmail = `${maskedUsername}@${domain}`;

		return maskedEmail;
	}

	getFirstNameFromDisplayName(displayName) {
		let retval = '';
		if (displayName) {
			displayName = displayName.trim();
			const nameParts = displayName.split(' ');
			if (nameParts && nameParts.length > 0) {
				retval = nameParts[0];
			}
		}
		return retval;
	}

	getLastNameFromDisplayName(displayName) {
		let retval = '';
		if (displayName) {
			displayName = displayName.trim();
			const idxOfFirstSpace = displayName.indexOf(' ');
			if (idxOfFirstSpace > 0) {
				retval = displayName.toString().substring(idxOfFirstSpace + 1);
			}
		}
		return retval;
	}

	setNewsletterControlForMParticleAccount(contentElement) {
		const nsfContainerElementCollection = contentElement.getElementsByClassName('nsf-container');
		if (!nsfContainerElementCollection || nsfContainerElementCollection.length < 1) {
			return;
		}

		// ASSUME ONLY 1 Newsletter Control
		const nsfContainerElement = nsfContainerElementCollection[0];
		const newsletterFieldName = nsfContainerElement?.querySelector('#nsf_subscription_attributes')?.value;

		const newsletterFNameElement = nsfContainerElement?.querySelector('input[name="nsf_first_name"]');
		const newsletterLNameElement = nsfContainerElement?.querySelector('input[name="nsf_last_name"]');
		const newsletterEmailElement = nsfContainerElement?.querySelector('input[name="nsf_email"]');

		if (newsletterFNameElement) {
			newsletterFNameElement.disabled = false;
		}
		if (newsletterFNameElement) {
			newsletterLNameElement.disabled = false;
		}
		if (newsletterFNameElement) {
			newsletterEmailElement.disabled = false;
		}

		if (
			window.mParticle.Identity &&
			window.mParticle.Identity.getCurrentUser && // Not a function in Whiz
			window.mParticle.Identity.getCurrentUser() &&
			window.mParticle.Identity.getCurrentUser().isLoggedIn()
		) {
			const mParticleUser = window.mParticle.Identity.getCurrentUser();
			const mParticleUserIdentities = mParticleUser?.getUserIdentities()?.userIdentities;
			const mParticleUserAttributes = mParticleUser?.getAllUserAttributes();

			// Hide Newsletter Control And Exit If The mParticle User Already Has A Truthy Value For Newsletter
			if (mParticleUserAttributes && mParticleUserAttributes[newsletterFieldName]) {
				nsfContainerElement.style.display = "none";
				return;
			}

			if (newsletterFNameElement) {
				const firstName = mParticleUserAttributes['$firstname'] ||
					this.getFirstNameFromDisplayName(window.firebase?.auth()?.currentUser?.displayName);
				if (firstName) {
					newsletterFNameElement.value = firstName;
					newsletterFNameElement.disabled = true;
				}
			}
			if (newsletterLNameElement) {
				const lastName = mParticleUserAttributes['$lastname'] ||
					this.getLastNameFromDisplayName(window.firebase?.auth()?.currentUser?.displayName);
				if (lastName) {
					newsletterLNameElement.value = lastName;
					newsletterLNameElement.disabled = true;
				}
			}
			if (newsletterEmailElement) {
				newsletterEmailElement.value = this.maskEmail(mParticleUserIdentities['email']);
				newsletterEmailElement.disabled = true;
			}
		}
	}

	initializeUserAttribute(mParticleUser, attributeName, attributeValue) {
		const mParticleUserAttributes = mParticleUser?.getAllUserAttributes();
		if (mParticleUser && attributeName && !mParticleUserAttributes[attributeName]) {
			mParticleUser.setUserAttribute(attributeName, attributeValue);
			console.log(`Set ${attributeName} to ${attributeValue} for mParticle user`);
		} else {
			console.log(`Not Setting ${attributeName} to ${attributeValue} for mParticle user because it is already set to ${mParticleUserAttributes[attributeName]}`);
		}
	}

	initializeUserAttributes(mParticleUser, userAttributeArray) {
		userAttributeArray.forEach(attr =>  this.initializeUserAttribute(mParticleUser, attr.attributeName, attr.attributeValue));
	}

	appboyWasSetForDOB = false;

	setAppBoyForwarderForDOB() {
		const appBoyForwarder = mParticle?.Store?.activeForwarders?.find(forwarderItem => forwarderItem.name === 'Appboy');
		if (appBoyForwarder && !this.appboyWasSetForDOB) {
			this.appboyWasSetForDOB = true;
			const funcCopy = appBoyForwarder.setUserAttribute

			const newFunc = (key, value) => {
				if (key === 'dob' && !(value instanceof Date)) {
					// ASSUME value is a string in YYYY-MM-DD format
					value = new Date(`${value}T00:00:00`);
				}
				return funcCopy(key, value);
			}
			appBoyForwarder.setUserAttribute = newFunc;
		}
	}

	/*
	Set an array of {attributeName: 'name', attributeValue: 'val'} objects.
	Complexity arose when we mask emails in UI. For this case we set the emailIsMaskedMParticleVal flag.
	 */
	setMParticleUserAttributeArray(userEmailAddress, emailIsMaskedMParticleVal, userAttributeArray) {
		this.setAppBoyForwarderForDOB();

		// Get Logged In mParticle User If Exists
		let mParticleUser;
		let mParticleUserIdentities;

		if (
			window.mParticle.Identity &&
			window.mParticle.Identity.getCurrentUser && // Not a function in Whiz
			window.mParticle.Identity.getCurrentUser() &&
			window.mParticle.Identity.getCurrentUser().isLoggedIn()
		) {
			mParticleUser = window.mParticle.Identity.getCurrentUser();
			mParticleUserIdentities = mParticleUser?.getUserIdentities()?.userIdentities;
		}

		// if mParticle email already matches desired email for attribute
		if (mParticleUserIdentities?.email &&
			(mParticleUserIdentities.email === userEmailAddress ||
				(emailIsMaskedMParticleVal && userEmailAddress === this.maskEmail(mParticleUserIdentities.email))
			)
		) {
			// Set mParticle Attribute and EXIT
			this.initializeUserAttributes(mParticleUser, userAttributeArray);
			return;
		}

		// Logged In User Did Not Match Email. Call mParticle Identify For ONLY Email
		const identifyRequest =
			{
				userIdentities: {
					email: userEmailAddress.toLowerCase(),
				},
			};
		const identityCallback = result => {
			const mParticleUser = result.getUser();
			if (mParticleUser) {
				this.initializeUserAttributes(mParticleUser, userAttributeArray);
				window.mParticle.Identity.logout({});
			}
		};
		window.mParticle.Identity.identify(identifyRequest, identityCallback);
	}
}

BeasleyAnalytics.confirmLoaded();

