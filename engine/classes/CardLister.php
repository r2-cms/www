<?php
	if ( !defined('SROOT')) {
		die('Required constant not defined! (E.U.P::CardLister)');
	}
	require_once( SROOT .'engine/classes/GT8.php');
	class CardLister extends GT8 {
		public $name	= 'explorer';
		public $keywords	= '';
		protected $options	= null;
		public $Pager	= null;
		public $index	= 0;
		public $limit	= 0;
		public $bts		= null;
		public $defaultLimit	= 10;
		public $search	= null;
		public $idDir	= null;
		public $dirPath	= null;
		public $dirPathCode	= null;
		public $rows	= null;
		public $html	= array();
		public $isModal	= null;
		public $order	= null;
		public $orderFilter	= array(//this is a real example from catalog.inc.
			array("mais-visualizados", "mais populares", 'ev.vtotal DESC'),//the first is the default
			array("menos-visualizados", "menos populares", 'ev.vtotal ASC'),
			array("maior-valor", "maior valor", 'CAST( SUBSTR(price, 4) AS SIGNED) DESC'),
			array("menor-valor", "menor valor", 'CAST( SUBSTR(price, 4) AS SIGNED) ASC'),
			array("ordem-crescente-nome", "nome", 'e.title')
		);
		
		public function CardLister($options=array()) {
			global $GT8;
			
			$this->saveGridState();
			
			if ( isset($options['defaultLimit'])) {
				$this->defaultLimit	= $options['defaultLimit'];
			}
			
			$this->isModal	= isset($_GET['modal']) && ($_GET['modal']==1 || $_GET['modal']=='true');
			
			
			/***************************************************************************
			 *                                 ORDER                                   *
			***************************************************************************/
			$order	= isset($_GET['order']) && $_GET['order']? $_GET['order']: (isset($_COOKIE[ $this->name .'-order'])? $_COOKIE[$this->name .'-order']: $this->orderFilter[0][0]);
			$order	= RegExp($order, '[a-zA-Z0-9\-]+');
			$this->keywords	= isset($_GET["q"])? str_replace(explode(" ",'" \' % + ? \\ / ^ ~ ` ´ ! $ * '), " ", $_GET["q"]): null;
			if ( $this->keywords) {
				$this->orderFilter[]	= array("relevancia", "relevância");
			}
			if ( $order) {
				
				for ( $i=0; $i<count($this->orderFilter); $i++) {
					$crr	= $this->orderFilter[$i];
					if ( $order == $crr[0]) {
						$order	= $crr[2];
						$this->order	= $crr[0];
						$this->options['order']		= $crr[2];
						
						break;
					}
				}
			}
			/***************************************************************************
			 *                                 INDEX                                   *
			***************************************************************************/
			$this->index		= isset($options['index'])? $options['index']: (integer)$_GET['index'];
			$this->options['index']		= $this->index;
			
			/***************************************************************************
			 *                                 OTHERS                                  *
			***************************************************************************/
			$options['bts']		= isset($options['bts'])? $options['bts']: 7;
			$this->bts			= $options['bts'];
			$this->options['format']	= isset($options['format']) && $options['format']? $options['format']: (isset($this->options['format'])&&$this->options['format']? $this->options['format']: null);
			$this->options['grid']		= isset($options['grid']) && $options['grid']? $options['grid']: (isset($this->options['grid'])&&$this->options['grid']? $this->options['grid']: null);
			$this->options['search']	= isset($options['search']) && $options['search']? $options['search']: (isset($this->options['search'])&&$this->options['search']? $this->options['search']: null);
			
			/***************************************************************************
			 *                                 LIMIT COMBO                             *
			***************************************************************************/
			$limit	= isset($_GET['limit'])? $_GET['limit']: (isset($_COOKIE[ $this->name .'-limit'])? $_COOKIE[$this->name .'-limit']: $this->defaultLimit);
			$options['limit']			= isset($options['limit'])? $options['limit']: $limit;
			$this->limit				= $options['limit'];
			$this->options['limit']		= $options['limit'];
			$this->html['limit']		= '';
			if ( $this->limit>60 || $this->limit<5) {
				$this->limit	= 20;
			}
			for ( $i=60; $i>5; $i--) {
				if ( $this->limit == $i ) {
					$this->html['limit']	= '<option selected="selected" >'. $i .'</option>' . $this->html['limit'];
				} else {
					$this->html['limit']	= '<option>'. $i .'</option>'. $this->html['limit'];
				}
			}
			$this->html['limit']	= "<select name='limit' onchange='Pager.limit(this)' >". $this->html['limit'] ."</select>";
			
			
			/***************************************************************************
			 *                                 COOKIES                                 *
			***************************************************************************/
			//salvando configurações personalizadas do usuário
			$expires	= time()+(60*60*24*30*6);
			setcookie($this->name .'-limit', $this->limit, $expires, "/");
			setcookie($this->name .'-order', $this->order, $expires, "/");
		}
		protected function saveGridState() {
			if ( isset($_GET['action']) && $_GET['action'] == 'save-grid-state') {
				$this->saveParam( 'grid-state-'. $_GET['name'], $_GET['value'], 'admin');
				die();
			}
		}
		protected function setPagination($options=array()) {
			die('<h1>DEPRECATED IN CardLister::setPagination($options)</h1>');
			require_once( SROOT .'engine/functions/Pager.php');
			if ( !$this->Pager && isset($options['sql'])) {
				$this->Pager	= Pager($options, $rows);
			}
			
			if ( isset($this->Pager['format']) && $this->Pager['format'] == 'GRID' ) {
				if ( $this->Pager['foundRows'] == 0) {
					$this->Pager['rows']	= '<div class="info" >Nenhum resultado</div>';
				}
			}
			$this->options	= $options;
		}
		public static function isAdmin() {
			global $GT8;
			if ( strpos('#'.$_SERVER['REQUEST_URI'], '/'.$GT8['admin']['root']) ) {
				$this->isAdmin	= true;
			} else {
				$this->isAdmin	= false;
			}
			return $this->isAdmin;
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
			$this->jsVars[]	= array('cardListerName', $this->name);
			$this->jsVars[]	= array('keywords',	$this->keywords);
			$this->jsVars[]	= array('isModal',	$this->isModal? 1:0, true);
			$this->jsVars[]	= array('limit',	$this->limit, true);
			$this->jsVars[]	= array('index',	$this->index, true);
			$this->jsVars[]	= array('order',	$this->order);
			return parent::getServerJSVars();
		}
		public function printBodyClass() {
			$class	= '';
			if ( $this->isModal) {
				$class	.= 'modal-window';
			}
			print('class="'. $class .'" ');
		}
		public function printModalClass() {
			if ( $this->isModal) {
				print('modal-window');
			} else {
				
			}
		}
		public function getCards( $template='') {
			
			$this->options['bts']		= '';
			if ( $template) {
				$this->options['format']	= isset($this->options['format'])&&$this->options['format']? $this->options['format']: 'TEMPLATE';
				$this->options['template']	= $template;
			}
			//example
			if ( 0 && $this->keywords) {
				$this->options['search']	= array(
					array('e.title', $this->keywords)
				);
			}
			
			if ( !$this->Pager) {
				require_once( SROOT .'engine/functions/Pager.php');
				$this->Pager	= Pager($this->options);
			}
			return $this->Pager['rows'];
		}
		public function printCards( $template='') {
			print($this->getCards($template));
		}
		public function getOrderOptions() {
			$html	= "";
			for ( $i=0; $i<count($this->orderFilter); $i++) {
				$crr	= $this->orderFilter[$i];
				$html	.= '<option value="'. $crr[0] .'" '. ($this->order==$crr[0]? "selected='selected'": "") .'>'. $crr[1] .'</option>'. PHP_EOL;
			}
			$html	.= "";
			return ( $html);
		}
		public function printPaginator($name) {
			if ( $name == 'pages') {
				print( $this->Pager['page']);
			} else if ( $name == 'limit') {
				print( $this->html['limit']);
			}
		}
		protected $sideBarItems	= array();
		public function addSidebarItem( $title, $name, $icon, $content) {
			$gbSidebarSelected	= $_COOKIE[$this->name .'-sidebar-crrVisible']; $gbSidebarSelected	= $gbSidebarSelected? $gbSidebarSelected: 'bookmarks';
			$this->sideBarItems[]	= '
					<section id="e'. strtoupper(substr($name, 0, 1)) .substr($name, 1) .'" class="'. $name . ($name=='filters'?' input-validation':' input-validation') .'" title="'. $name .'" '. ($gbSidebarSelected==$name?'##display-visible##':'style="##display-none##" ') .' >
						<header><h3><img class="left-icon-small" src="'. $icon .'" alt="[favorite icon]" />'. $title .'</h3></header>
						'. $content .'
					</section>
			';
		}
		public $toolbarItems	= '';
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
		public function printSidebar() {
			print($this->getSidebar());
		}
		public function getSidebar() {
			$html	= '
				<div class="sidebar left-pane" style="width:'. (isset($_COOKIE['explorer-sp-w'])? $_COOKIE['explorer-sp-w']: 270) .'px;" >
			';
			$hasVisibility	= false;
			for ( $i=0; $i<count($this->sideBarItems); $i++) {
				if ( strpos(' '. $this->sideBarItems[$i], '##display-visible##') > 0) {
					$hasVisibility	= true;
					$this->sideBarItems[$i]	= str_replace('##display-visible##', '', $this->sideBarItems[$i]);
				} else {
					if ( !$hasVisibility && $i==count($this->sideBarItems)-1) {
						$this->sideBarItems[$i]	= str_replace('##display-none##', '', $this->sideBarItems[$i]);
					} else {
						$this->sideBarItems[$i]	= str_replace('##display-none##', 'display:none', $this->sideBarItems[$i]);
					}
				}
				$html	.= $this->sideBarItems[$i];
			}
			$html	.= '
				</div>
			';
			return $html;
		}
		public function printHead( $title, $jsContent='', $jsInclude=array(), $cssInclude=array(), $cssContent= '') {
			
			if ( $cssContent == '') {
				$cssContent	= '.SplitPane-horizontal {height: '. isset($_COOKIE[ $this->name .'-sp-height'])? $_COOKIE[$this->name .'-sp-height']: '1440' .'px;}';
			}
			
			$this->printHead( $title, $jsContent, $jsInclude, $cssInclude, $cssContent);
		}
		public function getFooter($data=array()) {
			if ( isset($this->Pager['page'])) {
				if ( GT8::isAdmin() ) {
					$data['html']	= '
						<div class="paging" >
							<span class="flex-space" >&nbsp;</span>
							'. $this->Pager['page'] .'
							<label class="margin-left" >
								<span>Qtde:</span>
								'. $this->html['limit'] .'
							</label>
							<label class="margin-left" >
								<span>Ordenar:</span>
							</label>
							<div class="clear" >&nbsp;</div>
						</div>
					';
				} else {//coloquei em separado para personalizar futuramente o lado customer. Admin não deve ser alterado!
					$data['html']	= '
						<div class="paging" >
							<span class="flex-space" >&nbsp;</span>
							'. $this->Pager['page'] .'
							<label class="margin-left" >
								<span>Qtde:</span>
								'. $this->html['limit'] .'
							</label>
							<label class="margin-left" >
								<span>Ordenar:</span>
							</label>
							<div class="clear" >&nbsp;</div>
						</div>
					';
				}
			}
			
			return parent::getFooter($data);
		}
		public function printFooter($data=array()) {
			print( $this->getFooter($data));
		}
	}
?>