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

	jQuery.loadScript(buf_path+'/js/bs4/bootstrap.bundle.min.js', function(){

	   		if(buf_params.debug == 1) console.log('BS4 loaded from js. Async: '+bs_load_async);
	});
}

/*
function loadBufoc(){

	jQuery.loadScript = function (url, callback) {
		    jQuery.ajax({
		        url: url,
		        dataType: 'script',
		        success: callback,
		        async: init_bufoc
		    });
		}

	jQuery.loadScript(buf_path+'/libs/offcanvas/canvi.js', function(e){

	   		//var bufoc = new Bufoc({console:"lala"});

	   		buf_oc = new Canvi({
			    content: '#superwrapper',
			    navbar: '#buf_offcanvas',
			    openButton: '#bufoc_button'
			});


	});
}
*/


/**************************/
/**************************/
/****** OFFCANVAS *********/
/**************************/
/**************************/

/*
function offcanvas_v1(){
	//OFFCANVAS
	//console.log(php_tm_juri);

    if(buf_params.offcanvas == false){
        buf_debug('offcanvas disabled');
        return; 
    }
    jQuery('html').addClass('buf_canvas');

	//DEVICE
	if(buf_params.detection == 'device'){

		if(jQuery('.offcanvas').length >=1){
			
			var window_h = jQuery(window).height();
		
			//button
			jQuery('.offcanvas-button').click(function(){

				if(jQuery('body').hasClass('offcanvas_show')){
					jQuery('body').removeClass('offcanvas_show');
				}else{
					jQuery('body').addClass('offcanvas_show');
				}
			});

			//buf debug in offcanvas
			if(jQuery('.buf_debug').length >=1){
				jQuery('.offcanvas-inner').append(jQuery('.buf_debug'));
			}

			//offcanvas_drag();
		}
	}
	//MEDIA
	if(buf_params.detection == 'media'){

		buf_debug('Media detected');
		offcanvas_media();
		offcanvas_resize();

	}




	offcanvasClick();
}


function offcanvas_media(){


	var ancho = jQuery(window).width();

	jQuery('body').removeClass('buf_oc_'+buf_vars.currentDevice);

	if(ancho<=buf_params.media_w && buf_vars.currentDevice=='desktop'){

		buf_vars.currentDevice = 'mobile';
		offcanvasCreate();

		buf_debug("It looks like mobile");

	}else if(ancho>=buf_params.media_w && buf_vars.currentDevice=='mobile'){

		buf_vars.currentDevice = 'desktop';
		offcanvasRemove();
		buf_debug("It looks like desktop");
	}

	jQuery('body').addClass('buf_oc_'+buf_vars.currentDevice);



}



function offcanvasCreate(){

	//BUTTON
	jQuery('#bufoc_button').show();
	var window_h = jQuery(window).height();

}

function offcanvasRemove(){
	if(buf_params.offcanvas == 'mobile'){
		jQuery('#bufoc_button').hide();
		jQuery('body').removeClass('offcanvas_show');


		//HIDE offcanvas
		jQuery('body').removeClass('offcanvas_show');
		jQuery('#bufoc_button').removeClass('is-active');
		jQuery('#buf_offcanvas').one(
			"webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend",
	    	function(event) {
	    		if(!jQuery('body').hasClass('offcanvas_show')){
	    			jQuery('body').addClass("buf_offcanvas_hidden");
	    			jQuery('html').removeClass('buff_canvas_on');
	    		}
		});

	}
}



function offcanvas_resize(){

	jQuery( window ).resize(function() {
		offcanvas_media();
	});

}


*/

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


