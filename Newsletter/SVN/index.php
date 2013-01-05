<?php
	require_once( $_SERVER["DOCUMENT_ROOT"] ."/engine/Array/xml2array/xml2array.php");
	$bin	= 'svn';
	$src	= 'svn://r2-cms.web623.uni5.net/r2-cms/r2/trunk';
	if ( strpos('#'. $_SERVER['DOCUMENT_ROOT'], 'Roger') ) {
		$bin	= '/opt/subversion/bin/svn';
	}
	$xml	= shell_exec("$bin log $src --xml --username roger --password munique20");
	$xml	= str_replace(
		array(
			'<logentry'. PHP_EOL .'   revision="',
			'">'. PHP_EOL .'<author>'
		), array(
			'<logentry><revision>',
			'</revision><author>'
		),
		$xml
	);
	
	$rows	= array(xml2array($xml));
	$rows	= $rows[0]['logentry'];
	
	$xml	= '<?xml version="1.0" encoding="utf-8"?>';
	$xml		.= '<feed xmlns="http://www.w3.org/2005/Atom">';
	$xml			.= '<title>R2 Subversion</title>';
	$xml				.= '<generator uri="http://www.r2-cms.com.br/Newsletter/SVN/" version="0.1">Feed Generator</generator>';
	$xml				.= '<updated>'. $rows[0]['date'] .'</updated>';
	for( $i=0; $i<count($rows); $i++) {
		$row	= $rows[$i];
		$rv		= $row['revision'];
		$author	= ucwords($row['author']);
		$date	= $row['date'];
		$msg	= !empty($row['msg'])? $row['msg']: '  ';
		
		$xml	.= "".
			"<entry>".
				"<id>http://www.r2-cms.com.br/Newsletter/SVN/$rv/</id>".
				"<updated>$date</updated>".
				"<title>$rv: Alteração feita por $author</title>".
				"<author>$author</author>".
				"<link href=\"$rv/\" rel=\"alternate\" />".
				"<content type=\"text\" >$msg</content>".
			"</entry>".PHP_EOL
		;
	}
	$xml		.= '</feed>';
	print($xml);
?>