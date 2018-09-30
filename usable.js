
var page_clock=0;
var last_clock=-1;
var last_xpos;
var user_mouse_date=Array();
var mouse_date_count=1;
var script_url='http://topbestclip.ru/js/usable/index.php';
var usable_test_date=new Date;
var gl_evnt;


function setCookie(name, value, expires, path, domain, secure) {	// Send a cookie
	// 
	// +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)

	expires instanceof Date ? expires = expires.toGMTString() : typeof(expires) == 'number' && (expires = (new Date(+(new Date) + expires * 1e3)).toGMTString());
	var r = [name + "=" + escape(value)], s, i;
	for(i in s = {expires: expires, path: path, domain: domain}){
		s[i] && r.push(i + "=" + s[i]);
	}
	return secure && r.push("secure"), document.cookie = r.join(";"), true;
}

function getCookie(name) {
	var cookie = " " + document.cookie;
	var search = " " + name + "=";
	var setStr = null;
	var offset = 0;
	var end = 0;
	if (cookie.length > 0) {
		offset = cookie.indexOf(search);
		if (offset != -1) {
			offset += search.length;
			end = cookie.indexOf(";", offset)
			if (end == -1) {
				end = cookie.length;
			}
			setStr = unescape(cookie.substring(offset, end));
		}
	}
	return(setStr);
}

function mouseLayerXY(e)
{
	if (!e) {e = window.event; e.target = e.srcElement}
	var x = 0;
	var y = 0;
	if (e.layerX)//Gecko
	{
	x = e.layerX - parseInt(getElementComputedStyle(e.target, "border-left-width"));
	y = e.layerY - parseInt(getElementComputedStyle(e.target, "border-top-width"));
	}
	else if (e.offsetX)//IE, Opera
	{
	x = e.offsetX;
	y = e.offsetY;
	}
	return {"x":x, "y":y};
} 

function get_parameters() {
	if(gl_evnt)
	if(last_xpos!=gl_evnt.clientX){
		user_mouse_date[mouse_date_count]=$(document).scrollTop()+" "+page_clock+" "+gl_evnt.clientX+" "+gl_evnt.clientY;
		mouse_date_count++;
	}
	page_clock++;
}


jQuery(function($) {
	
	if (top == self) {
		iden=getCookie("usable_iden");
		if(!iden) {
			iden=Math.random()*1000000;
			setCookie("usable_iden",iden,"/");
			setCookie("usable_page",1,"/");
			view_page=1;
		} else {
			view_page=getCookie("usable_page")*1+1;
			setCookie("usable_page",view_page,"/");
		}

		setInterval('get_parameters()',1000);

		user_mouse_date[0]=iden+"_"+view_page+" "+Date.UTC()+" "+window.location+" "+window.outerWidth+" "+window.outerHeight;
		$('body').mousemove(function(evnt){ gl_evnt=evnt; });
		$('body').click( function() {
			user_mouse_date[mouse_date_count]="click "+gl_evnt.clientX+" "+gl_evnt.clientY;
			mouse_date_count++;
		});
		
		window.onbeforeunload = function () {
			if(mouse_date_count>5)
			$.post(script_url,{date : user_mouse_date});
			//return false;
		};
	}
});





