//J4
Joomla = window.Joomla || {};
(function(Joomla, document) {
	'use strict';
  document.addEventListener('DOMContentLoaded', function (event) {

    [].slice.call(document.head.querySelectorAll('link[rel="lazy-stylesheet"]'))
      .forEach(function($link){
        $link.rel = "stylesheet";
      });
  });

})(Joomla, document);
 


var buf_params;
var buf_vars = Array();
var buf_oc;


buf_try();

function buf_try(){
	try {
	  if(jQuery) {
	    buf_js_init();
	    //close if jQuery statement
	  }
	}catch(e){
		
	  console.log("buf_try: jQuery not active. Waitting...");

	  setTimeout(function() { buf_try() }, 1000);
	}
}
 
function buf_debug(msg){
	if(buf_params.debug) console.log('BUF |-*-| '+msg);
}
function buf_debug_n(msg){
	if(buf_params.debug) console.log('BUF |-*-| '+msg);
}

function buf_js_init(){

	buf_params = JSON.parse(php_buf_params).params;


	//READY
	document.addEventListener('DOMContentLoaded', function() {
		
		//UNDEFINED
		buf_vars.currentDevice = 'mobile';


		buf_debug_n("buf_try: jQuery loaded!");

/* 		if(jQuery('div.buf_dev_mode').length >= 1){
			jQuery('a.buf_dev_mode_close').click(function(e){
				e.stopPropagation();
				jQuery('div.buf_dev_mode').hide('slow');
			});
		} */

		const devModeDivs = document.querySelectorAll('div.buf_dev_mode');
		if (devModeDivs.length >= 1) {
		  const devModeCloseButtons = document.querySelectorAll('a.buf_dev_mode_close');
		  devModeCloseButtons.forEach(button => {
			button.addEventListener('click', function(e) {
			  e.stopPropagation();
			  devModeDivs.forEach(div => div.style.display = 'none');
			});
		  });
		}

	});
}


/********************************** */
/********************************** */
/****   DEP   ************* */
/********************************** */
/********************************** */
/*
function getBufParams(){
	return;
	var data = {
	    action : action,
	    tpath: tpath,
	    layout: buf_layout
	};

	var request = {
	    'option' : 'com_ajax',
	    'plugin' : 'bufajax',
	    'data'   : data,
	    'format' : 'json'
	};

	jQuery.ajax({
	    type   : 'POST',
	    dataType: 'json',
	    data   : request,
	    beforeSend: function( xhr ) {
	        //$( '#compile_bs_sass > .btn' ).html('<i class="fa fa-cog fa-spin buf-loading"></i> processing');
	                           
	    },
	    success: function (response) {

	        

	    },
	    error: function(response){
	        //alert('Somethings wrong, Try again');
	        console.log(response);

	    }
	})

	return buf_params;
}
*/



/**************************/
/**************************/
/****** OFFCANVAS *********/
/**************************/
/**************************/

function offcanvasClick(){
	
	//jQuery('#bufoc_button').click(function(e){

	const ocbutton = document.querySelector("#bufoc_button");
	const buf_offcanvas = document.querySelector("#buf_offcanvas");

	//CLICK
	ocbutton.addEventListener("click", function(e) {

		e.stopPropagation;

		//console.log(ocbutton);
		console.log(document.documentElement);
		//SHOW
		if(!ocbutton.classList.contains('is-active')){
			//vanilla
			ocbutton.classList.add('is-active');
			document.documentElement.classList.add('buff_canvas_on');
			document.body.classList.add('offcanvas_show');
			document.body.classList.remove('buf_offcanvas_hidden');

		}else{
			//HIDE
			//vanilla
			document.body.classList.remove('offcanvas_show');
			ocbutton.classList.remove('is-active');
			buf_offcanvas.ontransitionend = () => {
		  		if(!document.body.classList.contains('offcanvas_show')){
	    			document.body.classList.add("buf_offcanvas_hidden");
	    			document.documentElement.classList.remove('buff_canvas_on');
	    		}
			};
		}
	});  

};





