"use strict";

//import EditorManager from "oa/admin/editor/EditorManager";
export default class EditorManager {
	
	/**
	 * Constructor
	 */
	constructor() {
		//console.log("oa.admin.editor.EditorManager::constructor");
		
		this._controllers = new Object();
		
	}
	
	_getControllers(aEditorName) {
		if(!this._controllers[aEditorName]) {
			this._controllers[aEditorName] = new Array();
		}
		return this._controllers[aEditorName];
	}
	
	addEditorControl(aEditorName, aController) {
		//console.log("oa.admin.editor.EditorManager::addEditorControl");
		//console.log(aEditorName, aController);
		
		var currentArray = this._getControllers(aEditorName);
		currentArray.push(aController);
		
		return aController;
	}
	
	registerEditor(aEditor) {
		//console.log("oa.admin.editor.EditorManager::registerEditor");
		//console.log(aEditor);
		
		var currentName = aEditor.id;
		if(this._controllers[currentName]) {
			var currentArray = this._controllers[currentName];
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				try {
					var currentController = currentArray[i];
					currentController.setEditor(aEditor);
					currentController.start();
				}
				catch(theError) {
					console.error("Could not start editor controller.");
					console.log(currentController, aEditor);
				}
			}
		}
	}
}