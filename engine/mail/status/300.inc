<?php
	$data['title']		= 'Solicitação de redefinição de senha';
	$data['mail']		= $data['mail']? RegExp($data['mail'], '[a-zA-Z0-9\.\-\_\@]+'): '';
	$data['site']		= $data['site']? RegExp($data['site'], '[a-zA-Z0-9_\.\-\+\=\,\:\/]+'): '';
	
	$mail	= $data['mail'];
	$from	= array('envio-automatico@salaodocalcado.com.br', utf8_decode('Salão do Calçado'));
	$data['to']		= array($data['login'], $data['name']);
	
	$subject = "Salão do calçado | Solicitação para redefinir a senha";
	$content = "
		<p>Prezado(a) ##name##,</p>
		<p>Este e-mail foi-lhe enviado conforme sua solicitação para recuperar seu acesso ao site salaodocalcado.com.br. Para redefinir sua senha, basta clicar no link abaixo e você será conduzido à uma página web que lhe permitirá redefinir uma nova senha.</p>
		<p>Para sua segurança, este link expirará em 30 minutos após o envio deste e-mail.</p>
		<p>
			<a href='http://www.salaodocalcado.com.br/{{account-token-path}}' style='text-decoration:underline; color:#1A92B8;' >Redefinir sua senha</a>
		</p>
		<hr >
		<p style='font-size:11px;' >
			Se você não solicitou este e-mail, não se preocupe. Sua conta continua segura e ninguém terá acesso a ela. Existe a possibilidade de alguém ter digitado incorretamente um e-mail similar enquanto tentava redefinir sua própria senha.
		</p>
		<p>
			Em caso de dúvidas, entre em contato conosco {{PARAM:phone-comercial}}, {{PARAM:opening-hours}}
		</p>
	";
	$altBody	= '';
?>