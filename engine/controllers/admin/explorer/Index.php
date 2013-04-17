<?php
	if ( !defined('CROOT')) {
		require_once( "../../connect.php");
		//die('Undefined GT8: a1i32->p4o1u1o0->ca1a5o6o->Home');
	}
	
	//se a requisição for imagem (endereço virtual e !admin), apenas verifique os privilégios e carregue a imagem
	if ( isset($_GET['path']) && (strpos($_GET['path'], $GT8['admin']['root'] .'explorer/')===false) ) {
		require_once('LoadPhisic.php');
		die();
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/CheckPrivileges.php");
	
	if ( isset($_GET['edit']) || isset($_GET['action']) ) {
		require_once( SROOT ."engine/controllers/admin/explorer/Editor.php");
		new AdminEditor();
		//require_once( SROOT ."engine/views/admin/explorer/editor.inc");
		//die();
	}
	
	CheckPrivileges( null, null, 'explorer/', 1);
	require_once( SROOT .'engine/classes/CardLister.php');
	
	class Index extends CardLister {
		public $Pager	= null;
		public $index	= null;
		public $limit	= null;
		public $order	= null;
		public $search	= null;
		public $idDir	= null;
		public $dirPath	= null;
		public $dirPathCode	= null;
		public $rows	= null;
		
		public function __construct() {
			global $GT8;
			
			$this->idDir	= 0;
			$spath			= $this->getSPath($GT8['admin']['root'] .'explorer/');
			
			//se for endereço por ID
			if ( isset($_GET['d']) || !count($spath)) {
				$this->idDir	= (integer)$_GET['d'];
			} else if ( !$this->idDir ) {
				//security
				for($i=0, $len=count($spath); $i<$len; $i++) {
					$spath[$i]	= RegExp($spath[$i], '[a-zA-Z0-9\-\&\,_\.\@]+');
				}
				
				$spath2	= $spath;
				array_pop($spath2);
				//faça um cópia deste array, separe o path e o filename para obter o ID do arquivo
				$path		= join('/', $spath2) .'/';
				$path		= $path=='/'? '': $path;
				$filename	= $spath[count($spath)-1];
				
				$this->idDir	= mysql_fetch_assoc(mysql_query("SELECT id, dirpath FROM gt8_explorer WHERE path = '$path' AND filename = '$filename'"));
				$this->dirPathCode	= $this->idDir['dirpath'];
				$this->idDir	= $this->idDir['id'];
			}
			$this->isModal		= isset($_GET['modal']) && ($_GET['modal']==1 || $_GET['modal']=='true');
			self::CardLister();
			
			//em caso de 404 (no admin)
			if ( $this->idDir === null) {
				
			} else {
				
			}
			$this->check404();
			$this->createToolBarHTML();
			$this->checkAdds();
		}
		public function createToolBarHTML() {
			$this->addToolbarItem('Criar nova pasta de arquivos',	'new-folder', '?action=new-folder',	CROOT .'imgs/gt8/toolbar/folder-add-small.png',	'return Explorer.showModalNew(this, true)');
			$this->addToolbarItem('Criar novo arquivo', 			'new-file', '?action=new-file',		CROOT .'imgs/gt8/toolbar/file-add-small.png',	'return Explorer.showModalNew(this, false)');
			$this->addToolbarItem('Excluir arquivo(s)', 'delete-file', '?delete', CROOT .'imgs/gt8/toolbar/file-del-small.png', 'return Explorer.deleteFile(this)');
			$this->addToolbarItemG(array(
				array('Exibir favoritos', 'bookmarks', '', CROOT .'imgs/gt8/toolbar/favorite-small.png'),
				array('Exibir filtros', 'filters', '', CROOT .'imgs/gt8/toolbar/filter-small.png'),
				array('Exibir dispositivos', 'devices', '', CROOT .'imgs/gt8/toolbar/devices-small.png')
			));
			$this->addSideBarItem( 'Favoritos', 'bookmarks', (CROOT.'imgs/gt8/favorite-small.png'), $this->getBookmarks());
			$this->addSideBarItem( 'Filtros', 'filters', (CROOT.'imgs/gt8/filter-small.png'), '
				<label title="Procurar por palavras chaves" ><span><input type="text" value="'. utf8_encode(addslashes(isset($_GET['q'])? $_GET['q']:'')) .'" name="q" class="gt8-update input-rounded-shadowed" /><small>keywords</small></span></label>
			');
			$this->addSideBarItem( 'Dispositivos', 'devices', (CROOT.'imgs/gt8/devices-small.png'), $this->getDevices());
		}
		public function getBookmarks() {
			global $GT8;
			$Pager	= Pager(array(
				'sql'	=> 'explorer.list-bookmarks',
				'ids'	=> array(
					array('b.id_users', $_SESSION['login']['id'])
				),
				'foundRows'	=> 300
			));
			$rows	= $Pager['rows'];
			$html	= '
						<ul class="folder" >
			';
			for ( $i=0; $i<count($rows); $i++) {
				$crr	= $rows[$i];
				$isMe	= false;
				if ( $crr['id_dir'] == $this->idDir) {
					$isMe	= 'selected';
				}
				$html	.= '
					<li id="ebook-'. $crr['id'] .'" class="'.$isMe.'" >
						<a href="'. CROOT . $GT8['admin']['root'] .'explorer/'. $crr['path'] . $crr['filename'] .($crr['type']==='directory'? '/':'?edit').'" title="img-'. $crr['id_dir'] .'"  >
							<img class="left-icon-small" src="'. CROOT .$GT8['explorer']['root']. $crr['path'] . $crr['filename'] .'?thumb" alt="[favorite icon]" />
							<span>'. utf8_encode(($crr['title'])).'</span>
							<small class="ballon" >('. ($crr['files']+$crr['folders']) .')</small>
						</a>
					</li>
				';
			}
			if ( empty($html)) {
				$html	= '<li class="empty" ><a href="?d=empty" onclick="return false" >&nbsp;</a></li>';
			}
			$html	.= "
						</ul>
			";
			return $html;
			
		}
		public function getDevices() {
			global $GT8;
			$Pager	= Pager(array(
				'sql'	=> 'explorer.list',
				'foundRows'	=> 100,
				'required'	=> array(
					array('e.id_dir', 0)
				)
			));
			$rows	= $Pager['rows'];
			$html	= '
						<ul class="folder" >
			';
			for ( $i=0; $i<count($rows); $i++) {
				$crr	= $rows[$i];
				$isMe	= false;
				if ( $crr['id'] == $this->idDir) {
					$isMe	= 'selected';
				}
				$html	.= '
					<li class="'.$isMe.'" >
						<a href="'. CROOT . $GT8['admin']['root'].'explorer/'. $crr['filename'] .'/" title="img-'. $crr['id'] .'"  >
							<img class="left-icon-small" src="'. CROOT .$GT8['explorer']['root']. $crr['path'] . $crr['filename'] .'?thumb" alt="[favorite icon]" />
							<span>'. utf8_encode(($crr['title'])).'</span>
							<small class="ballon" >('. ($crr['files']+$crr['folders']) .')</small>
						</a>
					</li>
				';
			}
			$html	.= "
						</ul>
			";
			return $html;
		}
		public function on404() {
			
		}
		public function check404() {
			if ( $this->idDir === null) {
				$Pager	= $this->getUrlHistory($_GET['path'], true, '', SROOT .'engine/views/admin/explorer/404/index.inc');
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
			
			//all directories
			$path	= $this->getSPath($GT8['admin']['root'] .'explorer/');
			
			//PLUGGINS
			$plugpath	= SROOT.'engine/views/admin/explorer/pluggins/';
			$plugindex	= -1;
			$scripts	= array();
			$pages		= array();
			$len	= count($path);
			for ( $i=0; $i<$len; $i++) {
				$crr	= $path[$i];
				
				if ( $crr && file_exists("$plugpath$crr/")) {
					$plugindex	= $i;
					$plugpath	.= "$crr/";
				} else {
					break;
				}
				if ( file_exists($plugpath.'index.rec.js')) {
					$scripts[]	= $plugpath.'index.rec.js';
				}
				if ( file_exists($plugpath.'index.rec.inc')) {
					$pages[]	= $plugpath.'index.rec.inc';
				}
			}
			$this->data['PLUGGIN:index']	= '';
			$this->data['PLUGGIN:index.js']	= '';
			
			//exactly
			if ( $plugindex == $len) {
				
				if ( file_exists( $plugpath .'/index.inc') ) {
					die('Not implemented yet:(');
				}
				if ( file_exists($plugpath .'/index.js') ) {
					ob_start();
					include( $plugpath .'/index.js');
					$this->data['PLUGGIN:index.js']	= ob_get_contents();
					ob_end_clean();
				}
			}
			//recursively
			if ( $plugindex > -1) {
				
				if ( count($scripts) > 0) {
					ob_start();
					include( $scripts[count($scripts)-1]);
					$this->data['PLUGGIN:index.js']	.= ob_get_contents();
					ob_end_clean();
				}
				if ( count($pages) > 0) {
					ob_start();
					include( $pages[count($pages)-1]);
					$this->data['PLUGGIN:index']	.= ob_get_contents();
					ob_end_clean();
				}
			}
			//LEVELS
			$plugpath	= SROOT.'engine/views/admin/explorer/levels/';
			$plugindex	= -1;
			$scripts	= array();
			$pages		= array();
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
				if ( file_exists($plugpath.'index.rec.js')) {
					$scripts[]	= $plugpath.'index.rec.js';
				}
				if ( file_exists($plugpath.'index.rec.inc')) {
					$pages[]	= $plugpath.'index.rec.inc';
				}
			}
			
			//exactly
			if ( $plugindex == $len-1) {
				if ( file_exists( $plugpath .'/index.inc') ) {
					die('Not implemented yet:(');
				}
				if ( file_exists($plugpath .'/index.js') ) {
					ob_start();
					include( $plugpath .'/index.js');
					$this->data['PLUGGIN:index.js']	.= ob_get_contents();
					ob_end_clean();
				}
			}
			//recursively
			if ( $plugindex > -1) {
				
				if ( count($scripts) > 0) {
					ob_start();
					include( $scripts[count($scripts)-1]);
					$this->data['PLUGGIN:index.js']	.= ob_get_contents();
					ob_end_clean();
				}
				if ( count($pages) > 0) {
					ob_start();
					include( $pages[count($pages)-1]);
					$this->data['PLUGGIN:index']	.= ob_get_contents();
					ob_end_clean();
				}
			}
		}
		public function addAttributeInSelect( $attribute, $asName='') {
			$asName	= $asName? $asName: $attribute;
			
			$this->options['addSelect']	= $this->options['addSelect']? $this->options['addSelect']: '';
			
			$this->options['addSelect']	.= ",
				COALESCE(ev.vtotal, 0) AS vtotal, COALESCE(ev.vmonth, 0) AS vmonth, COALESCE(ev.vweek, 0) AS vweek, COALESCE(ev.vtoday, 0) AS vtoday,
				(
					SELECT
						v.value
					FROM
						gt8_explorer_attributes_value v
						JOIN gt8_explorer_attributes a ON a.id = v.id_attributes
					WHERE
						a.attribute = '$attribute' AND v.id_explorer = e.id AND v.id_attributes = a.id 
				) AS $asName
			";
		}
		public function printCards($template='') {
			$this->options['sql']	= 'explorer.list';
			$this->options['bts']		= '';
			$this->options['format']	= 'TEMPLATE';
			$this->options['template']	= $template;
			$this->options['where']	= "AND (u.id = {$_SESSION['login']['id']} || e.read_privilege<={$_SESSION['login']['level']})";
			//$this->options['debug']	= 1;
			if ( $this->keywords) {
				//procure do dir atual em diante
				if ( !$this->dirPathCode) {
					$this->dirPathCode	= mysql_fetch_array(mysql_query("SELECT dirpath FROM gt8_explorer WHERE id = '{$this->idDir}'"));
					$this->dirPathCode	= $this->dirPathCode[0];
				}
				$this->options['searchR']	= array();
				$this->options['searchR'][]	= array('e.dirpath', $this->dirPathCode);
				$this->options['where']		.= ' AND LENGTH(e.dirpath) > '. strlen($this->dirPathCode);
				$this->options['search']	= array(
					array('e.title, e.filename, e.path', $this->keywords)
				);
			} else {
				$this->options['ids']	= array(
					array('e.id_dir', $this->idDir, true)
				);
			}
			if ( !$this->Pager) {
				require_once( SROOT .'engine/functions/Pager.php');
				$this->Pager	= Pager($this->options);
			}
			
			$this->Pager['rows']	= str_replace(
				array('0x0', 'privilege-r', '/type=directory', '/type=file'),
				array('@', 'semi-invisible', '/', '?edit'),
				$this->Pager['rows']
			);
			
			print($this->Pager['rows']);
		}
		public function getServerJSVars() {
			$this->jsVars[]	= array('id', 0);
			$this->jsVars[]	= array('d', $this->idDir);
			$this->jsVars[]	= array('u', utf8_encode(addslashes($_SESSION['login']['name'])));
			
			return parent::getServerJSVars();
		}
	}
?>