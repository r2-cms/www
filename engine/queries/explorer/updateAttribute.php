<?php
	if ( !defined('CROOT')) {
		die("//#error: Direct access not allowed!<br />Please, contact the site administrator.". PHP_EOL);
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT ."engine/functions/Pager.php");
	/*
		Integer updateAttribute( options)
			options:
				id			Required attribute ID
				attribute	attribute name
				type		string|integer|float|enum(;deliminitator;)
				level		all,registered,customer,operacional,Designer,etc
				prefix
				suffix
				format
	*/
	function updateAttribute( $options) {
		$options['id']		= (integer)$options['id'];
		$options['attribute']	= mysql_real_escape_string(str_replace(str_split('<>{}[]\\'), '-', $options['attribute']));
		$options['type']	= mysql_real_escape_string(str_replace(str_split('<>{}[]\\'), '-', $options['type']));
		$options['level']	= RegExp($options['level'], '[a-zA-Z0-9\_\-]+');
		$options['prefix']	= mysql_real_escape_string(str_replace(str_split('<>{}[]\\'), '-', $options['prefix']));
		$options['suffix']	= mysql_real_escape_string(str_replace(str_split('<>{}[]\\'), '-', $options['suffix']));
		
		if ( !$options['id']) {
			die('//#error: Missing attribute id!'. PHP_EOL);
		}
		$idFile	= mysql_fetch_array(mysql_query("SELECT id_dir FROM gt8_explorer_attributes WHERE id = ". $options['id'])); $idFile = $idFile[0];
		
		$Pager	= Pager(array(
			'sql'		=> 'explorer.list',
			'ids'		=> array(
				array('e.id', $idFile)
			),
			'foundRows'	=> 1,
			'limit'		=> 1
		));
		$Pager		= $Pager['rows'][0];
		$myLevel	= $_SESSION['login']['level'];
		
		if ( $myLevel < $Pager['write_privilege']) {
			die('//#error: Você não tem privilégios suficientes para atualizar o atributo deste arquivo!'. PHP_EOL);
		}
		
		if ( $Pager['locked'] == 1) {
			die('//#error: O arquivo está bloqueado para edição!'. PHP_EOL);
		}
		
		mysql_query("
			UPDATE
				gt8_explorer_attributes
			SET
					attribute	= '". $options['attribute'] ."',
					type		= '". $options['type'] ."',
					level		= '". $options['level'] ."',
					prefix		= '". $options['prefix'] ."',
					suffix		= '". $options['suffix'] ."',
					modification	= NOW()
				WHERE
					id		= ". $options['id'] ."
		") or die('//#error: Explorer.updateAttribute fail'. ($_SESSION['login']['level']>6? ': '. mysql_error(): '.<br />Please, contact the site administrator and inform this error.') .PHP_EOL);
		$affected	= mysql_affected_rows();
		
		if ( $affected ){
			require_once( SROOT .'engine/functions/LogAdmActivity.php');
			LogAdmActivity(array(
				'action'	=> 'update',
				'page'		=> 'explorer/',
				'name'		=> 'attribute',
				'value'		=> $options['attribute'] .' - '. $options['prefix'] .' - '. $options['suffix'],
				'idRef'		=> $options['id']
			));
			print('//#affected rows: '. $affected . PHP_EOL);
			print('//#message: Atributo atualizado com sucesso!'. PHP_EOL);
		} else {
			die('//#error: Este atributo com essa definição já existe!'. PHP_EOL);
		}
		return $id;
	}
?>
