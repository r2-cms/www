<?php
	if ( !defined('CROOT')) {
		die('Undefined GT8: a1i32->a114e00->e1i1o4->Home');
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/classes/Editor.php");
	
	class AdminEditor extends Editor {
		public $name	= 'banners-config';
		public $tableType	= null;
		
		function __construct() {
			global $GT8, $spath;

			$page	= $this->getSPath('banners-config');
			$page	= mysql_real_escape_string($page[1]);
			
			$Pager	= Pager(array(
				'sql'		=> 'banners-config.list',
				'required'	=> array(
					array('bc.page', $page)
				)
			));
			$this->Pager	= $Pager['rows'];
			
			if ( !isset($this->Pager[0]) || !$this->Pager[0] || !isset($this->Pager[0]['id'])) {
				$this->on404();
			}
			
			$this->Pager	= $Pager['rows'][0];
			$this->data	= $this->Pager;
			$this->id	= $this->Pager['id'];
			$this->setFields();
			$this->name	= $this->name .'/'. $page .'/';
			
			$this->checkActionRequest();
			
			$this->checkReadPrivileges();
			
			$this->setToolbar();
			
			parent::Editor();
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				switch($_GET['action']) {
					case 'save-position': {
						require_once( SROOT .'engine/queries/banners-config/SaveSettings.php');
						new SaveSettings(array(
							'page'	=> 'home',
							'field'	=> 'source',
							'value'	=> $_GET['ids'],
							'format'=> 'JSON'
						));
						die();
					}
					case 'save-limit': {
						require_once( SROOT .'engine/queries/banners-config/SaveSettings.php');
						new SaveSettings(array(
							'page'	=> 'home',
							'field'	=> 'limit',
							'value'	=> $_GET['limit'],
							'format'=> 'JSON'
						));
						die();
					}
					case 'save-random-option': {
						require_once( SROOT .'engine/queries/banners-config/SaveSettings.php');
						new SaveSettings(array(
							'page'	=> 'home',
							'field'	=> 'random',
							'value'	=> $_GET['random'],
							'format'=> 'JSON'
						));
						die();
					}
					case 'update': {
						require_once( SROOT .'engine/queries/banners-config/SaveSettings.php');
						new SaveSettings(array(
							'page'	=> 'home',
							'field'	=> $_GET['field'],
							'value'	=> $_GET['value'],
							'format'=> 'JSON'
						));
						die();
					}
				}
			}
		}
		private function setToolbar() {
			$this->addToolbarItem('Adicionar produto', 'add-product', '?action=adicionar-produto', CROOT.'imgs/gt8/add-small.png', null);
			$this->addToolbarItem('Excluir produto', 'del-product', '?action=excluir-produto', CROOT.'imgs/gt8/cancel-small.png', null);
		}
		public function on404() {
			if ( !$this->id ) {
				parent::on404();
			}
		}
		public function setCards() {
			$ids	= explode(',', $this->Pager['source']);
			$this->options['sql']	= 'explorer.list';
			$this->options['required']	= array(
				array('ez.id', join(',', $ids))
			);
			$this->options['addFrom']	= 'INNER JOIN gt8_explorer ez	ON ez.id = e.id_dir';
			$this->options['addSelect']	.= ', SUBSTRING_INDEX(SUBSTRING_INDEX(e.path, "/", 4), "/", -1) AS brand';
			$this->options['addSelect']	.= ', SUBSTRING_INDEX(e.path, "/", -5) AS varname, e.filename AS imgname';
			$this->options['addSelect']	.= ', SUBSTRING_INDEX(e.path, "/", 4) AS l_path';
			$this->options['addSelect']	.= ', ez.price_selling, ez.title';
			$this->options['group']		= 'e.dirpath';
			$this->options['foundRows']		= count($ids);
			//$this->options['debug']	= 1;
			
			require_once( SROOT .'engine/functions/Pager.php');
			$Pager	= Pager($this->options);
			$Pager['rows']	= str_replace(
				array('0x0', 'privilege-r', '/type=directory',	'/type=file'),
				array('@', 'semi-invisible', '/',				'?edit'),
				$Pager['rows']
			);
			
			$rows	= array();
			for( $i=0; $i<count($ids); $i++) {
				$id	= $ids[$i];
				for( $j=0; $j<count($Pager['rows']); $j++) {
					if ( $Pager['rows'][$j]['id_dir'] == $id) {
						$rows[]	= $Pager['rows'][$j];
						break;
					}
				}
			}
			$this->data['cards']	= $rows;
		}
		public function update( &$field='', &$value='') {
			
		}
		public function getServerJSVars() {
			global $GT8;
			//$this->jsVars[]	= array('contactsAPath', CROOT.$GT8['admin']['root'].$GT8['admin']['contacts']['root']);
			
			return parent::getServerJSVars();
		}
		private function setFields() {
			global $GT8;
			$this->setCards();
			$this->data['random-selected']	= $this->data['random']=='1'? 'selected': '';
		}
	}
?>