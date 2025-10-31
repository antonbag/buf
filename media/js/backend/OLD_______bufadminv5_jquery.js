var buf_editor;



// Vanilla JS DOMContentLoaded
document.addEventListener('DOMContentLoaded', function () {
    
    function when_external_loaded(callback) {
        if (typeof Joomla.editors.instances.jform_params_scss_editor === 'undefined' || document.querySelectorAll('joomla-editor-codemirror').length == 0) {
            setTimeout(function () {
                when_external_loaded(callback);
            }, 500);
        } else { 
            callback();
        }
    }

    when_external_loaded(function () {
        document.getElementById('jform_params_scss_editor').setAttribute('name', 'cleaned_editor');
        
        buf_editor = Joomla.editors.instances.jform_params_scss_editor;

        buf_load_sccs_file();
        buf_handle_delete_button();

        setTimeout(function(){
            const checkedInput = document.querySelector('#jform_params_buf_layout_files input:checked');
            if (checkedInput) {
                checkedInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }, 300);

        setTimeout(function(){
            const checkedInput = document.querySelector('#jform_params_buf_layout_files input:checked');
            if (checkedInput) {
                checkedInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }, 500);
    });
});




/************** LAYOUT ****************/
/**************************************/
/******     LOAD SCSS FILE     ********/
/**************************************/
/**************************************/
function buf_load_sccs_file(){
    
    // Insert save toolbar
    const buttons = document.querySelector('.buf_scss_toolbar');
    const codemirror = document.querySelector('joomla-editor-codemirror');
    if (buttons && codemirror) {
        codemirror.parentNode.insertBefore(buttons, codemirror);
    }
    
    // Hide icons
    const icons = document.querySelectorAll('.buf_tb_icon');
    icons.forEach(icon => icon.style.display = 'none');

    // Handle radio button changes
    const radioButtons = document.querySelectorAll('#jform_params_buf_layout_files input');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function(event) {
            const container = document.querySelector('#jform_params_buf_layout_files');
            
            setTimeout(function(){
                const checkedInput = container.querySelector('input:checked');
                const scss_selected = checkedInput ? checkedInput.value : '';

                // Element mode handling
                if(scss_selected === 'element') {
                    const elementSelect = document.querySelector('#jform_params_buf_layout_edit_elements');
                    const element_selected = elementSelect ? elementSelect.value : '';
                    
                    if(element_selected && element_selected !== '' && element_selected !== '-1'){
                        loadElementFile(element_selected);
                    } else {
                        const pathElement = document.querySelector('.buf_tb_path');
                        if (pathElement) {
                            pathElement.innerHTML = 'buf/layouts/' + buf_layout + '/elements/ - Select an element';
                        }
                        console.log('Element mode selected. Please choose an element from the dropdown.');
                    }
                    return;
                }

                // Regular file loading
                const data = {
                    templateid: templateid,
                    action: 'doreadsccsfile',
                    tpath: tpath,
                    file: scss_selected,
                    layout: buf_layout
                };

                const request = {
                    'option': 'com_ajax',
                    'plugin': 'bufajax',
                    'data': data,
                    'format': 'json'
                };

                fetch('index.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(request)
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    
                    if (buf_editor && data.data && data.data[0]) {
                        buf_editor.setValue(data.data[0]);
                    }

                    const pathElement = document.querySelector('.buf_tb_path');
                    if (pathElement) {
                        if(scss_selected == 'buf_layout.js'){
                            pathElement.innerHTML = 'buf/layouts/' + buf_layout + '/js/' + scss_selected;
                        } else if(scss_selected == 'layout.php'){
                            pathElement.innerHTML = 'buf/layouts/' + buf_layout + '/' + scss_selected;
                        } else {
                            pathElement.innerHTML = 'buf/layouts/' + buf_layout + '/scss/' + scss_selected + '.scss';
                        }
                    }
                })
                .catch(error => {
                    console.log('ERROR in load scss: ' + error);
                });

            }, 150);
        });
    });

    // Handle element selection changes
    const elementSelect = document.querySelector('#jform_params_buf_layout_edit_elements');
    if (elementSelect) {
        elementSelect.addEventListener('change', function(event) {
            const layoutFilesChecked = document.querySelector('#jform_params_buf_layout_files input:checked');
            const layout_files_selected = layoutFilesChecked ? layoutFilesChecked.value : '';
            
            if(layout_files_selected !== 'element') {
                return;
            }
            
            setTimeout(function(){
                const element_selected = elementSelect.value;
                
                if(element_selected && element_selected !== '' && element_selected !== '-1'){
                    loadElementFile(element_selected);
                }
            }, 150);
        });
    }

    // Helper function to load element files
    function loadElementFile(element_selected) {
        const data = {
            templateid: templateid,
            action: 'doreadsccsfile',
            tpath: tpath,
            file: element_selected,
            layout: buf_layout,
            type: 'element'
        };

        const request = {
            'option': 'com_ajax',
            'plugin': 'bufajax',
            'data': data,
            'format': 'json'
        };

        fetch('index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(request)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Element loaded:', data);
            
            if (buf_editor && data.data && data.data[0]) {
                buf_editor.setValue(data.data[0]);
            }

            const pathElement = document.querySelector('.buf_tb_path');
            if (pathElement) {
                pathElement.innerHTML = 'buf/layouts/' + buf_layout + '/elements/' + element_selected + '.scss';
            }
        })
        .catch(error => {
            console.log('ERROR in load element scss: ' + error);
        });
    }
}


