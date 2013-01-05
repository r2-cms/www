<?php
	if ( !defined('SROOT') ) {
		header('location: ../');
		die('');
	}
	if ( !isset($GT8)) {
		global $GT8;
	}
	require_once(SROOT.'engine/controllers/account/Login.php');
	
	class ResetPassword extends Login {
		public function __construct() {
			global $spath, $GT8;
			
			if ( isset($_GET['token']) && $_GET['token']) {
				$token	= RegExp($_GET['token'], '[a-zA-Z0-9]{255}');
				$error	= '';
				
				if ( strlen($token) == strlen($_GET['token']) ) {
					require_once( SROOT .'engine/functions/Pager.php');
					$Pager	= Pager(array(
						'sql'	=> 'users.list-pass-reset',
						'required'	=> array(
							array('r.token', $token)
						)
					));
					if ( isset($Pager['rows'][0]) && $Pager['rows'][0]) {
						$Pager	= $Pager['rows'][0];
						$_POST["pass"]	= md5($Pager["pass"] .'-'. $_SESSION['GT8']['tstart']);
						$_GET["user"]	= $Pager['login'];
						require_once( SROOT .'engine/functions/CheckLogin.php');
						mysql_query("
							DELETE FROM
								gt8_users_pass_reset
							WHERE
								AND token = '". $token ."'
						");
						
						header('location: ../'. $GT8['account']['security']['root']);
						die();
					} else {
						$error	= 'O prazo para redefinir sua senha expirou. Por favor, tente solicitar um novo token para poder redefinir sua senha novamente <a href="'. CROOT . $GT8['account']['root'].$GT8['account']['resetPassword']['root'] .'" >clicando aqui</a>.';
					}
				} else {
					$error	= 'Token inválido. Certifique-se que não tenha alterado nada no endereço ou experimente solicitar um novo token para poder redefinir sua senha <a href="'. CROOT . $GT8['account']['root'].$GT8['account']['resetPassword']['root'] .'" >clicando aqui</a>.';
				}
			
				$this->data['message']	= '
					Não foi possível redefinir sua senha agora.<br /><br />
					'. $error .' <br /><br />
					Se preferir, entre em contato com nosso suporte para obter auxílio: '. $GT8['phone-number'] .'.
				';
				$this->data['title']	= 'Operação não concluída';
				$this->printView(
					SROOT .'engine/views/error.inc',
					$this->data
				);
				die();
			}
			
			$this->checkActionRequest();
		}
		private function checkActionRequest() {
			if ( isset($_GET['action']) && $_GET['action']) {
				if ( $_GET['action'] === 'pass-recover') {
					
					if ( isset($_POST['login']) ) {
						$login	= RegExp($_POST['login'], '[a-zA-Z0-9\-\.\@\_]+');
						
						if ( strlen($login) === strlen($_POST['login'])) {
							require_once( SROOT .'engine/functions/Pager.php');
							$Pager	= Pager(array(
								'sql'		=> 'users.list',
								'required'	=> array(
									array('hlogin', md5($login))
								)
							));
							if ( isset($Pager['rows'][0]) && $Pager['rows'][0]) {
								global $GT8;
								$Pager	= $Pager['rows'][0];
								
								//generate token
								$token	= md5(time());
								$token	.= md5('password-reset-'. $Pager['name']);
								$token	.= md5('password-reset-'. $Pager['login']);
								$token	.= md5($token);
								$token	.= md5('password-reset-'. $Pager['pass']);
								$token	.= md5('password-reset-'. $_SESSION['analytics']['id']);
								$token	= substr($token . strrev($token), 0, 255);
								$Pager['token']	= $token;
								$Pager['account-token-path']	= $GT8['account']['root'] . $GT8['account']['resetPassword']['root'] .'?token='. $token;
								
								mysql_query("
									INSERT INTO
										gt8_users_pass_reset(
											id_users,
											token
										) VALUES (
											'". $Pager['id'] ."',
											'$token'
										)
								") or die('Erro ao processar a solicitacao! Por favor, tente mais tarde.');
								
								
								require_once( SROOT .'engine/mail/Mail.php');
								$m	= new Mail(300, 'OBJECT');
								$m->copyOnDb	= false;
								$m->printAfterSending	= false;
								$m->send($Pager);
							}
						}
					}
					$this->data['title']	= 'E-mail enviado com sucesso';
					$this->data['message']	= 'Você receberá um e-mail com as informações necessárias para redefinir sua senha. Siga as instruções do e-mail.<br /><br />Obrigado.';
					$this->printView(
						SROOT .'engine/views/message.inc',
						$this->data
					);
					die();
				}
			}
		}
	}
	
?>