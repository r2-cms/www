<?php
	if ( !defined('CROOT')) {
		require_once( "../../../connect.php");
		//die('Undefined GT8: a1i32->p4o1u1o0->ca1a5o6o->Home');
	}
	
	if ( !isset($_GET['edit']) && !isset($_GET['action'])) {
		require_once( SROOT.'engine/controllers/admin/explorer/Index.php');
		$Index	= new Index();
		$Data	= array(
			'path'		=> RegExp($_GET['path'], '[a-z-A-Z0-9_\-\+\.\:\/\\\|\%\&\?\@]+'),
			'login'		=> isset($_SESSION['login']['login'])? $_SESSION['login']['login']: '',
			'login-id'	=> isset($_SESSION['login']['id'])? $_SESSION['login']['id']: ''
		);
		if ( isset($Index->data)) {
			$Data	= array_merge($Index->data, $Data);
		}
		$Index->printView(
			SROOT.'engine/views/admin/explorer/index.inc',
			$Data
		);
		die();
	}
	
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/CheckPrivileges.php");
	CheckPrivileges( null, null, 'explorer/', 1);
	require_once( SROOT .'engine/classes/Editor.php');
	
	class AdminEditor extends Editor {
		public $ext;
		public $type;
		public $name	= 'explorer';
		public $addToolbarItem	= '';
		public function __construct() {
			
			$this->checkActionRequest();
			
			parent::Editor();
			$this->idDir	= (integer)$_GET['d'];
			
			if ( $this->id || isset($_GET['path'])) {
				$options	= array(
					'sql'		=> 'explorer.list',
					'addSelect'	=> 'e.sumary, UNIX_TIMESTAMP(e.publish_up) AS tpublish_up, UNIX_TIMESTAMP(e.publish_down) AS tpublish_down, REPLACE(d.description, "\\\n", "'.PHP_EOL.'") as description',
					'addFrom'	=> 'LEFT JOIN gt8_explorer_data d ON e.id = d.id'
				);
				
				if ( $this->id) {
					$options['ids']	= array(
						array('e.id', $this->id)
					);
				} else {
					$path	= RegExp($_GET['path'], '[a-zA-Z0-9\-\&\,_\.\/\@]+');
					$path	= explode('/', $path);
					array_shift($path);
					array_shift($path);
					
					if ( !$path[count($path)-1]) {
						array_pop($path);
					}
					$name	= $path[count($path)-1];
					array_pop($path);
					$path	= join('/', $path) .'/';
					
					if ( $path == '/') {
						$path	= '';
					}
					
					$options['required']	= array(
						array('e.path', $path),
						array('e.filename', $name)
					);
					//$options['debug']=1;
				}
				$this->Pager	= Pager($options);
				$this->data	= $this->Pager['rows'][0];
				$this->type	= $this->data['type'];
				$ext	= $this->data['filename'];
				
				$this->id	= $this->id? $this->id: $this->data['id'];
				
				if ( !strpos('#'.$ext, '.') ) {
					$this->ext	= '';
				} else {
					$this->ext	= substr($ext, -(strpos(strrev($ext), '.')));
				}
			}
			$this->check404();
			$this->checkAdds();
			
			$this->createToolbarItems();
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) ) {
				$_GET['format']	= 'JSON';
				
				if ( $_GET['field'] == 'description' ) {
					$_POST['value']	= str_replace(
						array(CROOT),
						array('{{CROOT}}'),
						$_POST['value']
					);
				}
				if ( $_GET['action'] == 'new-folder' && $_GET['name'] == 'filename') {
					require_once( SROOT .'engine/queries/explorer/createNewFile.php');
					$_GET['type']	= 'directory';
					$_GET['write_privilege']	= $_SESSION['login']['level'];
					createNewFile( $_GET);
					die();
				}
				if ( $_GET['action'] == 'new-file' && $_GET['name'] == 'filename') {
					require_once( SROOT .'engine/queries/explorer/createNewFile.php');
					$_GET['type']	= 'file';
					$_GET['write_privilege']	= $_SESSION['login']['level'];
					createNewFile( $_GET);
					die();
				}
				if ( $_GET['action'] == 'delete') {
					require_once( SROOT .'engine/queries/explorer/deleteFile.php');
					deleteFile($_GET['value']);
					die();
				}
				if ( $_GET['action'] == 'removeFromBookmarks') {
					require_once( SROOT .'engine/queries/explorer/removeFromBookmarks.php');
					removeFromBookmarks($_GET['value']);
					die();
				}
				if ( $_GET['action'] == 'addToBookmarks') {
					require_once( SROOT .'engine/queries/explorer/addToBookmarks.php');
					addToBookmarks($_GET['value']);
					die();
				}
				if ( $_GET['action'] == 'new-attribute') {
					require_once( SROOT .'engine/queries/explorer/createAttribute.php');
					createAttribute($_GET);
					die();
				}
				if ( $_GET['action'] == 'update-attribute') {
					require_once( SROOT .'engine/queries/explorer/updateAttribute.php');
					updateAttribute($_GET);
					die();
				}
				if ( $_GET['action'] == 'delete-attribute') {
					require_once( SROOT .'engine/queries/explorer/deleteAttribute.php');
					deleteAttribute($_GET);
					die();
				}
				if ( $_GET['action'] == 'update-attribute-value') {
					require_once( SROOT .'engine/queries/explorer/updateAttributeValue.php');
					updateAttributeValue($_GET);
					die();
				}
			}
		}
		public function on404() {
			
		}
		public function check404() {
			if ( !$this->id) {
				$Pager	= $this->getUrlHistory($_GET['path'], false);
				if ( $Pager ) {
					header('location: '. CROOT . $Pager['new'], 301);
					die();
				} else {
					GT8::printView( SROOT .'engine/views/admin/explorer/404/index.inc', array('path'=>RegExp($_GET['path'], '[a-z-A-Z0-9_\-\+\.\:\/\\\|\%\&\?\@]+')));
					die();
				}
				die();
			}
		}
		private function checkAdds() {
			global $GT8;
			
			//all directories
			$path	= explode('/', $this->data['fullpath']);
			array_pop($path);
			array_shift($path);
			
			//PLUGGINS
			$plugpath	= SROOT.'engine/views/admin/explorer/pluggins/';
			$plugindex	= -1;
			$scripts	= array();
			$pages		= array();
			$tabs		= array();
			$cards		= array();
			$css		= array();
			$len	= count($path);
			for ( $i=0; $i<$len; $i++) {
				$crr	= $path[$i];
				
				if ( $crr && file_exists("$plugpath$crr/")) {
					$plugindex	= $i;
					$plugpath	.= "$crr/";
				} else {
					break;
				}
				if ( file_exists($plugpath.'editor.rec.js')) {
					$scripts[]	= $plugpath.'editor.rec.js';
				}
				if ( file_exists($plugpath.'editor.rec.inc')) {
					$pages[]	= $plugpath.'editor.rec.inc';
				}
				if ( file_exists($plugpath.'tabs.rec.inc')) {
					$tabs[]	= $plugpath.'tabs.rec.inc';
				}
				if ( file_exists($plugpath.'cards.rec.inc')) {
					$cards[]	= $plugpath.'cards.rec.inc';
				}
				if ( file_exists($plugpath.'editor.rec.css')) {
					$css[]		= $plugpath.'editor.rec.css';
				}
			}
			$this->data['PLUGGIN:tabs']	= '';
			$this->data['PLUGGIN:cards']	= '';
			$this->data['PLUGGIN:css']	= '';
			$this->data['PLUGGIN:editor']	= '';
			$this->data['PLUGGIN:editor.js']	= '';
			//exactly
			if ( $plugindex == $len) {
				if ( file_exists( $plugpath .'/editor.inc') ) {
					die('Not implemented yet:(');
				}
				if ( file_exists($plugpath .'/tabs.inc') ) {
					ob_start();
					include( $plugpath .'/tabs.inc');
					$this->data['PLUGGIN:tabs']	= ob_get_contents();
					ob_end_clean();
				}
				if ( file_exists($plugpath .'/cards.inc') ) {
					ob_start();
					include( $plugpath .'/cards.inc');
					$this->data['PLUGGIN:cards']	= ob_get_contents();
					ob_end_clean();
				}
				if ( file_exists($plugpath .'/editor.css') ) {
					ob_start();
					include( $plugpath .'/editor.css');
					$this->data['PLUGGIN:css']	= ob_get_contents();
					ob_end_clean();
				}
				if ( file_exists($plugpath .'/editor.js') ) {
					ob_start();
					include( $plugpath .'/editor.js');
					$this->data['PLUGGIN:editor.js']	= ob_get_contents();
					ob_end_clean();
				}
			}
			//recursively
			if ( $plugindex > -1) {
				if ( count($scripts) > 0) {
					ob_start();
					include( $scripts[count($scripts)-1]);
					$this->data['PLUGGIN:editor.js']	.= ob_get_contents();
					ob_end_clean();
				}
				if ( count($pages) > 0) {
					ob_start();
					include( $pages[count($pages)-1]);
					$this->data['PLUGGIN:editor']	.= ob_get_contents();
					ob_end_clean();
				}
				if ( count($tabs) > 0) {
					ob_start();
					include( $tabs[count($tabs)-1]);
					$this->data['PLUGGIN:tabs']	.= ob_get_contents();
					ob_end_clean();
				}
				if ( count($cards) > 0) {
					ob_start();
					include( $cards[count($cards)-1]);
					$this->data['PLUGGIN:cards']	.= ob_get_contents();
					ob_end_clean();
				}
				if ( count($css) > 0) {
					ob_start();
					include( $css[count($css)-1]);
					$this->data['PLUGGIN:css']	.= ob_get_contents();
					ob_end_clean();
				}
			}
			
			//LEVELS
			$plugpath	= SROOT.'engine/views/admin/explorer/levels/';
			$plugindex	= -1;
			$scripts	= array();
			$pages		= array();
			$tabs		= array();
			$cards		= array();
			$css		= array();
			for ( $i=0; $i<$len; $i++) {
				$crr	= $path[$i];
				
				if ( file_exists("$plugpath$crr/")) {
					$plugindex	= $i;
					$plugpath	.= "$crr/";
				} else if ( file_exists($plugpath .'levelin/')) {
					$plugindex	= $i;
					$plugpath	.= "levelin/";
				} else {
					break;
				}
				if ( file_exists($plugpath.'editor.rec.js')) {
					$scripts[]	= $plugpath.'editor.rec.js';
				}
				if ( file_exists($plugpath.'editor.rec.inc')) {
					$pages[]	= $plugpath.'editor.rec.inc';
				}
				if ( file_exists($plugpath.'tabs.rec.inc')) {
					$tabs[]	= $plugpath.'tabs.rec.inc';
				}
				if ( file_exists($plugpath.'cards.rec.inc')) {
					$cards[]	= $plugpath.'cards.rec.inc';
				}
				if ( file_exists($plugpath.'editor.rec.css')) {
					$css[]	= $plugpath.'editor.rec.css';
				}
			}
			//exactly
			if ( $plugindex == $len-1) {
				if ( file_exists( $plugpath .'/editor.inc') ) {
					die('Not implemented yet:(');
				}
				if ( file_exists($plugpath .'/tabs.inc') ) {
					ob_start();
					include( $plugpath .'/tabs.inc');
					$this->data['PLUGGIN:tabs']	.= ob_get_contents();
					ob_end_clean();
				}
				if ( file_exists($plugpath .'/cards.inc') ) {
					ob_start();
					include( $plugpath .'/cards.inc');
					$this->data['PLUGGIN:cards']	.= ob_get_contents();
					ob_end_clean();
				}
				if ( file_exists($plugpath .'/editor.css') ) {
					ob_start();
					include( $plugpath .'/editor.css');
					$this->data['PLUGGIN:css']	.= ob_get_contents();
					ob_end_clean();
				}
				if ( file_exists($plugpath .'/editor.js') ) {
					ob_start();
					include( $plugpath .'/editor.js');
					$this->data['PLUGGIN:editor.js']	.= ob_get_contents();
					ob_end_clean();
				}
			}
			//recursively
			if ( $plugindex > -1) {
				if ( count($scripts) > 0) {
					ob_start();
					include( $scripts[count($scripts)-1]);
					$this->data['PLUGGIN:editor.js']	.= ob_get_contents();
					ob_end_clean();
				}
				if ( count($pages) > 0) {
					ob_start();
					include( $pages[count($pages)-1]);
					$this->data['PLUGGIN:editor']	.= ob_get_contents();
					ob_end_clean();
				}
				if ( count($tabs) > 0) {
					ob_start();
					include( $tabs[count($tabs)-1]);
					$this->data['PLUGGIN:tabs']	.= ob_get_contents();
					ob_end_clean();
				}
				if ( count($cards) > 0) {
					ob_start();
					include( $cards[count($cards)-1]);
					$this->data['PLUGGIN:cards']	.= ob_get_contents();
					ob_end_clean();
				}
				if ( count($css) > 0) {
					ob_start();
					include( $css[count($css)-1]);
					$this->data['PLUGGIN:css']	.= ob_get_contents();
					ob_end_clean();
				}
			}
		}
		public function update( &$field='', &$value='') {
			//sleep(5);
			require_once( SROOT .'engine/queries/explorer/updateFile.php');
			updateFile($this->id, $_GET['field'], isset($_GET['value'])? $_GET['value']: $_POST['value'], 'JSON');
			exit;
		}
		public function newFile( &$field='', &$value='') {
			require_once( SROOT .'engine/queries/explorer/createNewFile.php');
			$qsa	= '';
			foreach( $_GET as $name=>$value) {
				if ( !($name == 'opt' && $value=='new') && !($name=='d') && !($name=='path') && !($name=='rewrite') && !($name=='edit') && !($name=='action') && !($name=='format') && !($name=='idDir')) {
					$qsa	.= "&$name=$value";
				}
			}
			$_GET['format']	= 'OBJECT';
			$this->id	= createNewFile($_GET);
			$filename	= mysql_fetch_array(mysql_query("SELECT filename FROM gt8_explorer e WHERE id = ". $this->id));
			$filename	= $filename[0];
			
			//header( 'HTTP/1.1 301 Moved Permanently' );
			header( 'Location: '. $filename .'?edit'. $qsa, true, 301 );
			die();
		}
		public function prntCheckbox( $field) {
			switch ( $field) {
				case 'locked': {
					//se o arquivo for de outro usuário, em nível superior, e for somente leitura, é como se estivesse com lock
					$rowE	= $this->Pager['rows'][0];
					if ( $rowE['id_user'] != $_SESSION['login']['id']) {
						$myLevel	= isset($_SESSION['login']['level'])? $_SESSION['login']['level']: 0;
						
						if ( $myLevel <= $rowE['write_privilege'] ) {
							$this->Pager['rows'][0]['locked']	= '1';
						}
					}
					parent::prntCheckbox( $field);
					break;
				}
				default: {
					parent::prntCheckbox( $field);
					break;
				}
			}
		}
		public function prnt( $field, $addSlashes=false) {
			switch( $field) {
				case 'read_privilege': {
					require_once( SROOT .'engine/functions/CreateComboLevels.php');
					$html		= '<select name="read_privilege" class="stretch gt8-update" >'. CreateComboLevels($this->Pager['rows'][0]['read_privilege']) . '</select>';
					print(utf8_encode($html));
					break;
				}
				case 'write_privilege': {
					require_once( SROOT .'engine/functions/CreateComboLevels.php');
					$html		= '<select name="write_privilege" class="stretch gt8-update" >'. CreateComboLevels($this->Pager['rows'][0]['write_privilege']) . '</select>';
					print(utf8_encode($html));
					break;
				}
				case 'ext': {
					print($this->ext);
					break;
				}
				case 'size': {
					print(number_format($this->Pager['rows'][0]['size'], 0, ',', '.'));
					break;
				}
				case 'publish_up': {
					print(( substr($this->Pager['rows'][0]['publish_up'], 0, 4) == '0000')? '': $this->Pager['rows'][0]['publish_up']);
					break;
				}
				case 'publish_down': {
					print(( substr($this->Pager['rows'][0]['publish_down'], 0, 4) == '0000')? '': $this->Pager['rows'][0]['publish_down']);
					break;
				}
				default: {
					parent::prnt( $field, $addSlashes);
				}
			}
		}
		public function getSizeInfo() {
			require_once( SROOT .'engine/functions/formatInBytes.php');
			$sizes	= Pager(array(
				'sql'	=> 'explorer.getDataSize',
				'ids'	=> array(
					array('d.id', $this->id)
				)
			));
			$sizes	= $sizes['rows'][0];
			
			$html	= '
							<div class="line" >
								<div class="col-4" ><strong>Original</strong></div>
								<div class="col-6" >'. formatInBytes($sizes['data']) .'</div>
							</div>
			';
			if ( strpos( $this->Pager['rows'][0]['mime'], 'image') !== false) {
				$html	.= '
							<div class="line" >
								<div class="col-4" ><strong>Pré-vizualização</strong></div>
								<div class="col-6" >'. formatInBytes($sizes['preview']) .'</div>
							</div>
							<div class="line" >
								<div class="col-4" ><strong>Regular</strong></div>
								<div class="col-6" >'. formatInBytes($sizes['regular']) .'</div>
							</div>
							<div class="line" >
								<div class="col-4" ><strong>Pequeno</strong></div>
								<div class="col-6" >'. formatInBytes($sizes['small']) .'</div>
							</div>
							<div class="line" >
								<div class="col-4" ><strong>Miniatura</strong></div>
								<div class="col-6" >'. formatInBytes($sizes['thumb']) .'</div>
							</div>
				';
			}
			return $html;
		}
		public function printMimeSpecificInfo() {
			$html	= '';
			$row	= $this->Pager['rows'][0];
			$mime	= $row['mime'];
			
			if ( strpos($mime, 'image') !== false) {
				$html	= '
									<tr>
										<td><strong>Largura</strong></td>
										<td><span id="eWidth" >'. $row['width'] .'</span></td>
									</tr>
									<tr>
										<td><strong>Altura</strong></td>
										<td><span id="eHeight" >'. $row['height'] .'</span></td>
									</tr>
				';
			}
			print($html);
		}
		public function getAllowModeration( $options=array()) {
			$myLevel	= $_SESSION['login']['level'];
			$result	= mysql_query("SELECT u.approval_level_required+0 AS approval_level_required FROM gt8_users u WHERE u.id = ". $this->Pager['rows'][0]['id_user']);
			$rowU	= mysql_fetch_assoc($result);
			
			$strDisabled	= '';
			if ( $myLevel < $rowU['approval_level_required'] ) {
				$strDisabled	= ' Switch-disabled';
			}
			$html	= '
					<hr />
					<div class="line" >
						<div class="name" >Aprovado</div>
						<div class="value" >
							<div class="Switch '. $strDisabled .'" >
								<div class="overflow">
									<div class="on" >SIM</div>
									<div class="knob" ><input type="checkbox" class="gt8-update" name="approved" '. ($this->Pager['rows'][0]['approved']? 'checked="checked"': '') .' /></div>
									<div class="off" >NÃO</div>
								</div>
							</div>
						</div>
					</div>
			';
			return $html;
		}
		public function printAttributes($template) {
			if ( $this->type == 'file') {
				return;
			}
			require_once( SROOT .'engine/functions/Pager.php');
			$Pager	= Pager(array(
				'sql'	=> 'explorer.list-attributes',
				'ids'	=> array(
					array('a.id_dir', $this->id)
				),
				'format'	=> 'TEMPLATE',
				'template'	=> $template
			));
			$Pager['rows']	= str_replace(
				array(
					'>all</',
					'>registered</',
					'>customer</',
					'>supplier</',
					'>support</',
					'>operacional</',
					'>IT</',
					'>Manager</',
					'>Owner</',
					'>Developer</',
					'>Master</',
					
					'>string</',
					'>integer</',
					'>float</'
				),
				array(
					'>público</',
					'>registrado</',
					'>clientes</',
					'>fornecedores</',
					'>suporte</',
					'>operacional</',
					'>TI</',
					'>Gerência</',
					'>Proprietário</',
					'>Desenvolvedor</',
					'>Dart Vader</',
					
					'>texto</',
					'>inteiro</',
					'>decimal</'
				),
				$Pager['rows']
			);
			print($Pager['rows']);
		}
		public function printAttributesValue($template) {
			if ( $this->type != 'file') {
				return;
			}
			
			require_once( SROOT .'engine/functions/Pager.php');
			$Pager	= Pager(array(
				'sql'	=> 'explorer.list-attributes-value',
				'addSelect'	=> 'IF ( a.level+0<='. $_SESSION['login']['level'] .', 1, 0) AS editable, '. $this->id .' AS id_explorer, LOWER(REPLACE(a.attribute, " ", "-")) AS attr_name',
				'where'	=> '
					AND a.id_dir IN ('. str_replace('/', ',', substr($this->Pager['rows'][0]['dirpath'], 0, -1)) .')
				',
				'replace'	=> array(
					array('vIn.id_explorer = vIn.id_explorer', 'vIn.id_explorer = '. $this->id)
				),
				'format'	=> 'OBJECT',
				'template'	=> $template,
				'foundRows'	=> 1,
				'limit'	=> 100
			));
			$this->data['attributes-value']	= $Pager['raw'];
			
			$html	= '';
			for( $i=0, $rows=$Pager['rows'], $len=count($rows); $i<$len; $i++) {
				$crr	= $rows[$i];
				$crrHtml	= '';
				
				if ( $crr['type'] === 'integer') {
					$crrHtml	= str_replace('{{input}}', '<input type="text" class="input-rounded-shadowed gt8-input-allow-integer align-right" value="[[value]]" name="##attr_name##" />', $template);
				} else if ( $crr['type'] === 'float') {
					$crrHtml	= str_replace('{{input}}', '<input type="text" class="input-rounded-shadowed gt8-input-allow-float align-right" value="[[value]]" name="##attr_name##" />', $template);
				} else if ( strpos('#'.$crr['type'], ';')) {//enum
					$options	= array();
					$arr		= explode(';', $crr['type']);
					foreach( $arr as $name=>$value) {
						$options[]	= '<option>'. utf8_encode(htmlentities($value)) .'</option>';
					}
					$value		= empty($crr['value'])? '&nbsp;': utf8_encode(htmlentities($crr['value']));
					$select	= '
						<span class="e-select" >
							<select name="##attr_name##" >
								<option>'. ($value==='&nbsp;'? 'Escolha': $value) .'</option>'. join(PHP_EOL, $options) .'
							</select>
							<span class="button group-button" ><strong>'. $value .'</strong><img src="{{CROOT}}imgs/gt8/arrow-down-mini.png" alt="" /></span>
						</span>
					';
					$crrHtml	.= str_replace('{{input}}', $select, $template);
				} else {//string
					$crrHtml	= str_replace('{{input}}', '<input type="text" class="input-rounded-shadowed" value="[[value]]" name="##attr_name##" />', $template);
				}
				$html	.= GT8::getHTML( $crrHtml, $crr);
			}
			print($html);
		}
		public function getServerJSVars() {
			
			$this->jsVars[]	= array('path', $this->Pager['rows'][0]['path']);
			$this->jsVars[]	= array('fileExtension', ($this->type=='file'? $this->ext:''));
			$this->jsVars[]	= array('type', $this->type);
			$this->jsVars[]	= array('isNewFile', false);
			$this->jsVars[]	= array('size', $this->Pager['rows'][0]['size']);
			
			return parent::getServerJSVars();
		}
		public function createToolbarItems() {
			global $GT8;
			
			$aroot	= CROOT . $GT8['admin']['root'];
			$this->addToolbarItem( 'Anexos', 'attachs', $this->data['filename'].'/', $aroot.'explorer/imgs/attachment-small.png');
			
		}
	}
?>