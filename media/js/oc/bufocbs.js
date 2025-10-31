/**************************/
/****** OFFCANVAS *********/
/**************************/
'use strict';
class offcanvas {

    constructor () {

        this.buf_params = Joomla.getOptions('buf.config').params;
        this.ocbutton = document.querySelector('#bufoc_button');

        ///////
        this.init();

    }


    init(){

        if(this.buf_params.offcanvas == false){
            this.buf_debug('offcanvas disabled');
            return; 
        }
        this.addEventListeners();
        document.documentElement.classList.add('buf_canvas');
    }


    addEventListeners () {
        this.buf_debug('offcanvas disabled');
        //this.ocbutton.addEventListener('click', this.offcanvasClick);
        this.ocbutton.addEventListener('click', () => this.offcanvasClick());
        //this.buf_debug('addEventListener');
        /*
        if(this.buf_params.oc_style == 'buf_off_cover'){
            this.superwrapper.addEventListener('touchstart', this.onSuperwrapperTouchStart, this.applyPassive());
            this.superwrapper.addEventListener('touchmove', this.onSuperwrapperTouchMove, this.applyPassive());
            this.superwrapper.addEventListener('touchend', this.onSuperwrapperTouchEnd);
        }
        */

    }

    /****************************/
    /****************************/
    //SHOW HIDE behavior
    /*****************************/
    /*****************************/
 
