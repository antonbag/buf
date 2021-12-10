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
	jQuery(document).ready(function($) {
		
		//UNDEFINED
		buf_vars.currentDevice = 'mobile';


		buf_debug_n("buf_try: jQuery loaded!");

		//offcanvas_v1();
		
		//loadBufoc();



		if(bs_load == 'true'){
			loadBS();
		}

		if(jQuery('div.buf_dev_mode').length >= 1){
			jQuery('a.buf_dev_mode_close').click(function(e){
				e.stopPropagation();
				jQuery('div.buf_dev_mode').hide('slow');
			});
		}

	});
}



function getBufParams(){

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











/**************************/
/**************************/
/****** BS4 script ********/
/**************************/
/**************************/

/**
 *  j4. Use JTfunc::getPromiseScript([]);
 */
function loadBS(){

	jQuery.loadScript = function (url, callback) {
		    jQuery.ajax({
		        url: url,
		        dataType: 'script',
		        success: callback,
		        async: bs_load_async
		    });
		}
	 
		if(bs_version == 4){
			jQuery.loadScript(buf_params.buf_path+'/libs/bootstrap4/dist/js/bootstrap.bundle.min.js', function(){
				if(buf_params.debug == 1) console.log('BS4 loaded from js. '+buf_params.buf_path+'/libs/bootstrap4/dist/js/bootstrap.bundle.min.js Async: '+bs_load_async);
			
			 });
		}

		if(bs_version == 5){
			jQuery.loadScript(buf_params.jtlibs_media+'/bootstrap/js/bootstrap.bundle.min.js', function(){
				if(buf_params.debug == 1) console.log('BS5 loaded from js. Async: '+bs_load_async);
			 });
		}

}


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
		//if(!jQuery(this).hasClass('is-active')){
			
			//jquery
			/*jQuery(this).addClass('is-active');
			jQuery('html').addClass('buff_canvas_on');
			jQuery('body').addClass('offcanvas_show').removeClass('buf_offcanvas_hidden');*/

			//vanilla
			ocbutton.classList.add('is-active');
			document.documentElement.classList.add('buff_canvas_on');
			document.body.classList.add('offcanvas_show');
			document.body.classList.remove('buf_offcanvas_hidden');

		}else{
		//HIDE
			//jquery
			/*
			jQuery('body').removeClass('offcanvas_show');
			jQuery(this).removeClass('is-active');
			jQuery('#buf_offcanvas').one(
				"webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend",
		    	function(event) {
		    		if(!jQuery('body').hasClass('offcanvas_show')){
		    			jQuery('body').addClass("buf_offcanvas_hidden");
		    			jQuery('html').removeClass('buff_canvas_on');
		    		}
			});
			*/

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





