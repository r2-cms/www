<?php
	if ( !defined('SROOT')) {
		die('Required constant not defined! (A.E::LoadPhisic)');
	}
	$sizeRequested	= 'data';
	if ( isset($_GET['thumb'])) {
		$sizeRequested	= 'thumb';
	} else if ( isset($_GET['small'])) {
		$sizeRequested	= 'small';
	} else if ( isset($_GET['preview'])) {
		$sizeRequested	= 'preview';
	} else if ( isset($_GET['regular'])) {
		$sizeRequested	= 'regular';
	}
	require_once( SROOT .'engine/queries/explorer/updateCacheViews.php');
	function __LoadPhisicImage( $fileName, $D=NULL, $f=NULL){
		if ( $D && $f) {
			updateCacheViews( $D, $f);
		}
		ob_start();
		header("Cache-Control: private, max-age=10800, pre-check=10800");
		header("Pragma: private");
		header("Expires: " . date( DATE_RFC822, strtotime(" 5 day")));
		header('Last-Modified: '. date( DATE_RFC822, filemtime($fileName)));
		header("Content-Type: image/". substr($fileName, -strpos(strrev($fileName), '.')));
		print(file_get_contents($fileName));
		ob_end_flush();
		die();
	}
	$f	= RegExp($_GET['path'], '[a-zA-Z0-9\-\_\&\.\/\@]+');
	//certifique-se que não há fraude na url
	if ( $GT8['explorer']['root'] == substr($f, 0, strlen($GT8['explorer']['root']) )) {
		$f	= substr($f, strlen($GT8['explorer']['root']));
	} else {
		die('Not found!');
	}
	if ( $f=='') {
		__LoadPhisicImage( SROOT ."imgs/gt8/not-found-regular.png");
		die();
	}
	$D	= substr($f, 0, -strpos(strrev($f), '/', 1));
	$f	= substr($f, strlen($D));
	$isDir	= substr($f, -1) == '/';
	if ( $isDir) {
		$f	= substr($f, 0, -1);
	}
	if ( !$f ) {
		__LoadPhisicImage( SROOT ."imgs/gt8/not-found-regular.png");
		die();
	}
	//if the image is cached send a 304
	if ( isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
		updateCacheViews( $D, $f);
		header('Last-Modified: '.$_SERVER['HTTP_IF_MODIFIED_SINCE'], true, 304);
		exit;
	}
	function __LoadAndPrintData( $D, $f) {
		global $sizeRequested;
		updateCacheViews( $D, $f);
		$data	= mysql_query("
			SELECT
				bl.id,
				UNIX_TIMESTAMP(e.modification) AS modification,
				SUBSTRING_INDEX(e.filename, '.', -1) AS ext,
				e.mime, e.type,
				bl.$sizeRequested AS data
			FROM
				gt8_explorer e
				LEFT JOIN gt8_explorer_data bl ON e.id = bl.id
			WHERE
				e.path = '$D' AND e.filename = '$f'
		") or die('Not Found (Pls, remove me before publishiing): '. mysql_error());
		$data	= mysql_fetch_assoc($data);
		ob_start();
		header("Cache-Control: private, max-age=10800, pre-check=10800");
		header("Pragma: private");
		header("Expires: " . date( DATE_RFC822, strtotime(" 5 day")));
		header('Last-Modified: '. date( DATE_RFC822, $data['modification']));
		if ( strpos('#'. $data['mime'], 'image/') ) {
			header("Content-Type: ". $data['mime']);
			print($data['data']);
		} else if ( $data['type'] == 'directory') {
			if ( empty($data['data'])) {
				header("Content-Type: image/png");
				print(file_get_contents(SROOT ."imgs/gt8/folder-generic-large.png"));
			} else {
				header("Content-Type: ". $data['mime']);
				print($data['data']);
			}
		} else {
			$mime	= $data['mime'];
			$data	= $data['data'];
			
			if ( !$mime) {
				$mime	= 'application-octet-stream-large';
			}
			
			if ( empty($data)) {
				if ( file_exists( SROOT.'imgs/gt8/mime/'.str_replace('/', '-', $mime) . '-large.png') ) {
					$data	= file_get_contents(SROOT .'imgs/gt8/mime/'.str_replace('/', '-', $mime) . '-large.png');
					$mime	= 'image/png';
				} else {
					$data	= file_get_contents(SROOT ."imgs/gt8/newfile-regular.png");
					$mime	= 'image/png';
				}
			} else {
				
			}
			header("Content-Type: ". $mime);
			print( $data);
		}
		ob_end_flush();
		die();
	}
	
	//ACCESS CONTROL *******************************************************
	$result	= mysql_query("SELECT e.id_users AS id_user, e.id, e.read_privilege, e.write_privilege FROM gt8_explorer e WHERE path = '$D' AND filename = '$f'");
	if ( ($row = mysql_fetch_assoc($result))) {
		$myLevel	= isset($_SESSION['login']['level'])? $_SESSION['login']['level']: 1;
		if ( $myLevel > $row['read_privilege'] || (isset($_SESSION['login']['id']) && $row['id_user'] == $_SESSION['login']['id'])) {
			//OK
		} else if ( $myLevel == $row['read_privilege']) {
			//OK
		} else {
			__LoadPhisicImage( SROOT ."imgs/gt8/forbidden-regular.png");
		}
	} else {
		//OK
		//the file still does not exists
		__LoadPhisicImage( SROOT ."imgs/gt8/newfile-regular.png", $D, $f);
	}
	
	//load the data based on its dir id and filename
	__LoadAndPrintData($D, $f);
?>