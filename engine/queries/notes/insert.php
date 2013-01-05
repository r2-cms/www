<?php
	require_once($_SERVER["DOCUMENT_ROOT"] ."/engine/connect.php");
	/**
	 * @function insert( options)
	 * @option idLibrary required
	 * @option name
	 * @option mail
	 * @option site
	 * @option notes
	*/
	$GT8['notes']	= isset($GT8['notes'])? $GT8['notes']: array();
	function no1es_insert( $props) {
		$_SESSION['notes']	= isset($_SESSION['notes'])? $_SESSION['notes']: array();
		
		$idLibrary	= (integer)$props['idLibrary'];
		$idAnalytics= (integer)$props['idAnalytics'];
		$name		= addslashes(substr(str_replace(str_split('\'"\\%$#@!ˆ&*()'), '', $props['name']), 0, 48));
		$mail		= substr(RegExp($props['mail'], '[a-zA-Z_0-9\.\-\@]+'), 0, 48);
		$site		= addslashes(substr(str_replace(array("'", '"', '\\', '&#'), '', $props['site']), 0, 128));
		$notes		= $props['notes'];
		
		if ( !$idLibrary) {
			die('idLibrary missing');
		} else if ( empty($name)) {
			die('name missing');
		} else if ( empty($mail)) {
			die('mail missing');
		} else if ( empty($notes)) {
			die('notes missing');
		}
		
		//o usuário deve estar ao menos dois minutos no site, do contrário saberemos que é SPAM
		if ( !isset($_SESSION['tstart']) || time()-$_SESSION['tstart'] < 120) {
			die('error 1'. time()-$_SESSION['tstart']);
		//o usuário pode postar o máximo de uma mensagem a cada dez minutos
		} else if ( isset($_SESSION['notes']['last-note']) && time()-$_SESSION['notes']['last-note'] < 400) {
			die('error 2'. (time()-$_SESSION['notes']['last-note']));
		//check final. Veja se o usuário não é blacklisted
		} else {
			global $GT8;
			
			require_once($GT8['root'] .'queries/blacklist/getByIP.php');
			if ( $GT8['blacklist']['getByIP']() ) {
				//usuário em lista negra
				die('error 3');
			}
		}
		
		if ( !$idAnalytics) {
			$idAnalytics	= $_SESSION["analytics"]['id'];
		}
		if ( !$idAnalytics) {
			die('Missing analytics');
		}
		
		//notes
		$notes		= 
			str_replace(
				array('&lt;script&gt;', '&lt;/script&gt;'),
				array('<pre class="brush:js" >', '</pre>'),
				str_replace(PHP_EOL, '<br />', htmlentities($_POST['notes']))
			)
		;
		$notes		= substr(addslashes(str_replace('	', '&nbsp;&nbsp;&nbsp;&nbsp;', $notes)), 0, 2048);
		
		mysql_query("
			INSERT INTO
				notes(
					id_library,
					id_analytics,
					name,
					mail,
					site,
					comment,
					inactive
				) VALUES(
					$idLibrary,
					$idAnalytics,
					'$name',
					'$mail',
					'$site',
					'$notes',
					1
				)
		") or die(mysql_error());
		
		$_SESSION['notes']['last-note']	= time();
		
		//notifique o moderador
		/*
		require_once( $GT8['root'] .'engine/mail.php');
		jCube['engine']['mail']['send'](array(
			'status'	=> 234,
			'id'		=> mysql_insert_id()
		));
		*/
		
		
		return true;
	}
	
	$GT8['notes']['insert']	= no1es_insert;
?>