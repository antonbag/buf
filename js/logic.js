var buf_params;
var buf_vars = Array();
var buf_oc;
var buf_params = JSON.parse(php_buf_params).params;


buf_try();

function buf_try(){
	try {
	  if(jQuery) {
	    buf_js_init();
	    //close if jQuery statement
	  }
	}catch(e){
		if(buf_params.debug){
	  		console.log("buf_try: jQuery not active. Waitting...");
		}

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
			jQuery.loadScript(buf_path+'/libs/bootstrap4/dist/js/bootstrap.bundle.min.js', function(){
				if(buf_params.debug == 1) console.log('BS4 loaded from js. Async: '+bs_load_async);
			 });
		}

		if(bs_version == 5){
			jQuery.loadScript(buf_path+'/libs/bootstrap/dist/js/bootstrap.bundle.min.js', function(){
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









//DRAGABLE
//v1
/*
function offcanvas_drag(){

	var text_enter=false;
	var tamano_ventana = jQuery(document).width();
	var pocentaje = 0;
	var currentX = 0;

	var touch_number = -25;
	if(jQuery('#buf_offcanvas').hasClass('buf_off_pos_right')){
		touch_number = -200;
	}


	jQuery(document).mouseup(function(e) {
	    	
	    	if(e.clientY <= 50) return;
			if(jQuery('body').hasClass('offcanvas_show')) return;
			if(text_enter==false) return;
	    	
	    	//OPEN RIGHT
			if(jQuery('#buf_offcanvas').hasClass('buf_off_pos_right')){
				
				//console.log('e.clientX-currentX:'+(e.clientX-currentX));

				if((e.clientX-currentX)<=touch_number){
		    		//ABRIMOS
		    		jQuery('.offcanvas-button').trigger('click');
		    		jQuery('#buf_offcanvas').removeAttr( 'style' );
		    		jQuery('#buf_offcanvas').removeClass('buff_off_animate');
		    	}else{
		    		//CERRAMOS
		    		jQuery('#buf_offcanvas').removeAttr( 'style' );
		    		jQuery('#buf_offcanvas').removeClass('buff_off_animate');
		    	}
			}else{
				//OPEN LEFT
				if((currentX-e.clientX)<=touch_number){
	    		//ABRIMOS
	    		jQuery('.offcanvas-button').trigger('click');
	    		jQuery('#buf_offcanvas').removeAttr( 'style' );
	    		jQuery('#buf_offcanvas').removeClass('buff_off_animate');
	    	}else{
	    		//CERRAMOS
	    		jQuery('#buf_offcanvas').removeAttr( 'style' );
	    		jQuery('#buf_offcanvas').removeClass('buff_off_animate');
	    	}
			}
	    	

	    	text_enter=false;
	    	currentX = e.clientX;

	  	}).mousedown(function(e) {

	  		if(jQuery('body').hasClass('offcanvas_show')) return;

	  		if(e.clientY <= 50) return;

	  		//OPEN RIGHT
			if(jQuery('#buf_offcanvas').hasClass('buf_off_pos_right')){
				if(e.clientX >= (tamano_ventana-80)) text_enter=true;
			}else{
				if(e.clientX <= 80) text_enter=true;
			}

	  		currentX=e.clientX;

  		}).mousemove(function(ta){

  			if(ta.clientY <= 50) return;
  			if(jQuery('body').hasClass('offcanvas_show')) return;
  	  		if(text_enter==false) return;

  	  		porcentaje = (ta.clientX*100)/tamano_ventana-100;

  	  		jQuery('#buf_offcanvas').css('transform', 'translateX('+porcentaje+'%)');
  	  		jQuery('#buf_offcanvas').addClass('buff_off_animate');

  	});




}
*/


