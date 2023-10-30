/*******************************/
/*********   BUF   *************/
/*******************************/
/*******  custom js    *********/
/*******************************/
/*******************************/
//V4.5.0

//with jQuery
/*
buf_custom_try();

function buf_custom_try(){
	try {
	  if(jQuery) {
	    buf_custom_js_init();
	    //close if jQuery statement
	  }
	}catch(e){
		if(buf_debug == 1){
	  		console.log("buf_custom_try: jQuery not active... waitting");
		}

	  setTimeout(function() { buf_custom_try() }, 1000);
	}
}

function buf_custom_js_init(){
  jQuery(document).ready(function($) {

	console.log("****************hola*****************");

  });
}
*/

//vanilla

document.addEventListener('DOMContentLoaded', function() {
    //console.log('The document is ready!');
});

