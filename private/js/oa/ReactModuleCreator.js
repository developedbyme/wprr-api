"use strict";

export default class ReactModuleCreator {
	
	/**
	 * Constructor
	 */
	constructor() {
		//console.log("oa.ReactModuleCreator::constructor");
		
		this._modules = new Object();
	}
	
	registerModule(aType, aObject) {
		this._modules[aType] = aObject;
		
		return aObject;
	}
	
	/**
	 * Creates a new image gallery within a node
	 *
	 * aType		String		The name of the module to create
	 * aHolderNode	HTMLElement	The element to add the gallery to
	 * aData		Object		The dynamic data for the module
	 */
	createModule(aType, aHolderNode, aData) {
		//console.log("oa.ReactModuleCreator::createGallery");
		
		return this._modules[aType].createModule(aHolderNode, aData);
	}
}