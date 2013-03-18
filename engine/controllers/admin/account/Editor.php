<?php
	if ( !defined('CROOT')) {
		die('Undefined GT8: a1i32->a114e00->e1i1o4->Home');
	}
	require_once( SROOT ."engine/classes/Editor.php");
	
	class AdminEditor extends Editor {
		public $name	= 'users';
		public $tableType	= null;
		
		function __construct() {
			global $GT8;
			$spath	= $this->getSPath('users/');
			$userLogin	= RegExp($spath[0], '[a-zA-Z0-9_\-\.\,\&\@]+');
			parent::Editor();
			$Pager	= Pager( array(
				'sql'		=> 'users.list',
				'addSelect'	=> 'u.cpfcnpj, u.document, u.natureza, u.genre, u.level+0 AS ilevel',
				'required'	=> array(
					array('u.hlogin', md5(strtolower($userLogin)), true)
				)
				//'where'		=> 'AND u.level+0 <= '. $_SESSION['login']['level'], 'debug'=>1
			));
			$this->Pager	= $Pager;
			$this->Pager['rows'][0]['explorer_img']	= 'users/'. $Pager['rows'][0]['login'] . '/?preview';
			$this->data	= $Pager['rows'][0];
			$this->id	= $this->data['id'];
			
			if ( $this->id != $_SESSION['login']['id']) {
				$this->checkReadPrivileges('users/', '*', (isset($_GET['format'])? $_GET['format']:'OBJECT'));
			}
			if ( $this->data['ilevel'] > $_SESSION['login']['level']) {
				$this->redirect('forbidden');
			}
			
			$this->checkActionRequest();
			
			if ( isset($_GET['opt']) && $_GET['opt']=='users.contact.insert') {
				$_GET['format']	= 'JSON';
				require_once( SROOT .'engine/queries/users/addContact.php');
				addContact($_GET);
				die();
			} else if ( isset($_GET['opt']) && $_GET['opt']=='users.contact.delete') {
				$_GET['format']	= 'JSON';
				$_GET['id']	= (integer)$_GET['idContact'];
				require_once( SROOT .'engine/queries/users/deleteContact.php');
				deleteContact($_GET);
				die();
			} else if ( isset($_GET['opt']) && $_GET['opt']=='users.privileges.update') {
				$_GET['format']	= 'JSON';
				require_once( SROOT .'engine/queries/users/updatePrivileges.php');
				$_GET['idPrivilege']	= (integer)$_GET['idPrivilegePage'];
				$_GET['login']	= $spath[0];
				$_GET['privilege']	= $_GET['value'];
				updatePrivileges($_GET);
				die();
			} else if ( isset($spath[1]) && $spath[1] == 'privilegios') {
				require_once('privileges.php');
				die();
			}
			
			if ( isset($spath[0]) && $spath[0]=='new-user-account') {
				$this->id	= 0;
			} else if ( !$this->id ) {
				$this->notFound();
			}
		}
		public function on404() {
			
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				switch($_GET['action']) {
					case 'set-pass': {
						$name	= 'pass';
						$this->update( $name, $_POST['pass']);
					}
				}
			}
		}
		public function update( &$field='', &$value='') {
			require_once(SROOT.'engine/queries/users/UpdateUsers.php');
			$this->checkWritePrivileges( 'users/', $field, (isset($_GET['format'])? $_GET['format']:'OBJECT'));
			new UpdateUsers(array(
				"id"		=> $this->id,
				"field"		=> $field,
				"value"		=> $value,
				'format'	=> 'JSON'
			));
			die();
		}
		public function newFile( &$field='', &$value='') {
			parent::newFile( $field, $value);
			die();
		}
		public function notFound() {
			die('User not found!');
		}
		public function prnt( $field, $props=array()) {
			switch( $field) {
				case 'attributes': {
					
					break;
				}
				default:
					print(utf8_encode(addslashes(htmlentities($this->data[$field]))));
					break;
			}
		}
		public function printAddress() {
			if ( $this->id) {
				$Pager	= Pager(array(
					'sql'	=> 'address.list',
					'format'	=> 'CARD',
					'required'	=> array(
						array('a.id_users', $this->id)
					)
				));
				print($Pager['rows']);
			}
		}
		public function getLevelArray( $table, $field) {
			require_once( SROOT .'engine/functions/CreateComboLevels.php');
			return CreateComboLevels($this->Pager['rows'][0]['level'], 'OBJECT');
		}
		public function getComboLevel($allow=0) {
			require_once( SROOT.'engine/functions/CreateComboLevels.php');
			return CreateComboLevels($allow);
		}
		public function printEditableContacts( $htmlTemplate) {
			global $GT8;
			
			require_once( SROOT ."engine/queries/users/list-contact.php");
			
			$result		= mysql_query("DESCRIBE gt8_users_contact");
			$type			= '';
			$channel		= '';
			$htmlTypes		= '';
			$htmlChannels	= '';
			while( ($row=mysql_fetch_assoc($result))) {
				if ( $row['Field'] == 'type') {
					$htmlTypes	= explode(',', str_replace("'", '', substr($row['Type'], strpos($row['Type'], "'"), -1)));
					$htmlTypes	= "<option>". join('</option><option>', $htmlTypes) ."</option>";
				} else if ( $row['Field'] == 'channel') {
					$htmlChannels	= explode(',', str_replace("'", '', substr($row['Type'], strpos($row['Type'], "'"), -1)));
					$htmlChannels	= "<option>". join('</option><option>', $htmlChannels) ."</option>";
				}
			}
			
			$Pager	= Pager(array(
				'sql'	=> 'users.list-contact',
				'ids'	=> array(
					array('uc.id_users', $this->id)
				)
			));
			
			$html	= '';
			for ($i=0, $len=count($Pager['rows']); $i<$len; $i++) {
				$crr	= $Pager['rows'][$i];
				
				if ( $i==0) {
					$channel	= $crr['channel'];
					$type		= $crr['type'];
				}
				$html	.= str_replace(
					array(
						'@@##channels##@@',
						'@@##channel##@@',
						'@@##types##@@',
						'@@##type##@@',
						'@@##value##@@',
						'id="eContactTemplate"'
					),
					array(
						'<option selected="selected" >'. $crr['channel'] .'</option>'. $htmlChannels,
						$crr['channel'],
						'<option selected="selected" >'. $crr['type'] .'</option>'. $htmlTypes,
						$crr['type'],
						$crr['value'],
						'id="eContactId-'. $crr['id'] .'"'
					),
					$htmlTemplate
				);
			}
			
			$html	.= str_replace(
				array(
					'@@##channels##@@',
					'@@##channel##@@',
					'@@##types##@@',
					'@@##type##@@',
					'@@##value##@@',
				),
				array(
					$htmlChannels,
					$channel,
					$htmlTypes,
					$type,
					''
				),
				$htmlTemplate
			);
			
			print($html);
		}
		public function printManagerModal($options=array()) {
			global $GT8;
			
			parent::printManagerModal(array(
				'html'		=> '
					<div class="line margin-top-small" >
						<a href="'. CROOT . $GT8['admin']['root'] . $GT8['admin']['privileges']['root'] . $this->Pager['rows'][0]['login'].'/" class="full-
						width href-button href-button-ok" ><span>Privilégios</span></a>
					</div>
					<div class="line margin-top-small" >
						<a href="historico/" class="full-width href-button href-button-ok" ><span>Histórico</span></a>
					</div>
				'
			));
		}
		public function getServerJSVars() {
			global $GT8;
			$this->jsVars[]	= array('contactsAPath', CROOT.$GT8['admin']['root'].$GT8['admin']['contacts']['root']);
			$this->jsVars[]	= array('accountAPath', CROOT.$GT8['admin']['root'].$GT8['admin']['account']['root']);
			$this->jsVars[]	= array('token', $GT8['account']['token']);
			return parent::getServerJSVars();
		}
	}
?>