    offcanvasClick(){
        this.buf_debug('offcanvasClick');
        //SHOW
        if(!this.ocbutton.classList.contains('is-active')){
            this.ocbutton.classList.add('is-active');
            //this.showOffcanvas();
        }else{
            this.ocbutton.classList.remove('is-active');
            //this.hideOffcanvas();
        }
     };


/*
    offcanvas_media(){

        const ancho = window.innerWidth;
        this.vw = ancho;

        document.body.classList.remove('buf_oc_'+this.currentDevice);

        this.buf_debug(ancho);
        this.buf_debug(this.buf_params.media_w);

        if(ancho<=this.buf_params.media_w && this.currentDevice=='desktop'){

            this.currentDevice = 'mobile';
            this.offcanvasCreate();
            this.buf_debug('looks like mobile');

        }else if(ancho>=this.buf_params.media_w && this.currentDevice=='mobile'){

            this.currentDevice = 'desktop';
            this.offcanvasRemove();
            this.buf_debug("It looks like desktop");
        }

        document.body.classList.add('buf_oc_'+this.currentDevice);

        if(this.currentDevice == 'mobile'){
            this.oc_max_px = this.max_mobile*(this.vw/100);
        }else{
            this.oc_max_px = this.max_desktop*(this.vw/100);
        }
        
    }


    offcanvasCreate(){
        this.buf_debug("show oc button");
        //show BUTTON
        this.topbar.classList.add('buf_oc_show');
        this.ocbutton.classList.add('buf_oc_show');

        this.topbar.classList.remove('buf_oc_hide');
        this.ocbutton.classList.remove('buf_oc_hide');

        this.topbar.setAttribute('aria-disabled', 'false');
        this.bufcanvas.setAttribute('aria-disabled', 'false');
        this.bufcanvas.setAttribute('aria-hidden', 'false');
    }

    offcanvasRemove(){

        if(this.buf_params.offcanvas == 'mobile'){
            this.topbar.classList.add('buf_oc_hide');
            this.ocbutton.classList.add('buf_oc_hide');

            this.topbar.classList.remove('buf_oc_show');
            this.ocbutton.classList.remove('buf_oc_show');

            this.ocbutton.classList.remove('is-active');

            this.topbar.setAttribute('aria-disabled', 'true');
            this.bufcanvas.setAttribute('aria-disabled', 'true');
            this.bufcanvas.setAttribute('aria-hidden', 'true');
            
            //HIDE offcanvas
            document.body.classList.remove('offcanvas_show');

            const offcanvas = document.querySelector('#buf_offcanvas');

            offcanvas.addEventListener('transitionend', function(event) {
              if (!document.body.classList.contains('offcanvas_show')) {
                document.body.classList.add('buf_offcanvas_hidden');
                document.documentElement.classList.remove('buff_canvas_on');
              }
            });
        }
    }


    //TODO
    offcanvas_resize(){
        const media = this.offcanvas_media;
        window.addEventListener('resize', media);
     }






    ////////////////////////////////
    //THOUCH SUPERWRAPPER behavior
    ////////////////////////////////

    onSuperwrapperTouchStart (evt) {

        //console.log('Start');
        this.startX = evt.touches[0].pageX;
 

        //to OPEN
        if(!this.show_complete){

            //<------left
            if(this.buf_params.oc_position == 'buf_off_pos_left'){
                
                if(this.startX >= 50)
                return;

                this.buf_debug("touch start left");

                this.bufcanvas.style.transform = `translateX(-102%)`;
                this.bufcanvas.style.transition = `inherit`;
                this.super_started = true;
            }

            //------>right
            if(this.buf_params.oc_position == 'buf_off_pos_right'){
                
                if(this.startX <= (this.vw-50))
                return;
                this.buf_debug("touch start right: "+this.startX+'<='+(this.vw-50));
                                    
                this.bufcanvas.style.transform = `translateX(102%)`;
                this.bufcanvas.style.transition = `inherit`;
                this.super_started = true;
            }
        }



        //to CLOSE
        if(this.show_complete){
            this.super_started = true;
            this.bufcanvas.style.transition = `inherit`;
            //<------left
            if(this.buf_params.oc_position == 'buf_off_pos_left'){
                this.bufcanvas.style.transform = `translateX(0%)`;
            }
            //------>right
            if(this.buf_params.oc_position == 'buf_off_pos_right'){
                this.bufcanvas.style.transform = `translateX(-100%)`;
            }
        }
       
    }


    onSuperwrapperTouchMove (evt) {

        if(!this.super_started)
        return;

        this.currentX = evt.touches[0].pageX;
        let percent = 100-(this.currentX*100)/this.vw;



        //<------left
        if(this.buf_params.oc_position == 'buf_off_pos_left'){
            
            //avoid click
            if(this.show_started === false){
                this.showOffcanvas();
            }
            if(this.show_complete){
                percent = -((this.currentX-this.startX)*100)/this.oc_max_px;
            }

            this.bufcanvas.style.transform = `translateX(-${percent}%)`;
        }


        //------>right
        if(this.buf_params.oc_position == 'buf_off_pos_right'){
           
            //avoid click
            if(this.show_started === false){
                this.showOffcanvas();
            }

            if(this.show_complete){
                percent = 100-((this.currentX-this.startX)*100)/this.oc_max_px;
            }


            //let recorrido = this.startX
            if(percent >=100) percent = 100;

            this.bufcanvas.style.transform = `translateX(-${percent}%)`;
            
        }

        this.percent = percent;

    }




    onSuperwrapperTouchEnd(evt){
        if(!this.super_started)
        return;
        this.super_started = false;


        //to OPEN
        if(!this.show_complete){
            
            //<------left
            if(this.buf_params.oc_position == 'buf_off_pos_left'){

                if(this.currentX >= 50){
                    this.bufcanvas.style = ``;
                    //this.showOffcanvas();
                }else{
                    this.bufcanvas.style = ``;
                    this.hideOffcanvas();
                }
            }
            
            //------>right
            if(this.buf_params.oc_position == 'buf_off_pos_right'){
                if(this.currentX <= (this.vw-100)){
                    this.bufcanvas.style = ``;
                    //this.showOffcanvas();
                }else{
                    this.bufcanvas.style = ``;
                    this.hideOffcanvas();
                }
            }
        }

        //to CLOSE
        if(this.show_complete){
           

            //<------left
            if(this.buf_params.oc_position == 'buf_off_pos_left'){
                if(this.percent >= 20){
                    this.bufcanvas.style = ``;
                    this.hideOffcanvas();
                }else{
                    this.bufcanvas.style = ``;
                }
            }

            //------>right
            if(this.buf_params.oc_position == 'buf_off_pos_right'){
               
                const resta = 100-this.percent;



                if(resta >= 20){
                    this.bufcanvas.style = ``;
                    this.hideOffcanvas();
                }else{
                    this.bufcanvas.style = ``;
                }
            }

        }

    }

*/






    /*
    showOffcanvas() {
        this.buf_debug('showOffcanvas');
        this.show_started = true;

        this.ocbutton.classList.add('is-active');
        document.documentElement.classList.add('buff_canvas_on');
        document.body.classList.add('offcanvas_show');
        document.body.classList.remove('buf_offcanvas_hidden');
        buf_offcanvas.ontransitionend = () => {
            this.buf_debug("Show transition complete");
            this.show_complete = true;
            this.show_started = false;
        };
    }

    hideOffcanvas() {
        this.buf_debug('hideOffcanvas');
        document.body.classList.remove('offcanvas_show');
        this.ocbutton.classList.remove('is-active');
        buf_offcanvas.ontransitionend = () => {
            if(!document.body.classList.contains('offcanvas_show')){
                document.body.classList.add("buf_offcanvas_hidden");
                document.documentElement.classList.remove('buff_canvas_on');
                this.show_complete = false;
                this.show_started = false;
            }
        };
    }
    */


    /*
    /////////////////////////////
    //ACC
    /////////////////////////////

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
*/

    buf_debug(msg){
        if(this.buf_params.debug) console.log('BUF |-*-| '+msg);
    }

}


new offcanvas();

