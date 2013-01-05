<?php
	if ( !defined('SROOT') ) {
		require_once('../../connect.php');
	}
	
	require_once( SROOT ."engine/controllers/account/Account.php");
	
	class Orders extends Account {
		public function __construct() {
			global $GT8;
			
			$this->checkActionRequest();
			$this->setFields();
		}
		protected function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				$idLogin	= (integer)$_SESSION['login']['id'];
			}
		}
		private function setFields() {
			
			require_once(SROOT .'engine/functions/Pager.php');
			$Pager	= Pager(array(
				'sql'		=> 'orders.list-orders',
				'addSelect'	=> ', i.id_explorer, e.path, e.filename, e.title, i.id_orders, SUBSTRING(e.path, 10) AS l_path',
				'addFrom'	=> '
					INNER JOIN gt8_orders_items i	ON o.id = i.id_orders
					INNER JOIN gt8_explorer e		ON e.id = i.id_explorer
				',
				'ids'		=> array(
					array('o.id_users', $_SESSION['login']['id'])
				)
			));
			$this->data['orders']	= $Pager['rows'];
			
		}
	}
?>