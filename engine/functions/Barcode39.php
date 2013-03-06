<?php
	/**
	 * Barcode39 - Code 39 Barcode Image Generator
	 * 
	 * @package Barcode39
	 * @category Barcode39
	 * @name Barcode39
	 * @version 1.0
	 * @author Shay Anderson 05.11
	 * @link http://www.shayanderson.com/php/php-barcode-generator-class-code-39.htm
	 * @license http://www.gnu.org/licenses/gpl.html GPL License
	 * This is free software and is distributed WITHOUT ANY WARRANTY
	 */
	function Barcode39($code, $options=array()) {
		$f2B = "11";
		$f2W = "00";
		$f2b = "10";
		$f2w = "01";
		$barThick = isset($options['barThick'])? $options['barThick']: 3;
		$barThin = isset($options['barThin'])? $options['barThin']: 1;
		
		$_code = array();
		$_codes_39 = array(
			'32' => '100011011001110110',
			'36' => '100010001000100110',
			'37' => '100110001000100010',
			'42' => '100010011101110110',
			'43' => '100010011000100010',
			'45' => '100010011001110111',
			'46' => '110010011001110110',
			'47' => '100010001001100010',
			'48' => '100110001101110110',
			'49' => '110110001001100111',
			'50' => '100111001001100111',
			'51' => '110111001001100110',
			'52' => '100110001101100111',
			'53' => '110110001101100110',
			'54' => '100111001101100110',
			'55' => '100110001001110111',
			'56' => '110110001001110110',
			'57' => '100111001001110110',
			'65' => '110110011000100111',
			'66' => '100111011000100111',
			'67' => '110111011000100110',
			'68' => '100110011100100111',
			'69' => '110110011100100110',
			'70' => '100111011100100110',
			'71' => '100110011000110111',
			'72' => '110110011000110110',
			'73' => '100111011000110110',
			'74' => '100110011100110110',
			'75' => '110110011001100011',
			'76' => '100111011001100011',
			'77' => '110111011001100010',
			'78' => '100110011101100011',
			'79' => '110110011101100010',
			'80' => '100111011101100010',
			'81' => '100110011001110011',
			'82' => '110110011001110010',
			'83' => '100111011001110010',
			'84' => '100110011101110010',
			'85' => '110010011001100111',
			'86' => '100011011001100111',
			'87' => '110011011001100110',
			'88' => '100010011101100111',
			'89' => '110010011101100110',
			'90' => '100011011101100110'
		);
		$code = (string)strtoupper($code.'');
		$i = 0;
		while(isset($code[$i])) {
			$_code[] = $code[$i++];
		}
		// add start and stop symbols
		array_unshift($_code, "*");
		array_push($_code, "*");
		
		if(!is_array($_code) || !count($_code)) {
			return false;
		}
		$bars = array();
		$barcode_string = null;
		// set code 39 codes
		$i = 0;
		foreach($_code as $k => $v) {
			if(isset($_codes_39[ord($v)])) {
				// valid code add code 39, also add separator between characters if not first character
				$code = ( $i ? $f2w : null ) . $_codes_39[ord($v)];
				if($code) {
					$barcode_string .= " {$v}";
					$w = 0;
					$f2 = $fill = null;
					for($j = 0; $j < strlen($code); $j++) {
						$f2 .= (string)$code[$j];
						if(strlen($f2) == 2) {
							$fill = $f2 == $f2B || $f2 == $f2b? "p": "b";
							$w = $f2 == $f2B || $f2 == $f2W? $barThick: $barThin;
							if($w && $fill) {
								$bars[] = "<div class='$fill$w' ></div>";
							}
							// reset params
							$f2 = $fill = null;
							$w = 0;
						}
					}
				}
				$i++;
			} else {
				unset($_code[$k]);
			}
		}
		if(!count($bars)) {
			return '';
		}
		return join('', $bars);
	}
?>