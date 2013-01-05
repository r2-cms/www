<?php
	$data['title']		= 'Contato GT8';
	$data['mail']		= $data['mail']? RegExp($data['mail'], '[a-zA-Z0-9\.\-\_\@]+'): '';
	$data['site']		= $data['site']? RegExp($data['site'], '[a-zA-Z0-9_\.\-\+\=\,\:\/]+'): '';
	
	$row	= mysql_fetch_assoc(mysql_query("SELECT id AS idAnalytics, id_users, ip, browser, browser_v, OS, referrer FROM gt8_analytics WHERE id = ". $_SESSION['analytics']['id']));
	if ( $row) {
		$data	= array_merge( $data, $row);
	}
	
	if ( $data['id_users']) {
		$row	= mysql_fetch_assoc(mysql_query("SELECT id, name, login FROM gt8_users WHERE id = ". $data['id_users']));
		$data['user']	= '<a href="http://www.gt8.com.br/cms/'. $GT8['admin']['root'] .'/users/'. $data['login'] .'/" >'. $data['name'] .'</a>';
	} else {
		$data['id_users']	= '';
		$data['user']		= '';
	}
	
	$to		= array('roger@gt8.com.br', 'Roger');
	$mail	= $data['mail'];
	$subject = "GT8 - comment from ". $data['name'];
	$content = "
		<p>O usuário {{name}} acaba de postar um comentário. O conteúdo está descrito abaixo.</p>
		<p style='border: 1px solid #CCC; padding: 10px;' > {{message}} </p>
		<p><b><strong>USER INFO</strong></b></p>
		<p>
			Name: {{name}}<br>
			User id: {{id_users}}<br>
			User: {{user}}<br>
			Mail: {{mail}}<br>
			Phone: {{phone}}<br>
			Site: {{site}}<br>
			id Analytics: {{idAnalytics}}<br>
			OS: {{OS}}<br>
			Browser: {{browser}}<br>
			Version: {{browser_v}}<br>
		</p>
		<p style='margin-top:40px;'>www.GT8.com.br</p>
	";
	$altBody	= '';
?>