//v2.1.21

var buf_editor;
jQuery(function($){
    "use strict";

    $(document).ready(function(){


        function when_external_loaded (callback) {
          if (typeof CodeMirror === 'undefined' || $('.CodeMirror').length == 0) {
            setTimeout (function () {
               when_external_loaded (callback);
            }, 1000); // wait 100 ms
          } else { callback (); }
        }



        when_external_loaded (function () {

                //remove current codemirror
               $('.CodeMirror').remove();

                //clean editor to avoid error in gzip and save in database
                $('#jform_params_scss_editor').attr('name','cleaned_editor');
                
                buf_editor = CodeMirror.fromTextArea(document.getElementById("jform_params_scss_editor"), {
                    "autofocus":false,
                    "lineWrapping":true,
                    "styleActiveLine":true,
                    "lineNumbers":true,
                    "gutters":["CodeMirror-linenumbers","CodeMirror-foldgutter","CodeMirror-markergutter"],
                    "foldGutter":true,
                    "markerGutter":true,
                    "mode":"scss",
                    "theme":"default",
                    "autoCloseBrackets":true,
                    "matchBrackets":true,
                    "scrollbarStyle":"native",
                    "keyMap":"default"
                });

                buf_load_sccs_file();
                $( '#jform_params_buf_layout_files input' ).trigger('change');

                //buf_editor.setValue('Text');
                
        });


        buf_checkplugins();
        
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
     

        //START Loading a file in editor
        $( '#jform_params_buf_layout_files input' ).trigger( "change" );


        if(jversion == '4'){

            buf_interface_4();

            $('#jform_params__buf_bs_v4__buf_bs_selector').trigger('change');
            $('#jform_params__buf_bs_v5__buf_bs_selector').trigger('change');


        }else{
            buf_interface();
        }

        var scss_layout = $('#jform_params_buf_layout').val();
        if(scss_layout == null || scss_layout == '') scss_layout = 'default';


    });


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


    function buf_checkplugins(){
        //$('.bufcheckplugins').hide();
    }



    function buf_fa_seletion(){

        
        $('#jform_params_buf_fa_selector').on('change', function(){

            var fa_version = $('#jform_params_buf_fa_selector input:checked').val();

            if(fa_version == '4'){

                $('#jform_params_buf_fa').parent().parent().show();
                $('#jform_params_buf_fa_defer').parent().parent().show();
                $('#jform_params_buf_fa_pro').parent().parent().hide();
                $('#jform_params_buf_fa5_tech').parent().parent().hide();
                $('#jform_params_buf_fa5_files').parent().parent().hide();
                $('#jform_params_buf_fa4fallback').parent().parent().hide();
            
            }else if(fa_version == '5'){
                $('#jform_params_buf_fa').parent().parent().show();
                $('#jform_params_buf_fa_defer').parent().parent().show();
                $('#jform_params_buf_fa_pro').parent().parent().show();
                $('#jform_params_buf_fa5_tech').parent().parent().show();
                $('#jform_params_buf_fa5_files').parent().parent().show();
                $('#jform_params_buf_fa4fallback').parent().parent().show();

            }else{
                $('#jform_params_buf_fa_pro').parent().parent().hide();
                $('#jform_params_buf_fa').parent().parent().hide();
                $('#jform_params_buf_fa5_tech').parent().parent().hide();
                $('#jform_params_buf_fa5_files').parent().parent().hide();
                $('#jform_params_buf_fa4fallback').parent().parent().hide();
                $('#jform_params_buf_fa_defer').parent().parent().hide();
            }

        });

        $('#jform_params_buf_fa_selector').trigger('change');



    }



    function buf_interface_4(){


        //COMPILATION BUTTONS
        var padre = $('.header-title');
        //padre.addClass('buf_title_wrapper');

        /*
        var hijo = $('#jform_params_runless' ).parent().parent();
            hijo.addClass('buf_compilation_button');
            hijo.insertAfter( padre);
*/
        
        //MAIN LOGO
        var main_logo = $('.buf_template_data');
        main_logo.appendTo($('#details .row .col-md-3 .card-body'));

        //DUPLICATE
        $('#jform_params_buf_layout').parent().addClass('jform_params_buf_layout');

            //FAVICONS ICONS
            //var padre = $('#imageModal_jform_params_buf_favicon').parent().parent().addClass('buf_thumbs_wrapper');
            //var hijo = $('.buffavicons_thumbs_wrapper' );
            //hijo.appendTo( padre);

        $('#toolbar-buf_empy_cache').appendTo('#toolbar');

        /*
            //minilogo bar
            $('.buf_minilogo_bar').prependTo($('.form-inline-header'));
        */
        $('#details >.row >.col-md-9 >div.info-labels').remove();
        
                        
            
    }


    function buf_interface(){

        //COMPILATION BUTTONS
        var padre = $('#jform_title' ).parent().parent();
        var hijo = $('#jform_params_runless' ).parent().parent().addClass('pull-right');
        hijo.appendTo( padre);


        //FAVICONS ICONS
/*             var padre = $('#imageModal_jform_params_buf_favicon' ).parent();
        var hijo = $('.buffavicons_thumbs_wrapper' );
        hijo.appendTo( padre);
*/

        //minilogo bar
        $('.buf_minilogo_bar').prependTo($('.form-inline-header'));

        //Empty cache
        $('#toolbar-buf_empy_cache').appendTo('#toolbar');

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


            //var scss_layout = $('#jform_params_buf_layout').val();
            //if(scss_layout == null || scss_layout == '') scss_layout = 'default';

            var data = {
                templateid : templateid,
                action : 'dosavesccsfile',
                tpath: tpath,
                file: scss_selected,
                layout: buf_layout,
                text:text
            };

            var request = {'option':'com_ajax','plugin':'bufajax','data':data,'format':'json'};


            $.ajax({
                type   : 'POST',
                dataType: 'json',
                data   : request,
                success: function (response) {

                    $('.buf_tb_icon').show( "fast");
                    $('.buf_tb_msg').html(response.data[0]).hide().show( "slow");
                    $('.buf_scss_save').addClass('disabled');

                    setTimeout(function(){
                        
                        $('.buf_tb_msg').hide("slow", function(){
                            $(this).html('');
                            $('.buf_scss_save').removeClass('disabled');
                        });
                        $('.buf_tb_icon').hide("fast");
                        //$('.buf_tb_msg').html('');

                    }, 2000);
                },
                error: function(response){
                    //alert('Somethings wrong, Try again');
                    $('.buf_tb_msg').html('Fatal error :( ');
                }
            });

        })


    }

    /************** LAYOUT ****************/
    /**************************************/
    /******     LOAD SCSS FILE     ********/
    /**************************************/
    /**************************************/
    function buf_load_sccs_file(){


        //Insert save
        var buttons = $('.buf_scss_toolbar');


        buttons.insertBefore( $("#jform_params_scss_editor") );
        $('.buf_tb_icon').hide("fast");


        $( '#jform_params_buf_layout_files input' ).on('change', function(event ) {

            console.log("change!");

            var current_element = $(this);
            var current_element =  $( '#jform_params_buf_layout_files' );

            //event.stopPropagation();
            
            //save before change
            //$( "a.buf_scss_save" ).trigger( "click" );
            
            setTimeout(function(){

    
                var scss_selected = current_element.find('input:checked').val();
                //var scss_layout = $('#jform_params_buf_layout').val();



                var data = {
                    templateid : templateid,
                    action : 'doreadsccsfile',
                    tpath: tpath,
                    file: scss_selected,
                    layout: buf_layout
                };

                var request = {'option':'com_ajax','plugin':'bufajax','data':data,'format':'json'};

                $.ajax({
                    type   : 'POST',
                    dataType: 'json',
                    data   : request,
                    success: function (response) {

                        console.log(response);

                        
                        buf_editor.setValue(response.data[0]);
                        
                        

                        if(scss_selected=='buf_layout.js'){
                            $('.buf_tb_path').html('buf/layouts/'+buf_layout+'/js/'+scss_selected);
                        }else if(scss_selected=='layout.php'){
                            $('.buf_tb_path').html('buf/layouts/'+buf_layout+'/'+scss_selected);
                        }else{
                            $('.buf_tb_path').html('buf/layouts/'+buf_layout+'/scss/'+scss_selected+'.scss');
                        }

                        
                    },
                    error: function(response){
                        //alert('Somethings wrong, Try again');
                        console.log('ERROR in load scss: '+response.responseText);
                    }
                });


            }, 150);
            

        });

        // $( "#jform_params_buf_layout_files" ).trigger( "click" );
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
        var buf_create = '';
            buf_create += '<a class="mt-2 btn btn-default bg-info text-light buf_favicon_btn_create" title="Create favicon" href="#"><i class="fa fa-magic"></i> Create favicons</a>';
            buf_create += '';

        if(jversion == '4'){

            $(buf_create).insertAfter($("#imageModal_jform_params_buf_favicon").parent()); 


        }else{
            //J36
            if($('.media-preview').length >=1){
                var btn_after = $('.media-preview').parent().find('>a.btn.hasTooltip');
                $(buf_create).insertAfter(btn_after); 
            }else{
                //J37
                //var btn_after = $('.field-media-preview').parent().find('>a.btn.hasTooltip');
                $(buf_create).insertAfter($("#imageModal_jform_params_buf_favicon").parent().find('.btn.button-select')); 
            }
        }
        
        
        $( '.buf_favicon_btn_create' ).on( 'click', function( event ) {
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

        if(jversion=='3'){
            $('#jform_params__buf_bs_v4__buf_bs_selector').change(function(){
                buf_check_bs_selector($('#jform_params__buf_bs_v4__buf_bs_selector_chzn a.chzn-single span').html(),4);
            });
            $('#jform_params__buf_bs_v5__buf_bs_selector').change(function(){
                buf_check_bs_selector($('#jform_params__buf_bs_v5__buf_bs_selector_chzn a.chzn-single span').html(),5);
            });
        }
        if(jversion=='4'){
            
            $('#jform_params__buf_bs_v4__buf_bs_selector').change(function(){
                buf_check_bs_selector_j4($(this).val(),4);
            });
            $('#jform_params__buf_bs_v5__buf_bs_selector').change(function(){
                buf_check_bs_selector_j4($(this).val(),5);
            });
        }
    }

    $('#jform_params__buf_bs_v4__buf_bs_selector').change(function(){
        buf_check_bs_selector_j4($(this).val(),4);
    });
    $('#jform_params__buf_bs_v5__buf_bs_selector').change(function(){
        buf_check_bs_selector_j4($(this).val(),5);
    });



    function  buf_check_bs_selector(selected, version){
        
        var files = version == 5 ? $('#jform_params__buf_bs_v5__buf_bs_files'):  $('#jform_params__buf_bs_v4__buf_bs_files');
        var files_option = version == 5 ? $('#jform_params__buf_bs_v5__buf_bs_files option'):  $('#jform_params__buf_bs_v4__buf_bs_files option');
        
        if(version == 4){
            var minimum = ['functions','variables','mixins', 'reboot', 'grid'];
            var recommended = ['functions','variables','mixins', 'root', 'reboot', 'images', 'grid', 'tables', 'forms', 'buttons', 'transitions', 'dropdown', 'button-group', 'input-group', 'nav', 'navbar', 'card', 'pagination', 'media', 'list-group', 'close', 'utilities'];
        }
        
        if(version == 5){
            var minimum = ['functions','variables','mixins', 'utilities', 'grid'];
            var recommended = [
                'functions','variables','mixins', 'root', 'reboot', 'utilities', 'grid', 
                    'type', 'images', 'containers', 'grid', 'tables', 'forms', 'buttons', 'transitions', 
                'dropdown', 'button-group', 'nav', 'navbar', 
                'card', 'pagination', 'list-group', 'close', 'api'];
        }

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

    function  buf_check_bs_selector_j4(selected, version){

        if(jversion=='3') return;
        
        console.log('CHECK all FILES IN BOOTSTRAP 4 ');
        
        //var example = new Choices(document.getElementById('jform_params__buf_bs_v5__buf_bs_files'));

        var files = version == 5 ? $('#jform_params__buf_bs_v5__buf_bs_files'):  $('#jform_params__buf_bs_v4__buf_bs_files');
        var files_option = version == 5 ? $('#jform_params__buf_bs_v5__buf_bs_files option'): $('#jform_params__buf_bs_v4__buf_bs_files option');
        
        if(version == 4){
            var minimum = ['functions','variables','mixins', 'reboot', 'grid'];
            var recommended = ['functions','variables','mixins', 'root', 'reboot', 'images', 'grid', 'tables', 'forms', 'buttons', 'transitions', 'dropdown', 'button-group', 'input-group', 'nav', 'navbar', 'card', 'pagination', 'media', 'list-group', 'close', 'utilities'];
            var all = [
                'functions',
                'variables',
                'mixins',
                'root',
                'grid',
                'media',
                'utilities',
                'print',
                'reboot',
                'type',
                'code',
                'images',
                'tables',
                'alert',
                'badge',
                'breadcrumb',
                'buttons',
                'button-group',
                'card',
                'carousel',
                'dropdown',
                'forms',
                'input-group',
                'custom-forms',
                'jumbotron',
                'list-group',
                'modal',
                'nav',
                'navbar',
                'pagination',
                'popover',
                'progress',
                'spinners',
                'toasts',
                'tooltip',
                'transitions',
                'close'
            ];
        }
        


        if(version == 5){
            var minimum = ['functions','variables','mixins', 'utilities', 'grid'];
            var recommended = [
                'functions','variables','mixins', 'root', 'reboot', 'utilities', 'grid', 
                    'type', 'images', 'containers', 'grid', 'tables', 'forms', 'buttons', 'transitions', 
                'dropdown', 'button-group', 'nav', 'navbar', 
                'card', 'pagination', 'list-group', 'close', 'api'];
            var all = [
                'functions',
                'variables',
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
                'helpers',
                'api'
            ];
        }


        selected = selected.toLowerCase();

        if(selected=='none'){
            var selectedValues = []; 
            files_option.removeAttr('selected').trigger("chosen:updated");

        }else if(selected == 'minimum'){
            var selectedValues = []; 

            files_option.removeAttr('selected');
            $.each( minimum, function( key, value ) {
                if(version == 5) $('#jform_params__buf_bs_v5__buf_bs_files option[value="'+value+'"]').attr('selected', 'selected');
                if(version == 4) $('#jform_params__buf_bs_v4__buf_bs_files option[value="'+value+'"]').attr('selected', 'selected');
                
                    var cosa = {value:value,label:value};
                    selectedValues.push(cosa);
                    
            });
            //files.trigger("chosen:updated");
            console.log(selectedValues);


        }else if(selected == 'recommended'){
            var selectedValues = []; 

            files_option.removeAttr('selected').trigger("chosen:updated");
            $.each( recommended, function( key, value ) {
                if(version == 5) $('#jform_params__buf_bs_v5__buf_bs_files option[value="'+value+'"]').attr('selected', 'selected');
                if(version == 4) $('#jform_params__buf_bs_v4__buf_bs_files option[value="'+value+'"]').attr('selected', 'selected');
                var cosa = {value:value,label:value};
                selectedValues.push(cosa);
            });
            //files.trigger("chosen:updated");
            //files.trigger("chosen:updated").trigger("liszt:updated.chosen");

        }else if(selected == 'all' || selected == 'custom'){

            //files_option.attr('selected', 'selected').trigger("chosen:updated");
            var selectedValues = []; 
            $.each(all, function( key, value ) {
            var cosa = {value:value,label:value};
            selectedValues.push(cosa);
            });
        }

        console.log(selected);
        
        if(version == 4){
            jQuery("#jform_params__buf_bs_v4__buf_bs_files").parents("joomla-field-fancy-select").each(function(i,e){
                
                this.choicesInstance.clearStore();
                this.choicesInstance.setValue(selectedValues);
            })
        }
                    
        if(version == 5){
            jQuery("#jform_params__buf_bs_v5__buf_bs_files").parents("joomla-field-fancy-select").each(function(i,e){
                this.choicesInstance.clearStore();
                this.choicesInstance.setValue(selectedValues);
            })
        }

    }









});




