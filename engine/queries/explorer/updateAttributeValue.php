<?php
	if ( !defined('CROOT')) {
		die("//#error: Direct access not allowed!<br />Please, contact the site administrator.". PHP_EOL);
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/Pager.php");
	/*
		Integer updateAttributeValue( options)
			options:
				id			Required attribute id
				idFile		Required explorer file id
				value		attribute value
				format
	*/
	function updateAttributeValue( $options) {
		$options['id']		= (integer)$options['id'];
		$options['idFile']		= (integer)$options['idFile'];
		$options['value']	= mysql_real_escape_string(str_replace(str_split('<>${}[]\\'), '-', $options['value']));
		
		if ( !$options['id']) {
			die('//#error: Missing attribute id!'. PHP_EOL);
		}
		if ( !$options['idFile']) {
			die('//#error: Missing explorer id!'. PHP_EOL);
		}
		
		$row	= mysql_fetch_array(mysql_query("SELECT id_dir, level+0, attribute FROM gt8_explorer_attributes WHERE id = ". $options['id']));
		$idDir = $row[0];
		$level = $row[1];
		$options['attribute'] = mysql_real_escape_string($row[2]);
		
		if ( !$_SESSION['login']['I am Darth Vader']) {
			//tem PRIVILÉGIO no ARQUIVO?
			$Pager	= Pager(array(
				'sql'		=> 'explorer.list',
				'ids'		=> array(
					array('e.id', $options['idFile'])
				),
				'foundRows'	=> 1,
				'limit'		=> 1
			));
			$Pager		= $Pager['rows'][0];
			$myLevel	= $_SESSION['login']['level'];
			
			if ( $myLevel < $Pager['write_privilege'] && $_SESSION['login']['id'] != $Pager['id_user'] ) {
				die('//#error: Você não tem privilégios suficientes para atualizar este arquivo!'. PHP_EOL);
			}
			
			//is locked?
			if ( $Pager['locked'] == 1) {
				die('//#error: O arquivo está bloqueado para edição!'. PHP_EOL);
			}
			//tem PRIVILÉGIO no ATRIBUTO?
			if ( $myLevel < $level ) {
				die('//#error: Você não tem privilégios suficientes para atualizar este atributo!'. PHP_EOL);
			}
		}
		
		//existe o attributo?
		$row	= mysql_fetch_array(mysql_query("SELECT id FROM gt8_explorer_attributes_value WHERE id_explorer = {$options['idFile']} AND id_attributes = ". $options['id']));
		$affected	= 0;
		if ( !isset($row['id'])) {
			mysql_query("
				INSERT INTO
					gt8_explorer_attributes_value(
						id_attributes,
						id_explorer,
						value,
						creation,
						modification
					) VALUES (
						{$options['id']},
						{$options['idFile']},
						'{$options['value']}',
						NOW(),
						NOW()
					)
					
			") or die("//#error: Não foi possível atualizar o atributo agora.<br />Por favor, tente mais tarde.". ($_SESSION['login']['level']>7? mysql_error(): ''). PHP_EOL);
			$affected	= mysql_affected_rows();
		}
		
		if ( !$affected) {
			mysql_query("
				UPDATE
					gt8_explorer_attributes a
					JOIN gt8_explorer_attributes_value v ON a.id = v.id_attributes
				SET
						v.value			= '". $options['value'] ."'
					WHERE
						a.id			= ". $options['id'] ." AND
						v.id_explorer	= {$options['idFile']}
						
			") or die('//#error: Explorer.updateAttributeValue fail'. ($_SESSION['login']['level']>7? ': '. mysql_error(): '.<br />Please, contact the site administrator and inform this error.') .PHP_EOL);
			$affected	= mysql_affected_rows();
		}
		if ( $affected ){
			require_once( SROOT .'engine/functions/LogAdmActivity.php');
			LogAdmActivity(array(
				'action'	=> 'update',
				'page'		=> 'explorer/',
				'name'		=> 'attribute',
				'value'		=> $options['attribute'] .' - '. $options['value'],
				'idRef'		=> $options['id']
			));
			print('//#affected rows: '. $affected . PHP_EOL);
			print('//#message: Atributo atualizado com sucesso!'. PHP_EOL);
		} else {
			print('//#message: Atributo já atualizado!'. PHP_EOL);
		}
		return $id;
	}
?>
