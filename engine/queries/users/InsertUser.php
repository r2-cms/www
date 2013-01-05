<?php
	if ( !defined('SROOT')) {
		die('Not allowed in Qu34ie0.I20e41U0e4!');
	}
	/*
		Integer InsertUser( options)
			Options:
				login
				level
				name
				genre						M,F
				enabled						0,1
				approval_level_required		8
				createImg					false|true
				remarks
				format
	*/
	require_once( SROOT .'engine/functions/Pager.php');
	
	function InsertUser( $options) {
		$login		= substr(RegExp($options['login'], '[A-Za-z0-9_\-\.\:\@]+'), 0, 64);
		$level		= isset($options['level'])? (integer)$options['level']: 1;
		$name		= isset($options['name'])? mysql_real_escape_string($options['name']): '';
		$birth		= isset($options['birth'])? RegExp($options['birth'], '[0-9\-\/]+'): '01/01/1900';
		$genre		= isset($options['genre'])? RegExp($options['genre'], 'M|F'): 'M';
		$natureza	= isset($options['natureza'])? RegExp($options['natureza'], 'J|F'): 'F';
		$cpfcnpj	= isset($options['cpfcnpj'])? RegExp($options['cpfcnpj'], '[0-9\.\-\/]+'): '';
		$document	= isset($options['document'])? RegExp($options['document'], '[0-9\.\-\/a-zA-Z\ ]+'): '';
		$enabled	= isset($options['enabled'])? (integer)$options['enabled']: 0;
		$approval	= (integer)(isset($options['approval_level_required'])? $options['approval_level_required']: min($_SESSION['login']['level'], 7));//7==Manager
		$remarks	= isset($options['remarks'])? mysql_real_escape_string($options['remarks']): '';
		$format		= isset($options['format'])? $options['format']: 'JSON';
		$createImg	= isset($options['createImg'])? $options['createImg']: false;
		$pass		= isset($options['pass'])? RegExp($options['pass'], '[a-zA-Z0-9]+'): 'c1ea0c5e8e08adb32fcce5769c6f1c52';//123456
		
		if ( !$login) {
			if ( $format === 'JSON') {
				die('//#error: login inválido!'. PHP_EOL);
			} else if ( $format === 'OBJECT'){
				return 'invalid login';
			} else {
				return false;
			}
		}
		
		preg_match('#([0-9]{2}).+([0-9]{2}).+([0-9]{4})#', $birth, $birth);
		$birth	= $birth[3] .'-'. $birth[2] .'-'. $birth[1];
		
		$level	= $level? $level: 1;
		$level	= min($level, isset($_SESSION['login']['level'])? $_SESSION['login']['level']: 1);
		
		//já existe?
		$hlogin	= md5(strtolower($login));
		$row	= mysql_query("SELECT id, login FROM gt8_users WHERE hlogin  = '$hlogin' ") or die($_SESSION['login']['level']>7? "//#error: SQL SELECT Error:". mysql_error() . PHP_EOL: '//#error: Erro ao acessar o banco de dados!'. PHP_EOL);
		$row	= mysql_fetch_assoc($row);
		
		if ( isset($row['id'])) {
			if ( $format === 'JSON') {
				die('//#error: login já existe. Por favor, escolha outro!'. PHP_EOL);
			} else if ( $format === 'OBJECT'){
				return 'login already exists';
			} else {
				return false;
			}
		}
		
		//com senha padrão: 123456. Deve-se criar um script que obrigue a pessoa a trocar essa senha no primeiro acesso
		$r	= mysql_query("
			INSERT INTO gt8_users(
				login,
				hlogin,
				level,
				name,
				birth,
				genre,
				natureza,
				cpfcnpj,
				document,
				enabled,
				approval_level_required,
				remarks,
				creation, modification,
				pass
			) VALUES(
				'$login',
				'$hlogin',
				$level,
				'$name',
				'$birth',
				'$genre',
				'$natureza',
				'$cpfcnpj',
				'$document',
				$enabled,
				$approval,
				'$remarks',
				NOW(), NOW(),
				'$pass'
			)
		") or die($_SESSION['login']['level']>7? "//#error: SQL INSERT Error:". mysql_error() . PHP_EOL: '//#error: Erro ao acessar o banco de dados!'. PHP_EOL);
		
		$id	= mysql_insert_id();
		if ( $id ) {
			
			if ( $format == 'JSON') {
				print('//#affected rows: 1!'. PHP_EOL);
				print('//#id inserted successfully ('. $id .')!'. PHP_EOL);
				print('//#message: Usuário "'. $login .'" criado com sucesso!'. PHP_EOL);
			}
			if ( $level > 3) {
				require_once( SROOT .'engine/functions/LogAdmActivity.php');
				LogAdmActivity( array(
					"action"	=> "insert",
					"page"		=> "users/",
					"name"		=> '',
					"value"		=> '',
					"idRef"		=> $id
				));
			}
			if ( $createImg) {
				require_once( SROOT.'engine/queries/explorer/createNewFile.php');
				$idDir	= Pager(array(
					'sql'	=> 'explorer.list',
					'ids'	=> array(
						array('e.id_dir', '0')
					),
					'required'	=> array(
						array('e.filename', 'users')
					)
				));
				$idDir	= $idDir['rows'][0];
				$idDir	= $idDir['id'];
				createNewFile(array(
					'idDir'	=> $idDir,
					'filename'	=> $login,
					'title'	=> (empty($name)? $login: $name),
					'type'	=> 'directory',
					'approved'	=> 1,
					'format'	=> $format,
					'read_privilege'	=> 1,
					'write_privilege'	=> $_SESSION['login']['level']
				));
				
			}
			
		} else {
			if ( $format == 'JSON') {
				print('//id inserted successfully');
			}
		}
		return $id;
	}
?>