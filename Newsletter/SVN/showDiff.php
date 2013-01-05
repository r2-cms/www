<?php
	require_once( $_SERVER["DOCUMENT_ROOT"] ."/engine/connect.php");
	require_once( $_SERVER["DOCUMENT_ROOT"] ."/engine/Array/xml2array/xml2array.php");
	$bin	= 'svn';
	$src	= '';//'svn://r2-cms.web623.uni5.net/r2-cms/r2/trunk';
	if ( strpos('#'. $_SERVER['DOCUMENT_ROOT'], 'Roger') ) {
		$bin	= '/opt/subversion/bin/svn';
	}
	$rev	= (integer)$_GET['revision'];
	$_GET['file']	= substr($_GET['file'], 1);
	$file	= RegExp($_GET['file'], '[a-zA-Z0-9_\/\.\,\-]+');
	
	//basic validations and security checking
	if ( strlen($file) != strlen($_GET['file'])) {
		die('Not allowed (1)');
	}
	if ( !$rev) {
		die('Not allowed (2)');
	}
	
	$result	= shell_exec("$bin diff {$GT8['root']}$file -r $rev --username roger --password munique20");
	
	
	$result	= explode(PHP_EOL, $result);
	
	$html	= '';
	for ( $i=4; $i<count($result); $i++) {
		$crr	= $result[$i];
		if ( substr($crr, 0, 3) == '+++') {
			$crr	= "<span class='lhead' >$crr</span>";
		} else if ( substr($crr, 0, 3) == '---') {
			$crr	= "<span class='lhead' >$crr</span>";
		} else if ( substr($crr, 0, 1) == '+') {
			$crr	= "<span class='plus' >$crr</span>";
		} else if ( substr($crr, 0, 1) == '-') {
			$crr	= "<span class='minus' >$crr</span>";
		} else if ( substr($crr, 0, 2) == '@@') {
			$crr	= "<span class='line' ". ($i==4? 'style="margin:0px;" ':'') ." >$crr</span>";
		} else {
			$crr	= "<span>$crr</span>";
		}
		$html	.= $crr;
	}
	print($html);
?>