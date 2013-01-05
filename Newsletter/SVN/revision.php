<?php
	require_once( $_SERVER["DOCUMENT_ROOT"] ."/engine/Array/xml2array/xml2array.php");
	$bin	= 'svn';
	$src	= 'svn://r2-cms.web623.uni5.net/r2-cms/r2/trunk';
	if ( strpos('#'. $_SERVER['DOCUMENT_ROOT'], 'Roger') ) {
		$bin	= '/opt/subversion/bin/svn';
	}
	
	$rv	= (integer)$_GET['id'];
	$result = shell_exec("$bin log $src -r $rv --verbose --username roger --password munique20");
	
	$rows	= explode(PHP_EOL, $result);
	
	$lines	= explode('|', $rows[1]);
	$author	= trim($lines[1]);
	$date	= trim($lines[2]);
	
	$table	= '
				<table class="list-filter bordered" >
					<tr>
						<th class="status" ><span class="col-2" >Status</span></th>
						<th class="file" ><span class="col-18" >File</span></th>
						<th class="file" ><span class="col-4" >Ações</span></th>
					</tr>'
	;
	for ( $i=3; $i<count($rows)-4; $i++) {
		$row	= $rows[$i];
		$status	= str_replace(array('?'), array('UN'), substr($row, 3,1));
		$file	= substr($row, 11);
		$table	.= '
					<tr>
						<td class="'. $status .'" ><span>&nbsp;</span></td>
						<td class="file" ><a href="'. $file .'" >'. $file .'</a></td>
						<td class="icns" >
							<span onclick="SVN.diff(this)" class="diff" >&nbsp;</span>
							<span onclick="SVN.update(this)" class="update" >&nbsp;</span>
						</td>
					</tr>'
		;
		
	}
	$table	.= '
				</table>'. PHP_EOL
	;
	
	
	$msg	= shell_exec("$bin log $src -r $rv --xml");
	$msg	= str_replace(
		array(
			'<logentry'. PHP_EOL .'   revision="',
			'">'. PHP_EOL .'<author>'
		), array(
			'<logentry><revision>',
			'</revision><author>'
		),
		$msg
	);
	$msg	= array(xml2array($msg));
	$row	= $msg[0]['logentry'][0];
	$msg	= $row['msg'];
	$date	= substr($row['date'], 0, strpos($row['date'], '.'));
	$date	= str_replace(array('T', '-'), array(' ', '/'), $date);
	$author	= $row['author'];
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>GT8 | Subversion</title>
		<link rel="stylesheet" type="text/css" href="/css/main.css" />
		<script type="text/javascript" src="/jCube/jCube.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			jCube.Import("/js/main.js");
			jCube.Include("Element.getElementsBySelector");
			jCube.Include("Element.getParent");
			jCube.Include("Element.getPreviousSibling");
			jCube.Include("Element.setStyle");
			jCube.Include("Server.HttpRequest");
			
			jCube(function() {
				
			});
			var SVN	= {
				svn:	function( eImg) {
					var fileName	= jCube(eImg).getParent().getParent().query(':td.file a').innerHTML;
					
					SVN.req	= SVN.req || new jCube.Server.HttpRequest({
							noCache: true,
							onLoad: function() {
								jCube(':#eResult').innerHTML	= this.responseText;
							}
						})
					;
					SVN.req.url	= '../svn.php';
					SVN.req.addGet( 'file', fileName);
					SVN.req.addGet( 'revision', '<?php print($rv); ?>');
					
					jCube(':#eResult').setStyle('display', 'block');
					
					return SVN.req;
				},
				diff:	function( eImg) {
					var req	= SVN.svn(eImg);
					req.addGet( 'opt', 'diff');
					req.start();
				},
				update:	function( eImg) {
					var req	= SVN.svn(eImg);
					req.addGet( 'opt', 'update');
					req.start();
				}
			}
		//]]>
		</script>
		<style type="text/css" >
			table td.file {
				padding: 0 20px;
			}
			table td.M span {
				background: url(../imgs/modified.png) no-repeat center;
			}
			table td.A span {
				background: url(../imgs/added.png) no-repeat center;
			}
			table td.D span {
				background: url(../imgs/deleted.png) no-repeat center;
			}
			table td.UN span {
				background: url(../imgs/unversioned.png) no-repeat center;
			}
			table td.CN span {
				background: url(../imgs/conflited.png) no-repeat center;
			}
			table th span {
				display: block;
			}
			table td.icns span {
				display: block;
				width: 30px;
				height: 28px;
				padding: 0px;
				margin: 5px;
				border: 1px solid #CCC;
				border-radius: 5px;
				float: left;
				cursor: pointer;
			}
			table td.icns span:hover {
				box-shadow: 0px 0px 2px #999;
			}
			table td.icns span.diff {
				background: url(../imgs/search-small.png) no-repeat center;
			}
			table td.icns span.update {
				background: url(../imgs/modified.png) no-repeat center;
			}
			h1 span {
				text-transform: capitalize;
			}
			#eResult {
				width: 100%;
				border: 1px solid #CCC;
				height: 400px;
				overflow: scroll;
				background: #FFF;
				display: none;
			}
			#eResult span {
				display: block;
			}
			#eResult span.lhead {
				display: none;
			}
			#eResult span.line {
				margin-top: 25px;
				background: #EEE;
				border-top: 1px solid #CCC;
			}
			#eResult span.plus {
				background: #FEE;
				color: #444;
				font-weight: bold;
			}
			#eResult span.minus {
				background: #EEE;
				color: #AAA;
			}
		</style>
	</head>
	<body>
		<div id="eMain" >
			<div class="body" >
				<div class="wrapper" >
					<h1>Revision <?php print($rv); ?> By <span><?php print($author); ?></span></h1>
					<h2 class="margin-top" ><?php print($comment); ?></h2>
					<h3 class="margin-top" ><?php print($date); ?></h3>
				</div>
				<div class="wrapper margin-top" >
					<?php print( $table); ?>
				</div>
				<div class="wrapper margin-top" >
					<pre id="eResult" ></pre>
				</div>
			</div>
		</div>
	</body>
</html>