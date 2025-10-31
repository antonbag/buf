var buf_params;
var buf_vars = Array();
var buf_oc;

 
function buf_debug(msg){
	if(buf_params.debug) console.log('BUF |-*-| '+msg);
}
function buf_debug_n(msg){
	if(buf_params.debug) console.log('BUF |-*-| '+msg);
}

function buf_js_init(){

	buf_params = Joomla.getOptions('buf.config').params;

	//READY
	document.addEventListener('DOMContentLoaded', function() {
		

		//lazy css
		[].slice.call(document.head.querySelectorAll('link[rel="lazy-stylesheet"]'))
		.forEach(function($link){
		  $link.rel = "stylesheet";
		});


		//UNDEFINED
		buf_vars.currentDevice = 'mobile';


		buf_debug_n("buf_js_init");


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
buf_js_init();


/**************************/
/**************************/
/****** OFFCANVAS DEPRECATED????*********/
/**************************/
/**************************/
/*
function offcanvasClick(){
	
	const ocbutton = document.querySelector("#bufoc_button");
	const buf_offcanvas = document.querySelector("#buf_offcanvas");


	//CLICK
	ocbutton.addEventListener("click", function(e) {

		e.stopPropagation;

		//console.log(ocbutton);
		//console.log(document.documentElement);
		//SHOW
		if(!ocbutton.classList.contains('is-active')){
			//vanilla
			ocbutton.classList.add('is-active');
			document.documentElement.classList.add('buff_canvas_on');
			document.body.classList.add('offcanvas_show');
			document.body.classList.remove('buf_offcanvas_hidden');

		}else{
			//HIDE
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
*/




