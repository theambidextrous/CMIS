var canvas;

var canvas, ctx;

var touchX, touchY;

var lastX, lastY = -1;

var Canvas2Image = (function(){

	// check if we have canvas support
	var bHasCanvas = false;
	var oCanvas = document.createElement("canvas");
	if (oCanvas.getContext("2d")){
		bHasCanvas = true;
	}

	// no canvas, bail out.
	if (!bHasCanvas){
		return {
			saveAsBMP: function(){},
			saveAsPNG: function(){},
			saveAsJPEG: function(){}
		}
	}

	var bHasImageData = !!(oCanvas.getContext("2d").getImageData);
	var bHasDataURL = !!(oCanvas.toDataURL);
	var bHasBase64 = !!(window.btoa);

	var strDownloadMime = "image/octet-stream";

	// ok, we're good
	var readCanvasData = function(oCanvas){
		var iWidth = parseInt(oCanvas.width);
		var iHeight = parseInt(oCanvas.height);
		return oCanvas.getContext("2d").getImageData(0, 0, iWidth, iHeight);
	}

	// base64 encodes either a string or an array of charcodes
	var encodeData = function(data){
		var strData = "";
		if (typeof data == "string"){
			strData = data;
		} else {
			var aData = data;
			for (var i = 0; i < aData.length; i++){
				strData += String.fromCharCode(aData[i]);
			}
		}
		return btoa(strData);
	}

	// creates a base64 encoded string containing BMP data
	// takes an imagedata object as argument
	var createBMP = function(oData){
		var aHeader = [];

		var iWidth = oData.width;
		var iHeight = oData.height;

		aHeader.push(0x42); // magic 1
		aHeader.push(0x4D);

		var iFileSize = iWidth * iHeight * 3 + 54; // total header size = 54 bytes
		aHeader.push(iFileSize % 256);
		iFileSize = Math.floor(iFileSize / 256);
		aHeader.push(iFileSize % 256);
		iFileSize = Math.floor(iFileSize / 256);
		aHeader.push(iFileSize % 256);
		iFileSize = Math.floor(iFileSize / 256);
		aHeader.push(iFileSize % 256);

		aHeader.push(0); // reserved
		aHeader.push(0);
		aHeader.push(0); // reserved
		aHeader.push(0);

		aHeader.push(54); // dataoffset
		aHeader.push(0);
		aHeader.push(0);
		aHeader.push(0);

		var aInfoHeader = [];
		aInfoHeader.push(40); // info header size
		aInfoHeader.push(0);
		aInfoHeader.push(0);
		aInfoHeader.push(0);

		var iImageWidth = iWidth;
		aInfoHeader.push(iImageWidth % 256);
		iImageWidth = Math.floor(iImageWidth / 256);
		aInfoHeader.push(iImageWidth % 256);
		iImageWidth = Math.floor(iImageWidth / 256);
		aInfoHeader.push(iImageWidth % 256);
		iImageWidth = Math.floor(iImageWidth / 256);
		aInfoHeader.push(iImageWidth % 256);

		var iImageHeight = iHeight;
		aInfoHeader.push(iImageHeight % 256);
		iImageHeight = Math.floor(iImageHeight / 256);
		aInfoHeader.push(iImageHeight % 256);
		iImageHeight = Math.floor(iImageHeight / 256);
		aInfoHeader.push(iImageHeight % 256);
		iImageHeight = Math.floor(iImageHeight / 256);
		aInfoHeader.push(iImageHeight % 256);

		aInfoHeader.push(1); // num of planes
		aInfoHeader.push(0);

		aInfoHeader.push(24); // num of bits per pixel
		aInfoHeader.push(0);

		aInfoHeader.push(0); // compression = none
		aInfoHeader.push(0);
		aInfoHeader.push(0);
		aInfoHeader.push(0);

		var iDataSize = iWidth * iHeight * 3;
		aInfoHeader.push(iDataSize % 256);
		iDataSize = Math.floor(iDataSize / 256);
		aInfoHeader.push(iDataSize % 256);
		iDataSize = Math.floor(iDataSize / 256);
		aInfoHeader.push(iDataSize % 256);
		iDataSize = Math.floor(iDataSize / 256);
		aInfoHeader.push(iDataSize % 256);

		for (var i = 0; i < 16; i++){
			aInfoHeader.push(0); // these bytes not used
		}

		var iPadding = (4 - ((iWidth * 3) % 4)) % 4;

		var aImgData = oData.data;

		var strPixelData = "";
		var y = iHeight;
		do {
			var iOffsetY = iWidth * (y - 1) * 4;
			var strPixelRow = "";
			for (var x = 0; x < iWidth; x++){
				var iOffsetX = 4 * x;

				strPixelRow += String.fromCharCode(aImgData[iOffsetY + iOffsetX + 2]);
				strPixelRow += String.fromCharCode(aImgData[iOffsetY + iOffsetX + 1]);
				strPixelRow += String.fromCharCode(aImgData[iOffsetY + iOffsetX]);
			}
			for (var c = 0; c < iPadding; c++){
				strPixelRow += String.fromCharCode(0);
			}
			strPixelData += strPixelRow;
		} while (--y);

		var strEncoded = encodeData(aHeader.concat(aInfoHeader)) + encodeData(strPixelData);

		return strEncoded;
	}


	// sends the generated file to the client
	var saveFile = function(strData){
		document.location.href = strData;
	}

	var makeDataURI = function(strData, strMime){
		return "data:" + strMime + ";base64," + strData;
	}

	// generates a <img> object containing the imagedata
	var makeImageObject = function(strSource){
		var oImgElement = document.createElement("img");
		oImgElement.src = strSource;
		return oImgElement;
	}

	var scaleCanvas = function(oCanvas, iWidth, iHeight){
		if (iWidth && iHeight){
			var oSaveCanvas = document.createElement("canvas");
			oSaveCanvas.width = iWidth;
			oSaveCanvas.height = iHeight;
			oSaveCanvas.style.width = iWidth + "px";
			oSaveCanvas.style.height = iHeight + "px";

			var oSaveCtx = oSaveCanvas.getContext("2d");

			oSaveCtx.drawImage(oCanvas, 0, 0, oCanvas.width, oCanvas.height, 0, 0, iWidth, iHeight);
			return oSaveCanvas;
		}
		return oCanvas;
	}

	return {

		saveAsPNG: function(oCanvas, bReturnImg, iWidth, iHeight){
			if (!bHasDataURL){
				return false;
			}
			var oScaledCanvas = scaleCanvas(oCanvas, iWidth, iHeight);
			var strData = oScaledCanvas.toDataURL("image/png");
			if (bReturnImg){
				return makeImageObject(strData);
			} else {
				saveFile(strData.replace("image/png", strDownloadMime));
			}
			return true;
		},

		saveAsJPEG: function(oCanvas, bReturnImg, iWidth, iHeight){
			if (!bHasDataURL){
				return false;
			}

			var oScaledCanvas = scaleCanvas(oCanvas, iWidth, iHeight);
			var strMime = "image/jpeg";
			var strData = oScaledCanvas.toDataURL(strMime);

			// check if browser actually supports jpeg by looking for the mime type in the data uri.
			// if not, return false
			if (strData.indexOf(strMime) != 5){
				return false;
			}

			if (bReturnImg){
				return makeImageObject(strData);
			} else {
				saveFile(strData.replace(strMime, strDownloadMime));
			}
			return true;
		},

		saveAsBMP: function(oCanvas, bReturnImg, iWidth, iHeight){
			if (!(bHasImageData && bHasBase64)){
				return false;
			}

			var oScaledCanvas = scaleCanvas(oCanvas, iWidth, iHeight);

			var oData = readCanvasData(oScaledCanvas);
			var strImgData = createBMP(oData);
			if (bReturnImg){
				return makeImageObject(makeDataURI(strImgData, "image/bmp"));
			} else {
				saveFile(makeDataURI(strImgData, strDownloadMime));
			}
			return true;
		}
	};

})();

