<?php
	if ( !defined('SROOT')) {
		require_once('../connect.php');
	}
	class GT8 {
		static $compilationInitialized	= false;
		protected $row;
		public $data	= array();
		public $jsVars	= array();
		public $isAdmin	= false;
		public function GT8() {
			//GT8::canonizeUrl();
			
			if ( strpos('#'.$_SERVER['REQUEST_URI'], '/'.$GT8['admin']['root']) ) {
				$this->isAdmin	= true;
			} else {
				$this->isAdmin	= false;
			}
			
			
			$this->checkActionRequest();
		}
		private function checkActionRequest() {
			if ( isset($_GET['clean-qsa']) && $_GET['clean-qsa']) {
				header('location: ./');
				die();
			}
		}
		public function printHTML($template, $data) {
			/**
			 * keys:
			 * 	((value))
			 * 	{{UTF-8}}
			 * 	$$number_format$$
			**/
			print( GT8::getMatchPairs( $template, $data));
		}
		public function getHTML( $template, $data) {
			$template	= GT8::getMatchPairs($template, $data);
			
			return $template;
		}
		protected function getMatchPairs( $text, $data) {
			//bidimensional arrays
			foreach( $data as $name=>$value) {
				
				if ( gettype($data[$name]) == 'array') {
					foreach( $data[$name] as $name2=>$value2) {
						$data[$name .'.'. $name2]	= $value2;
					}
				}
			}
			
			foreach( $data as $name=>$value) {
				
				if ( strpos('#'. $text, '##'. $name .'##')  ) {
					$text	= str_replace('##'. $name .'##', utf8_encode($value), $text);
				}
				if ( strpos('#'. $text, '{{'. $name .'}}')  ) {
					$text	= str_replace('{{'. $name .'}}', $value, $text);
				}
				if ( strpos('#'. $text, '[['. $name .']]')  ) {
					$text	= str_replace('[['. $name .']]', utf8_encode(htmlentities($value)), $text);
				}
				if ( strpos('#'. $text, '$$'. $name .'$$')  ) {
					$numOnly	= RegExp($value, '[0-9\.\-\+\,]+');
					$num		= (double)RegExp(trim(str_replace(',', '.', $numOnly)), '[0-9\.\-\+]+');
					$formated	= number_format( $num, 2, ',', '.');
					$formated	= str_replace($numOnly, $formated, $value);
					$text		= str_replace('$$'. $name .'$$', $formated, $text);
				}
				while ( strpos('#'. $text, '//'. $name .':') || strpos('#'. $text, '//'. $name .'\\') ) {
					$value	= explode('-', $data[$name]);
					$Y	= $value[0];
					$y	= substr($Y, 2);
					$m	= $value[1];
					$c	= (integer)$m;
					$d	= $value[2];
					$e	= (integer)$d;
					$H	= date('H');	//PHP: H
					$h	= date('h');	//PHP: h
					$k	= date('G');	//PHP: G
					$l	= date('g');	//PHP: g
					$i	= date('i');	//PHP: i
					$s	= date('s');	//PHP: s
					
					//máscara
					$start	= strpos('#'. $text, '//'. $name .(strpos('#'. $text, '//'. $name .':')?':':''))+strlen('//'. $name .'\\\\')-2;
					$len	= strpos('#'. $text, '\\\\', $start);
					$mask	= $start==$len? '': substr($text, $start, $len-$start-1);
					$mask2	= '';
					
					if ( empty($mask))  {
						$mask2	= '%d/%m/%Y';
						if ( $value[2] && strlen($value[2])>2) {
							$hasT	= explode(':', $value[2]);
							$mask2	= $hasT[0]? $mask2.' %H:': $mask2;
							$mask2	= $hasT[1]? $mask2.':%i': $mask2;
							$mask2	= $hasT[2]? $mask2.':%S': $mask2;
							$mask2	= str_replace(array(' :', '::'), ':', trim($mask2));
						}
					}
					
					//detectando se o valor do banco é DATETIME ou somente DATE
					if ( strpos('#'. $d, ':') > 0) {
						$t	= explode(':', $d);
						$H	= substr($t[0], 3);
						$h	= (integer)$H;
						$k	= substr( '0000'. $H%12, -2);
						$l	= (integer)$k;
						$i	= $t[1];
						$I	= (integer)$i;
						$S	= $t[2];
						$s	= (integer)$S;
						$d	= substr($d, 0, 2);
						$e	= (integer)$d;
					}
					$value	= str_replace(
						array('%Y','%y','%m','%c','%d','%e','%H','%h','%k','%l','%i','%I','%S','%s'),
						array($Y,  $y,  $m,  $c,  $d,  $e,  $H,  $h,  $k,  $l,  $i,  $I,  $S, $s),
						$mask2? $mask2: $mask
					);
					
					if ( $mask2) {
						$text		= str_replace(array('//'. $name .'\\\\', '//'. $name .':\\\\'), $value, $text);
					} else {
						$text		= str_replace('//'. $name .':'.$mask .'\\\\', $value, $text);
					}
				}
			}
			return $text;
		}
		public function getDirLocation($base='') {
			global $GT8;
			
			if ( $base) {
				$base	= strtolower($base);
			} else if ( GT8::isAdmin() ) {
				$base	= strtolower($GT8['admin']['root']);
			}
			
			$path	= explode('/', substr($_SERVER['REQUEST_URI'], 0, (strpos($_SERVER['REQUEST_URI'], '?')?strpos($_SERVER['REQUEST_URI'], '?'):strlen($_SERVER['REQUEST_URI']))));
			$dirs	= array();
			$baseFound	= $base? false: true;
			for ($i=0; $i<count($path); $i++) {
				$crr	= $path[$i];
				
				if ( !$baseFound) {
					if ( strtolower($crr.'/') == $base) {
						$baseFound	= true;
					}
				} else if ( $crr) {
					$dirs[]	= $crr;
				}
				
			}
			$pathQSA	= '';
			if ( strpos('#'.$_SERVER['REQUEST_URI'], '?')) {
				$pathQSA	= substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?'));
			}
			if ( GT8::isAdmin() ) {
				$path	= CROOT . $GT8['admin']['root'] . $dirs[0];
				$html	= '<a href="'. CROOT .'" class="button" style="z-index:'. (count($dirs)+20).'; " >'. (isset($GT8['title'])? $GT8['title']: 'Home') .'</a>';
				$html	.= '<a href="'. CROOT . $GT8['admin']['root'] .'" class="button" style="z-index:'. (count($dirs)+18).'; " >'. (isset($GT8['admin']['title'])? $GT8['admin']['title']: 'Admin') .'</a>';
				$html	.= '<a href="'. $path .'/" class="button" style="z-index:'. (count($dirs)+17).'; " >'. (isset($GT8[$this->name]['title'])? $GT8[$this->name]['title']: $dirs[0]) .'</a>';
				for ( $i=1, $len=count($dirs); $i<$len; $i++) {
					$path	= $path .'/'. $dirs[$i];
					$html	.= ' <a class="button" href="'. $path . ($i==$len-1?$pathQSA:'/').'" style="z-index:'. (count($dirs)-$i) .'; " >'. ($dirs[$i]) .'</a>';
				}
			} else {
				$path	= $_SERVER['REQUEST_URI'];
				if ( strpos('#'.$path, '?')) {
					$path	= substr($path, 0, strpos($path, '?'));
				}
				$dirs	= explode('/', CROOT);
				$paths	= explode('/', $path);
				
				if ( empty($paths[count($paths)-1])) {
					array_pop($paths);
					array_pop($dirs);
				}
				$paths	= array_reverse($paths);
				
				$html	= '<a href="'. CROOT .'" >'. $GT8['title'] .'</a><span>&nbsp;</span>';
				for ( $i=0, $len=count($dirs); $i<$len-1; $i++) {
					$html	.= '<a href="'. str_repeat('../', ($len-$i-1)) .'" >'. ($paths[$len-$i-1]) .'</a><span>&nbsp;</span>';
				}
				$html	.= ' <span>'. $paths[0] .'</span>';
			}
			
			return $html;
		}
		public function printDirLocation( $title, $base='', $location='') {
			
			if ( !$location) {
				$location	= GT8::getDirLocation($base);
			}
			
			$html	= GT8::getMatchPairs(
				file_get_contents(SROOT.'engine/views/location.inc'),
				array(
					'title'	=> $title,
					'location'	=> $location
				)
			);
			print(utf8_decode($html));
		}
		public function printTitle() {
			
		}
		public function getServerJSVars() {
			global $GT8;
			$this->jsVars[]	= array('padmin', (( isset($_SESSION['login']) && isset($_SESSION['login']['level']) && $_SESSION['login']['level'] > 3)? CROOT . $GT8['admin']['root']: CROOT));
			$this->jsVars[]	= array('CROOT', CROOT);
			
			if ( isset($_GET['tabIndex'])) {
				$this->jsVars[]	= array('tabIndex',	(integer)$_GET['tabIndex'], true);
			}
			
			$js	= 'var ASP	= {';
			for ( $i=0; $i<count($this->jsVars); $i++) {
				$crr	= $this->jsVars[$i];
				
				if ( isset($crr[2]) && $crr[2]) {
					$js	.= PHP_EOL .'				'. $crr[0] .': '. $crr[1] .',';
				} else {
					$js	.= PHP_EOL .'				'. $crr[0] .': "'. addslashes($crr[1]) .'",';
				}
			}
			$js	.= '
				meow: null
			};';
			return $js;
		}
		public function printHead( $title, $jsInclude=array(), $cssInclude=array(), $cssContent= '', $Index=null) {
			global $GT8;
			
			if ( $jsInclude && gettype($jsInclude)!='array') {
				print('<pre class="gt8-debug-error" >Have you updated the GT8::printHead() with the new arguments?<br /><br />'.
					
					$jsInclude
					
				.'<img src="'.CROOT.'imgs/gt8/delete-small.png" width="22" height="22" ></pre>');
			}
			
			$scripts	= '';
			for($i=0; $i<count($jsInclude); $i++) {
				if ( $i>0) {
					$scripts	.= '		';
				}
				$scripts	.= '<script type="text/javascript" src="'. $jsInclude[$i] .'" ></script>';
				if ( $i<count($jsInclude)-1) {
					$scripts	.= PHP_EOL;
				}
			}
			$css	= '';
			for($i=0; $i<count($cssInclude); $i++) {
				if ( $i>0) {
					$css	.= '		';
				}
				$css	.= '<link rel="stylesheet" type="text/css" href="'. $cssInclude[$i] .'" />';
				if ( $i<count($cssInclude)-1) {
					$css	.= PHP_EOL;
				}
			}
			GT8::printView(
				SROOT.'engine/views/'. (GT8::isAdmin()?'admin/': '') .'head.inc',
				array(
					'title'	=> $title,
					'CSSINCLUDE'	=> $cssContent,
					'scripts'	=> $scripts,
					'css'		=> $css
				),
				null, $Index
			);
		}
		public function getHeader( $data=array()) {
			$data['h1']	= isset($data['h1'])? $data['h1']: '';
			
			return GT8::printView(
				SROOT.'engine/views/'. (GT8::isAdmin()?'admin/': '') .'header.inc',
				$data,
				null,
				null,
				false
			);
		}
		public function printHeader( $data=array()) {
			print( GT8::getHeader( $data));
		}
		public function getFooter($data=array()) {
			global $GT8;
			$data['CROOT']	= CROOT;
			$data['AROOT']	= CROOT.(isset($GT8['admin'])? $GT8['admin']['root']: 'admin');
			$data['html']	= $data['html']? $data['html']: '';
			ob_start();
			include(SROOT.'engine/views/'. (GT8::isAdmin()?'admin/': '') .'footer.inc');
			$contents	= ob_get_contents();
			ob_end_clean();
			
			$html	= GT8::getMatchPairs(
				$contents,
				$data
			);
			return $html;
		}
		public function printFooter( $data=array()) {
			print( GT8::getFooter( $data));
		}
		private function getStringBlock( $raw) {
			
			$text	= '';
			$delimiter	= substr($raw, 0, 1);
			$ignore	= false;
			for ( $i=1, $len=strlen($raw); $i<$len; $i++) {
				$crr	= substr($raw, $i, 1);
				$chr	= ord($raw);
				
				if ( $crr == '\\' && $ignore===false) {
					$ignore	= true;
					$text	.= $crr;
				} else if ( $crr === $delimiter && $ignore===false) {
					break;
				} else {
					$text	.= $crr;
					
					$ignore	= false;
				}
			}
			return $text;
		}
		public function printView( $file, $data=array(), $Editor=null, $Index=null, $print=true, $content='') {
			global $GT8;
			
			$data['CROOT']	= CROOT;
			$data['AROOT']	= CROOT.(isset($GT8['admin'])? $GT8['admin']['root']: 'admin');
			$data['modal-class']	= isset($_GET['modal']) && ($_GET['modal']==1 || $_GET['modal']=='true')? 'modal-window': '';
			
			$OBJ	= null;
			if ( isset($this) ) {
				$OBJ	= $this;
			} else if ( isset($Editor) ) {
				$OBJ	= $Editor;
			} else if ( isset($Index) ) {
				$OBJ	= $Index;
			}
			
			if ( method_exists($OBJ, 'getServerJSVars')) {
				$data['JS_SERVER_VARS']	= $OBJ->getServerJSVars();
			}
			
			if ( $file) {
				ob_start();
				include($file);
				$contents	= ob_get_contents();
				ob_end_clean();
			} else {
				$contents	= $content;
			}
			
			if ( strpos('#'.$contents, '{{$this->') > 0) {//Properties and methods
				preg_match_all('/\{\{\$this\-\>([a-zA-Z0-9\_\[\]\'\(.*?\'\"\,\ \)\-]+)}\}/', $contents, $result);
				
				if ( $result && isset($result[1])) {
					for ( $i=0; $i<count($result[1]); $i++) {
						$value	= $result[1][$i];
						$var	= get_object_vars($OBJ);
						$count=0;//@ para evitar loop infinito. Depois da homologação, remova-a!@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
						while ( preg_match('/(\[\')?([a-zA-Z_0-9]+)(\]\')?(\(.*\))?/', $value, $reg)) {
							if ( $reg[4]) {//method
								//first time. So, $var is an array and must be changed to object
								$var	= $OBJ;
								
								//params
								$params	= explode(',', substr($reg[4], 1, -1));
								$var	= call_user_func_array( array($OBJ, $reg[2]), $params);
								break;
							} else {//property
								$var	= $var[$reg[2]];
								$value	= str_replace($reg[0].$reg[3], '', $value);
							}
							
							$count++;
							if ( $count>10) {
								print('<pre class="gt8-debug-error" >Atenção!<br /><br/>Loop infinito? no GT8::printView$property<img src="'.CROOT.'imgs/gt8/delete-small.png" width="22" height="22" ></pre>');
								die();
							}
						}
						$contents	= str_replace($result[0][$i], $var, $contents);
					}
				}
			}
			if ( strpos('#'.$contents, '{{METHOD:') > 0) {//METHOD
				preg_match_all('#\{\{METHOD\:(.+?)\}\}(.*?)\{\{\/METHOD\}\}#s', $contents, $result);
				if ( $result && isset($result[1])) {
					for ( $i=0; $i<count($result[1]); $i++) {
						$start		= strpos($contents, '{{METHOD:'.$result[1][$i].'}}')+strlen('{{METHOD:'.$result[1][$i].'}}');
						$end		= strpos($contents, '{{/METHOD}}');
						
						$contents	=
							substr( $contents, 0, $start-strlen('{{METHOD:'.$result[1][$i].'}}')) .
							call_user_func_array( array($OBJ, $result[1][$i]), array($result[2][$i])) .
							substr($contents, $end+strlen('{{/METHOD}}'))
						;
					}
					if ( isset($OBJ->data) && $data !== $OBJ->data) {
						$data	= array_merge($data, $OBJ->data);
					}
				}
			}
			if ( strpos('#'.$contents, '{{FOREACH:') > 0) {//LOOP
				//format: {{FOREACH:array's name|optional required fields}}. Sample: {{FOREACH:attributes|value}}...{{/FOREACH}}
				preg_match_all('/\{\{FOREACH\:([a-zA-Z\.\-0-9\_\/]+)(\|)?([a-zA-Z0-9\-\_\ ]+)?\}\}/', $contents, $result);
				if ( $result && isset($result[1])) {
					for ( $i=0; $i<count($result[1]); $i++) {
						$reqFields	= !empty($result[3][$i])? explode(',', $result[3][$i]): array();
						$start		= strpos($contents, '{{FOREACH:'.$result[1][$i].$result[2][$i].$result[3][$i].'}}')+strlen('{{FOREACH:'.$result[1][$i].$result[2][$i].$result[3][$i].'}}');
						$end		= strpos($contents, '{{/FOREACH}}');
						$templateStart	= substr($contents, $start, $end-$start);
						$templateEnd	= '';
						$templateIn		= '';
						$columnRepeat	= '';
						
						//este attributo emula um segundo loop mas com um array unidimensional
						if ( strpos('#'. $templateStart, '{{foreach-column:')) {
							$templateStart	= substr($templateStart, strpos($templateStart, '{{foreach-column:'));
							$columnRepeat	= substr($templateStart, strlen('{{foreach-column:'), strpos($templateStart ,'}}')-strlen('{{foreach-column:'));
							$templateIn		= substr($templateStart, strpos($templateStart, '}}')+2);
							$templateIn		= substr($templateIn, 0, strpos($templateIn, '{{/foreach-column}}'));
							$templateEnd	= substr($templateStart, strpos($templateStart, '{{/foreach-column}}')+strlen('{{/foreach-column}}'));
							$templateStart	= substr($contents, $start, $end-$start);
							$templateStart	= substr($templateStart, 0, strpos($templateStart, '{{foreach-column:'));
							
							$crrContent	= '';
							$lastColumn	= '';
							for ( $j=0; $j<count($OBJ->data[$result[1][$i]]); $j++) {
								$crr	= $OBJ->data[$result[1][$i]][$j];
								
								if ( $lastColumn != $crr[$columnRepeat]) {
									if ( $j>0) {
										$crrContent	.= GT8::getMatchPairs($templateEnd.PHP_EOL.PHP_EOL, $crr);
									}
									$crrContent	.= GT8::getMatchPairs($templateStart, $crr);
								}
								$crrContent	.= GT8::getMatchPairs($templateIn, $crr);
								
								$lastColumn	= $crr[$columnRepeat];
							}
							if ( $j>0) {
								$crrContent	.= GT8::getMatchPairs($templateEnd.PHP_EOL.PHP_EOL, $crr);
							}
							$contents	= substr( $contents, 0, $start-strlen('{{FOREACH:'.$result[1][$i].$result[2][$i].$result[3][$i].'}}')) . $crrContent . substr($contents, $end+strlen('{{/FOREACH}}'));
						} else {
							$crrContent	= '';
							for ( $j=0; $j<count($OBJ->data[$result[1][$i]]); $j++) {
								$crr	= $OBJ->data[$result[1][$i]][$j];
								if ( count($reqFields) ) {
									$allFieldsFound	= true;
									foreach( $reqFields AS $row=>$fieldName) {
										if ( !isset($crr[$fieldName]) || empty($crr[$fieldName])) {
											$allFieldsFound	= false;
											break;
										}
									}
									if ( $allFieldsFound) {
										$crrContent	.= GT8::getMatchPairs($templateStart, $crr);
									}
								} else {
									$crrContent	.= GT8::getMatchPairs($templateStart, $crr);
								}
							}
							$contents	= substr( $contents, 0, $start-strlen('{{FOREACH:'.$result[1][$i].$result[2][$i].$result[3][$i].'}}')) . $crrContent . substr($contents, $end+strlen('{{/FOREACH}}'));
						}
					}
				}
			}
			if ( strpos('#'.$contents, '{{tag:') > 0) {//tag-helper
				preg_match_all('/\{\{tag:([\|a-zA-Z0-9\.\-_]+)\:(.*)\}\}/', $contents, $result);
				if ( $result && isset($result[2])) {
					for ( $i=0; $i<count($result[2]); $i++) {
						$opts	= explode('|', $result[1][$i]);
						$tag	= $result[2][$i];
						if ( in_array('CROOT', $opts)) {
							$tag	= CROOT . $tag;
						}
						if ( in_array('AROOT', $opts)) {
							$tag	= $data['AROOT'] . $tag;
						}
						//tags
						if ( in_array('script', $opts)) {
							if ( in_array('inline', $opts)) {
								$tag	= '<script type="text/javascript" >//<![CDATA['. PHP_EOL . $tag . PHP_EOL .'//]]></script>';
							} else {
								$tag	= '<script type="text/javascript" src="'. $tag .'" ></script>';
							}
						} else if ( in_array('css', $opts)) {
							$tag	= '<link rel="stylesheet" type="text/css" href="'. $tag .'" />';
						} else {
							$tag	= ('<pre class="gt8-debug-error" >Invalid tag:<br />'.
								
								print_r($results[0][$i], 1)
								
							.'<img src="'.CROOT.'imgs/gt8/delete-small.png" width="22" height="22" ></pre>');
						}
						$contents	= str_replace($result[0][$i], $tag, $contents);
					}
				}
			}
			$contents	= GT8::getMatchPairs(
				$contents,
				$data
			);
			$proccessDataAgain	= false;
			if ( strpos('#'.$contents, '{{VIEW:') > 0) {
				preg_match_all('/\{\{VIEW:([a-zA-Z\.\-0-9\_\/]+)\}\}/', $contents, $result);
				if ( $result && isset($result[1])) {
					$result[1]	= str_replace('.', '/', $result[1]);
					
					for ( $i=0; $i<count($result[1]); $i++) {
						if ( $result[1][$i] == 'location') {
							$data['directories']	= isset($data['directories']) && $data['directories']? $data['directories']: GT8::getDirLocation();
						}
						
						ob_start();
						include( SROOT.'engine/views/'. $result[1][$i] .'.inc');
						$contents	= str_replace('{{VIEW:'. str_replace('/', '.', $result[1][$i]) .'}}', ob_get_contents(), $contents);
						ob_end_clean();
						$proccessDataAgain	= true;
					}
				}
			}
			if ( strpos('#'.$contents, '{{IF:') > 0) {//IF
				preg_match_all('#\{\{IF\:(.+?)\}\}(.+?)\{\{\/IF\}\}#s', $contents, $result);
				if ( $result && isset($result[1])) {
					for ( $i=0; $i<count($result[1]); $i++) {
						$start		= strpos($contents, '{{IF:'.$result[1][$i].'}}')+strlen('{{IF:'.$result[1][$i].'}}');
						$end		= strpos($contents, '{{/IF}}');
						$teste		= false;
						eval('$teste='. $result[1][$i].';');
						$contents	=
							substr( $contents, 0, $start-strlen('{{IF:'.$result[1][$i].'}}')) .
							($teste? $result[2][$i]: '') .
							substr($contents, $end+strlen('{{/IF}}'))
						;
					}
				}
			}
			if ( strpos('#'.$contents, '{{COMBO-OPTIONS:') > 0) {//COMBO-OPTIONS
				preg_match_all('#\{\{COMBO\-OPTIONS\:([A-Za-z0-9\-\_\.\ ]+)(\|)?([a-zA-Z0-9_]+)?(\|)?([a-zA-Z0-9_]+)?(\|)?([a-zA-Z0-9_]+)?(\|)?([a-zA-Z0-9_\,]+)?\}\}#s', $contents, $result);//options: array|name|value|selectedColumn
				if ( $result && isset($result[1])) {
					for ( $i=0; $i<count($result[1]); $i++) {
						$name		= $result[3][$i] !== null? $result[3][$i]: 0;
						$value		= $result[5][$i] !== null? $result[5][$i]: $name;
						$selected	= $result[7][$i] !== null? $result[7][$i]: null;
						$options	= $result[9][$i] !== null? explode(',',$result[9][$i]): array();
						$crr	= '';
						foreach( $OBJ->data[$result[1][$i]] AS $row=>$arr) {
							$val	= $arr[$value];
							$nam	= $arr[$name];
							if ( in_array('utf8', $options)) {
								$val	= utf8_encode($val);
								$nam	= utf8_encode($nam);
							}
							$crr	.= '<option value="'. $nam .'" '. ($selected!==null&&$arr[$selected]?'selected="selected"':'') .'>'. $val .'</option>';
						}
						$contents	= str_replace('{{COMBO-OPTIONS:'.$result[1][$i].$result[2][$i].$result[3][$i].$result[4][$i].$result[5][$i].$result[6][$i].$result[7][$i].$result[8][$i].$result[9][$i].'}}', $crr, $contents);
					}
				}
			}
			if ( strpos('#'.$contents, '{{GET:') > 0) {//allow: default
				preg_match_all('/\{\{GET:([a-zA-Z\.\-0-9\_\/]+)\}\}/', $contents, $result);
				if ( $result && isset($result[1])) {
					for ( $i=0; $i<count($result[1]); $i++) {
						$name		= $result[1][$i];
						$default	= '';
						if ( strpos('#'.$name, '|')>0) {
							$default	= substr($name, strpos($name, '|')+1);
							$name	= substr($name, 0, strpos($name, '|'));
						}
						$contents	= str_replace('{{GET:'.$result[1][$i].'}}', (isset($_GET[$name])? $_GET[$name]: $default), $contents);
					}
				}
			}
			if ( strpos('#'.$contents, '{{COOKIE:') > 0) {//allow: default
				preg_match_all('/\{\{COOKIE:([a-zA-Z\.\-0-9\_\/\|]+)\}\}/', $contents, $result);
				if ( $result && isset($result[1])) {
					for ( $i=0; $i<count($result[1]); $i++) {
						$name		= $result[1][$i];
						$default	= '';
						if ( strpos('#'.$name, '|')>0) {
							$default	= explode('|', $name);
							$name		= $default[0];
							$default	= $default[1];
						}
						$contents	= str_replace('{{COOKIE:'.$result[1][$i].'}}', (isset($_COOKIE[$name])? $_COOKIE[$name]: $default), $contents);
					}
				}
			}
			if ( strpos('#'.$contents, '{{GT8:') > 0) {
				preg_match_all('/\{\{GT8:([a-zA-Z\.\-0-9\_\/\|]+)\}\}/', $contents, $result);
				if ( $result && isset($result[1])) {
					for ( $i=0; $i<count($result[1]); $i++) {
						$default= '';
						$value	= $result[1][$i];
						if ( strpos('#'.$value, '|')>0) {
							$default	= explode('|', $value);
							$value		= $default[0];
							$default	= $default[1];
						}
						if ( isset($GT8[$value]) && $GT8[$value]) {
							$value	= isset($GT8[$value])? $GT8[$value]: '';
						} else {
							if ( strpos('#'. $value, '.')) {
								$value	= explode('.', $value);
								$attr	= $GT8;
								for( $j=0,$jlen=count($value); $j<$jlen; $j++) {
									$crr	= $value[$j];
									if ( isset($attr[$crr]) && $attr[$crr]!==null) {
										$attr	= $attr[$crr];
									} else {
										break;
									}
								}
								$value	= $attr;
							} else {
								$value	= $default;
							}
						}
						$contents	= str_replace('{{GT8:'.$result[1][$i].'}}', ($value? $value: $default), $contents);
					}
				}
			}
			if ( strpos('#'.$contents, '{{SESSION:') > 0) {
				preg_match_all('/\{\{SESSION:(.+?)\}\}/', $contents, $result);
				if ( $result && isset($result[1])) {
					for ( $i=0; $i<count($result[1]); $i++) {
						$default= '';
						$value	= $result[1][$i];
						
						if ( strpos('#'.$value, '|')>0) {
							$default	= explode('|', $value);
							$value		= $default[0];
							$default	= $default[1];
						}
						if ( isset($_SESSION[$value]) && $_SESSION[$value]) {
							$value	= isset($_SESSION[$value])? $_SESSION[$value]: '';
						} else {
							if ( strpos('#'. $value, '.')) {
								$value	= explode('.', $value);
								$attr	= $_SESSION;
								for( $j=0,$jlen=count($value); $j<$jlen; $j++) {
									$crr	= $value[$j];
									
									if ( isset($attr[$crr]) && $attr[$crr]!==null) {
										$attr	= $attr[$crr];
									} else {
										break;
									}
								}
								$value	= $attr;
							} else {
								$value	= $default;
							}
						}
						if ( $value && gettype($value) == 'array') {
							$value	= $default;
						}
						$value		= ($value? $value: $default);
						$value		= $proccessDataAgain? utf8_encode($value): $value;
						$contents	= str_replace('{{SESSION:'.$result[1][$i].'}}', $value, $contents);//aparentemente, não é necessário encodificar aqui. Quando o fiz, no header deu dupla encodificação. Se der problemas, experimente condicionar a encodificação com a variável: $proccessDataAgain
					}
				}
			}
			if ( strpos('#'.$contents, '$thisd') > 0) {//var $this
				$contents	= preg_replace('/(\{\{.*?)(\$this\b)(.*?\}\})/', '$1\$Index$3', $contents);
			}
			if ( strpos('#'.$contents, '{{$g') > 0) {//EVIL VARIABLES
				preg_match_all('/\{\{\$([a-zA-Z\.\-0-9\/\[\]\'\-\>\(\)\"\$_]+)\\|?([a-zA-Z0-9\.\,\-_]+)?}\}/', $contents, $result);
				global $ev;
				if ( $result && isset($result[1])) {
					for ( $i=0; $i<count($result[1]); $i++) {
						$raw	= '$'. $result[1][$i];
						eval('$ev	= '. $raw .';');
						$contents	= str_replace($result[0][$i], $ev, $contents);
					}
				}
			}
			if ( 1) {//function call
				preg_match_all('/\{\{([a-zA-Z0-9_]+)\((.*)\)\}\}/', $contents, $result);
				if ( $result && isset($result[1])) {
					for ( $i=0; $i<count($result[1]); $i++) {
						$name	= $result[1][$i];
						$params	= $result[2][$i];
						$default	= '';
						
						$args	= array();
						for ( $j=0, $jlen=strlen($params); $j<$jlen; $j++) {
							$crr	= substr($params, $j, 1);
							$chr	= ord($crr);
							
							if ( $crr == ' ') {
								
							} else if ( $chr>47 && $chr<58 || $crr=='-' || $chr=='+' || $chr=='.' ) {
								$crr	= RegExp(substr($params, $j), '[\-\+\.0-9]+');
								$j		+= strlen($crr);
								if ( strpos('#'.$crr, '.') > 0) {
									$crr	= (float)$crr;
								} else {
									$crr	= (integer)$crr;
								}
								$args[]	= $crr;
							} else if ( $crr=='"' || $crr=="'" ) {
								$crr	= GT8::getStringBlock(substr($params, $j));
								$j		+= strlen($crr) + 1;
								$args[]	= $crr;
							} else {
								
							}
						}
						$contents	= str_replace($result[0][$i], call_user_func_array( $name, $args), $contents);
					}
				}
			}
			
			if ( $proccessDataAgain ) {
				$contents	= GT8::getMatchPairs(
					$contents,
					$data
				);
			}
			
			if ( !GT8::$compilationInitialized) {
				GT8::$compilationInitialized	= true;
				if ( isset($this) ) {
					$contents	= $this->printView(null, $data, $Editor, $Index, $print, $contents);
				} else {
					$contents	= GT8::printView(null, $data, $Editor, $Index, $print, $contents);
				}
				GT8::$compilationInitialized	= false;
				if ( $print) {
					print($contents);
				}
			}
			return $contents;
		}
		public function includeView( $file, $data) {
			global $GT8;
			include_once( SROOT."engine/views/$file.inc");
		}
		public function on404() {
			$this->getUrlHistory();
		}
		public function getUrlHistory( $url='', $redirect=true, $url200='', $url404='') {
			global $GT8;
			
			if ( empty($url)) {
				$url	= $_SERVER['REQUEST_URI'];
				
				if ( strpos('#'.$url, '?') > 0) {
					$url	= substr( $url, 0, strpos($url,'?'));
				}
			}
			
			if ( substr($url, 0, 1) !== '/') {
				$url	= '/'. $url;
			}
			if ( substr($url, -1) === '/') {
				$url	= substr($url, 0, -1);
			}
			
			require_once( SROOT .'engine/functions/Pager.php');
			$Pager	= Pager(array(
				'sql'	=> 'urlHistory.list',
				'required'	=> array(
					array('old', $url)
				)
			));
			if ( isset($Pager['rows'][0])) {
				$qsas	= explode('&', $_SERVER['QUERY_STRING']);
				$qsa	= '?';
				for( $i=0; $i<count($qsas); $i++) {
					if ( (strpos($qsas[$i], 'rewrite=')===false) && (strpos($qsas[$i], 'path=')===false)) {
						$qsa	.= ($qsa=='?'? '': '&'). $qsas[$i];
					}
				}
				$qsa	= strlen($qsa)>1? $qsa: '';
				require_once(SROOT.'engine/queries/urlHistory/updateStats.php');
				updateStats($Pager['rows'][0]['id']);
				if ( $redirect) {
					header('location: '. ($url200!=''? $url200: $Pager['rows'][0]['new']) . $qsa, 301);
					die();
				} else {
					$Pager['rows'][0]['qsa']	= $qsa;
					return $Pager['rows'][0];
				}
			}
			
			//not found
			if ( $redirect) {
				global $Index, $Editor;
				if ( isset($Index)) {
					
				} else if ( isset($Editor)) {
					$Index	= $Editor;
				} else if ( isset($this)) {
					$Index	= $this;
				} else if ( class_exists('Index')) {
					$Index	= new Index();
				} else if ( class_exists('Editor')) {
					$Index	= new Editor();
				}
				$Data	= array('path'=>RegExp($_GET['path'], '[a-z-A-Z0-9_\-\+\.\:\/\\\|\%\&\?]+'));
				if ( $Index) {
					$Data	= array_merge($GT8, $Data);
					$Index->printView(
						($url404!=''? $url404: SROOT .'engine/views/404/index.inc'),
						$Data,
						$Index
					);
				} else {
					GT8::printView( ($url404!=''? $url404: SROOT .'engine/views/404/index.inc'), $Data);
				}
				die();
			}
			return false;
		}
		public function isAdmin() {
			global $GT8;
			$b	= false;
			
			if ( strpos('#'.$_SERVER['REQUEST_URI'], '/'.$GT8['admin']['root']) ) {
				$b	= true;
			} else {
				$b	= false;
			}
			return $b;
		}
		public function canonizeUrl() {
			global $spath;
			$total	= count($_GET);
			
			if ( isset($_GET['modal'])) {
				return null;
			}
			if ( isset($_GET['format'])) {
				if ( $_GET['format'] == 'JSON') {
					die();
				}
				return null;
			}
			if ( isset($_GET['mobile'])) {
				return null;
			}
			if ( $total > 2 ) {
				header('Location: ./');
				die();
			}
		}
		protected function redirect( $url) {
			require_once( SROOT.'engine/functions/includeif.php');
			
			if ( $url == 'forbidden') {
				includeif( SROOT .'engine/controllers/account/Forbidden.php');
				GT8::printView(
					SROOT .'engine/views/account/forbidden.inc',
					array(
						'path'	=> RegExp($_GET['path'], '[a-z-A-Z0-9_\-\+\.\:\/\\\|\%\&\?]+'),
						'CROOT'	=> CROOT
					)
				);
			} else if ( $url == 'not found' || $url == 404) {
				includeif( SROOT .'engine/controllers/404/Index.php');
				GT8::printView(
					SROOT .'engine/views/404/index.inc',
					array(
						'path'	=> RegExp($_GET['path'], '[a-z-A-Z0-9_\-\+\.\:\/\\\|\%\&\?]+'),
						'CROOT'	=> CROOT
					)
				);
			}
			die();
		}
		public function get($field, $addSlashes=false) {
			if ( isset($this->row) && isset($this->row[$field])) {
				switch( $field) {
					default: {
						$content	= $addSlashes? addslashes($this->row[$field]): $this->row[$field];
						return utf8_encode($content);
						break;
					}
				}
			}
			return '';
		}
		public function prnt($field, $addSlashes=false) {
			print($this->get($field, $addSlashes));
		}
		public function getParam( $name, $category='', $idUser=-1, $limit=1) {
			$idUser	= (integer)$idUser;
			if ( $idUser==-1 && isset($_SESSION['login']['id']) && $_SESSION['login']['id']) {
				$idUser	= $_SESSION['login']['id'];
			}
			$name		= mysql_real_escape_string($name);
			$category	= mysql_real_escape_string($category);
			
			$result		= mysql_query("
				SELECT
					id, id_users, name, value, category
				FROM
					gt8_param
				WHERE
					id_users = $idUser AND
					name = '$name' AND
					category = '$category'
			");
			$return	= $limit==1? '': array();
			if ( $result) {
				if ( $limit==1) {
					$row	= mysql_fetch_assoc($result);
					$return = $row['value'];
				} else {
					while( ($row=mysql_fetch_assoc($result))) {
						$return[]	= $row;
					}
				}
			}
			return $return;
		}
		public function saveParam( $name, $value, $category='', $idUser=-1, $allowDuplicates=false, $format='JSON') {
			$idUser	= (integer)$idUser;
			if ( $idUser==-1 && isset($_SESSION['login']['id']) && $_SESSION['login']['id']) {
				$idUser	= $_SESSION['login']['id'];
			}
			$name		= mysql_real_escape_string($name);
			$value		= mysql_real_escape_string($value);
			$category	= mysql_real_escape_string($category);
			
			if ( $allowDuplicates) {
				mysql_query("
					INSERT INTO
						gt8_param( name, value, category, id_users)
					VALUES(
						'$name', '$value', '$category', $idUser 
					)
				");
				if ($format=='JSON') {
					print('//#message: parâmetro salvo com sucesso!'. PHP_EOL);
				}
			} else {
				
				mysql_query("
					INSERT INTO
						gt8_param( name, value, category, id_users)
					SELECT
						'$name', '$value', '$category', $idUser 
					FROM
						gt8_param
					WHERE
						id_users = $idUser AND
						name = '$name' AND
						category = '$category'
					HAVING
						COUNT(*) = 0
				");
				mysql_query("
					UPDATE
						gt8_param
					SET
						value	= '$value'
					WHERE
						id_users = $idUser AND
						name = '$name' AND
						category = '$category'
					LIMIT
						1
				");
				if ($format=='JSON') {
					print('//#message: parâmetro salvo com sucesso!'. PHP_EOL);
				}
			}
		}
		public function deleteParam( $name, $value, $category='', $idUser=-1, $format='JSON') {
			$idUser	= (integer)$idUser;
			if ( $idUser==-1 && isset($_SESSION['login']['id']) && $_SESSION['login']['id']) {
				$idUser	= $_SESSION['login']['id'];
			}
			$name		= mysql_real_escape_string($name);
			$value		= mysql_real_escape_string($value);
			$category	= mysql_real_escape_string($category);
			
			mysql_query("
				DELETE FROM
					gt8_param
				WHERE
					id_users = $idUser AND
					name = '$name' AND
					category = '$category'
			");
			if ($format=='JSON') {
				print('//#affected rows: '. mysql_affected_rows() . PHP_EOL);
			}
		}
		static function leaveSSL() {
			if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ) {
				$redirect = "http://". $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				header("Location: $redirect");
				die();
			}
		}
		static function enterSSL() {
			if ($_SERVER['DOCUMENT_ROOT'] != '/home/robson/sites/r2-cms.com/www/trunk' && $_SERVER['DOCUMENT_ROOT'] != '/Users/Roger/Sites' && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "") ) {
				$redirect = "https://". $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				header("Location: $redirect");
				die();
			}
		}
		protected function checkReadPrivileges( $url='', $field='*', $format='OBJECT') {
			$this->checkPrivileges( $url, $field, $format, 1);
		}
		protected function checkWritePrivileges($url='', $field='*', $format='OBJECT') {
			$this->checkPrivileges( $url, $field, $format, 2);
		}
		private function checkPrivileges( $url=null, $field='*', $format='OBJECT', $min=2) {
			require_once( SROOT ."engine/functions/CheckPrivileges.php");
			$format	= $format? $format: 'OBJECT';
			if ( !$url) {
				global $paths;
				$url	= $paths;
				array_shift($url);
				if ( empty($url[count($url)-1]) ) {
					array_pop($url);
				}
				$url	= join('/',$url) .'/';
			}
			
			$prv	= CheckPrivileges($field, $format, $url, $min);
			//se o privilégio para o campo específico não foi encontrado, procure-o genericamente
			if ( $prv == 404 && $field!='*') {
				$prv	= CheckPrivileges('*', $format, $url, $min);
			}
			
			if ( $format == 'OBJECT' && $prv < $min) {
				$this->redirect('forbidden');
			}
		}
	}
?>