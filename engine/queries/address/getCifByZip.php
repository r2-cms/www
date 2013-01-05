<?php
	if ( !defined('SROOT')) {
		die('Missing SROOT in getCifByZip!');
	}
	/*
	 * Options:
	 * 	zip
	 * 	price		float
	 * 	weight		gr
	 * 	width		cm
	 * 	height		cm
	 * 	length		cm
	 * 	format
	*/
	function getCifByZip( $options) {
		$zip		= $options['zip'];
		$weight		= (integer)$options['weight'];
		$price		= (float)$options['price'];
		$width		= (integer)$options['width'];
		$height		= (integer)$options['height'];
		$length		= (integer)$options['length'];
		$format		= isset($options['format']) && $options['format']? $options['format']: 'OBJECT';
		
		if ( gettype($zip) == "string" && strlen($zip)>5) {
			$zip	= substr( $zip, 0, 5);
		}
		settype( $zip, "integer");
		
		//if ( !$idProduct && !$weight ) {
		//	if ( $format == "JSON") {
		//		print("//#error: ID do produto ausente!". PHP_EOL);
		//	}
		//	return 'ERROR: $idProduct';
		//}
		if ( !$zip) {
			if ( $format == "JSON") {
				print("//#error: Código postal ausente!". PHP_EOL);
			}
			return 'ERROR: missing $zip';
		}
		
		//get estado
		$result	= mysql_query("SELECT estado, tipo, id FROM frete_faixas WHERE $zip between de and ate");
		$row	= mysql_fetch_assoc($result);
		if ( !$row) {
			if ( $format == "JSON") {
				print("//#error: faixa de cep não encontrado!". PHP_EOL);
			}
			return 'ERROR: zip range not found';
		}
		$estadoCode	= $row["estado"] . $row["tipo"];
		
		
		//get freight
		$row	= mysql_fetch_array(mysql_query("SELECT $estadoCode, (SELECT $estadoCode FROM frete_descontos WHERE valor > '$price' ORDER BY valor ASC limit 1) AS freightOff FROM frete_custo WHERE peso BETWEEN $weight and ($weight+999) limit 1"));
		$freight	= $row[0];
		$freightOff	= ($row[1] * 100) ."%";
		$freight	= $freight * (1-$row[1]);
		
		$stateNamesSigla	= array("SP","MG","PR","RJ","SC","DF","ES","GO","MS","RS","AL","BA","MT","PB","PE","SE","TO","AC","AM","AP","CE","MA","PI","RN","RO","RRC","RRI");
		$stateNamesFull		= array("São Paulo","Minas Gerais","Paraná","Rio de Janeiro","Santa Catarina","Distrito Federal","Espírito Santo","Goiás","Mato Grosso do Sul","Rio Grande do Sul","Alagoas","Bahia","Mato Grosso","Paraíba","Pernanbuco","Sergipe","Tocantins","Acre","Amazonas","Amapá","Ceará","Maranhão","Piauí","Rio Grande do Norte","Rondônia","Roraima");
		
		$estadoName	= (str_replace($stateNamesSigla, $stateNamesFull, substr($estadoCode, 0, 2)));
		$estadoTipo	= str_replace(array("C", "I"), array("capital", "interior"), substr($estadoCode, 2));
		
		//get deliveryTime
		$row	= mysql_fetch_array(mysql_query("SELECT $estadoCode, id, peso FROM frete_prazos WHERE peso > $weight ORDER BY peso ASC LIMIT 1"));
		$deliveryTime	= $row[0];
		
		$s	= array();
		if ( $format == "JSON") {
			$s	= "{
				zip:		$zip,
				freight:	$freight,
				freightOff:	'$freightOff',
				estado:		'$estadoName',
				tipo:		'$estadoTipo',
				estadoCode:	'$estadoCode',
				deliveryTime: $deliveryTime
			}";
		} else{
			$s['zip']			= $zip;
			$s['freight']		= $freight;
			$s['freightOff']	= $freightOff;
			$s['estado']		= $estadoName;
			$s['tipo']			= $estadoTipo;
			$s['estadoCode']	= $estadoCode;
			$s['deliveryTime']	= $deliveryTime;
		}
		
		return $s;
	}
?>
