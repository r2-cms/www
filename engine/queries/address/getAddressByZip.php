<?php   
	if ( !defined('CROOT')) {
		die('Undefined GT8: QR0::a114e00->getAddressByZip');
	}
	/*
			Class
				Zip
			static method getZip( $zip, $format)
			
			Sample:
				Zip::getZip($_GET['zip'], 'JSON');
				
	*/
	class Zip {
		public function Zip( $zip, $format='OBJECT') {
			self::getZip( $zip, $format);
		}
		public static function getZip( $zip, $format='OBJECT') {
			$crr	= RegExp($zip, "[0-9\-]+");
			$row	= self::query( $crr);
			
			if ( isset($row['id'])) {
				$row['cidade']		= utf8_encode($row['cidade']);
				$row['bairro']		= utf8_encode($row['bairro']);
				$row['logradouro']	= utf8_encode($row['logradouro']);
			} else if ( $row=self::getFromKingHost($zip) && $row['id']) {
				
			} else if ($row=self::getFromRepublicaVirtual($zip) && $row['id']) {
				
			} else {
				while ( !isset($row['id'])) {
					$crr	= substr($crr, 0, -1);
					$row	= self::query( $crr);
				}
				if ( $row['id']) {
					$row['cidade']		= utf8_encode($row['cidade']);
					$row['bairro']		= utf8_encode($row['bairro']);
					$row['logradouro']	= utf8_encode($row['logradouro']);
				}
				
			}
			$s	= array();
			if ( $format == "OBJECT") {
				$s			= $row;
			} else if ( $format == "JSON") {
				$s	= "{
						id:				'{$row['id']}',
						cep:			'{$row['cep']}',
						estado:			'{$row['estado']}',
						cidade:			'". addslashes($row['cidade']) ."',
						bairro:			'". addslashes($row['bairro']) ."',
						logradouro:		'". addslashes($row['logradouro']) ."',
						tipo:			'{$row['tipo']}',
						fonte:			'{$row['fonte']}'
				}";
			} else if ( $format == "XML") {
				$s	= "<?xml version='1.0' encoding='UTF-8' ?>
						<cep>
							<id>{$row['id']}</id>
							<cep>{$row['cep']}</cep>
							<estado>{$row['estado']}</estado>
							<cidade>{$row['cidade']}</cidade>
							<bairro>{$row['bairro']}</bairro>
							<logradouro>{$row['logradouro']}</logradouro>
							<tipo>{$row['tipo']}</tipo>
							<fonte>{$row['fonte']}</fonte>
						</cep>
				";
			} else if ( $format == "TABLE") {
				$s	= "
					<table border='1' cellpadding='0' cellspacing='0' >
						<tr>
							<th>id</th>
							<th>cep</th>
							<th>estado</th>
							<th>cidade</th>
							<th>bairro</th>
							<th>logradouro</th>
							<th>tipo</th>
							<th>fonte</th>
						</tr>
						<tr>
							<td class='id' >{$row['id']}</td>
							<td class='cep' >{$row['cep']}</td>
							<td class='estado' >{$row['estado']}</td>
							<td class='cidade' >{$row['cidade']}</td>
							<td class='bairro' >{$row['bairro']}</td>
							<td class='logradouro' >{$row['logradouro']}</td>
							<td class='tipo' >{$row['tipo']}</td>
							<td class='fonte' >{$row['fonte']}</td>
						</tr>
					</table>
				";
			}
			return $s;
		}
		protected static function query( $zip) {
			if ( strlen($zip) == 9) {
				$row	= mysql_fetch_assoc(mysql_query("
					SELECT
						c.id, c.cep, c.estado, c.cidade, c.bairro, c.logradouro, c.tipo, c.fonte
					FROM
						cep c
					WHERE
						c.cep = '$zip'
				"));
			} else {
				$row	= mysql_fetch_assoc(mysql_query("
					SELECT
						c.id, c.cep, c.estado, c.cidade, c.bairro, c.logradouro, c.tipo, c.fonte
					FROM
						cep c
					WHERE
						c.cep LIKE '$zip%'
					LIMIT
						1
				"));
			}
			return $row;
		}
		protected static function insert($row) {
			$tipo		= mysql_real_escape_string($row['tipo_logradouro']);
			$logradouro	= mysql_real_escape_string($row['logradouro']);
			$bairro		= mysql_real_escape_string($row['bairro']);
			$cidade		= mysql_real_escape_string($row['cidade']);
			$fonte		= $row['fonte'];
			$estado		= $row['uf'];
			$zip		= $row['zip'];
			
			if ( $logradouro && $bairro && $cidade && $uf && $zip) {
				$sql	= "
					INSERT INTO
						cep( cep, estado, cidade, logradouro, bairro, tipo, fonte)
					VALUES(
						'$zip', '$estado', '$cidade', '$logradouro', '$bairro', '$tipo', '$fonte'
					)
				";
				//die($sql);
				mysql_query($sql);
				$row["id"]	= mysql_insert_id();
			} else {
				$row	= array(
					'zip'	=> $zip
				);
			}
			return $row;
		}
		protected static function getFromKingHost( $zip) {
			$contents	= file_get_contents("http://webservice.uni5.net/web_cep.php?auth=b31377df9a780d3550b4d1764186cb06&formato=query_string&cep=$zip");
			if ( !$contents) {
				$contents	= "&resultado=0&resultado_txt=erro+ao+buscar+cep";
			}
			parse_str($contents, $row);
			$row['zip']		= $zip;
			$row['fonte']	= 'kinghost';
			return self::insert($row);
		}
		protected static function getFromRepublicaVirtual( $zip) {
			$contents	= file_get_contents("http://cep.republicavirtual.com.br/web_cep.php?cep=$zip&formato=query_string");
			if ( !$contents) {
				$contents	= "&resultado=0&resultado_txt=erro+ao+buscar+cep";
			}
			parse_str($contents, $row);
			$row['zip']		= $zip;
			$row['fonte']	= 'republica virtual';
			return self::insert($row);
		}
	}
?>