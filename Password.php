<?php
	
	//Obs.: As mensagens de erros, positivas ou de status devem ser mensagens em linguagem de sistema
	
	if ( !defined('SROOT') ) {
		header('location: ../');
		die('');
	}
	require_once(SROOT .'engine/classes/R2.php');
	
	if ( !isset($R2)) {
		global $R2;
	}
	
	require_once(SROOT. 'engine/models/account/Account.php');
	
	class Password extends Account implements IPassword {
		//public $timeLimitAccess = 1800;
			
		public function Password(){
			require_once( SROOT . '/engine/mail/Mail.php');
			global $spath;
			
			if(isset($spath[2])){
				//print_r($spath[2]);
				//die();
			}
			
		}
		public function reset($props = array()){
			$token = isset($props['token'])? $props['token']: null;
			$hlogin = isset($props['hlogin'])? RegExp($props['hlogin'], '[A-Za-z0-9_\-\.]+'): null;
			$newPass = isset($props['newPass'])? $props['newPass']: null;
			$checkLogin = $this->checkLogin($hlogin);
			$login = "";
			$idUser = 0;
			$name = "";
			$status = 500;
			$email = "";
			$timeLimitAccess = 1800;
			
			if(!isset($token)){
				//Busca token e inicia o processo de reset da senha
				
				if($checkLogin){
					
					for($i=0; $i<count($checkLogin); $i++){
						for($j=0; $j<count($checkLogin['user']); $j++){
							$idUser = $checkLogin['user'][$j]['id'];
							$login = $checkLogin['user'][$j]['login'];
						}
						for($k=0; $k<count($checkLogin['contact']); $k++){
							$email = $checkLogin['contact'][$k]['value'];
						}
					}
					
					$token = $this->createToken($idUser, $login);
					
					if($token){
						$this->sendMailLinkReset($status, $name, $email, $token);
						print('//#Created Token Successfully!');
						die();
					}else{
						print('//#Token Error!');
						die();
					}
				}else{
					print('//#Token not found!');
					die();
				}
			}else{
				//Verificar existência, data e hora do token
				
				$create = 0;
				$accessing = 0;
				
				$sql = "
					SELECT
						ut.id,
						ut.id_user,
						UNIX_TIMESTAMP(ut.creation) AS creation,
						UNIX_TIMESTAMP(NOW()) AS accessing
					FROM
						usuarios_token ut
					WHERE
						ut.value = '$token'
				";
				
				$result = mysql_query($sql) or die("Password.reset: Select Error! " . mysql_error());
				$rows = array();
				
				while($row = mysql_fetch_assoc($result)){
					$rows[] = $row;
				}
				
				if(count($rows)>0){
					for($i=0; $i<count($rows); $i++){
						$idUser = $rows[$i]['id_user'];
						$create = $rows[$i]['creation'];
						$accessing = $rows[$i]['accessing'];
					}
				}
				
				//$access não pode ser maior que 2
				$access = 0;
				$newPass = "";
				
				if($access<3){
					$access+=1;
					
					if($accessing - $create > $timeLimitAccess){
						
						//Dorme por 30 segundos
						sleep(10);
						print("//#Password.reset: Access denied!");
						die();
					}else{
						if(isset($newPass)){
							$this->updatePass($idUser, $newPass);
							$this->cleanToken($idUser, $token);	
						}
					}
				}
				
			}
			die();
		}
		public function alterPass($login, $currentPass, $newPass){
			$hlogin = md5($login);
			$currentPass = md5($currentPass);
			$newPass = mysql_real_escape_string($newPass);
			$checkLogin = array();
			$idUser = 0;
			
			if($hlogin){
				$checkLogin = checkLogin($hlogin, $currentPass);
				if($checkLogin){
					for($i=0; $i<count($checkLogin); $i++){
						$idUser = $checkLogin[$i]['id'];
					}
					$this->updatePass($idUser, $newPass);
				}
			}
		}
		private function checkLogin($hlogin, $hpass = null){
			$userAcount = array();
			$user = array();
			$contacts = array();
			
			if(isset($hlogin)){
				if($hpass){
					$user = Pager(
						array(
							"format"	=> 'OBJECT',
							'sql'		=> 'users.list',
							"equal"		=> array(
								array("u.hlogin", $hlogin),
								array("u.hpass", $hpass)
							)
						)
					);
				}else{
					$user = Pager(
						array(
							"format"	=> 'OBJECT',
							'sql'		=> 'users.list',
							"equal"		=> array(
								array("u.hlogin", $hlogin)
							)
						)
					);
				}
				
				if(count($user['rows'])>0){
					$idUser = 0;
					
					for($i=0; $i<count($user); $i++){
						$idUser = $user[$i]['id'];
					}
					
					$contacts = Pager(
						array(
							"format"	=> 'OBJECT',
							'sql'		=> 'users.list-contact',
							'ids'		=> array(
								array('uc.id', $idUser)
							),
							'equal'		=> array(
								array('uc.channel', 'E-mail')
							)
						)
					);
					
					if(count($contacts)>0){
						$userAcount = array(
							"user"		=> $user['rows'],
							"contact"	=> $contacts['rows']
						);
					}
					return $userAcount;
				}else{
					return false;
				}
				
			}
		}
		private function createToken($idUser, $login){
			$token = "";
			for($i=0; $i<4; $i++){
				$token .= md5($login . time() . $i);
			}
			
			$sqlQuery = "
				INSERT INTO
					usuarios_token(
						id_user,
						value,
						creation,
						modification
					)VALUES(
						$idUser,
						'$token',
						NOW(),
						NOW()
					)
			";
			mysql_query($sqlQuery) or die("//#createToken: SQL INSERT Error");
			
			$id = mysql_insert_id();
			if($id){
				return $token;
			}
		}
		private function cleanToken($idUser, $token){
			$idUser = (integer)$idUser;
			$token = mysql_real_escape_string($token);
			
			$sqlDelete = "
				DELETE FROM
					usuarios_token
				WHERE
					id_user = $idUser
				AND
					VALUE = '$token'
			";
			mysql_query($sqlDelete) or die("#//error: Sql Delete Error");
			
			$rowsAffected = mysql_affected_rows();
			if($rowsAffected){
				print("//#affected rows:" . $rowsAffected);
			}else{
				print("//#error");
			}
		}
		private function sendMailLinkReset($status, $name, $email, $token){
			$mail = new Mail();
			$args['name'] = $name;
			$args['email'] = $email;
			$args['token'] = $token;
			
			$mail->send($status, $args);
		}
		private function updatePass($idUser, $newPass){
			$idUser = (integer)$idUser;
			$sqlUpdate = "
				UPDATE
					usuarios u
				SET
					u.pass = MD5('$newPass'),
					u.modification = NOW()
				WHERE
					u.id = $idUser
			";
			mysql_query($sqlUpdate) or die("//#error: SQL Update Error");
			
			$rowsAffected = mysql_affected_rows();
			if($rowsAffected){
				print("//#affected rows:" . $rowsAffected);
			}else{
				print("//#error");
			}
		}
		public function Login() {
			
			
			require_once( SROOT . $R2['admin']['root'] .'check.php');
			
			parent::R2();
		}
		
		
		
		public function debug(){
			print(date("H:i:s"));
		}
	}
?>