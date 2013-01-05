<?php
	if ( !defined('SROOT')) {
		die('//#error: Not allowed in Qu34ie0.U45::update!'. PHP_EOL);
	}
	/*
		Integer updateStats( id)
	*/
	function updateStats( $id) {
		$id	= (integer)$id;
		
		if ( $id) {
			mysql_query("
				UPDATE
					gt8_url_history
				SET
					total	= total + 1
				WHERE
					id = $id
			") or die('//#error: Erro desconhecido!'. ($_SESSION['login']['level']>8? ' '. mysql_error() . PHP_EOL: PHP_EOL));
		} else {
			print('//#debug: Id inválido para atualizar a contagem de urls!'. PHP_EOL);
		}
	}
?>
