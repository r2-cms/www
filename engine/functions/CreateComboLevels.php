<?php
	function CreateComboLevels( $allow=0, $format='HTML', $useDash=true, $showAllLevels=false) {
		$html		= $format=='HTML'? '': array();
		$options	= CreateLevelsArray();
		$len	= min(count($options), $_SESSION['login']['level']);
		if ( $showAllLevels) {
			$len	= count($options);
		}
		if ( $len < count($options)) {
			$_options	= array();
			
			if ( $useDash) {
				$len++;
			}
			
			for ($i=0; $i<$len; $i++) {
				if ( isset($options[$i])) {
					$_options[]	= $options[$i];
				}
			}
			if ( $useDash && $len < count($options)) {
				$options	= array_merge($_options, array(array('name'=>'-', 'id'=>$i)));
			}
			$len++;
		}
		$found	= false;
		$selected	= '';
		for ($i=0; $i<$len; $i++) {
			
			if ( !$found && $options[$i]['id'] == $allow) {
				$selected	= 'selected="selected" ';
				$found	= true;
			} else if ( !$found && $i==$len-1) {
				$selected	= 'selected="selected" ';
				if ( $format=='HTML') {
					$html	= str_replace('SELECTED="SELECTED" ', 'selected="selected" ', $html);
				}
				$found	= true;
			} else if ( $i==0) {
				$selected	= 'SELECTED="SELECTED" ';
			}
			if ( $format=='HTML') {
				$html		.= '<option value="'. $options[$i]['id'] .'" '.$selected.'>'. $options[$i]['pt'] .'</option>'. PHP_EOL;
			} else {
				$html[]	= array($options[$i]['id'], $options[$i]['name'], $options[$i]['pt']);
			}
			$selected	= '';
		}
		if ( $format=='HTML') {
			$html	= str_replace('SELECTED="SELECTED" ', '', $html);
		}
		
		return $html;
	}
	require_once(SROOT.'engine/functions/CreateLevelsArray.php');
?>