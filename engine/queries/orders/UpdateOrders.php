<?php
	if ( !defined('CROOT')) {
		die('Undefined connection! (Q40->A114e00::U91a1e)');
	}
	require_once( SROOT .'engine/classes/Update.php');
	
	class UpdateOrders extends Update {
		public $name	= 'orders';
		public $privilegeName	= 'orders/';
		private $isContact	= false;
		private $options	= null;
		
		public function __construct($options) {
			$this->options	= $options;
			$this->Update( $options);
			
			if ( $options['field'] === 'id_stts') {
				$idStatus	= (integer)$options['value'];
				
				if ( file_exists( SROOT .'engine/mail/status/'. $idStatus .'.inc')) {
					require_once( SROOT .'engine/mail/Mail.php');
					$m	= new Mail($idStatus, 'OBJECT');
					$m->printAfterSending	= false;
					$m->copyOnDb	= true;
					$m->send($this->data);								
				}
			}
		}
		public function getValue( $field, $value) {
			if ( $field == 'stt') {
				$value	= strtoupper(RegExp($value, '[A-Za-z]{2}'));
			}
			return $value;
		}
		public function checkPrivileges( $field, $value) {
			$this->checkWritePrivileges( 'orders/', '*', $this->options['format']);
			
		}
	}
?>