/************** LAYOUT ****************/
/**************************************/
/****** HANDLE DELETE BUTTON  ********/
/**************************************/
/**************************************/
function buf_handle_delete_button(){
    
    const deleteButton = document.querySelector('.buf_scss_delete');
    const deleteIcon = document.querySelector('.buf_delete_icon');
    
    // Verificar que los elementos existen
    if (!deleteButton) {
        console.log('Delete button not found');
        return;
    }
    
    // Inicialmente deshabilitar el botón delete y ocultar el spinner
    deleteButton.disabled = true;
    if (deleteIcon) {
        deleteIcon.style.display = 'none'; // Ocultar spinner inicialmente
    }
    
    // Manejar cambios en los radio buttons de layout files
    const radioButtons = document.querySelectorAll('#jform_params_buf_layout_files input');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            
            const current_element_container = document.querySelector('#jform_params_buf_layout_files');
            const checkedInput = current_element_container ? current_element_container.querySelector('input:checked') : null;
            const scss_selected = checkedInput ? checkedInput.value : '';
            
            // Activar/desactivar botón según selección
            if(scss_selected === 'element') {
                deleteButton.disabled = false;
                console.log('Delete button enabled - Element mode active');
            } else {
                deleteButton.disabled = true;
                console.log('Delete button disabled - Not in element mode');
            }
        });
    });

    // Manejar click del botón delete
    deleteButton.addEventListener('click', function(event) {
        event.preventDefault();
        
        const checkedInput = document.querySelector('#jform_params_buf_layout_files input:checked');
        const scss_selected = checkedInput ? checkedInput.value : '';
        
        if(scss_selected !== 'element') {
            alert('Delete is only available in element mode');
            return;
        }
        
        const elementSelect = document.querySelector('#jform_params_buf_layout_edit_elements');
        const element_selected = elementSelect ? elementSelect.value : '';
        
        if(!element_selected || element_selected === '' || element_selected === '-1') {
            alert('Please select an element to delete');
            return;
        }
        
        // Confirmar eliminación
        if(confirm('Are you sure you want to delete the element "' + element_selected + '"?')) {
            buf_delete_element(element_selected);
        }
    });
}

/************** LAYOUT ****************/
/**************************************/
/****** DELETE ELEMENT FUNCTION ******/
/**************************************/
/**************************************/
function buf_delete_element(element_name) {
    
    const deleteButton = document.querySelector('.buf_scss_delete');
    const deleteIcon = document.querySelector('.buf_delete_icon');
    
    // Mostrar spinner y deshabilitar botón al iniciar
    if (deleteIcon) {
        deleteIcon.style.display = 'inline-block';
    }
    if (deleteButton) {
        deleteButton.disabled = true;
    }
    
    const data = {
        templateid: templateid,
        action: 'dodeleteelement',
        tpath: tpath,
        file: element_name,
        layout: buf_layout,
        type: 'element'
    };

    const request = {
        'option': 'com_ajax',
        'plugin': 'bufajax',
        'data': data,
        'format': 'json'
    };

    fetch('index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(request)
    })
    .then(response => response.json())
    .then(data => {
        
        console.log('Delete response:', data);
        
        // Ocultar spinner
        if (deleteIcon) {
            deleteIcon.style.display = 'none';
        }
        
        if(data.data && data.data[0] === 'success') {
            // Limpiar el editor
            if (buf_editor) {
                buf_editor.setValue('/* Element deleted */');
            }
            
            // Actualizar la ruta
            const pathElement = document.querySelector('.buf_tb_path');
            if (pathElement) {
                pathElement.innerHTML = 'buf/layouts/' + buf_layout + '/elements/ - Element deleted';
            }
            
            // Recargar la lista de elementos (si usas Chosen)
            const elementSelect = document.querySelector('#jform_params_buf_layout_edit_elements');
            if (elementSelect && elementSelect.classList.contains('chzn-done')) {
                // Trigger chosen update
                const event = new Event('chosen:updated', { bubbles: true });
                elementSelect.dispatchEvent(event);
            }
            
            // Mostrar mensaje de éxito
            const msgElement = document.querySelector('.buf_tb_msg');
            if (msgElement) {
                msgElement.innerHTML = 'Element "' + element_name + '" deleted successfully';
                msgElement.style.display = 'block';
                
                // Fade in effect (opcional)
                msgElement.style.opacity = '0';
                msgElement.style.transition = 'opacity 0.5s';
                setTimeout(() => {
                    msgElement.style.opacity = '1';
                }, 10);
                
                setTimeout(function(){
                    msgElement.style.transition = 'opacity 0.5s';
                    msgElement.style.opacity = '0';
                    setTimeout(() => {
                        msgElement.style.display = 'none';
                        msgElement.style.opacity = '1';
                        msgElement.style.transition = '';
                    }, 500);
                }, 3000);
            }
            
            // Mantener botón habilitado para permitir más eliminaciones
            if (deleteButton) {
                deleteButton.disabled = false;
            }
            
        } else {
            // Error en la eliminación
            alert('Error deleting element: ' + (data.data ? data.data[0] : 'Unknown error'));
            
            // Rehabilitar botón
            if (deleteButton) {
                deleteButton.disabled = false;
            }
        }
        
    })
    .catch(error => {
        console.log('ERROR in delete element: ' + error);
        alert('Error deleting element');
        
        // Ocultar spinner y rehabilitar botón en caso de error
        if (deleteIcon) {
            deleteIcon.style.display = 'none';
        }
        if (deleteButton) {
            deleteButton.disabled = false;
        }
    });
}


