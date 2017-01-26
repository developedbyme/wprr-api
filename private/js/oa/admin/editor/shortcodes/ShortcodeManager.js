"use strict";

//import ShortcodeManager from "oa/admin/editor/shortcodes/ShortcodeManager";
export default class ShortcodeManager {
	
	/**
	 * Constructor
	 */
	constructor() {
		//console.log("oa.admin.editor.shortcodes.ShortcodeManager::constructor");
		
		this._canRegister = false;
		this._shortcodeViews = new Object();
	}
	
	addView(aShortcode, aObject) {
		this._shortcodeViews[aShortcode] = aObject;
		
		return aObject;
	}
	
	registerViews() {
		this._canRegister = true;
		
		if(wp.mce && wp.mce.views) {
			for(var objectName in this._shortcodeViews) {
				wp.mce.views.register(objectName, this._shortcodeViews[objectName]);
			}
		}
		else {
			console.warn("MCE views are not available");
		}
	}
}