<?php
	require_once( $_SERVER["DOCUMENT_ROOT"] ."/engine/connect.php");
	require_once( $_SERVER["DOCUMENT_ROOT"] ."/engine/String/splitJoin.php");

/*	==================================================================================================
		--Nome.............: getUsersSumary
		--Sistema..........: r2
		--Descrição........: Obtém a lista dos usuarios do painel administrativo
		--Função Principal.: getUsersSumary
	   ===============================================================================================
		--Versão--            --Data--      Responsável     Descrição da alteração
		----------------------------------------------------------------------------------------------
		--01.00              16/12/2010		Robson			Criaçao
		
	   ===============================================================================================
	   --PARÂMETROS					DESCRIÇÃO
		$props						Array, os parametros podem ser passados conforme a necessidade.
	   ===============================================================================================
		MÉTODO						RETORNOS							DESCRIÇÃO
		GET							OBJECT (Default)					Variável do tipo OBJETO
									TABLE								Tabela de dados
									XML									Dados no formato XML
									print								Resultado inpresso na tela (foi o robson que escreveu "inpresso" com "N". Não foi eu, não! kkkkkk)
									help								Ajuda, impressa na tela
	==================================================================================================*/

	function getUsersSumary($props = array( 'help' => true )){
		$idUser 	= (isset($props['idUser']))? $props['idUser'] : null;
		$groupBy	= (isset($props['groupBy']))? (string) $props['groupBy'] : null;
		$orderBy	= (isset($props['orderBy']))? (string) $props['orderBy'] : null;
		$limit		= (isset($props['limit']))? (integer) $props['limit'] : 100;
		$index		= (isset($props['index']))? (integer) $props['index'] : 0;
		$sortDesc   = (isset($props['sortDesc']))? (boolean) $props['sortDesc'] : true;
		$inativo	= (isset($props['inativo']))? (integer) $props['inativo'] : 2;
		$format     = (isset($props['format']))? (string) $props['format'] : 'OBJECT';
		
		
		
		if ($format != "OBJECT"){
			require_once( $_SERVER["DOCUMENT_ROOT"] ."/jsAdmin/check.php");
		}
		
		$sqlQuery	= "
			SELECT
				u.id, u.natureza, u.TYPE, u.NAME, u.company_name, u.cpfcnpj, u.document, u.genre, u.birth,
				u.login, u.hlogin, u.pass, u.LEVEL, u.enabled, u.creation, u.modification, u.remarks
			FROM
				gt8_users AS u
		";
		
		$where	= "";
		$limite	= "";
		
		if(isset($idUser)){
			$idUser = splitJoin($idUser);
			$where = "
				WHERE
					u.id IN (" . $idUser . ") "
			;
		}else{
			$where = "
				WHERE
					1 = 1 "
			;
			
		}
		if($inativo == 0){
			$where .= "
				AND
					u.inativo = 0 "
			;
		}elseif($inativo == 1){
			$where .= "
				AND
					u.inativo = 1 "
			;
		}
		if(isset($groupBy)){
			$groupBy = splitJoin($groupBy, ',', ',', 'string');
			$groupBy = "
				GROUP BY " .
					$groupBy
			;
		}
		if(isset($orderBy)){
			$orderBy = splitJoin($orderBy, ',', ',', 'string');
			$orderBy ="
				ORDER BY " .
					$orderBy
			;
			if ($sortDesc){
				$orderBy .= " DESC ";
			}
			else{
				$orderBy .= " ASC ";
			}
		}
		if($limit){
			$limite =" 
				LIMIT " . $index . ", " . $limit
			;
		} else {
			$limite	= "";
		}
		//die($sqlQuery . $where . $groupBy . $orderBy . $limite);
		$result	= mysql_query($sqlQuery . $where . $groupBy . $orderBy . $limite) or die("Erro ao acessar o banco de dados!");
		$rows	= array();
		while( $row = mysql_fetch_assoc($result)) {
				$rows[]	= $row;
		}
		
		switch($format){
			case "TABLE":
				$tabela = '
					<table border="1" cellpadding="0" cellspacing="0" style="margin:20px auto 0; color:#343434; " >
						<tr>
							<th>id</th>
							<th>natureza</th>
							<th>type</th>
							<th>name</th>
							<th>company_name</th>
							<th>cpfcnpj</th>
							<th>document</th>
							<th>genre</th>
							<th>birth</th>
							<th>login</th>
							<th>hlogin</th>
							<th>pass</th>
							<th>level</th>
							<th>enabled</th>
							<th>creation</th>
							<th>modification</th>
							<th>remarks</th>
						</tr>
				';
				for($i=0; $i < count($rows); $i++){
					$tabela .='
						<tr>
							<td>'. $rows[$i]['id'] .'</td>
							<td>'. $rows[$i]['natureza'] .'</td>
							<td>'. $rows[$i]['type'] .'</td>
							<td>'. $rows[$i]['name'] .'</td>
							<td>'. $rows[$i]['company_name'] .'</td>
							<td>'. $rows[$i]['cpfcnpj'] .'</td>
							<td>'. $rows[$i]['document'] .'</td>
							<td>'. $rows[$i]['genre'] .'</td>
							<td>'. $rows[$i]['birth'] .'</td>
							<td>'. $rows[$i]['login'] .'</td>
							<td>'. $rows[$i]['hlogin'] .'</td>
							<td>'. $rows[$i]['pass'] .'</td>
							<td>'. $rows[$i]['level'] .'</td>
							<td>'. $rows[$i]['enabled'] .'</td>
							<td>'. $rows[$i]['creation'] .'</td>
							<td>'. $rows[$i]['modification'] .'</td>
							<td>'. $rows[$i]['remarks'] .'</td>
						</tr>
					';
				}
				$tabela .='</table>';
				
				return $tabela;
			break;
			
			case "XML":
				$xml = '<?xml version="1.0" encoding="utf-8"?>
							<Usuarios>
				';
				
				for($i=0;$i < count($rows); $i++){
					$xml .="
								<Usuario>
									<id>". $rows[$i]['id'] ."</id>
									<>". $rows[$i]['natureza'] ."</>
									<>". $rows[$i]['type'] ."</>
									<>". $rows[$i]['name'] ."</>
									<>". $rows[$i]['company_name'] ."</>
									<>". $rows[$i]['cpfcnpj'] ."</>
									<>". $rows[$i]['document'] ."</>
									<>". $rows[$i]['genre'] ."</>
									<>". $rows[$i]['birth'] ."</>
									<>". $rows[$i]['login'] ."</>
									<>". $rows[$i]['hlogin'] ."</>
									<>". $rows[$i]['pass'] ."</>
									<>". $rows[$i]['level'] ."</>
									<>". $rows[$i]['enabled'] ."</>
									<>". $rows[$i]['creation'] ."</>
									<>". $rows[$i]['modification'] ."</>
									<>". $rows[$i]['remarks'] ."</>
								</Usuario>
					";
				}
				$xml .= "
							</Usuarios>
				";
				return $xml;
			break;
		
			case 'JSON':
				if( is_array( $rows ) )
				{
					$json = 'results=[';
					
					foreach( $rows as $v )
					{
						$json .= "{";
						
						foreach( $v as $_k => $_v )
						{
							$json .= '"'.$_k.'":"'.$_v.'",';
						}
						
						$json = substr($json,0,strlen($json)-1) . "},";
					}
					$json = substr( $json, 0, strlen($json)-1 ) . "];";
				}
				return $json;
			break;
		
			default:
				return $rows;
			break;
		}
	}
	
	
	if(isset($_GET['help'])){
		
		echo '<pre>';
		print_r(getUsersSumary());
		echo '</pre>';
		exit;
    }
	
	if ( isset($_GET["print"])) {
		if( $_GET['format'] =="XML" ){
			header("content-type: application/xml");
		}
		print(getUsersSumary(array(
			idUser		=> $_GET['idUser'],
			groupBy		=> $_GET['groupBy'],
			orderBy		=> $_GET['orderBy'],
			limit		=> $_GET['limit'],
			index		=> $_GET['index'],
			sortDesc	=> $_GET['sortDesc'],
			inativo		=> $_GET['inativo'],
			format		=> $_GET['format']
		)));
	}
?>