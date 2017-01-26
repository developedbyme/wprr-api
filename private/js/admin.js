//console.log("admin-main.js");

import ReactModuleCreator from "oa/ReactModuleCreator";
import GenericReactClassModuleCreator from "oa/GenericReactClassModuleCreator";
import EditorManager from "oa/admin/editor/EditorManager";
import ShortcodeManager from "oa/admin/editor/shortcodes/ShortcodeManager";

//import CheckSyncNotice from "mrouterdata/admin/sync/CheckSyncNotice";

if(!window.OA) {
	window.OA = new Object();
}

if(!window.OA.externallyAvailableClasses) {
	window.OA.externallyAvailableClasses = new Object();
}

if(!window.OA.mceEditorMananger) {
	window.OA.mceEditorMananger = new EditorManager();
}

if(!window.OA.mceShortcodeMananger) {
	window.OA.mceShortcodeMananger = new ShortcodeManager();
}

if(!window.OA.reactModuleCreator) {
	window.OA.reactModuleCreator = new ReactModuleCreator();
}

//window.OA.reactModuleCreator.registerModule("checkSyncNotice", (new GenericReactClassModuleCreator()).setClass(CheckSyncNotice));

document.addEventListener("DOMContentLoaded", function(event) {
	//console.log("admin-main.js DOMContentLoaded");
	if(oaWpAdminData.screen["base"] === "post") {
		window.OA.mceShortcodeMananger.registerViews();
	}
	
});