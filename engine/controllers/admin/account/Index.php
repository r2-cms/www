<?php
	if ( !defined('CROOT')) {
		require_once( "../../connect.php");
		//die('Undefined GT8: a1i32->p4o1u1o0->ca1a5o6o->Home');
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/CheckPrivileges.php");
	
	CheckPrivileges( null, null, 'users/', 1);
	require_once( SROOT .'engine/classes/CardLister.php');
	
	class Index extends CardLister {
		public $name	= 'users';
		public $orderFilter	= array(
			array("nome-crescente", "ordem alphabética", 'u.name'),
			array("nome-decrescente", "ordem decrescente", 'u.name DESC')
		);
		
		public function __construct() {
			global $GT8;
			
			$this->addToolbarItem('Adicionar', 'add-new', 'new-user-account/', CROOT.'imgs/gt8/toolbar/file-add-small.png');
			$this->jsVars[]	= array('id', 0);
			$this->jsVars[]	= array('u', utf8_encode(addslashes($_SESSION['login']['name'])));
			//$Users->addSideBarItem( 'Favoritos', 'bookmarks', (CROOT.'imgs/gt8/favorite-small.png'), $Users->getBookmarks());
			$this->addSideBarItem( 'Filtros', 'filters', (CROOT.'imgs/gt8/filter-small.png'), '
				<label title="Procurar por palavras chaves" ><span><input type="text" value="'. utf8_encode(addslashes(isset($_GET['q'])? $_GET['q']:'')) .'" name="q" class="gt8-update input-rounded-shadowed" /><small>keywords</small></span></label>
			');
			
			$spath			= $this->getSPath($GT8['admin']['root'] . $GT8['admin']['account']['root']);
			
			//spath
			$spath	= $this->getSPath();
			if ( isset($spath[0]) && ($spath[0]=='new-user-account' || $spath[0]=='new') ) {
				//require_once( 'new-user-account/index.inc');
				print("<pre>". print_r('666: Controller/Admin/Account/Index.php', 1) ."</pre>". PHP_EOL);
				die();
			} else if ( isset($spath[0])) {
				print("<pre>". print_r(98754595, 1) ."</pre>". PHP_EOL);
				die();
				require_once('editor/index.php');
				die();
			}
			self::CardLister();
		}
		public function on404() {
			
		}
		public function check404() {
			if ( count($this->Pager['rows']) == 0) {
				$Pager	= $this->getUrlHistory($_GET['path'], true, '', SROOT .'engine/views/admin/account/404/index.inc');
				
				if ( $Pager ) {
					header('location: '. CROOT . $Pager['new'], 301);
					die();
				} else {
					GT8::printView( SROOT .'engine/views/admin/account/404/index.inc', array('path'=>RegExp($_GET['path'], '[a-z-A-Z0-9_\-\+\.\:\/\\\|\%\&\?]+')));
					die();
				}
				die();
			}
		}
		public function printCards($template) {
			$this->options['sql']	= 'users.list';
			$this->options['where']	= "AND u.level <= {$_SESSION['login']['level']}";
			if ( $this->keywords) {
				$this->options['search']	= array(
					array('u.name', $this->keywords),
					array('u.login', $this->keywords)
				);
			}
			//$this->options['debug']	= 1;
			parent::printCards($template);
		}
	}
?>