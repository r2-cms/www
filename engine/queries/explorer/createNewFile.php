<?php
	if ( !defined('CROOT')) {
		die("//#error: Direct access not allowed!<br />Please, contact the site administrator.". PHP_EOL);
	}
	require_once( SROOT ."engine/functions/CheckLogin.php");
	require_once( SROOT .'engine/functions/Pager.php');
	
	/*
		Integer createNewFile( options)
			options:
				idDir		Required directory id
				idUser		default to $_SESSION['login']['id']
				filename
				useIndexFilename	Integer. If no filename is provided and useIndexFilename is set to true, then only a index number will be used as filename
				title
				read_privilege		0,1,2,3,...
				write_privilege		0,1,2,3,... . Default: login.level
				approved	0,1
				type		file(default),directory
				format
	*/
	function createNewFile( $options) {
		$d				= $options['idDir'];
		$filename		= isset($options['filename'])? $options['filename']: '';
		$title			= isset($options['title'])? $options['title']: '';
		$isDirectory	= isset($options['type'])&&$options['type']=='directory'? true: false;
		$readPrivilege	= isset($options['read_privilege'])? (integer)$options['read_privilege']: 0;
		$writePrivilege	= isset($options['write_privilege'])? (integer)$options['write_privilege']: $_SESSION['login']['level'];
		$approved		= isset($options['approved'])? (integer)$options['approved']: 0;
		$format			= isset($options['format'])? $options['format']: 'OBJECT';
		$idUser			= isset($options['idUser'])? $options['idUser']: 0;
		$useIndexFilename	= isset($options['useIndexFilename'])? (integer)$options['useIndexFilename']: 0;
		
		$d	= (integer)$d;
		//$f	= RegExp($f, '[a-zA-Z0-9_\-\.\&]+');
		
		if ( !$d) {
			//die('//#error: Missing id directory!'. PHP_EOL);
			$d	= 0;
		}
		if ( !$idUser) {
			$idUser	= $_SESSION['login']['id'];
		}
		
		if ( $d) {
			$Pager	= Pager(array(
				'sql'		=> 'explorer.list',
				'ids'		=> array(
					array('e.id', $d)
				),
				'foundRows'	=> 1
			));
			
			if ( !isset($Pager['rows'][0]) ) {
				die('//#error: Privilégios insuficientes!'. PHP_EOL);
			}
			$Pager		= $Pager['rows'][0];
		} else {
			$Pager	= array(
				'write_privilege'	=> 10,
				'path'		=> '',
				'dirpath'	=> ''
			);
		}
		$path		= $Pager['path'] . $Pager['filename'] .'/';
		$dirPath	= $Pager['dirpath'];
		$myLevel	= $_SESSION['login']['level'];
		$type		= $isDirectory? 'directory': 'file';
		
		if ( $myLevel < $Pager['write_privilege'] ) {
			die('//#error: Privilégios insuficientes!'. PHP_EOL);
		}
		
		if ( $title) {
			$title	= mysql_real_escape_string($title);
		}
		if ( $filename) {
			$filename	= RegExp(mysql_real_escape_string($filename), '[a-z-A-Z0-9_\.\,\-\(\)]+');
			if ( strpos('#'. $filename, '.' )) {
				$ext		= substr($filename, -strpos('#'.strrev($filename), '.'));
				$filename	= substr($filename, 0, strlen($filename) - strlen($ext));
			} else {
				$ext		= '';
			}
			while (strpos('#'. $filename, '--')) {
				$filename	= str_replace('--', '-', $filename);
			}
			if ( substr($filename,-1) == '-') {
				$filename	= substr( $filename, 0, -1);
			}
			if ( substr($filename,0,1) == '-') {
				$filename	= substr( $filename, 1);
			}
			$filename	= $filename.$ext;
			
			//existe este nome de arquivo?
			$result	= mysql_query("SELECT id FROM gt8_explorer WHERE id_dir = $d AND filename = '$dirName' ") or die('Explorer->editor::ImageEditor::ImageEditor Error:'. mysql_error());
			if ( $row = mysql_fetch_array($result)) {
				die('//#error: Nome de diretório já existente!'. PHP_EOL);
			}
		} else {
			//what's the last new file index?
			$index	= mysql_query("
				SELECT
					id, TRIM(SUBSTRING_INDEX(LOWER(filename), 'novo-arquivo-', -1))+0 AS lastIndex
				FROM
					gt8_explorer
				WHERE
					id_dir = $d AND
					filename LIKE '". ($useIndexFilename?'':'novo-arquivo') ."%'
				ORDER BY
					TRIM(SUBSTRING_INDEX(filename, 'Novo-arquivo-', -1))+0 DESC
				LIMIT
					1
			") or die('createNewFile.select: '. ($_SESSION['login']['level']>6? mysql_error(): ' cannot retrieve file name!').PHP_EOL);
			
			$index	= mysql_fetch_array($index);
			$index	= ( isset($index[1])? $index[1]: 0);
			$index++;
			
			if ( $useIndexFilename) {
				$filename	= $index;
			} else {
				$filename	= "Novo-arquivo-$index";
			}
		}
		if ( $options['debug']==1) {
			print("<h1>". $d ."</h1>".PHP_EOL);
			print("<h1>". $filename ."</h1>".PHP_EOL);
			print("<h1>". $type ."</h1>".PHP_EOL);
		}
		$newDirPath	= $d? $dirPath . $d .'/': '';
		$newPath	= $d? $path: '';
		$mime		= $isDirectory? 'directory': '';
		
		mysql_query("
			INSERT INTO
				gt8_explorer(
					id_users,
					id_dir,
					title,
					filename,
					type,
					mime,
					path,
					dirpath,
					read_privilege,
					write_privilege,
					approved,
					creation,
					modification,
					publish_up
				)
				SELECT
					$idUser,
					$d,
					'$title',
					'$filename',
					'$type',
					'$mime',
					'$newPath',
					'$newDirPath',
					$readPrivilege,
					$writePrivilege,
					$approved,
					NOW(),
					NOW(),
					NOW()
				FROM
					gt8_explorer
				WHERE
					id_dir		= $d AND
					filename	= '$filename' AND
					type
				HAVING
					COUNT(*) = 0
		") or die('//#error: createNewFile.insert: '. ($_SESSION['login']['level']>6? mysql_error(): ' cannot create file!').PHP_EOL);
		$id	= mysql_insert_id();
		
		//insert into explorer_data
		mysql_query("
			INSERT INTO
				gt8_explorer_data( id, thumb, small, preview, data)
			SELECT
				$id, '', '', '', ''
			FROM
				gt8_explorer_data
			WHERE
				id = $id
			HAVING
				COUNT(*) = 0
		") or die('//#error: explorer_data.insert: '. ($_SESSION['login']['level']>6? mysql_error(): ' cannot insert data into db.data!').PHP_EOL);
		
		//update childs count in parents
		if ( $id ) {
			if ( $format == 'JSON') {
				print('//#insert id: '. $id . PHP_EOL);
				print('//#affected rows: 1'. PHP_EOL);
				print('//#message: Arquivo criado com sucesso!'. PHP_EOL);
			}
			$idDir	= $d;
			while ( $idDir) {
				mysql_query("
					UPDATE
						gt8_explorer
					SET
						". ($isDirectory?'folders=folders+1':'files=files+1') ."
					WHERE
						id = $idDir
				");
				$idDir	= mysql_fetch_array(mysql_query("SELECT id_dir FROM gt8_explorer WHERE id = $idDir"));
				$idDir	= $idDir[0];
			}
		}
		
		if ( !$id ){
			die('//#error: Este nome de arquivo já está sendo usado!'. PHP_EOL);
		}
		return $id;
	}
?>
