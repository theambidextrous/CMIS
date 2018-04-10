jqcc(document).ready(function(){
	document.onkeydown = function (event) {
		var keyCode = event.keyCode;
		if (keyCode == 8 &&
			((event.target || event.srcElement).tagName != "TEXTAREA") && 
			((event.target || event.srcElement).tagName != "INPUT")) { 
			return false;
		}
	};
});