document.addEventListener('DOMContentLoaded', function(){
    
    // Llamar todas las funciones de inicialización
    buf_favicon();
    buf_toolbar_custom();
    buf_bs4_custom();
    buf_fa_custom();
    
    buf_clean_file();
    
    buf_save_sccs_file();
    buf_check_bs_selector_click();
    buf_layout_new();
    buf_layout_change();
    buf_fa_seletion();
    
    buf_save_aply();
    buf_clear_cache();
    
    buf_zip_layout();
    
    buf_interface_4();

    // Trigger change events en elementos específicos
    const bsV4Selector = document.getElementById('jform_params__buf_bs_v4__buf_bs_selector');
    if (bsV4Selector) {
        const changeEvent = new Event('change', { bubbles: true });
        bsV4Selector.dispatchEvent(changeEvent);
    }

    const bsV5Selector = document.getElementById('jform_params__buf_bs_v5__buf_bs_selector');
    if (bsV5Selector) {
        const changeEvent = new Event('change', { bubbles: true });
        bsV5Selector.dispatchEvent(changeEvent);
    }

    // Obtener valor del layout SCSS
    const scssLayoutElement = document.getElementById('jform_params_buf_layout');
    let scss_layout = scssLayoutElement ? scssLayoutElement.value : '';
    
    if(scss_layout == null || scss_layout == '') {
        scss_layout = 'default';
    }
    
    // Hacer disponible globalmente si es necesario
    window.buf_layout = scss_layout;
});



