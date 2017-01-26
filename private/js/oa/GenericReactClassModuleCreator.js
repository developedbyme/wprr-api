"use strict";

import React from "react";
import ReactDOM from "react-dom";

// import GenericReactClassModuleCreator from "oa/GenericReactClassModuleCreator";
export default class GenericReactClassModuleCreator {
	
	/**
	 * Constructor
	 */
	constructor() {
		//console.log("oa.GenericReactClassModuleCreator::constructor");
		
		this._reactClass = null;
	}
	
	/**
	 * Sets the class to use for creating classes
	 *
	 * @param	aClass	React.Component	The class to use for creatirn new modules.
	 *
	 * @return	self
	 */
	setClass(aClass) {
		this._reactClass = aClass;
		
		return this;
	}
	
	/**
	 * Creates a new module
	 *
	 * aHolderNode	HTMLElement	The element to add the module to
	 * aData		Object		The dynamic data for the module
	 */
	createModule(aHolderNode, aData) {
		//console.log("oa.GenericReactClassModuleCreator::createModule");
		//console.log(aHolderNode, aData);
		
		return ReactDOM.render(React.createElement(this._reactClass, aData), aHolderNode);
	}
}