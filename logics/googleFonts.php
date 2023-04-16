<?php defined( '_JEXEC' ) or die; 

/**********************/
//FONTS
/**********************/


/**********************/
if($jversion=='4'){
    $gfont = $templateparams->get('buf_googlefonts_name', '');


	
    $this->getPreloadManager()->dnsPrefetch('https://fonts.googleapis.com/', []);
    $this->getPreloadManager()->preconnect('https://fonts.googleapis.com/', []);
    $this->getPreloadManager()->preconnect('https://fonts.gstatic.com/', []);
    $this->getPreloadManager()->preload($gfont.'&display=swap', ['as' => 'style']);
    //$wa->registerAndUseStyle('fontscheme.current', $gfont.'&display=swap', [], ['media' => 'print', 'rel' => 'lazy-stylesheet', 'onload' => 'this.media=\'all\'']);
    $wa->registerAndUseStyle('fontscheme.current', $gfont.'&display=swap', [], []);
}


/**********************/
if($jversion=='3'){

	$gfont = $templateparams->get('buf_googlefonts_name', '');
	$gfont_array = explode('|',$gfont);
	$gfont_families = '';

	foreach ($gfont_array as $key => $value) {
		if(!$key==0) $gfont_families.=",";
		$gfont_families.="'".$value."'";
	}

	//option FETCH AND ALTER
		echo "<script>

			const loadGFont = (url) => {
			  // the 'fetch' equivalent has caching issues
			  var xhr = new XMLHttpRequest();
			  xhr.open('GET', url, true);
			  xhr.onreadystatechange = () => {
			    if (xhr.readyState == 4 && xhr.status == 200) {
			      
			      let css = xhr.responseText;

			      const head = document.getElementsByTagName('head')[0];
			      const style = document.createElement('style');
			      style.appendChild(document.createTextNode(css));
			      head.appendChild(style);
			    }
			  };
			  xhr.send();
			}

			loadGFont('https://fonts.googleapis.com/css?family=".$gfont."&display=swap');

		</script>";

}

?>