jQuery(function($){
    "use strict";

    /*
    $(document).ready(function(){
      
        buf_favicon();
        buf_toolbar_custom();
        buf_bs4_custom();
        buf_fa_custom();

        buf_clean_file();
        
        buf_save_sccs_file();
        buf_check_bs_selector_click();
        buf_layout_new();
        buf_layout_change();
        buf_fa_seletion();

        buf_save_aply();
        buf_clear_cache();

        buf_zip_layout();
     
        buf_interface_4();

        $('#jform_params__buf_bs_v4__buf_bs_selector').trigger('change');
        $('#jform_params__buf_bs_v5__buf_bs_selector').trigger('change');

        var scss_layout = $('#jform_params_buf_layout').val();
        if(scss_layout == null || scss_layout == '') scss_layout = 'default';

    });
    */

    function buf_clean_file(){
        $( '.btn_buf_clean').on( 'click', function( event ) {
            event.preventDefault();
            var action = $(this).children('a').attr('class');

            var data = {
                templateid : templateid,
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

            $.ajax({
                type   : 'POST',
                dataType: 'json',
                data   : request,
                beforeSend: function( xhr ) {
                    //$( '#compile_bs_sass > .btn' ).html('<i class="fa fa-cog fa-spin buf-loading"></i> processing');
                                        
                },
                success: function (response) {

                    //BOOTSTRAP
                    if(action == 'do_clean_bs'){

                        $( 'div.do_clean_bs div.buf_toolbar_file_info' ).html(response.data);
                        $( 'div.do_clean_bs div.btn_buf_clean' ).remove();

                    }else{
                    //FA
                        $( 'div.do_clean_fa div.buf_toolbar_file_info' ).html(response.data);
                        $( 'div.do_clean_fa div.btn_buf_clean' ).remove();
                    }

                },
                error: function(response){
                    //alert('Somethings wrong, Try again');
                    console.log(response);
                    $( '#compile_fa_sass > .btn' ).html('<i class="fa fa-exclamation-circle"></i> error :/');
                }
            });
            return false;

        });

    }



    function buf_fa_seletion(){
        return;
    }



    function buf_interface_4(){

        //COMPILATION BUTTONS
        var padre = $('.header-title');
        
        //MAIN LOGO
        var main_logo = $('.buf_template_data');
        main_logo.appendTo($('#details .row .col-md-3 .card-body'));

        //DUPLICATE
        $('#jform_params_buf_layout').parent().addClass('jform_params_buf_layout');
        $('#toolbar-buf_empy_cache').appendTo('#toolbar');
        $('#details >.row >.col-md-9 >div.info-labels').remove();                 
    }

    function buf_save_aply(){

        //$('#jform_params_runless-lbl').click(function(e){
        $('button.button-apply, button.button-save').click(function(e){
            event.preventDefault();
            var active_href = $('ul#myTabTabs li.active a').attr('href');

            if(active_href=='#attrib-layout'){
                $( 'a.buf_scss_save' ).trigger('click');
                setTimeout(function(){ 
                    //Joomla.submitform('style.apply', document.getElementById('style-form'));
                }, 1000);
            }              
        });
    }

    /**************************************/
    /**************************************/
    /******   CLEAR CACHE  ********/
    /**************************************/
    /**************************************/
    function buf_clear_cache(){

        $( '#toolbar-buf_empy_cache button').on( 'click', function( event ) {
            event.preventDefault();
            var data = {
                templateid : templateid,
                action : 'do_clear_cache',
                tpath: tpath,
                layout: buf_layout
            };

            var request = {
                'option' : 'com_ajax',
                'plugin' : 'bufajax',
                'data'   : data,
                'format' : 'json'
            };

            $.ajax({
                type   : 'POST',
                dataType: 'json',
                data   : request,
                beforeSend: function( xhr ) {
                    $( '#toolbar-buf_empy_cache > .buf_clear_cache_status' ).html('<i class="fa fa-cog fa-spin buf-loading"></i>');
                },
                success: function (response) {
                    

                    $( '#toolbar-buf_empy_cache .buf_clearcache_icon' ).hide();
                    if(response.data == 'ok'){
                        $( '#toolbar-buf_empy_cache .buf_clear_cache_status' ).html('<i class="fas fa-badge-check"></i>');
                    }else{
                            console.log(response);
                        $( '#toolbar-buf_empy_cache .buf_clear_cache_status' ).html('<i class="fas fa-times-octagon"></i>');
                    }

                    setTimeout(function(){ 
                        $( '#toolbar-buf_empy_cache .buf_clear_cache_status' ).html('');
                        $( '#toolbar-buf_empy_cache .buf_clearcache_icon' ).show();
                    }, 1000); 
                    
                },
                error: function(response){
                    //alert('Somethings wrong, Try again');
                    console.log(response);
                    $( '#compile_bs_sass > .btn' ).html('<i class="fa fa-exclamation-circle"></i> error :/');
                }
            });
            return false;

        });
    }



    /**************************************/
    /**************************************/
    /******   TOOLBAR CUSTOM  ********/
    /**************************************/
    /**************************************/
    function buf_toolbar_custom(){

        var buf_toolbar = '';
            //buf_toolbar += '<div class="btn-wrapper" id="compile_sass" style="float:right"><button class="btn btn-small"><span class="icon-play"></span>Compile SASS</button></div>';
            //buf_toolbar += '<div class="btn-wrapper" id="compile_bs_sass"><button class="btn btn-small"><span class="icon-play"></span>Compile BS</button></div>';
            //buf_toolbar += '<div class="btn-wrapper" id="compile_fa_sass"><button class="btn btn-small"><span class="icon-play"></span>Compile FA</button></div>';
            //buf_toolbar += '<div class="btn-wrapper" id="compile_sass_session" style="float:right"><button class="btn btn-small"><span class="icon-play"></span>Compile SASS session</button></div>';
            buf_toolbar += '';

        $(buf_toolbar).insertAfter('#toolbar-cancel');
    }

    /**************************************/
    /**************************************/
    /******   BUTTON COMPILE BS  ********/
    /**************************************/
    /**************************************/
    function buf_bs4_custom(){

        //on click -> send ajax -> write session 'buf_reload_bs_sass' to 1 -> then reload to exec #reloadbs

        //WRITE SESSION buf_reload_bs_sass
        $( '#compile_bs_sass, div.bs4_custom_btn a').on( 'click', function( event ) {
            event.preventDefault();
            

            var data = {
                templateid : templateid,
                action : 'doreload_bs_sass',
                tpath: tpath,
                layout: buf_layout
            };

            var request = {
                'option' : 'com_ajax',
                'plugin' : 'bufajax',
                'data'   : data,
                'format' : 'json'
            };

            $.ajax({
                type   : 'POST',
                dataType: 'json',
                data   : request,
                beforeSend: function( xhr ) {
                    $( '#compile_bs_sass > .btn' ).html('<i class="fa fa-cog fa-spin buf-loading"></i> processing');
                },
                success: function (response) {
                    //RELOAD
                    Joomla.submitform('style.apply', document.getElementById('style-form'));
                },
                error: function(response){
                    //alert('Somethings wrong, Try again');
                    console.log(response);
                    $( '#compile_bs_sass > .btn' ).html('<i class="fa fa-exclamation-circle"></i> error :/');
                }
            });
            return false;

        });


        /**************************************/
        /**************************************/

        //COMPILE RELOAD
        if($('#reloadbs').val()=='1'){

            $('#system-message-container > div').remove();



            var data = {
                templateid : templateid,
                action : 'do_bs_sass',
                tpath: tpath,
                layout: buf_layout
            };


            var request = {
                'option' : 'com_ajax',
                'plugin' : 'bufajax',
                'data'   : data,
                'format' : 'json'
            };

            $.ajax({
                type   : 'POST',
                dataType: 'json',
                data   : request,
                beforeSend: function( xhr ) {
                    $( '#compile_bs_sass > .btn' ).html('<i class="fa fa-cog fa-spin buf-loading"></i> processing');
                },

                success: function (response) {


                    console.log(response);


                    //ERROR    
                    if(response.success == false){
                        $( '#compile_bs_sass > .btn' ).html('<i class="icon-warning"></i>ERROR '+response.message);
                    }else{

                        console.log(response.data[0].debug);

                        $( '#compile_bs_sass > .btn' ).html('<i class="icon-save"></i>compiled');


                        //$( '#compile_sass' ).html("asdf");
                        setTimeout(function(){ 
                            //$( '#compile_sass > .btn' ).html('<i class="icon-play"></i>compile sass');
                            $( '#compile_bs_sass > .btn' ).html('<i class="icon-play"></i>Compile BS');

                            var construc = response.data[0]['file_data']['file_name']+response.data[0]['file_data']['file_date']+response.data[0]['file_data']['file_size'];
                            $( 'div.do_clean_bs div.buf_toolbar_file_info' ).html(construc);

                            //button clean
                            if($('.do_clean_bs .buf_toolbar_file_buttons .btn_buf_clean').length <=0){
                                $( '#compile_bs_sass' ).after(response.data[0]['file_data']['file_clean']);
                                buf_clean_file();
                            }

                        }, 1000); 
                    }


                },
                error: function(response){
                    //alert('Somethings wrong, Try again');
                    $( '#compile_bs_sass > .btn' ).html('<i class="icon-warning"></i>ERROR');
                    console.log(response);
                }
            });
            
        }

    }

    /**************************************/
    /**************************************/
    /******   BUTTON COMPILE FA  ********/
    /**************************************/
    /**************************************/
    function buf_fa_custom(){

        //COMPILE LESS SESSION
        $( '#compile_fa_sass').on( 'click', function( event ) {
            event.preventDefault();
            var data = {
                templateid : templateid,
                action : 'doreload_fa_sass',
                tpath: tpath,
                layout: buf_layout
            };

            var request = {
                'option' : 'com_ajax',
                'plugin' : 'bufajax',
                'data'   : data,
                'format' : 'json'
            };

            $.ajax({
                type   : 'POST',
                dataType: 'json',
                data   : request,
                beforeSend: function( xhr ) {
                    $( '#compile_fa_sass > .btn' ).html('<i class="fa fa-cog fa-spin buf-loading"></i> processing');
                                        
                },
                success: function (response) {

                    //$('#reloadsass').val(0);
                    //RELOAD
                    Joomla.submitform('style.apply', document.getElementById('style-form'));
                    //Joomla.submitform('style.apply');    
                    //console.log(response);

                },
                error: function(response){
                    //alert('Somethings wrong, Try again');
                    console.log(response);
                    $( '#compile_fa_sass > .btn' ).html('<i class="fa fa-exclamation-circle"></i> error :/');
                }
            });
            return false;

        });


        /**************************************/
        /**************************************/

        //COMPILE RELOAD
        if($('#reloadfa').val()=='1'){

            $('#system-message-container > div').remove();

            //console.log(templateid);
            var data = {
                templateid : templateid,
                action : 'do_fa_sass',
                tpath: tpath,
                layout: buf_layout
            };

            var request = {
                'option' : 'com_ajax',
                'plugin' : 'bufajax',
                'data'   : data,
                'format' : 'json'
            };

            $.ajax({
                type   : 'POST',
                dataType: 'json',
                data   : request,
                beforeSend: function( xhr ) {
                    //Joomla.submitform('style.apply');
                    //Joomla.submitform('style.apply', document.getElementById('style-form'));
                    $( '#compile_fa_sass > .btn' ).html('<i class="fa fa-cog fa-spin buf-loading"></i> processing');
                },
                success: function (response) {

                    console.log(response);
                    
                    $( '#compile_fa_sass > .btn' ).html('<i class="icon-save"></i>compiled');

                    //$( '#compile_sass' ).html("asdf");
                    setTimeout(function(){ 
                        //$( '#compile_sass > .btn' ).html('<i class="icon-play"></i>compile sass');
                        $( '#compile_fa_sass > .btn' ).html('<i class="icon-play"></i>Compile FA');


                        //$( '#compile_sass > .btn' ).html('<i class="icon-play"></i>compile sass');
                        $( '#compile_fa_sass > .btn' ).html('<i class="icon-play"></i>Compile FA');

                        var construc = response.data[0]['file_data']['file_name']+response.data[0]['file_data']['file_date']+response.data[0]['file_data']['file_size'];
                        $( 'div.do_clean_fa div.buf_toolbar_file_info' ).html(construc);

                        //button clean
                        if($('.do_clean_fa .buf_toolbar_file_buttons .btn_buf_clean').length <=0){
                            $( '#compile_fa_sass' ).after(response.data[0]['file_data']['file_clean']);
                        }

                        buf_clean_file();

                    }, 1000);
                },
                error: function(response){
                    //alert('Somethings wrong, Try again');
                    $( '#compile_fa_sass > .btn' ).html('<i class="icon-warning"></i>ERROR');
                    console.log(response);
                }
            });
            
        }

    }


/***********************************************************************************/
/***************************       LAYOUT       ************************************/
/***********************************************************************************/

    /************** LAYOUT ****************/
    /**************************************/
    /******     SAVE SCSS FILE     ********/
    /**************************************/
    /**************************************/

function buf_save_sccs_file(){

    $( 'a.buf_scss_save' ).on( 'click', function(event ) {
        event.preventDefault();
        var text = buf_editor.getValue();

        var scss_selected = $( '#jform_params_buf_layout_files' ).find('input:checked').val();

        var data = {
            templateid : templateid,
            action : 'dosavesccsfile',
            tpath: tpath,
            layout: buf_layout,
            text: text
        };

        // Debug: Log del modo seleccionado
        console.log('SCSS Selected mode:', scss_selected);

        // Verificar si está en modo "element"
        if(scss_selected === 'element') {
            // Obtener el elemento seleccionado
            var element_selected = $( '#jform_params_buf_layout_edit_elements' ).val();
            
            console.log('Element selected:', element_selected); // Debug
            
            if(element_selected && element_selected !== '' && element_selected !== '-1') {
                // Guardar el elemento seleccionado
                data.file = element_selected;
                data.type = 'element'; // Indicador para el servidor
                
                console.log('Sending element data:', data); // Debug
            } else {
                // No hay elemento seleccionado, mostrar error
                $('.buf_tb_msg').html('Please select an element to save').show("slow");
                setTimeout(function(){
                    $('.buf_tb_msg').hide("slow");
                }, 2000);
                return;
            }
        } else {
            // Modo normal, usar el archivo seleccionado
            data.file = scss_selected;
            console.log('Sending normal file data:', data); // Debug
        }

        var request = {'option':'com_ajax','plugin':'bufajax','data':data,'format':'json'};

        console.log('Final request:', request); // Debug completo

        $.ajax({
            type   : 'POST',
            dataType: 'json',
            data   : request,
            success: function (response) {

                console.log('Save response:', response); // Debug respuesta

                $('.buf_tb_icon').show( "fast");
                $('.buf_tb_msg').html(response.data[0]).hide().show( "slow");
                $('.buf_scss_save').addClass('disabled');

                setTimeout(function(){
                    
                    $('.buf_tb_msg').hide("slow", function(){
                        $(this).html('');
                        $('.buf_scss_save').removeClass('disabled');
                    });
                    $('.buf_tb_icon').hide("fast");

                }, 2000);
            },
            error: function(response){
                console.log('Save error:', response); // Debug error
                $('.buf_tb_msg').html('Fatal error :( ');
            }
        });

    });

}



    /************** LAYOUT ****************/
    /**************************************/
    /******    CHANGE LAYOUT       ********/
    /**************************************/
    /**************************************/
    function buf_layout_change(){

        $('#jform_params_buf_layout').on('change', function(){

            var layout_name = '';
            if($(this).val() == ''){
                layout_name = 'default';
            }else{
                layout_name = $(this).val();
            }
            

            Joomla.submitform('style.apply', document.getElementById('style-form'));

        });


    }

    /************** LAYOUT ****************/
    /**************************************/
    /******       NEW LAYOUT       ********/
    /**************************************/
    /**************************************/
    function buf_layout_new(){

        //var padre = $('#jform_params_buf_layout' ).parent();
        //var hijo = $('.buf_duplicate_layout' );
        //hijo.appendTo( padre);

        $('.buf_layout_new_name_wrapper').hide();
        
            //LOTATE
        $('.buf_duplicate_layout_a' ).click(function(){
            $('.buf_duplicate_layout_a').hide('slow');
            $('.buf_layout_new_name_wrapper').show('slow');
        });


            //CREATE
        $('.buf_btn_create_layout' ).on('click', function(event){
            event.preventDefault();
            $('.buf_btn_create_layout .fa-cog').addClass('fa-spin');

            var new_name = $('#buf_layout_name').val();
            var validation = validate_layout_name(new_name);

            if(validation != 'valid'){

                $('.buf_layout_toolbar_status').html(validation).show('slow');
                $('.buf_btn_create_layout .fa-cog').removeClass('fa-spin');
                setTimeout(function(){
                    $('.buf_layout_toolbar_status').hide('slow');
                }, 2000);

            }else{
                
                //VALID
                //var scss_layout = $('#jform_params_buf_layout').val();

                var data = {
                    templateid : templateid,
                    action : 'doduplicatelayout',
                    tpath: tpath,
                    name: new_name,
                    layout: buf_layout
                };

                var request = {'option':'com_ajax','plugin':'bufajax','data':data,'format':'json'};

                $.ajax({
                    type   : 'POST',
                    dataType: 'json',
                    data   : request,
                    success: function (response) {

                        if(response['data'] != 'OK'){
                            $('.buf_layout_toolbar_status').html(response['data']).show('slow');
                        }else{
                            Joomla.submitform('style.apply', document.getElementById('style-form'));
                        }
                
                    },
                    error: function(response){
                        //alert('Somethings wrong, Try again');
                        $('.buf_layout_toolbar_status').html('Fatal error :( ').show('slow');
                        console.log(response);
                    }
                });

            }

        });


        //CANCEL
        $('.buf_btn_cancel_layout' ).click(function(){
            $('.buf_btn_create_layout .fa-cog').removeClass('fa-spin');
            $('.buf_duplicate_layout_a').show('slow');
            $('.buf_layout_new_name_wrapper').hide('slow');
        });       
    }

    /************** LAYOUT ****************/
    /**************************************/
    /****   VALIDATE LAYOUT NAME    *******/
    /**************************************/
    /**************************************/
    function validate_layout_name(val){

        //check long
        if(val.length >=30){
            return 'Name too long';
        }
        
        var str = (/^[\\da-zA-Z0-9\-\_]{1,30}$/.test(val.toLowerCase()))? 'valid' : 'Name not valid';

        return str;
    }


    /************** LAYOUT ****************/
    /**************************************/
    /******       ZIP LAYOUT       ********/
    /**************************************/
    /**************************************/
    function buf_zip_layout(){

        //CREATE
        $('.buf_zip_layout_a' ).on('click', function(event){
            event.preventDefault();

            var esto = $(this);
            esto.addClass('disabled');

/*
            var scss_layout = $('#jform_params_buf_layout').val();
            if(scss_layout == ''){
                scss_layout = 'default';
            }
*/
            var data = {
                templateid : templateid,
                action : 'do_zip_layout',
                tpath: tpath,
                layout: buf_layout
            };

            var request = {'option':'com_ajax','plugin':'bufajax','data':data,'format':'json'};

            console.log(request);

            $.ajax({
                type:'POST',dataType:'json',data:request,
                success: function (response) {
                        console.log(response.data);
                    if(response.data[0].status == 'ok'){
                        $('.buf_layout_toolbar_status').html('<a download rel="nofollow" href="'+response.data[0].file_path+'">Download '+response.data[0].filename+'</a>');
                    }else{
                        console.log(response.data);
                        $('.buf_layout_toolbar_status').html('Error in backup. See console log.');
                    }

                    esto.removeClass('disabled');

                },
                error: function(response){
                    $('.buf_layout_toolbar_status').html('Fatal Error in backup. See console log.');
                    $(this).removeClass('disabled');
                    console.log(response);
                }
            });

        });

    }




    /***********************************************************************************/
    /***************************       STYLE       ************************************/
    /***********************************************************************************/

    function buf_favicon(){

        //FAVICON CREATOR
        //insert button
        /*
        var buf_create = '';
            buf_create += '<a class="mt-2 btn btn-default bg-info text-light buf_favicon_btn_create" title="Create favicon" href="#"><i class="fa fa-magic"></i> Create favicons</a>';
            buf_create += '';

            $(buf_create).insertAfter($("#imageModal_jform_params_buf_favicon").parent()); 
        */   
        
        $('.buf_favicon_btn_create' ).on( 'click', function( event ) {
            event.preventDefault();
            
            var favicon = $('#jform_params_buf_favicon').val();

            if(favicon == ''){
                alert('You have not selected an image');
                return false;
            }


            var data = {
                templateid : templateid,
                action : 'dofavicon',
                tpath: tpath,
                image: favicon,
                layout: buf_layout
            };

            var request = {'option':'com_ajax','plugin':'bufajax','data':data,'format':'json'};

            $.ajax({
                type   : 'POST',
                dataType: 'json',
                data   : request,
                success: function (response) {
                    
                    if(response.data != 'true'){
                        $( '.buffavicons_messages' ).show();
                            $( '.buf_favicon_btn_create' ).addClass('btn-warning').html('<span class="icon-delete"></span> NOT created');
                        //$( '#compile_sass' ).html("asdf");
                        setTimeout(function(){ 
                            $( '.buf_favicon_btn_create' ).removeClass('btn-warning').html('<i class="fa fa-magic"></i> Create favicons');
                        }, 2000);
                        $( '.buffavicons_messages' ).addClass('alert').html(response.data);

                    }else{


                        $( '.buf_favicon_btn_create' ).html('<i class="fa fa-cog fa-spin"></i> Creating');
                        
                        setTimeout(function(){ 
                            $( '.buf_favicon_btn_create' ).html('<span class="icon-save"></span>created');
                            $( '.buffavicons_messages' ).hide();
                        }, 2000);

                        setTimeout(function(){ 
                            $( '.buf_favicon_btn_create' ).html('<i class="fa fa-magic"></i> Create favicon');
                        }, 3000);

                        //$( '.buffavicons_messages' ).addClass('text-success').html('Created');
                        //$( '.buffavicons_thumbs_wrapper' ).html('');

                        setTimeout(function(){ 
                            var d = new Date();
                            //$( '.buffavicons_thumbs_wrapper' ).html('<div class="buffavicons_thumbs"><img src="../templates/buf/images/icons/favicon-96x96.png?'+d.getTime()+'" title="96x96"/></div><div class="buffavicons_thumbs"><img src="../templates/buf/images/icons/favicon-16x16.png?'+d.getTime()+'"/></div>');
                            $( '.buffavicons_thumbs_wrapper' ).html('<div class="buffavicons_thumbs buffavicons_thumbs_svg"><div class="buffavicons_thumbs_svg_img"><img src="../templates/buf/images/icons/svgfavicon.svg" width="64"/></div></div><div class="buffavicons_thumbs"><img src="../templates/buf/layouts/'+buf_layout+'/icons/favicon-96x96.png?'+d.getTime()+'" title="96x96"/></div><div class="buffavicons_thumbs"><img src="../templates/buf/layouts/'+buf_layout+'/icons/apple-icon-57x57.png?'+d.getTime()+'" title="57x57"/></div><div class="buffavicons_thumbs"><img src="../templates/buf/layouts/'+buf_layout+'/icons/favicon-16x16.png?'+d.getTime()+'" title="16x16"/></div>');

                        }, 1000);

                    }
    
                    console.log(response);

                },
                error: function(){
                    //alert('Somethings wrong, Try again');
                    console.log('error');
                }
            });
            return false;
        });
    }




/***********************************************************************************/
/***************************      BOOTSTRAP       ************************************/
/***********************************************************************************/

    /**************************************/
    /**************************************/
    /******       BS SELECTOR      ********/
    /**************************************/
    /**************************************/
    function  buf_check_bs_selector_click(){

        $('#jform_params__buf_bs_v5__buf_bootstrap_css').change(function(){
           //buf_check_bs_selector_j4($(this).val());
          
        });

    }
    /*
    $('#jform_params__buf_bs_v4__buf_bs_selector').change(function(){
        buf_check_bs_selector_j4($(this).val(),4);
    });
    */
    $('#jform_params__buf_bs_v5__buf_bs_selector input').click(function(){
        buf_check_bs_selector_j4($(this).val());
        console.log($(this).val());
    });



    function  buf_check_bs_selector(selected, version){
        
        var files = version == 5 ? $('#jform_params__buf_bs_v5__buf_bs_files'):  $('#jform_params__buf_bs_v4__buf_bs_files');
        var files_option = version == 5 ? $('#jform_params__buf_bs_v5__buf_bs_files option'):  $('#jform_params__buf_bs_v4__buf_bs_files option');
        
        var minimum = ['functions','variables','maps','mixins', 'utilities', 'grid'];
        var recommended = [
                'functions','variables','maps','mixins', 'root', 'reboot', 'utilities', 'grid', 
                    'type', 'images', 'containers', 'grid', 'tables', 'forms', 'buttons', 'transitions', 
                'dropdown', 'button-group', 'nav', 'navbar', 
                'card', 'pagination', 'list-group', 'close', 'api'];
 

        selected = selected.toLowerCase();

        if(selected=='none'){
            
            files_option.removeAttr('selected').trigger("liszt:updated");

        }else if(selected == 'minimum'){
            
            files_option.removeAttr('selected').trigger("liszt:updated");
            $.each( minimum, function( key, value ) {
                if(version == 5) $('#jform_params__buf_bs_v5__buf_bs_files option[value="'+value+'"]').attr('selected', 'selected');
                if(version == 4) $('#jform_params__buf_bs_v4__buf_bs_files option[value="'+value+'"]').attr('selected', 'selected');
            });
            files.trigger("liszt:updated");


        }else if(selected == 'recommended'){
            
            files_option.removeAttr('selected').trigger("liszt:updated");
            $.each( recommended, function( key, value ) {
                if(version == 5) $('#jform_params__buf_bs_v5__buf_bs_files option[value="'+value+'"]').attr('selected', 'selected');
                if(version == 4) $('#jform_params__buf_bs_v4__buf_bs_files option[value="'+value+'"]').attr('selected', 'selected');
            });
            files.trigger("liszt:updated");

        }else if(selected == 'all'){
            
            files_option.attr('selected', 'selected').trigger("liszt:updated");

            
        }else if(selected == 'custom'){
            files.removeAttr('disabled', 'disabled').trigger("liszt:updated");
        }
    }

    function  buf_check_bs_selector_j4(selected){

        var files_bs_core = $('#jform_params__buf_bs_v5__buf_bs_files_core input');
        var files_bs_layout = $('#jform_params__buf_bs_v5__buf_bs_files_layout input');
        var files_bs_components = $('#jform_params__buf_bs_v5__buf_bs_files_components input');
        var files_bs_helpers = $('#jform_params__buf_bs_v5__buf_bs_files_helpers input');

        var minimum = ['functions','variables','maps','mixins', 'utilities', 'grid'];
        var recommended = [
            'functions','variables','maps','mixins', 'root', 'reboot', 'utilities', 'grid', 
                'type', 'images', 'containers', 'grid', 'tables', 'forms', 'buttons', 'transitions', 
            'dropdown', 'button-group', 'nav', 'navbar', 
            'card', 'pagination', 'list-group', 'close', 'api'];
        var all = [
            'functions',
            'variables',
            'maps',
            'mixins',
            'utilities',
            'root',
            'reboot',
            'type',
            'images',
            'containers',
            'grid',
            'tables',
            'forms',
            'buttons',
            'transitions',
            'dropdown',
            'button-group',
            'nav',
            'navbar',
            'card',
            'accordion',
            'breadcrumb',
            'pagination',
            'badge',
            'alert',
            'progress',
            'list-group',
            'close',
            'toasts',
            'modal',
            'tooltip',
            'popover',
            'carousel',
            'spinners',
            'offcanvas',
            'placeholders',
            'helpers',
            'api'
        ];
    
        selected = selected.toLowerCase();

        if(selected=='none'){

            files_bs_core.prop('checked',false);
            files_bs_layout.prop('checked',false);
            files_bs_components.prop('checked',false);
            files_bs_helpers.prop('checked',false);

       
            document.querySelectorAll('#jform_params__buf_bs_v5__buf_bs_files_core input').forEach(function (el) {
                el.checked = false;
            });

        }else if(selected == 'minimum'){

            //files_option.removeAttr('selected');

            //borro todos
            files_bs_core.prop('checked',false);
            files_bs_layout.prop('checked',false);
            files_bs_components.prop('checked',false);
            files_bs_helpers.prop('checked',false);
            
            minimum.forEach(function(value) {

                document.querySelectorAll('#jform_params__buf_bs_v5__buf_bs_files_core input[value="'+value+'"]').forEach(function (el) {
                    el.checked = true;
                });
                document.querySelectorAll('#jform_params__buf_bs_v5__buf_bs_files_layout input[value="'+value+'"]').forEach(function (el) {
                    el.checked = true;
                });
                document.querySelectorAll('#jform_params__buf_bs_v5__buf_bs_files_components input[value="'+value+'"]').forEach(function (el) {
                    el.checked = true;
                });
                document.querySelectorAll('#jform_params__buf_bs_v5__buf_bs_files_helpers input[value="'+value+'"]').forEach(function (el) {
                    el.checked = true;
                });

              
            });

        }else if(selected == 'recommended'){
            var selectedValues = []; 

            files_bs_core.prop('checked',false);
            files_bs_layout.prop('checked',false);
            files_bs_components.prop('checked',false);
            files_bs_helpers.prop('checked',false);

            //files_option.removeAttr('selected').trigger("chosen:updated");
            $.each( recommended, function( key, value ) {
                $('#jform_params__buf_bs_v5__buf_bs_files_core input[value="'+value+'"]').prop('checked',true);
                $('#jform_params__buf_bs_v5__buf_bs_files_layout input[value="'+value+'"]').prop('checked',true);
                $('#jform_params__buf_bs_v5__buf_bs_files_components input[value="'+value+'"]').prop('checked',true);
                $('#jform_params__buf_bs_v5__buf_bs_files_helpers input[value="'+value+'"]').prop('checked',true);

            });
            //files.trigger("chosen:updated");
            //files.trigger("chosen:updated").trigger("liszt:updated.chosen");

        }else if(selected == 'all' || selected == 'custom'){

            files_bs_core.prop('checked',true);
            files_bs_layout.prop('checked',true);
            files_bs_components.prop('checked',true);
            files_bs_helpers.prop('checked',true);
        }
 
    }

});