/***********************************/
$(function(){

	document.body.addEventListener("touchstart", function(e){
		if (e.target == canvas){
			e.preventDefault();
		}
	}, false);
	document.body.addEventListener("touchend", function(e){
		if (e.target == canvas){
			e.preventDefault();
		}
	}, false);
	document.body.addEventListener("touchmove", function(e){
		if (e.target == canvas){
			e.preventDefault();
		}
	}, false);

	canvas = document.querySelector('#paint');
	var strDataURI = canvas.toDataURL("image/jpeg");
	var ctx = canvas.getContext('2d');


	var sketch = document.querySelector('#sketch');
	var sketch_style = getComputedStyle(sketch);
	canvas.width = parseInt($(window).width());
	canvas.height = parseInt($(window).height());
	ctx.fillStyle = '#ffffff';
	ctx.fillRect(0, 0, canvas.width, canvas.height); // now fill the canvas
	var mouse = {
		x: 0,
		y: 0
	};
	var last_mouse = {
		x: 0,
		y: 0
	};

	/* Mouse Capturing Work */
	canvas.addEventListener('mousemove', function(e){
		last_mouse.x = mouse.x;
		last_mouse.y = mouse.y;

		mouse.x = e.pageX - this.offsetLeft + 4;
		mouse.y = e.pageY - this.offsetTop + 14;
	}, false);

	/* Drawing on Paint App */

	ctx.lineWidth = '1';
	ctx.lineJoin = 'round';
	ctx.lineCap = 'round';
	ctx.strokeStyle = '#ff0000';
	var selectedColor = '#ff0000';

	canvas.addEventListener('touchstart', sketchpad_touchStart, false);
	canvas.addEventListener('touchend', sketchpad_touchEnd, false);
	canvas.addEventListener('touchmove', sketchpad_touchMove, false);


	canvas.addEventListener('mousedown', function(e){
		ctx.beginPath();
		ctx.moveTo(mouse.x, mouse.y);

		canvas.addEventListener('mousemove', onPaint, false);
	}, false);

	canvas.addEventListener('mouseup', function(){
		canvas.removeEventListener('mousemove', onPaint, false);
	}, false);



	function drawLine(ctx, x, y){
		if (lastX == -1){
			lastX = x;
			lastY = y;
		}
		ctx.beginPath();
		ctx.moveTo(lastX, lastY);
		ctx.lineTo(x, y);
		ctx.stroke();
		ctx.closePath();

		lastX = x;
		lastY = y;
	}

	function sketchpad_touchStart(){
		// Update the touch co-ordinates
		getTouchPos();

		drawLine(ctx, touchX, touchY);

		// Prevents an additional mousedown event being triggered
		event.preventDefault();
	}

	function sketchpad_touchEnd(){
		// Reset lastX and lastY to -1 to indicate that they are now invalid, since we have lifted the "pen"
		lastX = -1;
		lastY = -1;
	}

	// Draw something and prevent the default scrolling when touch movement is detected
	function sketchpad_touchMove(e){
		getTouchPos(e);
		drawLine(ctx, touchX, touchY);
		event.preventDefault();
	}

	function getTouchPos(e){
		if (!e)
			var e = event;

		if (e.touches){
			if (e.touches.length == 1){ // Only deal with one finger
				var touch = e.touches[0]; // Get the information for finger #1
				touchX = touch.pageX - touch.target.offsetLeft;
				touchY = touch.pageY - touch.target.offsetTop;
			}
		}
	}

	var onPaint = function(){
		ctx.lineTo(mouse.x, mouse.y);
		ctx.stroke();
	};
	$('span.width-select').click(function(){
		$('span.width-select').removeClass("selected");
		ctx.lineWidth = $(this).attr('val');
		$(this).addClass('selected');
	});

	$('div.color-btn').click(function(){
		$('div.color-select').toggle();
	});
	$('div.color-opt').click(function(){
		if ($('div.eraser-btn').hasClass('select')){
			ctx.strokeStyle = "white";
		} else {
			ctx.strokeStyle = $(this).attr('val');
			selectedColor = ctx.strokeStyle;
			$('div.color-btn img').css('border-bottom-color', $(this).attr('val'));
		}
	});
	$('div.eraser-btn').click(function(){
		$('div.eraser-btn').addClass('select');
		ctx.lineWidth = $('span.selected').attr('val');
		$('canvas').css('cursor', 'url(<?php echo STATIC_CDN_URL;?>plugins/handwrite/images/eraser.png), auto');
		$('div.color-select').hide();
		ctx.fillStyle = 'white';
		ctx.strokeStyle = 'white';
	});
	$('div.pencil-btn').click(function(){
		$('div.eraser-btn').removeClass("select");
		ctx.lineWidth = $('span.selected').attr('val');
		ctx.strokeStyle = selectedColor;
		$('canvas').css('cursor', 'url(<?php echo STATIC_CDN_URL;?>plugins/handwrite/images/pencil.png), auto');
	});
	$('div.clear-btn').click(function(){
		ctx.clearRect(0, 0, canvas.width, canvas.height);
	});
	$(window).resize(function(){
		canvas.width = parseInt($(window).width());
		canvas.height = parseInt($(window).height());
		ctx.lineWidth = '1';
		ctx.lineJoin = 'round';
		ctx.lineCap = 'round';
		ctx.strokeStyle = '#ff0000';
		ctx.fillStyle = '#ffffff';
		ctx.fillRect(0, 0, canvas.width, canvas.height);
	});
});

function send(){
	var oImgPNG = Canvas2Image.saveAsPNG(canvas, true);
	var sendername = $('#sendername').val();
	$.post("send.php", {
		embed: 'web',
		image: $(oImgPNG).attr('src'),
		tid: tid,
		other: 1,
		sendername: sendername
	}, function(data){
		eval(data);
	});
	var ctx = canvas.getContext('2d');
	ctx.clearRect(0, 0, canvas.width, canvas.height);
	ctx.fillStyle = '#ffffff';
	ctx.fillRect(0, 0, canvas.width, canvas.height);
}
