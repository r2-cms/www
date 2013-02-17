<?php
	session_start();
	if ( isset($_SESSION['GT8']) && $_SESSION['GT8']) {
		
	} else {
		$_SESSION['GT8']	= array(
			'tstart'	=> time()
		);
	}
	$GT8	= $_SESSION['GT8'];
	define( 'SROOT', substr(__FILE__, 0, strlen(__FILE__)-strlen('engine/connect.php')) );
	define( 'DS', DIRECTORY_SEPARATOR );
	define( 'CROOT', str_repeat('../', (count(explode('/', substr(substr($_SERVER['REQUEST_URI'], 0, (strpos($_SERVER['REQUEST_URI'], '?')?strpos($_SERVER['REQUEST_URI'], '?'):strlen($_SERVER['REQUEST_URI']))), 1)))-1)  -  (count(explode('/', substr(SROOT, strlen($_SERVER['DOCUMENT_ROOT'])+1)))-(DS=='/'? 1: -1))   )); require_once( SROOT.'engine/preferences/user.php');
	define( 'AROOT', CROOT . $GT8['admin']['root']);
	
	if ( isset($_GET['analytics']) && $_GET['analytics']=='GT8' && (isset($GT8['analytics']) && $GT8['analytics']===false)) {
		$_SESSION['analytics-page']	= 1;
		exit;
	}
	if ( !isset($_SESSION['param-cache'])) {
		$_SESSION['param-cache']	= array();
	}
	if ( !isset($_SESSION['login']) ) {
		$_SESSION['login']	= array();
	}
	if ( !isset($_SESSION['login']['level']) ) {
		$_SESSION['login']['level']	= 0;
	}
	if ( !isset($_SESSION['cron'])) {
		$dates	= mysql_fetch_assoc(mysql_query('
			SELECT
				daily, weekly, monthly
			FROM
				gt8_cron
		'));
		$_SESSION['cron']	= array(
			'daily'		=> $dates['daily'],
			'weekly'	=> $dates['weekly'],
			'monthly'	=> $dates['monthly']
		);
	}
	if ( $_SESSION['cron']['daily'] != date('j')) {
		require_once( SROOT .'engine/crons/daily.php');
	}
	if ( $_SESSION['cron']['weekly'] != date('W')) {
		require_once( SROOT .'engine/crons/weekly.php');
	}
	if ( $_SESSION['cron']['monthly'] != date('n')) {
		require_once( SROOT .'engine/crons/monthly.php');
	}
	function RegExp( $value, $reg = '[a-z]+') {
		preg_match( "/". $reg ."/", $value, $__tmp);
		return $__tmp[0];
	}
	if ( isset($_GET['rewrite']) && $_GET['rewrite'] == 1) {
		require_once( SROOT . 'engine/functions/dispatcher.php');
	}
	
?>