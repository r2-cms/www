<?php
	if ( !defined('SROOT')) {
		die('Invalid Mail request!');
	}
	require_once(SROOT .'engine/classes/PHPMailer.php');
	require_once(SROOT .'engine/classes/PHPMailer.smtp.php');
	
	class Mail extends PHPMailer {
		public $copyOnDb	= true;
		public $format	= 'JSON';
		public $printAfterSending	= false;
		public $from	= array();
		public $statusId		= 0;
		public $statusCode	= 0;
		public $statusTitle	= '';
		public $statusDescription	= '';
		
		public function Mail( $status, $format='JSON') {
			global $GT8;
			
			$this->statusCode	= (integer)$status;
			
			if ( !count($this->from)) {
				$this->from	= array( $this->getParam('call-center-mail','system'), utf8_decode($this->getParam('call-center-mail-title','system')));
			}
			
			if ( !$this->statusCode) {
				return;
			}
			parent::__construct(true);
			$this->format	= $format;
		}
		protected function retrieveStatus() {
			if ( !$this->statusId ) {
				$row	= mysql_fetch_assoc(mysql_query("SELECT id, ttl, dscrptn FROM gt8_stts WHERE stts = {$this->statusCode}"));
				$this->statusId				= $row['id'];
				$this->statusDescription	= $row['dscrptn'];
				$this->statusTitle			= utf8_encode($row['ttl']);
			}
		}
		public function send($data) {
			/**
			* options:
			* 	to			array(mail, name)		//can be set in templates
			* 	from		array(mail, name)		//can be set in templates
			*	name
			*	
			*/
			global $GT8;
			$this->retrieveStatus();
			
			$body		= file_exists( SROOT .'engine/mail/templates/'. $this->statusCode.'.tpl')? file_get_contents( SROOT. 'engine/mail/templates/'. $this->statusCode.'.tpl'): file_get_contents( SROOT .'engine/mail/templates/default.tpl');
			
			//data params
			if ( isset($data['from'])) {
				$data['from']	= array(RegExp($data['from'][0], '[a-zA-Z0-9\.\-\_\@]+'), $data['from'][1]);
			}
			if ( isset($data['to'])) {
				$data['to']	= array(RegExp($data['to'][0], '[a-zA-Z0-9\.\-\_\@]+'), $data['to'][1]);
			}
			if ( isset($data['name'])) {
				$data['name']	= str_replace(str_split('<>%$&;{}[]'), '-', $data['name']);
			}
			
			//required fields from status.inc
			$title		= $this->statusDescription;
			$subject	= $this->statusTitle;
			$content;
			$mail;
			$from;
			$to;
			$bcc;
			$idRef	= 0;
			if ( file_exists( SROOT .'engine/mail/status/'. $this->statusCode .'.inc')) {
				include( SROOT .'engine/mail/status/'. $this->statusCode .'.inc');
			} else if ( file_exists( SROOT .'engine/mail/status/index.inc')) {
				include( SROOT .'engine/mail/status/index.inc');
			} else {
				die('//#error: Erro inconsistence. Por favor, contate o administrador do site!'. PHP_EOL);
			}
			
			$from[0]	= isset($from[0]) && $from[0]? $from[0]: $this->from[0];
			$from[1]	= isset($from[1]) && $from[1]? $from[1]: $this->from[1];
			$mail		= $mail? $mail: $this->from[0];
			$data['phone']		= $data['phone']? $data['phone']: '';
			$data['from']		= $from[0];
			$data['message']	= strip_tags($data['message'], '<a><br>');
			$data				= array_merge($data, $GT8, $_SESSION);
			
			$data['message']	= str_replace( PHP_EOL, '<br>', $data['message']);
			$data['content']	= GT8::getHTML($content, $data);
			$body	= utf8_decode(GT8::getHTML($body, $data));
			$status	= $this->statusCode;
			
			if ( !$to && isset($data['to']) && $data['to']) {
				$to	= $data['to'];
			}
			if ( !$from && isset($data['from']) && $data['from']) {
				$from	= $data['from'];
			}
			
			if ( $this->copyOnDb) {
				if ( !$this->statusId) {
					if ( $this->format == 'JSON') {
						print('//#error: status id is required to copy mail into db!'. PHP_EOL);
					}
					die('Erro: id de pedido ausente!');
				} else if ( !isset($to[0])) {
					if ( $this->format == 'JSON') {
						print('//#error: missing the destination of the e-mail!'. PHP_EOL);
					}
					die('Erro: destinatário ausente!');
				} else if ( !$body) {
					if ( $this->format == 'JSON') {
						print('//#error: Cannot send a mail without content!'. PHP_EOL);
					}
					die('Erro: conteúdo ausente!');
				}
				mysql_query("
					INSERT INTO
						gt8_mail_copy(
							id_stts,
							id_rfrnc,
							sbjct,
							frm,
							dstntn,
							cntnt
						)
						SELECT
							{$this->statusId}, 
							$idRef,
							'$subject',
							'{$from[0]}',
							'{$to[0]}',
							'". mysql_real_escape_string($body) ."'
						FROM
							gt8_mail_copy m
						WHERE
							m.id_stts	= {$this->statusId} AND
							m.id_rfrnc	= $idRef AND
							m.sbjct		= '$subject' AND
							m.frm		= '{$from[0]}' AND
							m.dstntn	= '{$to[0]}' AND
							m.cntnt		= '". mysql_real_escape_string($body) ."'
						HAVING
							COUNT(*) = 0
						
				") or die("//#error: copy error.". (isset($_SESSION['login']) && $_SESSION['login']['level']>6? mysql_error(): ''));
			}
			$this->SetFrom( $from[0], $from[1]);
			$this->AddAddress( $to[0], $to[1]);
			$this->Subject    = utf8_decode( $subject);
			$this->AltBody    = $altBody;
			$this->MsgHTML($body);
			
			if ( $bcc){ 
				$this->AddBcc($bcc);
			}
			
			$b	= parent::Send();
			if ( $this->format == 'JSON') {
				if ( $b) {
					print('//#affected rows: 1'. PHP_EOL);
					print('//#message: E-mail enviado com sucesso!'. PHP_EOL);
				} else {
					print('//#error: '. $this->ErrorInfo . PHP_EOL);
				}
			}
			if ( $this->printAfterSending) {
				print(utf8_encode($body)); 
			}
			return $b;
		}
	}
?>