<?php
	if ( !defined('SROOT')) {
		die('Undefined connection! (Util->Pager::GT8Editor)');
	}
	require_once( SROOT .'engine/functions/Pager.php');
	require_once( SROOT .'engine/classes/GT8.php');
	class Editor extends GT8 {
		public $name	= 'unnamed';
		public $id		= 0;
		public $idDir;
		public $Pager;
		public $data	= array();
		private $jsEvents	= array();
		protected $fields;
		public $isAdmin	= false;
		public $table	= '';
		public $root	= '';
		public $isModal	= null;
		public $approved	= false;
		public $htmlManagerModal	= '';
		
		public function Editor() {
			global $GT8;
			$this->id		= $this->id? $this->id: (integer)$_GET['id'];
			
			$this->isModal	= isset($_GET['modal']) && ($_GET['modal']=='1' || $_GET['modal']=='true');
			
			if ( strpos('#'. $_SERVER['REQUEST_URI'], '/'.$GT8['admin']['root']) ) {
				$this->isAdmin	= true;
			} else {
				$this->isAdmin	= false;
			}
			
			if ( $_GET['action'] == 'update') {
				$this->update( $_GET['field'], $_GET['value']);
				die();
			} else if ( $_GET['action'] == 'new') {
				$this->newFile();
			}
			if ( isset($this->Pager['rows'])) {
				$this->data	= $this->Pager['rows'][0];
			}
		}
		public function update( &$field='', &$value='') {
			$field	= RegExp($_GET['field'], '[a-zA-Z0-9_\.s]+');
			$value	= mysql_real_escape_string(isset($_GET['value'])? $_GET['value']: $_POST['value']);
			
			if ( strlen($field) != strlen($_GET['field']) ) {
				die('invalid');
			} else if ( !$this->id) {
				die('missing id');
			}
			return true;
		}
		public function newFile( &$field='', &$value='') {
			$field	= RegExp($_GET['field'], '[a-zA-Z0-9_\.s]+');
			$value	= mysql_real_escape_string(isset($_GET['value'])? $_GET['value']: $_POST['value']);
			
			if ( strlen($field) != strlen($_GET['field']) ) {
				die('//#error: invalid field!'. PHP_EOL);
			} else if ( !$this->id) {
				die('//#error: missing id!'. PHP_EOL);
			}
			return true;
		}
		public function printSrc() {
			
		}
		public function prntCheckbox( $field) {
			print('<input type="checkbox" name="'.$field.'" class="gt8-update" value="'. utf8_encode(htmlentities(addslashes($this->Pager['rows'][0][$field]))) .'" '. ($this->Pager['rows'][0][$field]? 'checked="checked"': '').' />');
		}
		public function prnt($field, $addSlashes=false) {
			$row	= $this->Pager['rows'][0];
			switch( $field) {
				default: {
					$content	= $addSlashes? addslashes($row[$field]): $row[$field];
					print( utf8_encode($content));
					break;
				}
			}
		}
		public function writeInputLabeled( $field, $pseudo, $class='', $minLength='', $maxLenght='', $options=array()) {
			$type	= '';
			$value	= $this->Pager['rows'][0][$field];
			if ( $minLength) {
				$minLength	= ' minlength:'. $minLength;
			}
			if ( $maxLenght) {
				$maxLenght	= ' maxlength="'. $maxLenght .'"';
			}
			if ( isset($options['onkeyup'])) {
				$this->jsEvents[]	= $options['onkeyup'];
			}
			if ( isset($options['type'])) {
				$type	= ' '. $options['type'];
				if ( $type==' currency' ) {
					$options['colWidth']	= isset($options['colWidth'])? $options['colWidth']: 6;
					$value	= number_format($value, 2, '.', ',');
				} else if ( $type==' float' ) {
					$options['colWidth']	= isset($options['colWidth'])? $options['colWidth']: 6;
					$value	= number_format($value, 0, '.', ',');
				} else if ( $type==' integer' ) {
					$options['colWidth']	= isset($options['colWidth'])? $options['colWidth']: 6;
				} else if ( $type==' date' ) {
					$options['colWidth']	= isset($options['colWidth'])? $options['colWidth']: 6;
				}
			}
			
			$colWidth	= isset($options['colWidth'])? $options['colWidth']: ($this->isModal?9:10);
			$readonly	= isset($options['readonly']) && $options['readonly']==true? ' readonly': '';
			$style			= isset($options['style'])? 'style="'. $options['style'] .'" ': '';
			$styleEM		= isset($options['styleEM'])? 'style="'. $options['styleEM'] .'" ': '';
			$styleSTRONG	= isset($options['styleSTRONG'])? 'style="'. $options['styleSTRONG'] .'" ': '';
			$styleINPUT	= isset($options['styleINPUT'])? 'style="'. $options['styleINPUT'] .'" ': '';
			$styleLABEL	= isset($options['styleLABEL'])? 'style="'. $options['styleLABEL'] .'" ': '';
			$html	= '
						<label class="line '. $class . $type . ($readonly) .'" title="'. $minLength .'" '.$styleLABEL.'>
							'. ( isset($options['noSTRONG'])? '': '<strong class="col-'. ($this->isModal? 4: 5) .'" '.$styleSTRONG.'>'. $pseudo .'</strong>') .'
							<span class="col-'.$colWidth.'" '.$style.'><input type="text" '. ($readonly?'readonly="readonly"':'') .' class="gt8-update input-rounded-shadowed'. $readonly .'" name="'. $field .'" value="'. utf8_encode(htmlentities(addslashes($value))) .'" '. $maxLength .' '.$styleINPUT.'/><small>'. $pseudo .'</small></span>
							'. (isset($options['noEM'])? '': '<em class="col-'. ($this->isModal? 1: 6) .'" '.$styleEM.'>&nbsp;</em>') .'
						</label>
			';
			print($html);
		}
		public function writeTextareaLabeled( $field, $pseudo, $class='', $minLength='', $maxLenght='', $options=array()) {
			if ( $minLength) {
				$minLength	= ' minlength:'. $minLength;
			}
			if ( $maxLenght) {
				$maxLenght	= ' maxlength:'. $maxLenght .'';
			}
			if ( isset($options['onkeyup'])) {
				$this->jsEvents[]	= $options['onkeyup'];
			}
			$colWidth		= isset($options['colWidth'])? $options['colWidth']: ($this->isModal? 13: 14);
			$style			= isset($options['style'])? 'style="'. $options['style'] .'" ': '';
			$styleEM		= isset($options['styleEM'])? 'style="'. $options['styleEM'] .'" ': '';
			$styleSTRONG	= isset($options['styleSTRONG'])? 'style="'. $options['styleSTRONG'] .'" ': '';
			$styleTA		= isset($options['styleTA'])? 'style="'. $options['styleTA'] .'" ': '';
			$styleLABEL		= isset($options['styleLABEL'])? 'style="'. $options['styleLABEL'] .'" ': '';
			$html	= '
						<label class="line '. $class .'" title="'. $minLength . $maxLenght .'" '.$styleLABEL.'>
							'. ( isset($options['noSTRONG'])||$this->isModal? '': '<strong class="col-5" '.$styleEM.'>'. $pseudo .'</strong>') .'
							<span class="col-'. $colWidth .'" '.$style.' ><textarea cols="1" rows="1" class="gt8-update input-rounded-shadowed" name="'. $field .'" '.$styleTA.'>'. utf8_encode(htmlentities(addslashes($this->Pager['rows'][0][$field]))) .'</textarea><small>'. $pseudo .'</small></span>
							'. (isset($options['noEM'])? '': '<em class="col-'.($this->isModal? 1: 6).'" '.$stylestrong.'>&nbsp;</em>') .'
						</label>
			';
			print($html);
		}
		public function writeSelectLabeled( $field, $pseudo, $class='', $rows, $options=array()) {
			
			$type		= '';
			
			if ( gettype($rows[0]) == 'integer' && gettype($rows[1]) == 'integer' ) {
				for ( $i = $rows[0], $l	= $rows[1], $rows=array(); $i<$l; $i++) {
					$rows[]	= array($i, $i);
				}
			}
			if ( isset($options['type'])) {
				$type	= ' '. $options['type'];
				if ( $type==' integer' ) {
					
				}
			}
			
			$found	= false;
			$htmlOptions	= array();
			$value	= '&nbsp;';
			$rowValue	= $this->Pager['rows'][0][$field];
			
			for ( $i=count($rows)-1; $i>-1; $i--) {
				$selected	= '';
				//print("<h1>$i - $rowValue - ". strtolower($rowValue) .' -  '. strtolower($rows[$i][0]) ."</h1>".PHP_EOL);
				if ( !$found && strtolower($rowValue) == strtolower($rows[$i][0])) {
					$selected	= 'selected="selected"';
					$found		= true;
					$value		= utf8_encode(htmlentities(utf8_decode(isset($rows[$i][1])? $rows[$i][1]: $rows[$i][0])));
				}
				if ( !$found && $i==0) {
					$selected	= 'selected="selected"';
					$value		= utf8_encode(htmlentities(utf8_decode(isset($rows[$i][1])? $rows[$i][1]: $rows[$i][0])));
				}
				$htmlOptions[]	= '<option value="'. $rows[$i][0] .'" '. $selected .' >'. (isset($rows[$i][1])? $rows[$i][1]: $rows[$i][0]) .'</option>';
			}
			//print("<pre>". print_r($htmlOptions, 1) ."</pre>". PHP_EOL);
			//die();
			//die();
			$colWidth	= isset($options['colWidth'])? $options['colWidth']: 6;
			$style			= isset($options['style'])? 'style="'. $options['style'] .'" ': '';
			$styleEM		= isset($options['styleEM'])? 'style="'. $options['styleEM'] .'" ': '';
			$styleSTRONG	= isset($options['styleSTRONG'])? 'style="'. $options['styleSTRONG'] .'" ': '';
			$styleINPUT	= isset($options['styleINPUT'])? 'style="'. $options['styleINPUT'] .'" ': '';
			$styleLABEL	= isset($options['styleLABEL'])? 'style="'. $options['styleLABEL'] .'" ': '';
			$htmlOptions	= join('', array_reverse($htmlOptions));
			$html	= '
						<label class="line '. $class . $type .'" title="'. $minLength . $maxLenght .'" >
							'. ( isset($options['noSTRONG'])? '': '<strong class="col-'. ($this->isModal? 4: 5) .'" '.$styleEM.'>'. $pseudo .'</strong>') .'
							<span class="col-'. $colWidth .' e-select" '.$style.'>
								<select class="gt8-update" name="'. $field .'" >'. $htmlOptions .'</select>
								<span class="button group-button" >
									<strong>'. $value .'</strong>
									<img src="'. CROOT .'imgs/gt8/arrow-down-mini.png" alt="" />
								</span>
							</span>
							'. (isset($options['noEM'])? '': '<em class="col-'. ($this->isModal? 1: 6) .'" '.$styleEM.'>&nbsp;</em>') .'
						</label>
			';
			print($html);
		}
		protected function getSPath( $base='') {
			global $GT8;
			
			if ( !$base	&& isset($GT8[$this->name]['root']) ) {
				$base	= $GT8[$this->name]['root'];
			}
			
			$dirs	= explode($base, $_GET['path']);
			if ( !$dirs[1]) {
				return array();
			}
			$dirs	= explode('/', $dirs[1]);
			
			if ( !$dirs[ count($dirs)-1]) {
				array_pop($dirs);
			}
			
			return $dirs;
		}
		public function getServerJSVars() {
			global $GT8;
			
			$this->jsVars[]	= array('id', $this->id, true);
			$this->jsVars[]	= array('cardListerName', $this->name);
			$this->jsVars[]	= array('padmin', ($this->isAdmin? CROOT . $GT8['admin']['root']: CROOT));
			$this->jsVars[]	= array('CROOT', CROOT);
			$this->jsVars[]	= array('keywords',	$this->keywords);
			$this->jsVars[]	= array('isModal',	$this->isModal? 1:0, true);
			
			if ( isset($this->Pager['rows'][0]['locked'])) {
				$this->jsVars[]	= array('locked', $this->Pager['rows'][0]['locked'], true);
			}
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
		public function printBodyClass() {
			$class	= 'Editor';
			if ( $this->isModal) {
				$class	.= ' modal-window';
			}
			print('class="'. $class .'" ');
		}
		public function printModalClass() {
			if ( $this->isModal) {
				print('modal-window');
			} else {
				
			}
		}
		public function printManagerTab() {
			if ( $this->isModal) {
				print('<div class="tab" ><div>Admin</div></div>');
			}
		}
		public function printManagerCard() {
			if ( $this->isModal) {
				print('<div class="card manager line-height" ></div>');
			}
		}
		public function printModalButtons( $options=array()) {
			if ( !$this->isModal) {
				return;
			}
			$label	= isset($options['label'])? $options['label']: 'Concluir';
			if ( isset($options['html'])) {
				$html	= $options['html'];
			} else {
				$html	= '
					<div id="eModalBtC" >
						<a href="#" onclick="return Modal.hide()" class="href-button href-button-ok" ><span>'. $label .'</span></a>
					</div>
				';
			}
			print($html);
		}
		public function printManagerModal($options=array()) {
			/*
				options:
					html
					img-preview
					publish[ field1, field2, label1, label2]
			*/
			global $GT8;
			
			////////////////////////////PUBLISH/////////////////////////////////
			$publish	= array(
				'publish_up',
				'publish_down',
				'Publicação (início)',
				'Publicação (fim)'
			);
			if ( isset($options['publish'])) {
				$publish[0]	= isset($options['publish'][0]) && $options['publish'][0]? $options['publish'][0]: $publish[0];
				$publish[1]	= isset($options['publish'][1]) && $options['publish'][1]? $options['publish'][1]: $publish[1];
				$publish[2]	= isset($options['publish'][2]) && $options['publish'][2]? $options['publish'][2]: $publish[2];
				$publish[3]	= isset($options['publish'][3]) && $options['publish'][3]? $options['publish'][3]: $publish[3];
			}
			if ( isset($this->Pager['rows'][0][$options[0]])) {
				$up		= (substr($this->Pager['rows'][0][$publish[0]], 0, 4) == '0000')? '': $this->Pager['rows'][0][$publish[0]];
				$down	= (substr($this->Pager['rows'][0][$publish[1]], 0, 4) == '0000')? '': $this->Pager['rows'][0][$publish[1]];
				$publish	= '
					<div class="line" >
						<div class="name" >'. $publish[2] .'</div>
						<div class="value" ><input class="gt8-update input-rounded-shadowed input-small fixed-mask date-time" value="'. $up .'" type="text" id="ePublishedUp" name="'. $publish[0] .'" title="####/##/## ##:##:##" /></div>
					</div>
					<div class="line" >
						<div class="name" >'. $publish[3] .'</div>
						<div class="value" ><input class="gt8-update input-rounded-shadowed input-small fixed-mask date-time" value="'. $down .'" type="text" id="ePublishedDown" name="'. $publish[1] .'" title="####/##/## ##:##:##" /></div>
					</div>
					<hr />
				';
			} else {
				$publish	= '';
			}
			
			////////////////////////////IMG/////////////////////////////////////
			$img	= '';
			if ( isset($options['img-preview'])) {
				$img	= '<div class="line img-preview" >'. $options['img-preview'] .'</div>';
			} else if ( isset($this->Pager['rows'][0]['fullpath'])) {
				$img	= '<div class="line img-preview" ><span class="imgC border" title="Pré-visualização" ><img src="'. CROOT . $this->Pager['rows'][0]['fullpath'] .'?regular" alt="" /></span></div>';
			} else if ( isset($this->Pager['rows'][0]['explorer_img'])) {
				$img	= '<div class="line img-preview" ><span class="imgC border cursor-pointer" title="Pré-visualização" onclick="Modal.show({objRef:this, tabIndex:2, onChoose: null, url:\''. CROOT . $GT8['admin']['root'] . 'explorer/users/' . $this->Pager['rows'][0]['login'] .'/?edit&locationbar=0\'})" ><img src="'. CROOT . $GT8['explorer']['root'] . $this->Pager['rows'][0]['explorer_img'] .'" alt="[Preview]" /></span></div>';
			}
			
			////////////////////////////LOCK////////////////////////////////////
			$lock	= '';
			if ( isset($this->Pager['rows'][0]['locked']) ) {
				$lock	= '
					<div class="line" >
						<div class="name" >Bloqueado</div>
						<div class="value" >
							<div class="Switch" >
								<div class="overflow">
									<div class="on" >ON</div>
									<div class="knob" ><input type="checkbox" class="gt8-update" name="locked" '. ($this->Pager['rows'][0]['locked']? 'checked="checked"': '') .' /></div>
									<div class="off" >OFF</div>
								</div>
							</div>
						</div>
					</div>
				';
			}
			
			////////////////////////////ENABLED/////////////////////////////////
			$enabled	= '';
			if ( isset($this->Pager['rows'][0]['enabled']) ) {
				$lock	= '
					<div class="line" >
						<div class="name" >Habilitado</div>
						<div class="value" >
							<div class="Switch" >
								<div class="overflow">
									<div class="on" >ON</div>
									<div class="knob" ><input type="checkbox" class="gt8-update" name="enabled" '. ($this->Pager['rows'][0]['enabled']? 'checked="checked"': '') .' /></div>
									<div class="off" >OFF</div>
								</div>
							</div>
						</div>
					</div>
				';
			}
			
			$html	= '
				<section id="ePublishC" class="modal-info" >
					<h3 title="'. $this->id .'" >Gerenciar</h3>
					'. $img .'
					'. $publish .'
					'. $lock .'
					'. $enabled .'
					'. (isset($options['html'])? $options['html']: '') .'
					'. $this->htmlManagerModal .'
				</section>
			';
			print($html);
		}
		public function printLog($page='') {
			global $GT8;
			require_once( SROOT .'engine/functions/Pager.php');
			
			if ( !$page) {
				$page	= $this->name.'/';
			}
			$Pager	= Pager(array(
				'sql'	=> 'analytics.list-admin',
				'equal'	=> array(
					array('a.page', $page)
				),
				'ids'	=> array(
					array('a.id_reference', $this->id)
				),
				'limit'	=> 50
			));
			$html	= '
							<div class="find" ><input type="text" value="" onfocus="this.select()" onkeyup="Editor.Usage.search(this, event)" ></div>
							<table class="list-filter bordered" >
								<tr>
									<th class="time" ><span class="col-5" >Data</span></th>
									<th class="user" ><span class="col-4" >Usuário</span></th>
									<th class="action" ><span class="col-2" >Ação</span></th>
									<th class="field" ><span class="col-5" >Campo</span></th>
									<th class="value" ><span class="col-8" >Valor</span></th>
								</tr>
			';
			for( $i=0, $len=count($Pager['rows']); $i<$len; $i++) {
				$crr	= $Pager['rows'][$i];
				$login	= $crr['login'];
				
				if ( $login) {
					$login	= '<a href="'. CROOT . $GT8['admin']['root'] .'users/'. utf8_encode(htmlentities($crr['login'])) .'/" >('. utf8_encode(htmlentities($crr['login'])) .') '. utf8_encode(htmlentities($crr['user'])) .'</a>';
				}
				
				$html	.= '
								<tr id="log-'. $crr['id'] .'" >
									<td class="time" >'. $crr['creation'] .'</td>
									<td class="user" >'. $login .'</td>
									<td class="action '. $crr['action'] .'" >&nbsp;</td>
									<td class="field" >'. utf8_encode(htmlentities($crr['name'])) .'</td>
									<td class="value" >'. utf8_encode(htmlentities($crr['value'])) .'</td>
								</tr>
				';
			}
			$html	.= '
							</table>
			';
			print($html);
		}
		public function addToolbarItem($title, $name, $link, $icon, $onClick=null) {
			$link	= $link? $link: '#';
			$onClick	= $onClick? 'onclick="'. $onClick .'"': '';
			
			$this->toolbarItems	.= '<a class="button '. $name .'" href="'. $link .'" '. $onClick .' title="'. $name .'" ><img src="'. $icon .'" alt="[icon]" title="'. $title .'" /></a>';
		}
		public function addToolbarItemG( $bts, $groupType='group-unique') {
			$gbSidebarSelected	= $_COOKIE[$this->name .'-sidebar-crrVisible']; $gbSidebarSelected	= $gbSidebarSelected? $gbSidebarSelected: 'bookmarks';
			
			//#eTBSideBar não use mais ID
			$this->toolbarItems	.= '<span class="group-button '. $groupType .'" >';
			for ( $i=0; $i<count($bts); $i++) {
				//$title, $name, $link, $icon, $onClick=null, $label
				$crr	= $bts[$i];
				
				$title		= $crr[0];
				$name		= $crr[1];
				$link		= $crr[2];
				$icon		= $crr[3];
				$onClick	= $crr[4];
				$label		= $crr[5];
				
				$link	= $link? $link: '#';
				$onClick	= $onClick? 'onclick="'. $onClick .'"': '';
				
				$selected	= $gbSidebarSelected == $name? ' selected': '';
				//print("<h1>". $name .' - '. $_COOKIE[$this->name .'-sidebar-crrVisible'] ."</h1>".PHP_EOL);
				$this->toolbarItems	.= '<a class="'. $name . $selected .'" href="'. $link .'" '. $onClick .' title="'. $name .'" >'. ($icon? '<img src="'. $icon .'" alt="[icon]" title="'. $title .'" />': '<em title="'. $title .'" >'. $label .'</em>') .'</a>';
			}
			$this->toolbarItems	.= '</span>';
		}
	}
?>