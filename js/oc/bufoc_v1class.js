/**************************/
/**************************/
/****** OFFCANVAS *********/
/**************************/
/**************************/


'use strict';
class offcanvas {

    constructor () {
        this.buf_params = JSON.parse(php_buf_params).params;

        this.ocbutton = document.querySelector('#bufoc_button');
        this.bufcanvas = document.querySelector('#buf_offcanvas');
        this.superwrapper = document.querySelector('#superwrapper');

        this.offcanvasClick = this.offcanvasClick.bind(this);
        this.showOffcanvas  = this.showOffcanvas.bind(this);

        this.startX = 0;
        this.currentX = 0;
        this.touchingSideNav = false;

        this.show_complete = false;
        this.hide_complete = false;

        this.onSuperwrapperTouchStart = this.onSuperwrapperTouchStart.bind(this);
        this.onSuperwrapperTouchMove = this.onSuperwrapperTouchMove.bind(this);
        this.onSuperwrapperTouchEnd = this.onSuperwrapperTouchEnd.bind(this);

        this.onTouchStart = this.onTouchStart.bind(this);
        this.onTouchMove = this.onTouchMove.bind(this);
        this.onTouchEnd = this.onTouchEnd.bind(this);

        this.update = this.update.bind(this);


        this.addEventListeners();
    }








    addEventListeners () {
        if(this.buf_params.offcanvas == false){
            return; 
        }

        this.ocbutton.addEventListener('click', this.offcanvasClick);


        this.bufcanvas.addEventListener('touchstart', this.onTouchStart, this.applyPassive());
        this.bufcanvas.addEventListener('touchmove', this.onTouchMove, this.applyPassive());
        this.bufcanvas.addEventListener('touchend', this.onTouchEnd);


        this.superwrapper.addEventListener('touchstart', this.onSuperwrapperTouchStart, this.applyPassive());
        this.superwrapper.addEventListener('touchmove', this.onSuperwrapperTouchMove, this.applyPassive());
        this.superwrapper.addEventListener('touchend', this.onSuperwrapperTouchEnd);


    }




    applyPassive () {
        if (this.supportsPassive !== undefined) {
          return this.supportsPassive ? {passive: true} : false;
        }
        // feature detect
        let isSupported = false;
        try {
          document.addEventListener('test', null, {get passive () {
            isSupported = true;
          }});
        } catch (e) { }
        this.supportsPassive = isSupported;
        return this.applyPassive();
    }



    update () {
        
        if (!this.touchingSideNav)
        return;

        requestAnimationFrame(this.update);

        const translateX = Math.min(0, this.currentX - this.startX);

        console.log('update: '+translateX);
        if (translateX < -50) {
            this.bufcanvas.style.transform = `translateX(${translateX}px)`;
        }
        
    }







    /****************************/
    //SHOW HIDE behavior
    /****************************/
    offcanvasClick(){
        console.log('offcanvasClick');
        //SHOW
        if(!this.ocbutton.classList.contains('is-active')){
            this.showOffcanvas();
        }else{
            this.hideOffcanvas();
        }
     };

    showOffcanvas() {
        console.log('showOffcanvas');
        this.ocbutton.classList.add('is-active');
        document.documentElement.classList.add('buff_canvas_on');
        document.body.classList.add('offcanvas_show');
        document.body.classList.remove('buf_offcanvas_hidden');
        buf_offcanvas.ontransitionend = () => {
            console.log("Show transition complete");
            this.show_complete = true;  
        };
    }

    hideOffcanvas() {
        console.log('hideOffcanvas');
        document.body.classList.remove('offcanvas_show');
        this.ocbutton.classList.remove('is-active');
        buf_offcanvas.ontransitionend = () => {
            if(!document.body.classList.contains('offcanvas_show')){
                document.body.classList.add("buf_offcanvas_hidden");
                document.documentElement.classList.remove('buff_canvas_on');
            }
        };
    }






    /****************************/
    /****************************/
    //THOUCH SUPERWRAPPER behavior
    /****************************/
    /****************************/
    onSuperwrapperTouchStart (evt) {
        
        
        if (this.ocbutton.classList.contains('is-active'))
        return;
        console.log('onSuperwrapperTouchStart');


        this.showOffcanvas();
        this.startX = evt.touches[0].pageX;
        const translateX = Math.min(0, -300);

        

        this.bufcanvas.style.transition = `inherit`;
        //requestAnimationFrame(this.update);
        this.bufcanvas.style.transform = `translateX(${translateX}px)`;
    }

    onSuperwrapperTouchMove (evt) {
       
        if (this.show_complete)
        return;
        console.log('onSuperwrapperTouchMove');

        this.currentX = evt.touches[0].pageX;

        const translateX = Math.min(0, this.currentX - 300);

        console.log(this.currentX);

        this.bufcanvas.style.transform = `translateX(${translateX}px)`;


    }

    onSuperwrapperTouchEnd(evt){


        if(this.currentX >= 50){
            this.bufcanvas.style = ``;
            this.showOffcanvas();
        }else{
            this.bufcanvas.style = ``;
            this.hideOffcanvas();
        }



    }

    /****************************/
    /****************************/
    /****************************/
    //THOUCH BAR behavior
    /****************************/
    /****************************/
    /****************************/


    onTouchStart (evt) {
        console.log('onTouchStart');
        console.log(this);
        if (!this.ocbutton.classList.contains('is-active'))
        return;

        this.startX = evt.touches[0].pageX;
        this.currentX = this.startX;

        this.touchingSideNav = true;
        this.bufcanvas.style.transition = `inherit`;
        requestAnimationFrame(this.update);
    }

    onTouchMove (evt) {
        console.log('onTouchMove');
        if (!this.touchingSideNav)
        return;

        this.currentX = evt.touches[0].pageX;

        //console.log(this.startX);

    }

    onTouchEnd (evt) {
        console.log('onTouchEnd');
        if (!this.touchingSideNav) return;

        this.touchingSideNav = false;

        const translateX = Math.min(0, this.currentX - this.startX);
        
        this.bufcanvas.style = ``;
        this.hideOffcanvas();

    }













    offcanvas(){
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




        //offcanvasClick();
    }


    offcanvas_media(){


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


    /*
        var ancho = jQuery(window).width();
            buf_vars.currentDevice = 'desktop';
            if(ancho<=buf_params.media_w && buf_vars.currentDevice) {
                buf_vars.currentDevice = 'mobile';
            }
    */
    }



    offcanvasCreate(){

        //BUTTON
        jQuery('#bufoc_button').show();
        var window_h = jQuery(window).height();

    }

    offcanvasRemove(){
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



    offcanvas_resize(){

        jQuery( window ).resize(function() {
            offcanvas_media();
        });

    }









}


new offcanvas();
