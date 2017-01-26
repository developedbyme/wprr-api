/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	"use strict";

	var _ReactModuleCreator = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"oa/ReactModuleCreator\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

	var _ReactModuleCreator2 = _interopRequireDefault(_ReactModuleCreator);

	var _GenericReactClassModuleCreator = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"oa/GenericReactClassModuleCreator\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

	var _GenericReactClassModuleCreator2 = _interopRequireDefault(_GenericReactClassModuleCreator);

	var _EditorManager = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"oa/admin/editor/EditorManager\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

	var _EditorManager2 = _interopRequireDefault(_EditorManager);

	var _ShortcodeManager = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"oa/admin/editor/shortcodes/ShortcodeManager\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

	var _ShortcodeManager2 = _interopRequireDefault(_ShortcodeManager);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	//import CheckSyncNotice from "mrouterdata/admin/sync/CheckSyncNotice";

	//console.log("admin-main.js");

	if (!window.OA) {
		window.OA = new Object();
	}

	if (!window.OA.externallyAvailableClasses) {
		window.OA.externallyAvailableClasses = new Object();
	}

	if (!window.OA.mceEditorMananger) {
		window.OA.mceEditorMananger = new _EditorManager2.default();
	}

	if (!window.OA.mceShortcodeMananger) {
		window.OA.mceShortcodeMananger = new _ShortcodeManager2.default();
	}

	if (!window.OA.reactModuleCreator) {
		window.OA.reactModuleCreator = new _ReactModuleCreator2.default();
	}

	//window.OA.reactModuleCreator.registerModule("checkSyncNotice", (new GenericReactClassModuleCreator()).setClass(CheckSyncNotice));

	document.addEventListener("DOMContentLoaded", function (event) {
		//console.log("admin-main.js DOMContentLoaded");
		if (oaWpAdminData.screen["base"] === "post") {
			window.OA.mceShortcodeMananger.registerViews();
		}
	});

/***/ }
/******/ ]);