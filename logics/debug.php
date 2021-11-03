<?php defined( '_JEXEC' ) or die; 

echo '<div class="table-responsive buf_debug"> <table class="table table-striped table-condensed table-sm">';

foreach ($buf_debug as $key => $value) {

	if(!isset($anterior)) $anterior = 0.0;
	$actual = $value['totaltime'] - $anterior;

	$clase='';

	if($bs_version == 4){
		$clase = ($actual<=10) ? 'badge-success' : 'badge-warning';
		if($actual>=1000) $clase = 'badge-danger';
	}

	if($bs_version == 5){
		$clase = ($actual<=10) ? 'bg-success' : 'bg-warning';
		if($actual>=1000) $clase = 'bg-danger';
	}

	/*
	echo '<tr class="'.$value[3].'"><td><i class="fa fa-'.$value[0].'" aria-hidden="true"></i></td><td>'.$key.'</td><td>'.$value[1].'</td><td><span class="badge">'.round($value[2],4).'ms</span></td><td><span class="badge '.$clase.'">+'.round($actual,3).'</span></td></tr>';
	$anterior = $value[2];
	*/

	
	$fa_fixed = explode(' ', $value['icon']);
	$final_fa='';
	if(in_array('fas', $fa_fixed) || in_array('far', $fa_fixed) || in_array('fab', $fa_fixed)){
		$final_fa = 'fa-'.$value['icon'];
	}else{
		$final_fa = 'fas fa-'.$value['icon'];
	}


	echo '<tr class="'.$value['tr_class'].'">
		<td><small>'.$value['service'].'</small></td>
		<td><i class="'.$final_fa.'" aria-hidden="true"></i></td>
		<td>'.$key.'</td><td>'.$value['value'].'</td>
		<td><span class="badge bg-light text-dark">'.round($value['totaltime'],2).'ms</span></td>
		<td><span class="badge '.$clase.'">+'.round($actual,3).'</span></td>
	</tr>';
	$anterior = $value['totaltime'];
	
}

echo '</table></div>';