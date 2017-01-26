export default class Cookie {
	
	static setRootCookie(aName, aValue, aDays) {
		var expires = new Date();
		expires.setTime(expires.getTime() + (aDays * 24 * 60 * 60 * 1000));
		document.cookie = aName + '=' + aValue + ';expires=' + expires.toUTCString() + "; path=/";
	}
	
	static getCookie(aName) {
		var keyValue = document.cookie.match('(^|;) ?' + aName + '=([^;]*)(;|$)');
		return keyValue ? keyValue[2] : null;
	}
	
}