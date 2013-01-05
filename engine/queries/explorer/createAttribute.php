<?php
	if ( !defined('CROOT')) {
		die("//#error: Direct access not allowed!<br />Please, contact the site administrator.". PHP_EOL);
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT .'engine/functions/Pager.php');
	/*
		Integer createAttribute.php( options)
			options:
				id			Required directory ID
				attribute	Required attribute name
				type		string|integer|float|enum(;deliminitator;)
				level		all,registered,customer,operacional,Designer,etc
				prefix
				suffix
				format
	*/
	function createAttribute( $options) {
		$options['id']		= (integer)$options['id'];
		$options['attribute']	= mysql_real_escape_string(str_replace(str_split('<>{}[]\\'), '-', $options['attribute']));
		$options['type']	= mysql_real_escape_string(str_replace(str_split('<>{}[]\\'), '-', $options['type']));
		$options['level']	= RegExp($options['level'], '[a-zA-Z0-9\_\-]+');
		$options['prefix']	= mysql_real_escape_string(str_replace(str_split('<>{}[]\\'), '-', $options['prefix']));
		$options['suffix']	= mysql_real_escape_string(str_replace(str_split('<>{}[]\\'), '-', $options['suffix']));
		
		if ( !$options['id']) {
			die('//#error: Missing id directory!'. PHP_EOL);
		}
		
		$Pager	= Pager(array(
			'sql'		=> 'explorer.list',
			'ids'		=> array(
				array('e.id', $options['id'])
			),
			'foundRows'	=> 1
		));
		$Pager		= $Pager['rows'][0];
		$myLevel	= $_SESSION['login']['level'];
		
		if ( $myLevel < $Pager['write_privilege'] ) {
			die('//#error: writing privilege required!'. PHP_EOL);
		}
		
		mysql_query("
			INSERT INTO
				gt8_explorer_attributes(
					id_dir,
					attribute,
					type,
					level,
					prefix,
					suffix,
					creation
				)
				SELECT
					". $options['id'] .",
					'". $options['attribute'] ."',
					'". $options['type'] ."',
					'". $options['level'] ."',
					'". $options['prefix'] ."',
					'". $options['suffix'] ."',
					NOW()
				FROM
					gt8_explorer_attributes
				WHERE
					id_dir		= ". $options['id'] ." AND
					attribute	= '". $options['attribute'] ."' AND
					type		= '". $options['type'] ."'
				HAVING
					COUNT(*) = 0
		") or die('//#error: Explorer.createAttribute fail'. ($_SESSION['login']['level']>6? ': '. mysql_error(): '.<br />Please, contact the site administrator and inform this error.') .PHP_EOL);
		$id	= mysql_insert_id();
		
		require_once( SROOT .'engine/functions/LogAdmActivity.php');
		if ( $id ){
			LogAdmActivity(array(
				'action'	=> 'insert',
				'page'		=> 'explorer/',
				'name'		=> 'attribute',
				'value'		=> $options['attribute'],
				'idRef'		=> $options['id']
			));
			print('//#insert id: '. $id . PHP_EOL);
			print('//#affected rows: 1'. PHP_EOL);
			print('//#message: Atributo criado com sucesso!'. PHP_EOL);
		} else {
			die('//#error: O atributo já existe!'. PHP_EOL);
		}
		return $id;
	}
?>
