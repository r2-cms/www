<?php
	if ( !defined('SROOT')) {
		die('//#error: Not allowed in Qu34ie0.U45::insert!'. PHP_EOL);
	}
	/*
		Integer addNewUrlHistory( options)
			Options:
				format
				old
				new
				remarks
				
	*/
	global $GT8;
	require_once( SROOT .'engine/functions/CheckLogin.php');
	
	function addNewUrlHistory( $options) {
		$options['old']		= RegExp($options['old'], '[a-zA-Z0-9\/\:\.\,\%\?\&\#\@\-\+\=]+');
		$options['new']		= RegExp($options['new'], '[a-zA-Z0-9\/\:\.\,\%\?\&\#\@\-\+\=]+');
		$options['remarks']	= isset($options['remarks'])? mysql_real_escape_string($options['remarks']): '';
		$options['format']	= isset($options['format'])? $options['format']: 'OBJECT';
		
		if ( substr($options['old'], 0, 1) !== '/') {
			$options['old']	= '/'. $options['old'];
		}
		if ( substr($options['old'], -1) === '/') {
			$options['old']	= substr($options['old'], 0, -1);
		}
		
		if ( substr($options['new'], 0, 1) !== '/') {
			$options['new']	= '/'. $options['new'];
		}
		if ( substr($options['new'], -1) === '/') {
			$options['new']	= substr($options['new'], 0, -1);
		}
		
		if ( $options['old'] != $options['new']) {
			$urlOld	= $options['old'];
			if ( substr($urlOld, -1) === '/') {
				$urlOld	= substr($urlOld, 0, -1);
			}
			mysql_query("
				INSERT INTO
					gt8_url_history ( old, new, creation, remarks)
				SELECT
					'". $options['old'] ."',
					'". $options['new'] ."',
					NOW(),
					'". $options['remarks'] ."'
				FROM
					gt8_url_history
				WHERE
					old	= '". $urlOld ."' AND
					new	= '". $options['new'] ."'
				HAVING
					COUNT(*) = 0
			") or die('//#error: Valores inválidos!'. ($_SESSION['login']['level']>8? ' '. mysql_error() . PHP_EOL: PHP_EOL));
			mysql_query("
				UPDATE
					gt8_url_history
				SET
					new	= '". $options['new'] ."'
				WHERE
					new = '". $options['old'] ."'
			") or die('//#error: Valores inválidos!'. ($_SESSION['login']['level']>8? ' '. mysql_error() . PHP_EOL: PHP_EOL));
		} else {
			if ( $_SESSION['login']['level'] > 8 && $options['format']=='JSON') {
				print('//#warning: Os urls são idênticos e, por isso, não foram registrados no banco.'. PHP_EOL);
			}
		}
		
		if ( mysql_insert_id()) {
			if ( $options['format'] == 'JSON') {
				print('//#affected rows: '. mysql_affected_rows() . PHP_EOL);
				print('//#id inserted successfully ('. mysql_insert_id() .')'. PHP_EOL);
			}
			require_once( SROOT .'engine/functions/LogAdmActivity.php');
			LogAdmActivity( array(
				"action"	=> "insert",
				"page"		=> "url-history/",
				"name"		=> $options['old'],
				"value"		=> $options['new']
			));
		} else {
			if ( $options['format']=='JSON') {
				print('//#warning: Não foram inseridos registros!'. PHP_EOL);
			}
		}
	}
?>
