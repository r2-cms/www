<?php
	if ( isset($_GET['action'])) {
		switch( $_GET['action']) {
			case 'print-qr-bar': {
				require_once( SROOT .'engine/classes/BarcodeQR.php');
				$qr	= new BarcodeQR();
				$qr->url(mysql_real_escape_string($_GET['url']));
				$qr->draw();
				break;
			}
		}
		die();
	}
	
	/****************************************
	 *
	 * Lista últimas impressões
	 *
	 ***************************************/
	$result	= mysql_query("
		SELECT
			id, id_explorer, creation, printed
		FROM
			print_queue
		WHERE
			printed = 1
		ORDER BY
			id DESC
		LIMIT 10
	");
	$rows	= array();
	while( ($row=mysql_fetch_assoc($result))) {
		$rows[]	= $row;
	}
	
	/****************************************
	 *
	 * Procure por fila de impressão
	 *
	 ***************************************/
	$row	= mysql_fetch_assoc(mysql_query("SELECT id, id_explorer, creation, printed FROM print_queue WHERE printed = 0 ORDER BY id ASC"));
	if ( $row) {
		require_once( SROOT .'engine/functions/Pager.php');
		$Pager	= Pager(array(
			'sql'	=> 'explorer.list',
			'ids'	=> array(
				array('e.id', $row['id_explorer'])
			),
			'foundRows'	=> 1
		));
		$Pager	= $Pager['rows'][0];
		
		//marque como impresso
		mysql_query("UPDATE print_queue SET printed = 1 WHERE id_explorer = ". $Pager['id'] ." AND printed = 0 LIMIT 1") or die(mysql_error());
		
		//Cria código de barras, padrão code39, nas variações dos produtos
		$code39		= '789';//country code
		$code39		.= '1000';//company code
		$code39		.= substr('0000'. $Pager['id'], -5);//prod code
		$code39_dv	= 0;
		for ( $i=0; $i<strlen($code39); $i++) {
			$code39_dv	+= ((integer)substr($code39, $i, 1) * ($i%2===0?1:3));
		}
		$code39_dv	= ceil($code39_dv/10)*10 - $code39_dv;
		$code39	.= $code39_dv .'';
		
		$codFamily	= explode('/', $Pager['path']);
		$codFamily	= $codFamily[2];
		$codFamily	= mysql_fetch_array(mysql_query("SELECT e.code FROM gt8_explorer e WHERE e.path = 'catalogo/calcados/' AND e.filename = '$codFamily' "));
		$codFamily	= substr('00'. strtoupper($codFamily[0]), -2);
		$tamanho	= Pager(array(
			'sql'	=> 'explorer.list-attributes-value',
			'where'	=> '
				AND a.id_dir IN ('. str_replace('/', ',', substr($Pager['dirpath'], 0, -1)) .')
				AND a.attribute = "tamanho"
			',
			'replace'	=> array(
				array('vIn.id_explorer = vIn.id_explorer', 'vIn.id_explorer = '. $Pager['id'])
			),
			'foundRows'	=> 1,
			'limit'	=> 1
		));
		$tamanho	= $tamanho['rows'][0]['value'];
		$Pager['tamanho']	= $tamanho;
		$Pager['title']	= mysql_fetch_array(mysql_query("SELECT e.title, e.id_dir FROM gt8_explorer e WHERE e.id = '{$Pager['id_dir']}' "));
		$Pager['family']	= $Pager['title'][1];
		$Pager['title']		= utf8_encode($Pager['title'][0]);
		$Pager['family']	= mysql_fetch_array(mysql_query("SELECT e.title, e.id_dir FROM gt8_explorer e WHERE e.id = '{$Pager['family']}' "));
		$Pager['brand']		= $Pager['family'][1];
		$Pager['family']	= utf8_encode($Pager['family'][0]);
		$Pager['brand']		= mysql_fetch_array(mysql_query("SELECT e.title FROM gt8_explorer e WHERE e.id = '{$Pager['brand']}' "));
		$Pager['brand']		= utf8_encode($Pager['brand'][0]);
		$price	= mysql_fetch_array(mysql_query("SELECT e.price_cost FROM gt8_explorer e WHERE e.id = ". $Pager['id_dir']));
		$price	= $price[0].'';
		$price	= substr('000'. substr($price, 0, strpos($price, '.')), -3);
		
		$Pager['fullpath']	= explode('/', $Pager['fullpath']);
		array_pop($Pager['fullpath']);
		$Pager['fullpath']	= join('/', $Pager['fullpath']);
		
		$Pager['url']	= explode('/', $Pager['fullpath']);
		array_shift($Pager['url']);
		array_shift($Pager['url']);
		$Pager['url']		= join('/', $Pager['url']);
		$Pager['url']		= 'http://www.salaodocalcado.com.br/'. $Pager['url'] .'/';
		
		$Pager['cel']		= Pager(array(
			'sql'	=> 'storage.list',
			'ids'	=> array(
				array('e.id', $Pager['id'])
			),
			'limit'	=> 1
		));
		$Pager['cel']	= $Pager['cel']['rows'][0]['filename'];
		
		require_once( SROOT.'engine/functions/Barcode39.php');
	} else {
		print('<!DOCTYPE html>
			<html lang="en">
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					<title>Impressão automática</title>
					<link rel="stylesheet" type="text/css" href="'. CROOT .'css/gt8/gt8.css" />
					<script type="text/javascript" src="'. CROOT .'jCube/jCube.min.js" ></script>
					<script type="text/javascript" >
						var tstart	= new Date().getTime();
						var delay	= 30 * 1000;
				window.focus();
						window.onload	= function(){
							jCube(":#eDelay").addEvent("onclick", function(E){
								E.stop();
								window.location.reload();
							});
							window.setInterval(function(){
								var secs	= Math.round((delay -((new Date().getTime()-tstart)))/1000);
								if ( secs < 1) {
									window.location.reload();
								} else {
									jCube(":#eDelay").setHTML( secs +" segundos");
								}
							}, 950);
						}
					</script>
				</head>
				<body>
					<div id="eMain" class="text-align-center" >
						<h1>Não há filas de impressão</h1>
						<p>
							Procurando novamente em <a href="#" id="eDelay" >30 segundos</a>.
						</p>
						<p>
							
						</p>
					</div>
				</body>
			</html>
		');
		die();
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Automação de impressões de código de barras | GT8</title>
		{{VIEW:admin.header-default}}
		<link rel="stylesheet" type="text/css" href="{{AROOT}}explorer/levels/catalogo/levelin/levelin/levelin/levelin/barcode.css" />
		<style type="text/css" >
			#eBarPrint {
				cursor: default;
			}
				#eBarPrint .info .id {
					text-transform: uppercase;
				}
		</style>
		<script type="text/javascript" >
			window.onload	= function(){
				window.setTimeout(function(){
					jsPrintSetup.setOption('printSilent', 1);
					jsPrintSetup.setSilentPrint(true);
					jsPrintSetup.printWindow(window);
					window.setTimeout(function(){ window.location.reload();}, 7000);
				}, 5000);
			}
		</script>
	</head>
	<body>
		<div id="eContent" >
			<section id="eBarPrint" class="barcode-printer clearfix-child" >
				<div class="content" >
					<div class="img" >
						<img alt="" src="{{CROOT}}<?php print($Pager['fullpath']); ?>?preview" />
					</div>
					<div class="size" >
						<?php print($Pager['tamanho']); ?>
					</div>
					<div class="info" >
						<strong class="title" ><?php print($Pager['title']); ?></strong>
						<span class="family" ><?php print($Pager['family']); ?></span>
						<span class="brand" ><?php print($Pager['brand']); ?></span>
						<span class="id" >REF: <?php print($Pager['id'] .' '. $Pager['cel']); ?></span>
					</div>
					<div class="clearfix" ></div>
					<div class="middle-info" >
						
					</div>
					<div class="barcode code39C" >
						<div class="barcode code39" >
							<?php print(Barcode39( $code39)); ?>
						</div>
						<img class="img-qr" src="?action=print-qr-bar&url=<?php print($Pager['url']); ?>" alt="" />
						<div class="barcode code39 font-size-small textual-code" >
							*<?php print($code39); ?>*
						</div>
					</div>
					<div class="clearfix" ></div>
					<div class="barfoot" >
						<small>www.salaodocalcado.com.br</small>
					</div>
				</div>
			</section>
		</div>
	</body>
</html>