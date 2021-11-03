<?php
/**
* @package BUF Framework
* @author jtotal https://jtotal.org
* @copyright Copyright (c) 2005 - 2021 jtotal
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/  
// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\CMS\Filesystem\Folder;


class BUFsass
{

  public static $cachepath = JPATH_SITE.'/cache/buf';
  public static $lesspath = JPATH_SITE.'/templates/buf/css';
  //public static $vendorpath = JPATH_SITE.'/templates/buf/vendor';
  public static $libspath = JPATH_SITE.'/templates/buf/libs';
  public static $img_path;
  public static $layout_img_path;
  public static $buf_layout;
  public static $recache;
  public static $isajax = false;
  public static $cssmix = 0;
  public static $css_sha = '';
  public static $buf_debug = array();
  public static $startmicro;
  public static $layoutpath;
  public static $buf_bs_container_fluid_max;
  public static $buf_bs_container_max;
  public static $buf_bs_container_content_max;

  //OFFCANVAS
  public static $buf_offcanvas_width = '90';
  public static $buf_offcanvas_width_desktop = '50';
  public static $buf_offcanvas_style = 'buf_off_move';
  public static $buf_offcanvas_bg_color = '#ffffff';
  public static $buf_oc_button_color;
  public static $buf_oc_button_color_hover;
  public static $buf_oc_button_color_active;
  public static $buf_offcanvas_speed;
  public static $buf_oc_button_style;
  public static $buf_oc_button_reverse;
  public static $buf_oc_button_editor;

  public static $buf_topbar_height;
  public static $buf_topbar_color;

  public static $buf_topbar_oc_height;
  public static $buf_topbar_oc_color;

  public static $buf_bs_on = 4;

  public static $buf_bs_css_source = "cdn";


  public static $bs5_all = ["functions","variables","mixins","utilities","root","reboot","type","images",
  "containers","grid",  "tables",  "forms",  "buttons",  "transitions",  "dropdown",
  "button-group",  "nav",  "navbar",  "card",  "accordion",  "breadcrumb",  "pagination",  "badge",
  "alert",  "progress",  "list-group",  "close",  "toasts",  "modal",  "tooltip",  "popover",
  "carousel",  "spinners",  "offcanvas", "helpers", "api"];

  //just in devel
  public static $debug_develmode_bs = false;



  public static function runsass($templateid='', $params='', $template_name='', $startmicro='', $bs_or_fa=''){

    $uri = Uri::root();

    self::$startmicro = $startmicro;
    $session = Factory::getSession();
    
    //TEMPLATE PARAMS IF AJAX
    if($templateid){

      //is AJAX
      self::$isajax = true;

      //AJAX
      //$templateparams = json_decode(BUFsass::getCurrentParams($templateid)->params);
      $template_name = BUFsass::getCurrentParams($templateid)->template;

      $templateparams = new Registry(BUFsass::getCurrentParams($templateid)->params);


      self::$buf_debug += self::addDebug('SASS | common', 'cubes', 'common.scss', $startmicro);

      //forces compiles
      $process = 0;
  
      $session->set('buf_reload_sass','0');

    }else{
      //NON-AJAX
      //$templateparams = $params;
      $templateparams = new Registry($params);
      $template_name = $template_name;
      $process = $templateparams->get('runless', 0);
    }


    self::$buf_layout = $templateparams->get('buf_layout','default');
    self::$img_path = $uri.'templates/buf/images/';
    self::$layout_img_path = $uri.'templates/buf/'.self::$buf_layout.'images/';

    //PARAMS
    $sass_editor = $templateparams->get('create_editor',0);
    $sass_compresion = $templateparams->get('buf_sass_compression', 'EXPANDED');

    $buf_fa = $templateparams->get('buf_fa', 1);
    $buf_fa_pro = $templateparams->get('buf_fa_pro', 0);
    $buf_fa_selector = $templateparams->get('buf_fa_selector', '4');
    $buf_fa5_tech = $templateparams->get('buf_fa5_tech', '1');
    $buf_fa5_files = $templateparams->get('buf_fa5_files', 'solid');

    if($buf_fa_pro == 0){
       $buf_fa5_files = array_diff($buf_fa5_files, ["light", "duotone"]);
    }

    $buf_fa5_fa4fallback = $templateparams->get('buf_fa4fallback', 0);

    self::$buf_bs_on = $templateparams->get('buf_bs_on',4);
    $buf_bs_on = $templateparams->get('buf_bs_on',4);


		$bs_4 = new Registry; 
		$bs_4->loadString(json_encode($templateparams->get('buf_bs_v4'))); 


		$bs_5 = new Registry; 
		$bs_5->loadString(json_encode($templateparams->get('buf_bs_v5'))); 


    self::$buf_bs_css_source = $bs_5->get('buf_bootstrap_css', 'cdn');

    $bs_styles = new Registry; 
    $bs_styles->loadString(json_encode($templateparams->get('buf_bs_styles'))); 

    //bs4 and bs5
    $bs_custom_colors = array(
      'bs_custom_body_bg'    => $bs_styles->get('bs_custom_body_bg', ''),
      'bs_custom_body_color' => $bs_styles->get('bs_custom_body_color', ''),
      'bs_custom_primary'    => $bs_styles->get('bs_custom_primary', ''),
      'bs_custom_secondary'  => $bs_styles->get('bs_custom_secondary', ''),
      'bs_custom_success'    => $bs_styles->get('bs_custom_success', ''),
      'bs_custom_info'       => $bs_styles->get('bs_custom_info', ''),
      'bs_custom_warning'    => $bs_styles->get('bs_custom_warning', ''),
      'bs_custom_danger'     => $bs_styles->get('bs_custom_danger', ''),
      'bs_custom_light'      => $bs_styles->get('bs_custom_light', ''),
      'bs_custom_dark'       => $bs_styles->get('bs_custom_dark', '')
    );

    //SET CONTAINER PARAMS
    self::buf_get_container($templateparams->get('buf_bs_container_fluid_max','100%'), $templateparams->get('buf_bs_container_max','1140'),$templateparams->get('buf_bs_container_content_max','1140'));


    
    self::$recache = $templateparams->get('force_recache','0');

    self::$cssmix = $templateparams->get('buf_scss_mix','0');

    ///////////////////////
    //OFFCANVAS
    ///////////////////////
    self::$buf_offcanvas_bg_color = $templateparams->get('buf_offcanvas_bg_color','#ffffff');
    
    self::$buf_offcanvas_width = $templateparams->get('buf_offcanvas_width','90');
    self::$buf_offcanvas_width_desktop = $templateparams->get('buf_offcanvas_width_desktop','50');
    self::$buf_offcanvas_style = $templateparams->get('buf_offcanvas_style','buf_off_move');
    self::$buf_offcanvas_speed = $templateparams->get('buf_offcanvas_speed','300');


    $oc_button = new Registry; 
    $oc_button->loadString(json_encode($templateparams->get('buf_oc_button'))); 

    self::$buf_oc_button_style        = $oc_button->get('buf_oc_button_style','3dx');
    self::$buf_oc_button_reverse      = $oc_button->get('buf_oc_button_reverse','l');
    self::$buf_oc_button_color        = $oc_button->get('buf_oc_button_color','#000000');
    self::$buf_oc_button_color_hover  = $oc_button->get('buf_oc_button_color_hover','#000');
    self::$buf_oc_button_color_active = $oc_button->get('buf_oc_button_color_active','#000');
    self::$buf_oc_button_editor       = $oc_button->get('buf_oc_button_editor','-ms-flex-pack: justify');



    $buf_topbar = new Registry; 
    $buf_topbar->loadString(json_encode($templateparams->get('buf_topbar'))); 

    self::$buf_topbar_height =  $buf_topbar->get('buf_topbar_height','54');
    self::$buf_topbar_color =  $buf_topbar->get('buf_topbar_color','#fff'); 


    $buf_topbar_oc = new Registry; 
    $buf_topbar_oc->loadString(json_encode($templateparams->get('buf_topbar_oc'))); 

    self::$buf_topbar_oc_height =  $buf_topbar_oc->get('buf_topbar_height','54');
    self::$buf_topbar_oc_color =  $buf_topbar_oc->get('buf_topbar_color','#fff'); 

    //CIRCUS start

    self::$layoutpath = JPATH_SITE.'/templates/buf/layouts/'.self::$buf_layout;
    
    $sass_bs_files = array();
    $sass_fa_files = array();


    self::$cachepath = self::$cachepath.'_'.self::$buf_layout;

    $buf_bs_css_exists  = file_exists (self::$cachepath . '/buf_bs.css');
    $buf_fa_css_exists  = file_exists (self::$cachepath . '/buf_fa.css');
    $buf_css_exists     = file_exists (self::$cachepath . '/buf.css');






    /**********************************/
    /*******   BS & FA FILES     ******/
    /**********************************/

    //AJAX fa
    //if($bs_or_fa != 'fa'){
    if ($process == 0 || $buf_bs_css_exists == false || self::$debug_develmode_bs){

      if($buf_bs_on){


        //BS4
        if($buf_bs_on == 4){

            if($bs_4->get('buf_bs_selector','recommended') != 'none'){

                //BOOSTRAP
                $bs4files = $bs_4->get('buf_bs_files', '');

                foreach ($bs4files as $key => $value) {
                  $sass_bs_files += array(self::$libspath . '/bootstrap4/scss/_'.$value.'.scss' => $uri);
                  self::$buf_debug += self::addDebug('BS4 | '.$value, 'cubes', '/bootstrap/scss/_'.$value.'.scss', $startmicro, 'table-secondary');
                }
            }else{
                self::$buf_debug += self::addDebug('BS4 | Selector', 'cubes','bs selector none', $startmicro);
            }
        }
        


        //BS5
        if($buf_bs_on == 5 && self::$buf_bs_css_source == 'custom'){
         
          if($bs_5->get('buf_bs_selector','recommended') != 'none'){

              //BOOSTRAP
              //select correnct order and files
              $bs5files = $bs_5->get('buf_bs_files', array());

              //aproximacion diferente: 
              //recorro el array de todos archivos y veo si coincide. Así preservo el orden.
              foreach (self::$bs5_all as $key => $value) {
                
                if(in_array($value, $bs5files)){

                  //special api case
                  if($value == 'api'){
                    $sass_bs_files += array(self::$libspath . '/bootstrap/scss/utilities/_'.$value.'.scss' => $uri);
                  }else{
                    $sass_bs_files += array(self::$libspath . '/bootstrap/scss/_'.$value.'.scss' => $uri);
                  }
                  
                  self::$buf_debug += self::addDebug('BS5 | '.$value, 'bootstrap fab', '/bootstrap/scss/_'.$value.'.scss', $startmicro, 'table-secondary');
                }   
              }

              //$sass_bs_files += array(self::$libspath . '/bootstrap/scss/utilities/_api.scss' => $uri);
          }

        }

      }
      
    }





    /**********************************/
    /*******      FA FILES       ******/
    /**********************************/

    $fa_process = false;

    //ON
    if ($process == 0 || $buf_fa_css_exists == false){
      
      if($buf_fa_selector == 0) $buf_fa = 0;
      
      //files ON
      if($buf_fa != 0) {
        $fa_process = true;

        //AJAX BS
        if($bs_or_fa == 'bs'){
          $fa_process = false;
        }


      }else{
        self::$buf_debug += self::addDebug('FA | ', 'hand-paper fab', 'NOT LOADED', $startmicro, 'table-info');
      }

    }



    if($fa_process) {

      //FA4
      if($buf_fa_selector == '4'){
        
        $fafiles  = BUFsass::buf_fa_files($buf_fa);
        
        foreach ($fafiles as $key => $value) {
          $sass_fa_files += array(self::$lesspath . '/font-awesome4/scss/_'.$value.'.scss' => $uri);
          self::$buf_debug += self::addDebug('FA4 | '.$value, 'smile-o', '/font-awesome4/_'.$value.'.scss', $startmicro);
        }

      }else{

        /**********************************/
        /*******      CSS + FONT      ******/
        /**********************************/
        if($buf_fa5_tech == 2){
        
          //check fa5pro files
          $fa5pro_exists = file_exists (self::$libspath . '/font-awesome/fontawesome5pro/webfonts/fa-brands-400.ttf') ? true:false;


          if($fa5pro_exists == false && $buf_fa_pro){
             self::$buf_debug += self::addDebug('FA5PRO | check', 'font-awesome fab', 'fontawesome5 PRO <strong> doesnt exist - default to fa5 free</strong>', self::$startmicro);
          }

 
          /*******    PRO    ******/
          if($fa5pro_exists == true && $buf_fa_pro){
          
            self::$buf_debug += self::addDebug('FA5PRO | file', 'font-awesome fab', 'fa5pro files <strong>exist</strong>', $startmicro);

              //copy font to cache folder
              //CHECK IF CACHE EXISTS
              
              $runsass = self::buf_fa_copy_to_cache('fontawesome5pro');

              //minimum
              if($buf_fa==1){
                self::$buf_debug += self::addDebug('FA5pro | minimum', 'font-awesome-alt fab','fa5/fontawesomePRO_mini.scss', $startmicro);
                $sass_fa_files += array(self::$libspath . '/font-awesome/fontawesomePRO_mini.scss' => $uri);
              //all
              }elseif($buf_fa==2){
                self::$buf_debug += self::addDebug('FA5pro | full', 'font-awesome-flag fab', 'fa5/fontawesomePRO.scss', $startmicro);
                $sass_fa_files += array(self::$libspath . '/font-awesome/fontawesomePRO.scss' => $uri);
              }
            
              //if not deactivated, process fonts
              if($buf_fa){
                //font style
                foreach ($buf_fa5_files as $key => $value) {
                  self::$buf_debug += self::addDebug('FA5pro | '.$value, 'font-awesome fab', '/fontawesome5pro/scss/'.$value.'.scss', $startmicro);
                  //buf 115
                  //$sass_bs_files += array(self::$lesspath . '/fontawesome5pro/scss/fa-'.$value.'.scss' => $uri);
                  $sass_fa_files += array(self::$libspath . '/font-awesome/fontawesome5pro/scss/'.$value.'.scss' => $uri);
                }

                //fa4 fallback
                if($buf_fa5_fa4fallback==1){
                  self::$buf_debug += self::addDebug('FA5pro | fallback', 'font-awesome-flag fab', self::$libspath . '/font-awesome/fontawesome5pro/scss/v4-shims.scss', $startmicro);
                  $sass_fa_files += array(self::$libspath . '/font-awesome/fontawesome5pro/scss/v4-shims.scss' => $uri);
                }

              }
            

          }else{

            /*******    FREE    ******/

            //FREE
            self::$buf_debug += self::addDebug('FA5free | started', 'flag-checkered', 'FONT+CSS', $startmicro);


            //copy font to cache folder
            $runsass = self::buf_fa_copy_to_cache('fontawesome5');

            //minimum
            if($buf_fa==1){
              self::$buf_debug += self::addDebug('FA5free | minimum', 'font-awesome-alt fab','fontawesome_mini.scss', $startmicro);
              $sass_fa_files += array(self::$libspath . '/font-awesome/fontawesome_mini.scss' => $uri);
            }elseif($buf_fa==2){
              self::$buf_debug += self::addDebug('FA5free | full', 'font-awesome-flag fab', '/fontawesome5/scss/fontawesome.scss', $startmicro);
              //$sass_bs_files += array(self::$lesspath . '/fa5/fontawesome5/scss/buf_variable.scss' => $uri);
              $sass_fa_files += array(self::$libspath . '/font-awesome/fontawesome.scss' => $uri);
            }

            
            //if not deactivated, process fonts
            if($buf_fa){
              //font style
              foreach ($buf_fa5_files as $key => $value) {
              
                 if($value == 'light' || $value == 'duotone'){
                  self::$buf_debug += self::addDebug('FA5free | '.$value, 'font-awesome fab', $value.' font skipped in FREE', $startmicro);
                }else{
                  self::$buf_debug += self::addDebug('FA5free | '.$value, 'font-awesome fab', self::$libspath . '/font-awesome/fontawesome5/scss/'.$value.'.scss', $startmicro);
                  $sass_fa_files += array(self::$libspath . '/font-awesome/fontawesome5/scss/'.$value.'.scss' => $uri);
                }
              }

              //fa4 fallback
              if($buf_fa5_fa4fallback==1){
                self::$buf_debug += self::addDebug('FA5| fallback', 'font-awesome-flag fab', self::$libspath . '/font-awesome/fontawesome5/scss/v4-shims.scss', $startmicro);
                $sass_fa_files += array(self::$libspath . '/font-awesome/fontawesome5/scss/v4-shims.scss' => $uri);
              }

            }
          }

        }
      }

    }

 
    

    /**********************************/
    /**********************************/
    /*******   SASS COMPILER     ******/
    /**********************************/
    /**********************************/


    //precomposer
    //require_once self::$lesspath.'/scssphp/scss.inc.php';
    //require_once self::$lesspath.'/scssphp/src/Anton_ajax.php';

    //require_once self::$vendorpath.'/autoload.php';
    //require_once self::$lesspath.'/scssphpAnton/loadphpscss.php';

    //new 2.2.0
    require_once JPATH_LIBRARIES.'/jtlibs/scssphp/scss.inc.php';
    require_once JPATH_SITE.'/templates/buf/classes/loadphpscss.php';



    //$scss->setFormatter('ScssPhp\ScssPhp\Formatter\Compressed');
    /*$scss->setImportPaths(self::$lesspath.'/bootstrap4/');*/

    //deprecated
    //$sass_comp_path = 'ScssPhp\ScssPhp\Formatter\\'.$sass_compresion;
    //$scss->setFormatter($sass_comp_path);
    
    //2.2.60
    if($sass_compresion == 'EXPANDED'){
      $scss->setOutputStyle(\ScssPhp\ScssPhp\OutputStyle::EXPANDED);
    }else{
      $scss->setOutputStyle(\ScssPhp\ScssPhp\OutputStyle::COMPRESSED);
    }


      /*****************************************/
      /*******    BUF SCSS compiler       ******/
      /**********************************/
      self::$css_sha = self::get_template_name();

      // MIX
      if(self::$cssmix == '1'){
        //self::$buf_debug += self::addDebug('CSS | MIXED', 'css3-alt fab', self::$cachepath . '/buf.css written', $startmicro, 'table-info', 'bufsass');
        
        //CHECK FILES
        if ($process != 2 || file_exists(self::$cachepath . '/'.self::$css_sha.'_mix.css') == false){

          //Compile buf
          if(file_exists(self::$cachepath. '/buf_bs.css') != true){
            self::buf_bs_scss($scss, $bs_custom_colors, $sass_bs_files,$process,$bs_or_fa);
          }
          //Compile fa
          if(file_exists(self::$cachepath. '/buf_fa.css') != true){

            self::buf_fa_scss($scss, $sass_fa_files,$process,$bs_or_fa);
          }

          //Compile template
          if(file_exists(self::$cachepath . '/'.self::$css_sha.'.css') != true){
            self::tmpl_scss($scss,$process);
          }

          $buf_bs_css = file_get_contents(self::$cachepath. '/buf_bs.css');
          $buf_fa_css = file_get_contents(self::$cachepath. '/buf_fa.css');
          $tmpl_css = file_get_contents(self::$cachepath . '/'.self::$css_sha.'.css');

          $cssOut = $buf_bs_css.$buf_fa_css.$tmpl_css;

          file_put_contents(self::$cachepath . '/'.self::$css_sha.'_mix.css', $cssOut);

          self::$buf_debug += self::addDebug('CSS | MIX', 'css3-alt fab', 'WRITTEN <small>'.self::$cachepath . '/'.self::$css_sha.'_mix.css </small>', self::$startmicro, 'table-info');
        }

      //NOMIX
      } else{
          
          // BUF_BS.css 
          if ($process == 0 || $buf_bs_css_exists == false || file_exists(self::$cachepath. '/buf_bs.css') == false || self::$debug_develmode_bs){
            self::buf_bs_scss($scss, $bs_custom_colors, $sass_bs_files,$process,'bs');

          }

          // BUF_FA.css 
          if ($process == 0 || $buf_fa_css_exists == false || file_exists(self::$cachepath. '/buf_fa.css') == false){
            self::buf_fa_scss($scss,$sass_fa_files,$process,'fa');
          }



          //JOIN TO BUF
          if ($process == 0 || file_exists(self::$cachepath. '/buf.css') == false || self::$debug_develmode_bs){

              $buf_bs_css = file_get_contents(self::$cachepath. '/buf_bs.css');
              $buf_fa_css = file_get_contents(self::$cachepath. '/buf_fa.css');
             
              $cssOut = $buf_bs_css.$buf_fa_css;

              file_put_contents(self::$cachepath . '/buf.css', $cssOut);

              self::$buf_debug += self::addDebug('CSS JOINED', 'css3-alt fab', 'WRITTEN <small>'.self::$cachepath . '/buf.css</small>', self::$startmicro, 'table-info');

          }


          // template.css 
          if ($process != 2  || self::$recache || file_exists(self::$cachepath . '/'.self::$css_sha.'.css') == false){
            self::tmpl_scss($scss, $process);
          }

      }


      //EDITOR
      //editor scss
      if ($sass_editor){
        if ($process == 0 || file_exists(self::$layoutpath. '/scss/editor.css') == false){

          $buf_bs_css = file_get_contents(self::$cachepath. '/buf_bs.css');
          $tmpl_css = file_get_contents(self::$cachepath . '/'.self::$css_sha.'.css');
          $cssOut = $buf_bs_css.$tmpl_css;
          file_put_contents(self::$layoutpath . '/scss/editor.css', $cssOut);

        }
      }



      /**********************************/
      /**********************************/
      /******* AJAX BS & FA COMPILATION  *****/
      /**********************************/
      /**********************************/

      if($session->get('buf_reload_bs_sass') == '1'){

        //self::$buf_debug = array();

        
        //Compile buf
        self::buf_bs_scss($scss, $bs_custom_colors, $sass_bs_files,$process,$bs_or_fa);

        $session->set('buf_reload_bs_sass','0');
        return self::$buf_debug;
      }

      if($session->get('buf_reload_fa_sass') == '1'){

        //self::$buf_debug = array();

        //Compile buf
        
        self::buf_fa_scss($scss, $sass_fa_files, $process, $bs_or_fa);
   

        $session->set('buf_reload_fa_sass','0');
        return self::$buf_debug;
      }



    //$buf_debug += self::addDebug('SASS | completed', 'css3-alt fab', 'template.css written', $startmicro, 'table-info');

    return self::$buf_debug;

  }


  /***************************************/
  /*******    COMPILE BUF_BS.CSS       ******/
  /***************************************/
  private static function buf_bs_scss($scss,$bs_custom, $sass_bs_files, $process,$bs_or_fa=''){

        if($bs_or_fa == 'fa'){
          return;
        }

        $imports = '';
       
        if(self::$buf_bs_on == 4){

          //recorro los archivos de bs
          foreach ($sass_bs_files as $key => $value) {
            
            $imports .= '@import "'.$key.'";
';

            //add the custom variables before _mixins
            if($key == self::$libspath.'/bootstrap4/scss/_mixins.scss'){
              $imports .= self::bs4_custom($bs_custom);
            }

            //var_dump($imports);


          }
        }


        if(self::$buf_bs_on == 5){

          if(self::$buf_bs_css_source == "custom"){
            
            //recorro los archivos de bs5
            foreach ($sass_bs_files as $key => $value) {

              //$imports .= '/*'.$key.'*/';
              
              $imports .= '@import "'.$key.'";
              ';
              
              //add the custom variables before _mixins
              if($key == self::$libspath.'/bootstrap/scss/_mixins.scss'){
                $imports .= self::bs5_custom($bs_custom);
              }
            } 

          }else{
            self::$buf_debug += self::addDebug('BS CSS SOURCE', 'css3-alt fab', 'NOT CUSTOM <small>'.self::$buf_bs_css_source, self::$startmicro, 'table-info');
          }

         
        }

  
        //buf_bs_fluid_max
        $imports .= '@media (min-width: 1200px){
          .container-fluid {
              max-width: '.self::$buf_bs_container_fluid_max.';
          }

          .container {
              max-width: '.self::$buf_bs_container_max.';
          }

          #contents.container-fluid{
              max-width: '.self::$buf_bs_container_content_max.';
          }
           #contents.container{
              max-width: '.self::$buf_bs_container_content_max.';
          }
        }';

   

        $cssOut = $scss->compile($imports);


        //Check cache directory is created
        if (!file_exists(self::$cachepath)) {
            mkdir(self::$cachepath, 0777, true);
        }

        file_put_contents(self::$cachepath . '/buf_bs.scss', $imports);

        file_put_contents(self::$cachepath . '/buf_bs.css', $cssOut);
        
        //if(self::$cssmix) file_put_contents(self::$cachepath . '/buf_comp.scss', $cssOut);

        if($process=='0'){
          //ALL
          self::$buf_debug += self::addDebug('CSS BUF | ALL', 'css3-alt fab', 'WRITTEN ALL reload<small>'.self::$cachepath . '/buf_bs.css</small>', self::$startmicro, 'table-info');

        }elseif($process=='1'){
          //OWN
          self::$buf_debug += self::addDebug('CSS BUF | OWN', 'css3-alt fab', 'WRITTEN OWN reload<small>'.self::$cachepath . '/buf_bs.css</small>', self::$startmicro, 'table-info');
        }else{
          //PRODUCTION
          self::$buf_debug += self::addDebug('CSS BUF | PROD', 'css3-alt fab', 'WRITTEN NOT EXISTS <small>'.self::$cachepath . '/buf_bs.css</small>', self::$startmicro, 'table-warning');
        }

        //self::$buf_debug += self::addDebug('CSS | PROD', 'css3-alt fab', 'WRITTEN NOT EXISTS <small>'.self::$cachepath . '/buf.css</small>', self::$startmicro, 'table-warning');
}


  /***************************************/
  /*******    COMPILE BUF_FA.CSS       ******/
  /***************************************/
  
  private static function buf_fa_scss($scss,$sass_fa_files, $process,$bs_or_fa=''){


      if($bs_or_fa == 'bs'){
        return;
      }

        $imports = '';

        foreach ($sass_fa_files as $key => $value) {
          $imports .= '@import "'.$key.'";';
          //$imports .= '@import "'.$key.'";';
        }
     
        $cssOut = $scss->compile($imports);

        //Check cache directory is created
        if (!file_exists(self::$cachepath)) {
            mkdir(self::$cachepath, 0777, true);
        }

        file_put_contents(self::$cachepath . '/buf_fa.css', $cssOut);


        if($process=='0'){
          //ALL
          self::$buf_debug += self::addDebug('CSS BUF FA | ALL', 'css3-alt fab', 'WRITTEN ALL reload<small>'.self::$cachepath . '/buf_fa.css</small>', self::$startmicro, 'table-info');

        }elseif($process=='1'){
          //OWN
          self::$buf_debug += self::addDebug('CSS BUF FA | OWN', 'css3-alt fab', 'WRITTEN OWN reload<small>'.self::$cachepath . '/buf_fa.css</small>', self::$startmicro, 'table-info');
        }else{
          //PRODUCTION
          self::$buf_debug += self::addDebug('CSS BUF FA | PROD', 'css3-alt fab', 'WRITTEN NOT EXISTS <small>'.self::$cachepath . '/buf_fa.css</small>', self::$startmicro, 'table-warning');
        }

        
}



  /***************************************/
  /*******    COMPILE TEMPLATE.CSS       ******/
  /***************************************/
  
  private static function tmpl_scss($scss,$process){

        $imports_css = '';

        //VARIABLES GENERALES
        $imports_css .= '$img_path: "'.self::$img_path.'";';
        $imports_css .= '$layout_img_path: "'.self::$layout_img_path.'";';
        $imports_css .= '$layout_path: "'.self::$layoutpath.'";';

        
        //OFFCANVAS
        $imports_css .= self::getOffcanvasStyles();

        $imports_css .= '@import "'.self::$lesspath.'/common.scss";';
        $imports_css .= '@import "'.self::$layoutpath.'/scss/template.scss";';

        //DEBUG
       //file_put_contents(self::$cachepath . '/'.self::$css_sha.'.scss', $imports_css);

        $cssOut = $scss->compile($imports_css);  

        //CREATE CACHE CSS
        
        file_put_contents(self::$cachepath . '/'.self::$css_sha.'.css', $cssOut);

       
        if($process=='0'){
          //ALL
          self::$buf_debug += self::addDebug('CSS tmpl | ALL', 'css3-alt fab', 'WRITTEN ALL reload<small>'.self::$cachepath . '/'.self::$css_sha.'.css</small>', self::$startmicro, 'table-info');

        }elseif($process=='1'){
          //OWN
          self::$buf_debug += self::addDebug('CSS tmpl | OWN', 'css3-alt fab', 'WRITTEN OWN reload<small>'.self::$cachepath . '/'.self::$css_sha.'.css</small>', self::$startmicro, 'table-info');
        }else{
          //PRODUCTION
          self::$buf_debug += self::addDebug('CSS tmpl | PROD', 'css3-alt fab', 'WRITTEN NOT EXISTS <small>'.self::$cachepath . '/'.self::$css_sha.'.css</small>', self::$startmicro, 'table-warning');
        }

  }



  private static function getOffcanvasStyles(){

    //OFFCANVAS VARIABLES
    $imports_css  = '';

    $imports_css .= '$buf_offcanvas_bg_color: '.self::$buf_offcanvas_bg_color.';';
    $imports_css .= '$buf_offcanvas_width: '.self::$buf_offcanvas_width.'%;';
    $imports_css .= '$buf_offcanvas_width_desktop: '.self::$buf_offcanvas_width_desktop.'%;';
    $imports_css .= '$buf_oc_speed:'.self::$buf_offcanvas_speed.'ms;';
  

    $imports_css .= '$buf_oc_button_color: '.self::$buf_oc_button_color.';';
    $imports_css .= '$buf_oc_button_color_hover: '.self::$buf_oc_button_color_hover.';';
    $imports_css .= '$buf_oc_button_color_active: '.self::$buf_oc_button_color_active.';';
    
    //I need to define -ms to avoid empty error;
    $imports_css .= '%buf_oc_button_editor{'.self::$buf_oc_button_editor.'};';
 

    $buf_reverse = (self::$buf_oc_button_reverse == 'r') ? '-r' : '';
    $imports_css .= '$buf_oc_button_style:'.self::$buf_oc_button_style.$buf_reverse.';';


    $imports_css .= '$buf_topbar_height:'.self::$buf_topbar_height.'px;';
    $imports_css .= '$buf_topbar_color:'.self::$buf_topbar_color.';';

    $imports_css .= '$buf_topbar_oc_height:'.self::$buf_topbar_oc_height.'px;';
    $imports_css .= '$buf_topbar_oc_color:'.self::$buf_topbar_oc_color.';';
    


    //button
    $imports_css .= '@import "'.self::$libspath.'/offcanvas/hamburgers/bufburger.scss";';
    //$imports_css .= '@import "'.self::$libspath.'/offcanvas/hamburgers/types/'.$buf_oc_button_style.'";';


    //bar
    $imports_css .= '@import "'.self::$libspath.'/offcanvas/offcanvas.scss";';

    return $imports_css;
  }





 


  /***************************************/
  /****    GET CONTAINER PARAMS       ****/
  /***************************************/
  
  private static function buf_get_container($buf_bs_container_fluid_max='100%',$buf_bs_container_max='1140',$buf_bs_container_content_max='1140'){

      $findpercentage   = '%';

      //CONTAINER FLUID MAX WIDTH
      $pos = strpos($buf_bs_container_fluid_max, $findpercentage);

      if ($pos === false) {
        self::$buf_bs_container_fluid_max = $buf_bs_container_fluid_max.'px';
      } else {
        self::$buf_bs_container_fluid_max = $buf_bs_container_fluid_max;
      }

      //CONTAINER MAX WIDTH
      $pos = strpos($buf_bs_container_max, $findpercentage);

      if ($pos === false) {
        self::$buf_bs_container_max = $buf_bs_container_max.'px';
      } else {
        self::$buf_bs_container_max = $buf_bs_container_max;
      }


      //CONTENT CONTAINER MAX WIDTH
      $pos = strpos($buf_bs_container_content_max, $findpercentage);

      if ($pos === false) {
        self::$buf_bs_container_content_max = $buf_bs_container_content_max.'px';
      } else {
        self::$buf_bs_container_content_max = $buf_bs_container_content_max;
      }
  }


  /***************************************/
  /****     GET TEMPLATE NAME         ****/
  /***************************************/
  private static function get_template_name(){
    
    if (!file_exists(self::$cachepath)) {
        mkdir(self::$cachepath, 0777, true);
    }

    $session = Factory::getSession();

    //RECACHE ACTIVATED
    if(self::$recache){
      $sha = self::$buf_layout.'_template_';
      $sha .= hash('crc32', self::$buf_layout.'template.css').rand();
      
      $session->set('buf_template_sha', $sha);

      self::$buf_debug += self::addDebug('recache | ', 'smile-o', 'RECACHE activated: <small>'.$sha.'</small>', self::$startmicro);

    }else{
      //RECACHE NOT ACTIVATED
      $sha = self::$buf_layout.'_template';
      //$session->set('buf_template_sha', $sha);
    }

    //if(self::$cssmix) $sha .= '_mix';

    return $sha;
  }










  private static function getCurrentParams($id){
    //V3

       $db = Factory::getDBO();
        $query = $db->getQuery(true);
        $query->select(array($db->quoteName('template'), $db->quoteName('params')))
        ->from($db->quoteName('#__template_styles'))
        ->where($db->quoteName('id') . ' = ' . $id);


        //$sql = "SELECT template,params FROM `#__template_styles` WHERE `id` = $id";
        $db->setQuery($query); 
        //$db->query();
        $row = $db->loadObject();
        $json = $row;

        return $json;

  }


  //FA4 files
  private static function buf_fa_files($buf_fa){
    $fafiles = array();
    //var_dump($buf_fa);
    //minimum
    if($buf_fa == 1){
      array_push($fafiles,'variables','mixins', 'path', 'core', 'icons');
      //array_push($fafiles,'variables','mixins');
    //All
    }else{
      //array_push($fafiles,'variables','mixins','path','core', 'larger', 'fixed-width', 'list', 'bordered-pulled', 'animated', 'rotated-flipped', 'stacked', 'icons', 'screen-reader');
      //do not include path... I will load it async
      array_push($fafiles,'variables','mixins','path', 'core', 'larger', 'fixed-width', 'list', 'bordered-pulled', 'animated', 'rotated-flipped', 'stacked', 'icons', 'screen-reader');
    }

    return $fafiles ;

  }


  //BS4 CUSTOM COLORS
  private static function bs4_custom($bs4_custom){
    $custom = '';

    if($bs4_custom['bs_custom_body_bg']) $custom .= '$body-bg: '.$bs4_custom['bs_custom_body_bg'].';';
    if($bs4_custom['bs_custom_body_color']) $custom .= '$body-color: '.$bs4_custom['bs_custom_body_color'].';';

    $custom .= '$theme-colors: (';

    //if($bs4_custom['bs_custom_primary']) $custom .= '"primary": '.$bs4_custom['bs_custom_primary'].',';
    //if($bs4_custom['bs_custom_secondary']) $custom .= '"secondary": '.$bs4_custom['bs_custom_secondary'].',';
    //if($bs4_custom['bs_custom_success']) $custom .= '"success": '.$bs4_custom['bs_custom_success'].',';
    //if($bs4_custom['bs_custom_info']) $custom .= '"info": '.$bs4_custom['bs_custom_info'].',';
    //if($bs4_custom['bs_custom_warning']) $custom .= '"warning": '.$bs4_custom['bs_custom_warning'].',';
    //if($bs4_custom['bs_custom_danger']) $custom .= '"danger": '.$bs4_custom['bs_custom_danger'].',';
    //if($bs4_custom['bs_custom_light']) $custom .= '"light": '.$bs4_custom['bs_custom_light'].',';
    //if($bs4_custom['bs_custom_dark']) $custom .= '"dark": '.$bs4_custom['bs_custom_dark'].',';

    $custom .= ($bs4_custom['bs_custom_primary']) ? '"primary": '.$bs4_custom['bs_custom_primary'].',' : '"primary": $primary,';
    $custom .= ($bs4_custom['bs_custom_secondary']) ? '"secondary": '.$bs4_custom['bs_custom_secondary'].',' : '"secondary": $secondary,';
    $custom .= ($bs4_custom['bs_custom_success']) ? '"success": '.$bs4_custom['bs_custom_success'].',' : '"success": $success,';
    $custom .= ($bs4_custom['bs_custom_info']) ? '"info": '.$bs4_custom['bs_custom_info'].',' : '"info": $info,';
    $custom .= ($bs4_custom['bs_custom_warning']) ? '"warning": '.$bs4_custom['bs_custom_warning'].',' : '"warning": $warning,';
    $custom .= ($bs4_custom['bs_custom_danger']) ? '"danger": '.$bs4_custom['bs_custom_danger'].',' : '"danger": $danger,';
    $custom .= ($bs4_custom['bs_custom_light']) ? '"light": '.$bs4_custom['bs_custom_light'].',' : '"light": $light,';
    $custom .= ($bs4_custom['bs_custom_dark']) ? '"dark": '.$bs4_custom['bs_custom_dark'].',' : '"dark": $dark,';


    //$custom .= '"primary": '.$bs4_custom['bs4_custom_primary'].',';

    $custom .= ');';




    return $custom;
  }

  //BS5 CUSTOM COLORS
    private static function bs5_custom($bs5_custom){

      //llamo a bs4 porque es igual
      $custom = self::bs4_custom($bs5_custom);
      
      return $custom;
    }


  //FA COPY TO CACHE
  public static function buf_fa_copy_to_cache($fa_version = 'fontawesome5'){

    
      //check fa5pro files
      if($fa_version == 'fontawesome5pro'){
          $fa5pro_exists = file_exists (self::$libspath . '/font-awesome/fontawesome5pro/webfonts/fa-brands-400.ttf') ? true:false;
          if(!$fa5pro_exists){
              $fa_version = 'fontawesome5';
          }
      }
    
      if (!file_exists(self::$cachepath.'/'.$fa_version.'/webfonts/fa-regular-400.woff2')){

        //check cache/fa_folder
        if (!file_exists(self::$cachepath.'/'.$fa_version)) {
            mkdir(self::$cachepath.'/'.$fa_version, 0777, true);
        }
        //check cache/fa_folder/webfonts to delete it!
        if (file_exists(self::$cachepath.'/'.$fa_version.'/webfonts')) {
           rmdir(self::$cachepath.'/'.$fa_version.'/webfonts');
        }

        $dir = self::$cachepath.'/'.$fa_version.'/webfonts';
        if (count(glob("$dir/*")) === 0) {
          jimport('joomla.filesystem.folder');
          jimport('joomla.filesystem.file');
         
          Folder::copy(self::$libspath . '/font-awesome/'.$fa_version.'/webfonts', self::$cachepath.'/'.$fa_version.'/webfonts');

          self::$buf_debug += self::addDebug('FA5 | cache', 'font-awesome fab', $fa_version.' cache webfonts doesnt exist files <strong>CREATED</strong>', self::$startmicro);
        }
      }
      

    return true;
  }



/*
  private static function addDebug($name, $icon, $value, $startmicro, $tr_class=''){

    //AJAX
    if(self::$isajax) return array();

    $current_time = microtime(TRUE);
    $totaltime = $current_time - $startmicro;

    $mireturn = array($name => array($icon, $value, $totaltime*10000, $tr_class));
    return $mireturn;
  }
*/

  /***************************************/
  /***************************************/
  /************ ESSENTIAL FUNCS **********/
  /***************************************/
  /***************************************/

  private static function addDebug($name='', $icon='', $value='', $startmicro=0, $tr_class='', $service='bufsass.php'){

    if(self::$isajax) return  array($name => array('icon'=>$icon, 'value'=>$value, 'totaltime'=>'', 'tr_class'=>'', 'service'=>''));
    $current_time = microtime(TRUE);
    $totaltime = $current_time - $startmicro;

    $mireturn = array($name => array('icon'=>$icon, 'value'=>$value, 'totaltime'=>$totaltime*10000, 'tr_class'=>$tr_class, 'service'=>$service));
    return $mireturn;
  }


}



