<?php
/**
* @package BUF Framework
* @author dibuxo http://www.dibuxo.com
* @copyright Copyright (c) 2005 - 2017 dibuxo
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/  

namespace BUF;
// no direct access
defined('_JEXEC') or die('Restricted access');

use BUF\PHP_ICO;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Uri\Uri;


class BUFfavicon
{

  public static function create_favicons($templateid, $image){
    
    //$templateparams = json_decode(BUFfavicon::getCurrentParams($templateid)->params);
    $templateparams = new Registry(BUFfavicon::getCurrentParams($templateid)->params);
    $template_name = BUFfavicon::getCurrentParams($templateid)->template;
    
    $template_path = JPATH_SITE.'/templates/buf/';
    $image_path =  JPATH_SITE.'/'.$image;
    $buf_layout = $templateparams->get('buf_layout','default');
    $fav_path = JPATH_SITE.'/templates/buf/layouts/'.$buf_layout.'/icons/';

    if(!Folder::exists($fav_path)){
      Folder::create($fav_path);
    }
    
    $uri = Uri::base();

    $error = '';
    
    $source = $image_path;
    $destination = $fav_path;

    $android = BUFfavicon::_generateAndroid($image_path, $fav_path);

    if($android!==true){
      return $android;
    }

    $android_json = BUFfavicon::_generateAndroidJson($template_name,$fav_path);
    
    $ms_json = BUFfavicon::_generateMsXml($template_name,$fav_path,$templateparams->get('buf_mscolor','#57616d'),$buf_layout);

    $ios = BUFfavicon::_generateIos($image_path, $fav_path);

    $favicon_ico = BUFfavicon::_generateIco($image_path, $fav_path);

    return true;
  }

  private static function _generateAndroid($image, $output){
    $imgSrc = $image; 
    //create image from the jpeg
    //var_dump($image);
    $output_file = $output.'android-icon-192x192.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 192,192,0);
    if($myImage!==true){
      return $myImage;
    }
    $image = $output_file;
    $output_file = $output.'android-icon-144x144.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 144,144,0);

    $output_file = $output.'android-icon-96x96.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 96,96,0);

    $output_file = $output.'android-icon-96x96.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 96,96,0);

    $output_file = $output.'android-icon-72x72.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 72,72,0);

    $output_file = $output.'android-icon-48x48.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 48,48,0);

    $output_file = $output.'android-icon-48x48.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 48,48,0);

    //favicon PNG
    $output_file = $output.'favicon-96x96.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 96,96,0);

    $output_file = $output.'favicon-32x32.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 32,32,0);

    $output_file = $output.'favicon-16x16.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 16,16,0);

    //MICROSOFT PNG
    $output_file = $output.'ms-icon-70x70.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 70,70,0);

    $output_file = $output.'ms-icon-144x144.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 144,144,0);

    $output_file = $output.'ms-icon-150x150.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 150,150,0);


    return true ;
  }

  private static function _generateAndroidJson($template_name, $fav_path){

    $icos = array('name'=>$template_name, 'icons'=>
      array(
        array(
        'src'=>'/android-icon-36x36.png',
        'sizes'=>'36x36',
        'type'=>'image/png',
        'density'=>'0.75'
        ),
        array(
        'src'=>'/android-icon-48x48.png',
        'sizes'=>'48x48',
        'type'=>'image/png',
        'density'=>'1.0'
        ),
        array(
        'src'=>'/android-icon-72x72.png',
        'sizes'=>'72x72',
        'type'=>'image/png',
        'density'=>'1.5'
        ),
        array(
        'src'=>'/android-icon-96x96.png',
        'sizes'=>'96x96',
        'type'=>'image/png',
        'density'=>'2.0'
        ),
        array(
        'src'=>'/android-icon-144x144.png',
        'sizes'=>'144x144',
        'type'=>'image/png',
        'density'=>'3.0'
        ),
        array(
        'src'=>'/android-icon-192x192.png',
        'sizes'=>'192x192',
        'type'=>'image/png',
        'density'=>'4.0'
        )
      )
    );

    $fp = fopen($fav_path.'manifest.json', 'w');
    fwrite($fp, json_encode($icos));
    fclose($fp);

    return true ;
  }

  private static function _generateMsXml($template_name, $fav_path, $color,$buf_layout){

    $xml = '<?xml version="1.0" encoding="utf-8"?>
          <browserconfig>
            <msapplication>
              <tile>
                <square70x70logo src="templates/'.$template_name.'/layouts/'.$buf_layout.'/icons/ms-icon-70x70.png"/>
                <square150x150logo src="templates/'.$template_name.'/layouts/'.$buf_layout.'/icons/ms-icon-150x150.png"/>
                <square310x310logo src="templates/'.$template_name.'/layouts/'.$buf_layout.'/icons/ms-icon-310x310.png"/>
                <TileColor>'.$color.'</TileColor>
              </tile>
            </msapplication>
          </browserconfig>';

    file_put_contents($fav_path.'/browserconfig.xml', $xml); 

    return true ;
  }
  //MAC IOS
  private static function _generateIos($image, $output){
    $imgSrc = $image; 
    //create image from the jpeg
    //var_dump($image);
    $output_file = $output.'apple-icon-precomposed.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 192,192,0);
    if($myImage!==true){
      return $myImage;
    }
    $image = $output_file;

    $output_file = $output.'apple-icon.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 192,192,0);

    $output_file = $output.'apple-icon-180x180.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 180,180,0);

    $output_file = $output.'apple-icon-152x152.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 152,152,0);

    $output_file = $output.'apple-icon-144x144.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 144,144,0);

    $output_file = $output.'apple-icon-120x120.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 120,120,0);

    $output_file = $output.'apple-icon-114x114.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 114,114,0);

    $output_file = $output.'apple-icon-76x76.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 76,76,0);

    $output_file = $output.'apple-icon-72x72.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 72,72,0);

    $output_file = $output.'apple-icon-60x60.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 60,60,0);

    $output_file = $output.'apple-icon-57x57.png';
    $myImage = BUFfavicon::redimesionImage($image,$output_file, 57,57,0);

    return true ;
  }

  //ICO
  private static function _generateIco($image, $output){

      //ICO root
      $destination = $output.'favicon.ico';
      //$destination_icons = $output.'icons/favicon.ico';
      $source = $output.'/android-icon-192x192.png';

      $sizes = array(
          array( 16, 16 ),
           array( 24, 24 ),
            array( 32, 32 ),
             array( 48, 48 )

      );
      $ico_lib = new PHP_ICO( $source, $sizes);
      $ico_lib->save_ico( $destination );
      //$ico_lib->save_ico( $destination_icons );
      //base
      //$ico_lib->save_ico(JPATH_SITE.'/templates/buf/');
     
      return true ;
    }


  //TODO create a general class
  private static function getCurrentParams($id){
    /*
      $db = Factory::getDBO();
      $sql = "SELECT template,params FROM `#__template_styles` WHERE `id` = $id";
      $db->setQuery($sql); 
      $db->query();
      $row = $db->loadObject();
      $json = $row;
      return $json;
    */
      $db = Factory::getDbo();
      $query = $db->getQuery(true);

      $query
          ->select(array('template,params'))
          ->from($db->quoteName('#__template_styles'))
          ->where($db->quoteName('id').' = '.$db->quote($id));
      $db->setQuery($query);
      $result = $db->loadObject();


     return $result;
  }


  public static function redimesionImage($src, $dst, $width, $height, $crop=1){


    if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";

    $type = strtolower(substr(strrchr($src,"."),1));
    if($type == 'jpeg') $type = 'jpg';
    switch($type){
      case 'bmp': $img = imagecreatefromwbmp($src); break;
      case 'gif': $img = imagecreatefromgif($src); break;
      case 'jpg': $img = imagecreatefromjpeg($src); break;
      case 'png': $img = imagecreatefrompng($src); break;
      default : return "Unsupported picture type!";
    }

    // resize
    if($crop){
      if($w < $width or $h < $height) return "Picture is too small!";
      $ratio = max($width/$w, $height/$h);
      $h = $height / $ratio;
      $x = ($w - $width / $ratio) / 2;
      $w = $width / $ratio;
    }
    else{
      if($w < $width and $h < $height) return "Picture is too small!";
      $ratio = min($width/$w, $height/$h);
      $width = $w * $ratio;
      $height = $h * $ratio;
      $x = 0;
    }

    // create canvas
    $size = max($width,$height);
    $new = imagecreatetruecolor($size, $size);
    
    // preserve transparency
    if($type == "gif" or $type == "png"){
      imagesavealpha($new, true);
      $trans_background = imagecolorallocatealpha($new, 0, 0, 0, 127);
      imagefill($new, 0, 0, $trans_background);
    }elseif($type == "jpg"){
      $trans_background = imagecolorallocatealpha($new, 255, 255, 255, 0);
      imagefill($new, 0, 0, $trans_background);
    }

    //put images in canvas
    //imagecopyresampled($new, $img, ($size-$width)/2, ($size-$height)/2, $x, 0, $width, $height, $w, $h);



    imagecopyresampled($new, $img, 
      (int) (($size-$width)/2), 
      (int) (($size-$height)/2), 
      (int) ($x), 
      0, 
      (int) ($width), 
      (int) ($height), 
      (int) ($w), 
      (int) ($h)
    );



    //create PNG
    imagepng($new, $dst); 
    return true;
  }


}
