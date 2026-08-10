/**************************/
/****** OFFCANVAS *********/
/**************************/
'use strict';
class offcanvas {

    constructor () {
        this.buf_params = Joomla.getOptions('buf.config').params;
        this.ocbutton   = document.querySelector('#bufoc_button');
        this.occanvas   = document.querySelector('#bsOffcanvas');

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

    addEventListeners() {
        this.buf_debug('offcanvas addEventListeners');
        this.ocbutton.addEventListener('click', () => this.offcanvasClick());

        // Bootstrap 5: gestión de inert para bloquear foco en descendientes
        if (this.occanvas) {
            this.occanvas.addEventListener('show.bs.offcanvas',   () => this.onShow());
            this.occanvas.addEventListener('hidden.bs.offcanvas', () => this.onHidden());
        }
    }

    /****************************/
    /****************************/
    // SHOW HIDE behavior
    /****************************/
    /****************************/

    offcanvasClick(){
        this.buf_debug('offcanvasClick');
        this.ocbutton.classList.toggle('is-active');
    }

    onShow(){
        this.buf_debug('offcanvas opening — inert removed');
        this.occanvas.removeAttribute('inert');
    }

    onHidden(){
        this.buf_debug('offcanvas closed — inert restored');
        this.occanvas.setAttribute('inert', '');
    }

    buf_debug(msg){
        if(this.buf_params.debug) console.log('BUF |-*-| ' + msg);
    }
}

new offcanvas();