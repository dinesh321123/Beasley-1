/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


__webpack_require__(1);

var _frame = __webpack_require__(6);

var _frame2 = _interopRequireDefault(_frame);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

window.wp.media.view.MediaFrame.Select = (0, _frame2.default)(window.wp.media.view.MediaFrame.Select);
window.wp.media.view.MediaFrame.Post = (0, _frame2.default)(window.wp.media.view.MediaFrame.Post);

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2);
if(typeof content === 'string') content = [[module.i, content, '']];
// Prepare cssTransformation
var transform;

var options = {}
options.transform = transform
// add the styles to the DOM
var update = __webpack_require__(4)(content, options);
if(content.locals) module.exports = content.locals;
// Hot Module Replacement
if(false) {
	// When the styles change, update the <style> tags
	if(!content.locals) {
		module.hot.accept("!!../../../node_modules/css-loader/index.js!./styles.css", function() {
			var newContent = require("!!../../../node_modules/css-loader/index.js!./styles.css");
			if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
			update(newContent);
		});
	}
	// When the module is disposed, remove the <style> tags
	module.hot.dispose(function() { update(); });
}

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(3)(undefined);
// imports


// module
exports.push([module.i, ".video__embed {\n\ttext-align: center;\n}\n\n.video__url {\n\twidth: 80% !important;\n}\n\n.video__preview {\n\tmargin-top: 1em;\n}\n", ""]);

// exports


/***/ }),
/* 3 */
/***/ (function(module, exports) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
module.exports = function(useSourceMap) {
	var list = [];

	// return the list of modules as css string
	list.toString = function toString() {
		return this.map(function (item) {
			var content = cssWithMappingToString(item, useSourceMap);
			if(item[2]) {
				return "@media " + item[2] + "{" + content + "}";
			} else {
				return content;
			}
		}).join("");
	};

	// import a list of modules into the list
	list.i = function(modules, mediaQuery) {
		if(typeof modules === "string")
			modules = [[null, modules, ""]];
		var alreadyImportedModules = {};
		for(var i = 0; i < this.length; i++) {
			var id = this[i][0];
			if(typeof id === "number")
				alreadyImportedModules[id] = true;
		}
		for(i = 0; i < modules.length; i++) {
			var item = modules[i];
			// skip already imported module
			// this implementation is not 100% perfect for weird media query combinations
			//  when a module is imported multiple times with different media queries.
			//  I hope this will never occur (Hey this way we have smaller bundles)
			if(typeof item[0] !== "number" || !alreadyImportedModules[item[0]]) {
				if(mediaQuery && !item[2]) {
					item[2] = mediaQuery;
				} else if(mediaQuery) {
					item[2] = "(" + item[2] + ") and (" + mediaQuery + ")";
				}
				list.push(item);
			}
		}
	};
	return list;
};

function cssWithMappingToString(item, useSourceMap) {
	var content = item[1] || '';
	var cssMapping = item[3];
	if (!cssMapping) {
		return content;
	}

	if (useSourceMap && typeof btoa === 'function') {
		var sourceMapping = toComment(cssMapping);
		var sourceURLs = cssMapping.sources.map(function (source) {
			return '/*# sourceURL=' + cssMapping.sourceRoot + source + ' */'
		});

		return [content].concat(sourceURLs).concat([sourceMapping]).join('\n');
	}

	return [content].join('\n');
}

// Adapted from convert-source-map (MIT)
function toComment(sourceMap) {
	// eslint-disable-next-line no-undef
	var base64 = btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap))));
	var data = 'sourceMappingURL=data:application/json;charset=utf-8;base64,' + base64;

	return '/*# ' + data + ' */';
}


