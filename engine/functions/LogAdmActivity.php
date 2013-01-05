<?php
	function LogAdmActivity( $props) {
		/* exemplo
		LogAdmActivity( array(
			"action"	=> "update",
			"page"		=> "privileges",
			"name"		=> "senha",
			"value"		=> '654321',
			"idRef"		=> "123545"
			"remarks"	=> "Alteração de senha"
		));
		*/
		if ( isset($props['page'])) {
			$page	= $props['page'];
		} else {
			$page	= $_SERVER["PHP_SELF"];
			$page	= substr($page, 0, strpos($page, '/index.php')+1);
			$page	= str_replace('/jsAdmin/', '', $page);
		}
		$page		= mysql_real_escape_string($page);
		$idRef		= (integer)$props["idRef"];
		$idUser		= (integer)$_SESSION["login"]["id"];
		$idAP		= (integer)$_SESSION['analytics-page'];
		
		$action		= mysql_real_escape_string($props["action"]);
		$name		= mysql_real_escape_string($props["name"]);
		$value		= mysql_real_escape_string($props["value"]);
		$remarks	= mysql_real_escape_string($props["remarks"]);
		
		$sql	= "
			INSERT INTO
				gt8_analytics_adm(
					id_users,
					id_reference,
					id_analytics_page,
					
					page,
					action,
					name,
					value,
					remarks
				) VALUES (
					$idUser,
					$idRef,
					$idAP,
					
					'$page',
					'$action',
					'$name',
					'$value',
					'$remarks'
				)
		";
		//die($sql);
		mysql_query($sql) or die("//#error: ". isset($_SESSION['login']['level']) && $_SESSION['login']['level']>7? mysql_error(): 'Erro ao registrar atividade!'. PHP_EOL);
	}
	
?>