<?php

use BUF\BufHelper;
use Joomla\CMS\Factory;

defined( '_JEXEC' ) or die; 

/**********************/
/**********************/
//ANALYTICS
/**********************/
/**********************/

$anal_code = false;

$a_code = $buf_anal->get('buf_analytics_code', 'UA-XXXXX-Y');
$m_id = $buf_anal->get('buf_analytics_measurementid', 'G-XXXXXXXXXX');

$buf_analytics_storage = ($buf_anal->get('buf_analytics_storage', '0')=='0')?false:true;
$buf_analytics_cookie_consent = $buf_anal->get('buf_analytics_cookie_consent', 'reDimCookieHint');

$app = property_exists($this, 'app') ? $this->app : Factory::getApplication();
$s = $app->getSession();


//anal v3
if($buf_anal->get('buf_analytics_version','3') == '3'){

    if($a_code != 'UA-XXXXX-Y'){
        $anal_code = $a_code;
    }
}

//anal v4
if($buf_anal->get('buf_analytics_version','3') == '4'){

    if($m_id != 'G-XXXXXXXXXX'){
        $anal_code = $m_id;
    }
}

$anal3_datalayer = "window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
";

//denied by default
$anal3_datalayer .=  "
window['ga-disable-MEASUREMENT_ID'] = true;
gtag('consent', 'default', {
    'ad_storage': 'denied',
    'analytics_storage': 'denied',
    'allow_google_signals': false,
    'allow_ad_personalization_signals': false
    });
";

//granted
$anal3_datalayer .=  "
function bufAnalCookieConsent(){
    window['ga-disable-MEASUREMENT_ID'] = false;
    gtag('config', '".$anal_code."', {'client_id': '".$s->getId()."'});
    gtag(
        'consent', 'update', 
        {
        'analytics_storage': 'granted'
        }
    );
}";

if($buf_analytics_storage){
    $anal3_datalayer .=  "bufAnalCookieConsent();";
}else{
    // Get cookie
    $cookieValue = $app->input->cookie->get($buf_analytics_cookie_consent);
    if($cookieValue=='1'){
        $anal3_datalayer .=  "bufAnalCookieConsent();";
    }
}

/*******j4*********/
if($jversion == '4'){
    if($anal_code){
        $wa->registerAndUseScript('buf_anal_gtag','https://www.googletagmanager.com/gtag/js?id='.$anal_code,[],['async'=>'async']);
        $wa->addInlineScript($anal3_datalayer);
    }
}

/*******j3*********/
if($jversion == '3'){
    if($anal_code){
        $doc->addScript('https://www.googletagmanager.com/gtag/js?id='.$a_code, array(), array('async'=>'async'));
        $doc->addScriptDeclaration($anal3_datalayer);
    }
}

if($anal_code){
    $buf_debug += BufHelper::addDebug('Buf anal', 'chart-bar', '<strong>Analytics:</strong> <small>'.$anal_code.'</small>', $startmicro, 'table-info', 'googleAnalytics.php');
}else{
    $buf_debug += BufHelper::addDebug('Buf anal', 'chart-bar', '<strong>Analytics:</strong> <small>NOT LOADED</small>', $startmicro, 'table-info', 'googleAnalytics.php');
}


?>