/***/ }),
/* 4 */
/***/ (function(module, exports, __webpack_require__) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

var stylesInDom = {};

var	memoize = function (fn) {
	var memo;

	return function () {
		if (typeof memo === "undefined") memo = fn.apply(this, arguments);
		return memo;
	};
};

var isOldIE = memoize(function () {
	// Test for IE <= 9 as proposed by Browserhacks
	// @see http://browserhacks.com/#hack-e71d8692f65334173fee715c222cb805
	// Tests for existence of standard globals is to allow style-loader
	// to operate correctly into non-standard environments
	// @see https://github.com/webpack-contrib/style-loader/issues/177
	return window && document && document.all && !window.atob;
});

var getElement = (function (fn) {
	var memo = {};

	return function(selector) {
		if (typeof memo[selector] === "undefined") {
			memo[selector] = fn.call(this, selector);
		}

		return memo[selector]
	};
})(function (target) {
	return document.querySelector(target)
});

var singleton = null;
var	singletonCounter = 0;
var	stylesInsertedAtTop = [];

var	fixUrls = __webpack_require__(5);

module.exports = function(list, options) {
	if (typeof DEBUG !== "undefined" && DEBUG) {
		if (typeof document !== "object") throw new Error("The style-loader cannot be used in a non-browser environment");
	}

	options = options || {};

	options.attrs = typeof options.attrs === "object" ? options.attrs : {};

	// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
	// tags it will allow on a page
	if (!options.singleton) options.singleton = isOldIE();

	// By default, add <style> tags to the <head> element
	if (!options.insertInto) options.insertInto = "head";

	// By default, add <style> tags to the bottom of the target
	if (!options.insertAt) options.insertAt = "bottom";

	var styles = listToStyles(list, options);

	addStylesToDom(styles, options);

	return function update (newList) {
		var mayRemove = [];

		for (var i = 0; i < styles.length; i++) {
			var item = styles[i];
			var domStyle = stylesInDom[item.id];

			domStyle.refs--;
			mayRemove.push(domStyle);
		}

		if(newList) {
			var newStyles = listToStyles(newList, options);
			addStylesToDom(newStyles, options);
		}

		for (var i = 0; i < mayRemove.length; i++) {
			var domStyle = mayRemove[i];

			if(domStyle.refs === 0) {
				for (var j = 0; j < domStyle.parts.length; j++) domStyle.parts[j]();

				delete stylesInDom[domStyle.id];
			}
		}
	};
};

function addStylesToDom (styles, options) {
	for (var i = 0; i < styles.length; i++) {
		var item = styles[i];
		var domStyle = stylesInDom[item.id];

		if(domStyle) {
			domStyle.refs++;

			for(var j = 0; j < domStyle.parts.length; j++) {
				domStyle.parts[j](item.parts[j]);
			}

			for(; j < item.parts.length; j++) {
				domStyle.parts.push(addStyle(item.parts[j], options));
			}
		} else {
			var parts = [];

			for(var j = 0; j < item.parts.length; j++) {
				parts.push(addStyle(item.parts[j], options));
			}

			stylesInDom[item.id] = {id: item.id, refs: 1, parts: parts};
		}
	}
}

function listToStyles (list, options) {
	var styles = [];
	var newStyles = {};

	for (var i = 0; i < list.length; i++) {
		var item = list[i];
		var id = options.base ? item[0] + options.base : item[0];
		var css = item[1];
		var media = item[2];
		var sourceMap = item[3];
		var part = {css: css, media: media, sourceMap: sourceMap};

		if(!newStyles[id]) styles.push(newStyles[id] = {id: id, parts: [part]});
		else newStyles[id].parts.push(part);
	}

	return styles;
}

function insertStyleElement (options, style) {
	var target = getElement(options.insertInto)

	if (!target) {
		throw new Error("Couldn't find a style target. This probably means that the value for the 'insertInto' parameter is invalid.");
	}

	var lastStyleElementInsertedAtTop = stylesInsertedAtTop[stylesInsertedAtTop.length - 1];

	if (options.insertAt === "top") {
		if (!lastStyleElementInsertedAtTop) {
			target.insertBefore(style, target.firstChild);
		} else if (lastStyleElementInsertedAtTop.nextSibling) {
			target.insertBefore(style, lastStyleElementInsertedAtTop.nextSibling);
		} else {
			target.appendChild(style);
		}
		stylesInsertedAtTop.push(style);
	} else if (options.insertAt === "bottom") {
		target.appendChild(style);
	} else {
		throw new Error("Invalid value for parameter 'insertAt'. Must be 'top' or 'bottom'.");
	}
}

function removeStyleElement (style) {
	if (style.parentNode === null) return false;
	style.parentNode.removeChild(style);

	var idx = stylesInsertedAtTop.indexOf(style);
	if(idx >= 0) {
		stylesInsertedAtTop.splice(idx, 1);
	}
}

function createStyleElement (options) {
	var style = document.createElement("style");

	options.attrs.type = "text/css";

	addAttrs(style, options.attrs);
	insertStyleElement(options, style);

	return style;
}

function createLinkElement (options) {
	var link = document.createElement("link");

	options.attrs.type = "text/css";
	options.attrs.rel = "stylesheet";

	addAttrs(link, options.attrs);
	insertStyleElement(options, link);

	return link;
}

function addAttrs (el, attrs) {
	Object.keys(attrs).forEach(function (key) {
		el.setAttribute(key, attrs[key]);
	});
}

function addStyle (obj, options) {
	var style, update, remove, result;

	// If a transform function was defined, run it on the css
	if (options.transform && obj.css) {
	    result = options.transform(obj.css);

	    if (result) {
	    	// If transform returns a value, use that instead of the original css.
	    	// This allows running runtime transformations on the css.
	    	obj.css = result;
	    } else {
	    	// If the transform function returns a falsy value, don't add this css.
	    	// This allows conditional loading of css
	    	return function() {
	    		// noop
	    	};
	    }
	}

	if (options.singleton) {
		var styleIndex = singletonCounter++;

		style = singleton || (singleton = createStyleElement(options));

		update = applyToSingletonTag.bind(null, style, styleIndex, false);
		remove = applyToSingletonTag.bind(null, style, styleIndex, true);

	} else if (
		obj.sourceMap &&
		typeof URL === "function" &&
		typeof URL.createObjectURL === "function" &&
		typeof URL.revokeObjectURL === "function" &&
		typeof Blob === "function" &&
		typeof btoa === "function"
	) {
		style = createLinkElement(options);
		update = updateLink.bind(null, style, options);
		remove = function () {
			removeStyleElement(style);

			if(style.href) URL.revokeObjectURL(style.href);
		};
	} else {
		style = createStyleElement(options);
		update = applyToTag.bind(null, style);
		remove = function () {
			removeStyleElement(style);
		};
	}

	update(obj);

	return function updateStyle (newObj) {
		if (newObj) {
			if (
				newObj.css === obj.css &&
				newObj.media === obj.media &&
				newObj.sourceMap === obj.sourceMap
			) {
				return;
			}

			update(obj = newObj);
		} else {
			remove();
		}
	};
}

var replaceText = (function () {
	var textStore = [];

	return function (index, replacement) {
		textStore[index] = replacement;

		return textStore.filter(Boolean).join('\n');
	};
})();

function applyToSingletonTag (style, index, remove, obj) {
	var css = remove ? "" : obj.css;

	if (style.styleSheet) {
		style.styleSheet.cssText = replaceText(index, css);
	} else {
		var cssNode = document.createTextNode(css);
		var childNodes = style.childNodes;

		if (childNodes[index]) style.removeChild(childNodes[index]);

		if (childNodes.length) {
			style.insertBefore(cssNode, childNodes[index]);
		} else {
			style.appendChild(cssNode);
		}
	}
}

function applyToTag (style, obj) {
	var css = obj.css;
	var media = obj.media;

	if(media) {
		style.setAttribute("media", media)
	}

	if(style.styleSheet) {
		style.styleSheet.cssText = css;
	} else {
		while(style.firstChild) {
			style.removeChild(style.firstChild);
		}

		style.appendChild(document.createTextNode(css));
	}
}

function updateLink (link, options, obj) {
	var css = obj.css;
	var sourceMap = obj.sourceMap;

	/*
		If convertToAbsoluteUrls isn't defined, but sourcemaps are enabled
		and there is no publicPath defined then lets turn convertToAbsoluteUrls
		on by default.  Otherwise default to the convertToAbsoluteUrls option
		directly
	*/
	var autoFixUrls = options.convertToAbsoluteUrls === undefined && sourceMap;

	if (options.convertToAbsoluteUrls || autoFixUrls) {
		css = fixUrls(css);
	}

	if (sourceMap) {
		// http://stackoverflow.com/a/26603875
		css += "\n/*# sourceMappingURL=data:application/json;base64," + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + " */";
	}

	var blob = new Blob([css], { type: "text/css" });

	var oldSrc = link.href;

	link.href = URL.createObjectURL(blob);

	if(oldSrc) URL.revokeObjectURL(oldSrc);
}


/***/ }),
/* 5 */
/***/ (function(module, exports) {


/**
 * When source maps are enabled, `style-loader` uses a link element with a data-uri to
 * embed the css on the page. This breaks all relative urls because now they are relative to a
 * bundle instead of the current page.
 *
 * One solution is to only use full urls, but that may be impossible.
 *
 * Instead, this function "fixes" the relative urls to be absolute according to the current page location.
 *
 * A rudimentary test suite is located at `test/fixUrls.js` and can be run via the `npm test` command.
 *
 */

module.exports = function (css) {
  // get current location
  var location = typeof window !== "undefined" && window.location;

  if (!location) {
    throw new Error("fixUrls requires window.location");
  }

	// blank or null?
	if (!css || typeof css !== "string") {
	  return css;
  }

  var baseUrl = location.protocol + "//" + location.host;
  var currentDir = baseUrl + location.pathname.replace(/\/[^\/]*$/, "/");

	// convert each url(...)
	/*
	This regular expression is just a way to recursively match brackets within
	a string.

	 /url\s*\(  = Match on the word "url" with any whitespace after it and then a parens
	   (  = Start a capturing group
	     (?:  = Start a non-capturing group
	         [^)(]  = Match anything that isn't a parentheses
	         |  = OR
	         \(  = Match a start parentheses
	             (?:  = Start another non-capturing groups
	                 [^)(]+  = Match anything that isn't a parentheses
	                 |  = OR
	                 \(  = Match a start parentheses
	                     [^)(]*  = Match anything that isn't a parentheses
	                 \)  = Match a end parentheses
	             )  = End Group
              *\) = Match anything and then a close parens
          )  = Close non-capturing group
          *  = Match anything
       )  = Close capturing group
	 \)  = Match a close parens

	 /gi  = Get all matches, not the first.  Be case insensitive.
	 */
	var fixedCss = css.replace(/url\s*\(((?:[^)(]|\((?:[^)(]+|\([^)(]*\))*\))*)\)/gi, function(fullMatch, origUrl) {
		// strip quotes (if they exist)
		var unquotedOrigUrl = origUrl
			.trim()
			.replace(/^"(.*)"$/, function(o, $1){ return $1; })
			.replace(/^'(.*)'$/, function(o, $1){ return $1; });

		// already a full url? no change
		if (/^(#|data:|http:\/\/|https:\/\/|file:\/\/\/)/i.test(unquotedOrigUrl)) {
		  return fullMatch;
		}

		// convert the url to a full url
		var newUrl;

		if (unquotedOrigUrl.indexOf("//") === 0) {
		  	//TODO: should we add protocol?
			newUrl = unquotedOrigUrl;
		} else if (unquotedOrigUrl.indexOf("/") === 0) {
			// path should be relative to the base url
			newUrl = baseUrl + unquotedOrigUrl; // already starts with '/'
		} else {
			// path should be relative to current directory
			newUrl = currentDir + unquotedOrigUrl.replace(/^\.\//, ""); // Strip leading './'
		}

		// send back the fixed url(...)
		return "url(" + JSON.stringify(newUrl) + ")";
	});

	// send back the fixed css
	return fixedCss;
};


/***/ }),
/* 6 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});

var _view = __webpack_require__(7);

var _view2 = _interopRequireDefault(_view);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var frame = function frame(mediaFrame) {
	return mediaFrame.extend({
		bindHandlers: function bindHandlers() {
			var self = this;

			for (var _len = arguments.length, params = Array(_len), _key = 0; _key < _len; _key++) {
				params[_key] = arguments[_key];
			}

			mediaFrame.prototype.bindHandlers.apply(self, params);

			self.on('content:render:video', self.videoContent, self);
		},
		browseRouter: function browseRouter() {
			for (var _len2 = arguments.length, params = Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
				params[_key2] = arguments[_key2];
			}

			mediaFrame.prototype.browseRouter.apply(this, params);

			params[0].set({
				video: {
					text: 'Video',
					priority: 30
				}
			});
		},
		videoContent: function videoContent() {
			var self = this;
			var view = new _view2.default({
				controller: self
			});

			self.$el.removeClass('hide-toolbar');
			self.content.set(view);
		}
	});
};

exports.default = frame;

/***/ }),
/* 7 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});
var _window = window,
    fvideo = _window.fvideo,
    wp = _window.wp;


var request = function request(action, data) {
	return new Promise(function (resolve, reject) {
		wp.ajax.send(action, {
			type: 'GET',
			data: data,
			success: resolve,
			error: reject
		});
	});
};

var requestvideo = function requestvideo(action, data) {
	return new Promise(function (resolve, reject) {
		wp.ajax.send(action, {
			type: 'post',
			data: data,
			contentType: false,
			processData: false,
			success: resolve,
			error: reject
		});
	});
};
// @see wp.media.View.UploaderInline
var mediaView = wp.media.View.extend({

	tagName: 'div',
	className: 'video-embed-import',
	template: wp.template('video-embed-import'),

	events: {
		'keyup .video__url': 'onUrlChange',
		'click .video__submit': 'addVideo',
		'click .video__mediaimg': 'showMediaImg',
		'click #media_loadmore': 'loadMoreMediaImg',
		'click .s_btn_mediaimage': 'searchMediaImg',
		'click .img-attachment': 'getSelectedMediaImg',
		'click #upload_image': 'showUploadImage',
		'click #select_media_library': 'showMediaLibrary',
	},

	onUrlChange: function onUrlChange() {
		var $el = this.$el;

		var $preview = $el.find('.video__preview');
		var $submit = $el.find('.video__submit');

		request('fvideos_get_embed', { url: $el.find('.video__url').val() }).then(function (html) {
			$preview.html(html);
			$submit.removeAttr('disabled');
		}).catch(function () {
			$preview.html('');
			$submit.attr('disabled', 'disabled');
		});
	},
	addVideo: function addVideo() {
		// var $el = this.$el;
		var self		= this;
		var url			= self.$el.find('.video__url').val();
		var postId		= wp.media.view.settings.post.id;
		var $selectedImageOption = self.$el.find("input[name='image_option']:checked").val();
		let fileName	= "";
		let fdata		= new FormData();
		
		if (!$selectedImageOption) {
			// alert( 'Select option: ',fvideo.missingImage );
			alert( fvideo.missingImage );
			return;
		}

		if (self.loading) {
			return;
		}
		
		if (!url) {
			alert(fvideo.wrongUrl);
			return;
		}
		
		fdata.append( 'action', 'fvideos_import_embed' );
		fdata.append( 'image_option', $selectedImageOption );
		fdata.append( 'url', url );
		fdata.append( 'post_id', postId );

		if( $selectedImageOption == 'upload_image' ) {
			/* File upload code*/
			let fileInputElement = document.getElementById('custom_featured_img');
			fileName = fileInputElement.files.length ? fileInputElement.files[0].name : '' ;

			if( fileName == "" ) {
				alert( fvideo.missingImage );
				return false;
			}
			console.log( 'File Data: ', fileInputElement.files[0] );
			console.log( 'File Name: ', fileInputElement.files[0].name );
			fdata.append( 'imagearr', fileInputElement.files[0], fileInputElement.files[0].name );
		}
		if( $selectedImageOption == 'select_media_library' ) {
			var $mediaImageId = self.$el.find('#media_image_id');
			
			if(!$mediaImageId) {
				alert( fvideo.missingMediaImage );
				return false;
			}

			fdata.append( 'mediaImageId', $mediaImageId.val() );
		}
		// fdata.append('key2', 'value2');
		/* console.log( 'Image name: ', fileInputElement.files[0].name );
		console.log('fileInputElement.files.length: ', fileInputElement.files.length );
		console.log( 'Form Data: ', fdata.entries() );
		console.log( 'Form key1 data: ', fdata.getAll('key1') ); */
		// Display the key/value pairs
		/* for (var pair of fdata.entries()) {
			console.log(pair[0]+ ', ' + pair[1]); console.log(' object, ', pair[1]);
		} */

		self.loading = true;
		// spinner load
		var $video__submit_spinner = self.$el.find( '#video__submit_spinner' );
			$video__submit_spinner.addClass( 'is-active' );
		
		requestvideo('fvideos_import_embed', fdata).then(function (imageId) {
			$video__submit_spinner.removeClass( 'is-active' );	// remove spinner load
			self.loading = false;

			var library = self.controller.content.mode('browse').get('library');

			library.options.selection.reset();
			library.collection.props.set({ ignore: +new Date() });

			library.collection.once('update', function () {
				var image = wp.media.attachment(imageId);
				if (image) {
					library.options.selection.add(image);
				}
			});
		}).catch(function (error) {
			console.log( 'Embed_Array: ', error.Embed_Array );
			console.log( 'File Array: ', error.File_Array );
			console.log( 'IsWPError: ', error.isWpError );
			console.log( 'Post Value: ', error.post_value );
			console.log( 'File Value: ', error.file_value );
			$video__submit_spinner.removeClass( 'is-active' );	// remove spinner load
			self.loading = false;
			alert( fvideo.cannotEmbed );
		});
	},
	loadMoreMediaImg: function loadMoreMediaImg() {
		var $el = this.$el;
		var $media_loadmore = $el.find('#media_loadmore');
		var $previewMediaImgUl = $el.find('.mediaimg-ul');
		var $paged_mediaimage = $el.find('#paged_mediaimage')
		
		$media_loadmore.attr('disabled', 'disabled');
		// spinner load
		var $loadmore_spinner = $el.find( '#loadmore_spinner' );
			$loadmore_spinner.addClass( 'is-active' );
		
		request( 'fvideos_load_more_media_image', { media: 'media_show', s_mediaimage: $el.find('#s_mediaimage').val(), paged_mediaimage: $el.find('#paged_mediaimage').val() } ).then(function (success) {
			$previewMediaImgUl.append( success.media_image_list );
			$paged_mediaimage.val( success.paged_mediaimage );
			$loadmore_spinner.removeClass( 'is-active' );	// remove spinner load
			$media_loadmore.removeAttr('disabled');
			console.log( 'loadMoreMediaImg: ', success.imgs_array );
			if(!success.media_image_list){
				$media_loadmore.hide();
			}
		}).catch(function ( error ) {
			console.log( error );
			alert(fvideo.cannotEmbedImage);
			$loadmore_spinner.removeClass( 'is-active' );	// remove spinner load
		});
	},
	searchMediaImg: function searchMediaImg() {
		var $el = this.$el;
		var $preview = $el.find('.mediaimage__preview');
		// spinner load
		var $s_spinner = $el.find( '#s_spinner' );
			$s_spinner.addClass( 'is-active' );

		request( 'fvideos_get_media_image', { media: 'media_show', s_mediaimage: $el.find('#s_mediaimage').val() } ).then(function (success) {
			$preview.html( success.html );
			console.log( 'searchMediaImg: ', success.imgs_array );
		}).catch(function ( error ) {
			console.log( error );
			alert(fvideo.cannotEmbedImage);
		});
	},
	showMediaImg: function showMediaImg() {
		var $el = this.$el;
		var $preview = $el.find('.mediaimage__preview');
		var $video__mediaimg_button = $el.find('.video__mediaimg');
		$video__mediaimg_button.attr('disabled', 'disabled');
		// spinner load when click on Open Media Library
		var $image__preview_spinner = $el.find( '#image__preview_spinner' );
			$image__preview_spinner.addClass( 'is-active' );

		request( 'fvideos_get_media_image', { media: 'media_show' } ).then(function (success) {
			$image__preview_spinner.removeClass( 'is-active' );	// remove spinner load
			$preview.html( success.html );
			console.log( 'showMediaImg: ', success.imgs_array );
			$video__mediaimg_button.removeAttr('disabled');
		}).catch(function ( error ) {
			console.log( error );
			alert(fvideo.cannotEmbedImage);
		});
	}, 
	getSelectedMediaImg: function getSelectedMediaImg() {
		var $el = this.$el;
		var $imagePreview = $el.find('.image__preview');
		var self = this;
		var $getImageAttrId = self.$el.find('.selected-media-img').attr("image-id");
		// spinner load when single thumbnail image
		var $image__preview_spinner = $el.find( '#image__preview_spinner' );
			$image__preview_spinner.addClass( 'is-active' );
			$imagePreview.html( '' );

		if( !$getImageAttrId || $getImageAttrId == "" ) {
			alert( fvideo.missingImage );
			return false;
		}
		request( 'get_selected_media_image', { imageAttrId: $getImageAttrId } ).then(function (success) {
			$imagePreview.html( success.single_image_div );
			self.$el.find("img").removeClass("selected-media-img");
			self.$el.find('#media_image_id').val( $getImageAttrId );
			$image__preview_spinner.removeClass( 'is-active' );	// remove spinner load
		}).catch(function ( error ) {
			console.log( error );
			alert(fvideo.cannotEmbedImage);
		});
	},
	showUploadImage: function showUploadImage() {
		// alert( 'Select Upload image radio button' );
		var $el = this.$el;
		var $mediaImgLibrary = $el.find('.media__img__option');
		var $uploadImg = $el.find('.upload__img__option');
		var $mediaImgPreview = $el.find('.mediaimage__preview');
		var $ImgPreview = $el.find('.image__preview');
		
		$uploadImg.show();
		$mediaImgLibrary.hide();
		$mediaImgPreview.html('');
		$ImgPreview.html('');
	},
	showMediaLibrary: function showMediaLibrary() {
		// alert('Select Media library radio button');
		var $el = this.$el;
		var $mediaImgLibrary = $el.find('.media__img__option');
		var $uploadImg = $el.find('.upload__img__option');
		var $mediaImgPreview = $el.find('.mediaimage__preview');
		var $ImgPreview = $el.find('.image__preview');

		$mediaImgLibrary.show();
		$uploadImg.hide();
		$mediaImgPreview.html('');
		$ImgPreview.html('');
	}	
});

exports.default = mediaView;

/***/ })
/******/ ]);
//# sourceMappingURL=media.js.map