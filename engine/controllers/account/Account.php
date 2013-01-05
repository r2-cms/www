<?php
	require_once(SROOT ."engine/functions/CheckLogin.php");
	require_once(SROOT .'engine/functions/Pager.php');
	
	GT8::enterSSL();
	
	class Account extends GT8 {
		public $contacts	= array();
		public $mail		= '';
		public $address		= array();
		public $preferences	= array();
		
		public function __construct() {
			global $GT8;
			
			$this->retrievePersonalInfo();
			$this->setContacts();
			$this->setAddress();
			parent::GT8();
		}
		private function retrievePersonalInfo() {
			$Pager	= Pager(array(
				'sql'	=> 'users.list',
				'ids'	=> array(
					array('u.id', $_SESSION['login']['id'])
				),
				'addSelect'	=> ',
					u.natureza,
					u.cpfcnpj,
					u.document,
					u.genre
				'
			));
			$this->data	= $Pager['rows'][0];
			$this->data['genre']			= $this->data['genre']=='M'? 'Masculino': 'Feminino';
			$this->data['document-type']	= $this->data['natureza']=='F'? 'RG': 'Inscrição';
			$this->data['cadastro-type']	= $this->data['natureza']=='F'? 'CPF': 'CNPJ';
			$this->data['natureza']			= $this->data['natureza']=='F'? 'Física': 'Jurídica';
		}
		private function setContacts() {
			
			$Pager	= Pager(array(
				'sql'	=> 'users.list-contact',
				'ids'	=> array(
					array('uc.id_users', $_SESSION['login']['id'])
				),
				'order'	=> 'uc.channel, uc.type, uc.value'
			));
			$Pager	= $Pager['rows'];
			$this->data['primary-mail']	= $_SESSION['login']['login'];
			$this->data['contacts']	= $Pager;
			
			if ( strpos('#'. $this->mail, '@') == 0 ) {
				for( $i=0; $i<count($Pager); $i++) {
					$value	= $Pager[$i];
					
					if ( $value['channel']=='E-mail' ) {
						$this->data['primary-mail']	= $value['value'];
						break;
					}
				}
			}
		}
		private function setAddress() {
			$Pager	= Pager(array(
				'sql'	=> 'address.list',
				'required'	=> array(
					array('a.id_users', $_SESSION['login']['id'])
				)
			));
			$this->data['address']	= $Pager['rows'];
		}
	}
	
	interface IUsers{
		public function addUser($props = array());
		public function updateUser($props = array());
		public function printUser($idUser);
		public function deleteUser($idUser);
		public function setProfile($idUser);
	}
	interface IProfile{
		
	}
?>