<?php
	require_once( $_SERVER["DOCUMENT_ROOT"] ."/engine/connect.php");
	require_once( $_SERVER["DOCUMENT_ROOT"] ."/engine/String/splitJoin.php");
	
	
/*	==================================================================================================
		--Nome......: getUsersSumary
		--Sistema...: r2
		--Descrição.: Obtém os privilégios de acesso de cada usuário 
	   ===============================================================================================
		--Versão--            --Data--      Responsável     Descrição da alteração
	   -----------------------------------------------------------------------------------------------
		--01.00              30/09/2011		Robson			Criação
	   
	   ===============================================================================================
	   Função Principal:	 getPrivileges
	   ===============================================================================================
	   
	   --PARÂMETROS					DESCRIÇÃO
		$props						Array, os parametros podem ser passados conforme a necessidade.											
	   ===============================================================================================
		MÉTODO							RETORNOS							DESCRIÇÃO
		GET								OBJECT (Default)					Variável do tipo OBJETO
										TABLE								Tabela de dados
										XML									Dados no formato XML
										print								Resultado inpresso na tela
										help								Ajuda, impressa na tela
	==================================================================================================*/
	function getPrivileges($props = array( 'help' => true)){
		$idUser 	= (isset($props['idUser']))? (integer) $props['idUser'] : 0;
		$privilege	= (isset($props['privilege']))? (string) $props['privilege'] : null;
		$orderBy	= (isset($props['orderBy']))? (string) $props['orderBy'] : "category,url";
		$limit		= (isset($props['limit']))? (integer) $props['limit'] : 100;
		$index		= (isset($props['index']))? (integer) $props['index'] : 0;
		$sortDesc   = (isset($props['sortDesc']))? (boolean) $props['sortDesc'] : true;
		$format     = (isset($props['format']))? (string) $props['format'] : 'OBJECT';
		$print		= (isset($props['print']))? (boolean) $props['print'] : false;
		
		if ($format != "OBJECT"){
			require_once( $_SERVER["DOCUMENT_ROOT"] ."/jsAdmin/check.php");
		}
		
		$sqlQuery ="
			SELECT
				plIn.id AS id_user,
				plIn.name,
				pf.id AS id_page,
				IF(plIn.privilege IS NOT NULL, plIn.privilege, '-') AS privilege,
				IF(plIn.privilege IS NOT NULL, plIn.privilege-1, 0) AS iprivilege,
				pf.creation,
				plIn.modification,
				pf.category,
				pf.url,
				pf.field
			FROM
				gt8_privileges_fields pf
				LEFT JOIN (
					SELECT 
						pl.id, pl.id_user, pl.id_page, pl.privilege, pl.modification,
						u.name, u.login
					FROM 
						gt8_privileges pl
						JOIN gt8_users u ON u.id = pl.id_user
					WHERE 
						id_user = $idUser
				) plIn ON pf.id = plIn.id_page
		";
		
		$where = "
			WHERE
				1 = 1
		";
		if(isset($privilege)){
			$where .= "
				AND
					pl.privilege IN (". "'". splitJoin($privilege, ',', ',', 'string') ."'" .") "
			;
		}
		if(isset($orderBy)){
			$orderBy = "
				ORDER BY " .
					splitJoin($orderBy, ',', ',', 'string')
			;
			if ($sortDesc){
				$orderBy .= " DESC ";
			}
			else{
				$orderBy .= " ASC ";
			}
		} else {
			$orderBy	= "";
		}
		if($limit){
			$limit =" 
				LIMIT " . $index . ", " . $limit
			;
		}
		
		//die($sqlQuery ."@1". $where ."@2". $orderBy ."@3". $limit);
		$result	= mysql_query($sqlQuery . $where . $orderBy . $limit);
		$rows	= array();
		while( $row = mysql_fetch_assoc($result)) {
			$rows[]	= $row;
		}
		
		switch($format){
			case "TABLE":
				$tabela = '
					<table border="1" cellpadding="0" cellspacing="0" style="margin:20px auto 0; color:#343434; " >
						<tr>
							<th>id_user</th>
							<th>name</th>
							<th>id_page</th>
							<th>privilege</th>
							<th>iprivilege</th>
							<th>category</th>
							<th>creation</th>
							<th>modification</th>
							<th>url</th>
							<th>field</th>
						</tr>
				';
				
				for($i=0; $i<count($rows); $i++){
					$tabela .='
						<tr>
							<td>'. $rows[$i]['id_user'] .'</td>
							<td>'. $rows[$i]['name'] .'</td>
							<td>'. $rows[$i]['id_page'] .'</td>
							<td>'. $rows[$i]['privilege'] .'</td>
							<td>'. $rows[$i]['iprivilege'] .'</td>
							<td>'. $rows[$i]['category'] .'</td>
							<td>'. $rows[$i]['creation'] .'</td>
							<td>'. $rows[$i]['modification'] .'</td>
							<td>'. $rows[$i]['url'] .'</td>
							<td>'. $rows[$i]['field'] .'</td>
						</tr>
					';
				}
				$tabela .='</table>';
				
				if($print){
					print($tabela);	
				}
				return $tabela;
				break;
			case "XML":
				$xml = '<?xml version="1.0" encoding="utf-8"?>
							<Usuarios>
				';
				
				for($i=0;$i < count($rows); $i++){
					$xml .="
								<Usuario>
									<id_user>". $rows[$i]['id_user'] ."</id_user>
									<name>". utf8_encode($rows[$i]['name']) ."</name>
									<id_page>". $rows[$i]['id_page'] ."</id_page>
									<privilege>". $rows[$i]['privilege'] ."</privilege>
									<iprivilege>". $rows[$i]['iprivilege'] ."</iprivilege>
									<category>". $rows[$i]['category'] ."</category>
									<creation>". $rows[$i]['creation'] ."</creation>
									<modification>". $rows[$i]['modification'] ."</modification>
									<url>". $rows[$i]['url'] ."</url>
									<field>". $rows[$i]['field'] ."</field>
								</Usuario>
					";
				}
				$xml .= "
							</Usuarios>
				";
				
				if ( $print) {
					header("content-type: application/xml");
					print($xml);	
				}
				return $xml;
				break;
			default:
				return $rows;
				break;
		}
	}
	if(isset($_GET['help'])){
		echo '<pre>';
		print_r(getPrivileges());
		echo '</pre>';
		exit;
    }
	if ( isset($_GET["print"])) {
		$props['idUser'] 	= $_GET['idUser'];
		$props['privilege']	= $_GET['privilege'];
		$props['groupBy']	= $_GET['groupBy'];
		$props['orderBy'] 	= $_GET['orderBy'];
		$props['limit'] 	= $_GET['limit'];
		$props['index'] 	= $_GET['index'];
		$props['sortDesc'] 	= $_GET['sortDesc'];
		$props['format'] 	= $_GET['format'];
		$props['print']		= $_GET['print'];
		getPrivileges($props);
	}
?>