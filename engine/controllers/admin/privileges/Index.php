<?php
	if ( !defined('CROOT')) {
		require_once( "../../connect.php");
		//die('Undefined GT8: a1i32->p4o1u1o0->ca1a5o6o->Home');
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/CheckPrivileges.php");
	
	CheckPrivileges( null, null, 'privileges/', 1);
	require_once( SROOT .'engine/classes/CardLister.php');
	
	class Index extends CardLister {
		public $name	= 'privileges';
		public $orderFilter	= array(
			array("nome-crescente", "ordem alphabética", 'u.name'),
			array("nome-decrescente", "ordem decrescente", 'u.name DESC')
		);
		
		public function __construct() {
			global $GT8;
			
			$this->jsVars[]	= array('id', 0);
			$this->jsVars[]	= array('u', utf8_encode(addslashes($_SESSION['login']['name'])));
			$this->addSideBarItem( 'Filtros', 'filters', (CROOT.'imgs/gt8/filter-small.png'), '
				<label title="Procurar por palavras chaves" ><span><input type="text" value="'. utf8_encode(addslashes(isset($_GET['q'])? $_GET['q']:'')) .'" name="q" class="gt8-update input-rounded-shadowed" /><small>keywords</small></span></label>
			');
			
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
					GT8::printView( SROOT .'engine/views/admin/account/404/index.inc', array('path'=>RegExp($_GET['path'], '[a-z-A-Z0-9_\-\+\.\:\/\\\|\%\&\?\@]+')));
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