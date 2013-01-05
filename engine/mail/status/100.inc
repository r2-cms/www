<?php
	$data['title']		= 'Contato Funicar';
	$data['mail']		= $data['mail']? RegExp($data['mail'], '[a-zA-Z0-9\.\-\_\@]+'): '';
	$data['phone']		= $data['phone']? RegExp($data['phone'], '[a-zA-Z0-9\.\-\_\@\(\)\+\-\ ]+'): '';
	$data['subject']	= $data['subject']? str_replace('<>%$;{}[]', '-', $data['subject']): '';
	$data['name']		= $data['name']? str_replace('<>%$;{}[]', '-', $data['name']): '';
	$data['company']	= $data['company']? str_replace('<>%$;{}[]', '-', $data['company']): '';
	$data['phone']		= $data['phone']? RegExp($data['phone'], '[0-9\+\ \(\)\-\.]+'): '';
	
	$to		= array('belk@funicar.com.br', 'Belk');
	$mail	= $data['mail'];
	$data['to']	= $to[0];
	$subject	= utf8_encode($data['subject']);
	
	$content = "
		<p style='padding: 10px; padding-left: 20px; ' >
			<span style='font-weight:bold; color:#222; ' ><b>Nome: </b></span>[[name]]<br>
			<span style='font-weight:bold; color:#222; ' ><b>Empresa: </b></span>{{company}}<br>
			<span style='font-weight:bold; color:#222; ' ><b>Telefone: </b></span>{{phone}}<br>
			<span style='font-weight:bold; color:#222; ' ><b>E-mail: </b></span>{{mail}}<br>
			<span style='font-weight:bold; color:#222; ' ><b>Assunto: </b></span>##subject##<br>
		</p>
		<p style='padding:10px; padding-left:20px; ' >##message##</p>
		<p style='margin-top:40px; padding-left:20px; ' ><a href='http://www.funicar.com.br/' >www.funicar.com.br</a></p>
	";
	$altBody	= '';
?>