<?php
	if ( !defined('CROOT')) {
		die("//#error: Direct access not allowed!<br />Please, contact the site administrator.". PHP_EOL);
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT .'engine/functions/Pager.php');
	/*
		Integer deleteAttribute( options)
			options:
				id			Required attribute ID
				format
	*/
	function deleteAttribute( $options) {
		$options['id']		= (integer)$options['id'];
		
		if ( !$options['id']) {
			die('//#error: Missing attribute id!'. PHP_EOL);
		}
		
		$row	= mysql_fetch_array(mysql_query("SELECT id_dir, attribute, type FROM gt8_explorer_attributes WHERE id = ". $options['id']));
		$idFile		= $row[0];
		$attribute	= $row[1];
		$type		= $row[2];
		
		$Pager	= Pager(array(
			'sql'		=> 'explorer.list',
			'ids'		=> array(
				array('e.id', $idFile)
			),
			'limit'	=> 1,
			'foundRows'	=> 1
		));
		$Pager		= $Pager['rows'][0];
		$myLevel	= $_SESSION['login']['level'];
		print("<h1>". $myLevel ."</h1>".PHP_EOL);
		print("<h1>". $Pager['write_privilege'] ."</h1>".PHP_EOL);
		if (
			($myLevel < $Pager['write_privilege']) ||
			( $myLevel == $Pager['write_privilege'] && $Pager['write_privilege'] < 3 )
		) {
			die('//#error: Você não tem privilégios suficientes para excluir este atributo!'. PHP_EOL);
		}
		
		mysql_query("
			DELETE FROM
				gt8_explorer_attributes
			WHERE
				id	= ". $options['id'] ."
		") or die('//#error: Explorer.deleteAttribute fail'. ($_SESSION['login']['level']>6? ': '. mysql_error(): '.<br />Please, contact the site administrator and inform this error.') .PHP_EOL);
		$id	= mysql_affected_rows();
		
		require_once( SROOT .'engine/functions/LogAdmActivity.php');
		if ( $id ){
			LogAdmActivity(array(
				'action'	=> 'delete',
				'page'		=> 'explorer/',
				'name'		=> 'attribute',
				'value'		=> $attribute ." ($type)",
				'idRef'		=> $options['id']
			));
			print('//#affected rows: 1'. PHP_EOL);
			print('//#message: Atributo excluído com sucesso!'. PHP_EOL);
		} else {
			die('//#error: O atributo já foi excluído - eu acho :('. PHP_EOL);
		}
		return $id;
	}
?>
