<?php
	require_once( $_SERVER["DOCUMENT_ROOT"] ."/engine/dbconnect.php");
	require_once( $_SERVER["DOCUMENT_ROOT"] ."/engine/String/splitJoin.php");
	
	/*	==================================================================================================
		--Nome......: getLog
		--Sistema...: GT8 jsAdmin
		--Descrição.: Obtém o histórico das açoes dos usuários no painel administrativo
	   ===============================================================================================
		--Versão--            --Data--      Responsável     Descrição da alteração
	   -----------------------------------------------------------------------------------------------
		--01.00              20/12/2010		Robson			Criação
	   
	   ===============================================================================================
	   Função Principal:	 getLog
	   ===============================================================================================
	   
	   --PARÂMETROS					DESCRIÇÃO
		$props						Array, os parametros podem ser passados conforme a necessidade.
		----------------------------------------------------------------------------------------------
		$props['idUser']			Se for mais de um id deve ser passado entre vírgulas.
		$props['nomeUser']			Nome do usuário, pode ser passado o nome exato.
		$props['idProduto']			Se for mais de um id deve ser passado entre vírgulas.
		$props['page']				Pode ser passado o caminho da página.
		$props['groupBy']			Deve ser passado o campo desejado para o agrupamento. Obs.: Como
									há campos com o mesmo nome de tabelas pode dar ambiguidade, por
									isso é necessário passar o nome ou o aliás da tabela sucedido de
									ponto antes do nome do campo ex. (p.id = 'id: campo, p: tabela
									de produtos')
		props['orderBy']			Deve ser passado o campo para ordenação, nesse caso segue-se a
									regra do 'groupBy' quanto a ambiguidade.
		$props['limit']				Quantidade limite de registro que se deseja como resultado.
		$props['index']				Registro inicial que se deseja iniciar a consulta, será usado 
									junto com o 'limit'. ex (index=10, limit=30), será buscado os
									registros de 10 a 30
		$props['sortDesc']			Quando não passado ele assume o valor padrão: verdadeiro
									passando o valor DESC à clausula ORDER BY, caso contrário ASC
		$props['format']			Pode ser escolhido os seguintos valores de retorno dos resultados:
									TABLE, XML, OBJECT.
		$props['print']				Opção para imprimir os valores na tela,
									deve ser passado 'TRUE' ou 'FALSE'.
	   ===============================================================================================
		MÉTODO							RETORNOS							DESCRIÇÃO
		GET								OBJECT (Default)					Variável do tipo OBJETO
										TABLE								Tabela de dados
										XML									Dados no formato XML
										print								Resultado inpresso na tela
										help								Ajuda, impressa na tela
	==================================================================================================*/
	
	function getLog($props){
		$idUser 	= (isset($props['idUser']))? (string) $props['idUser'] : null;
		$nomeUser	= (isset($props['nomeUser']))? (string) $props['nomeUser'] : null;
		$idProduto	= (isset($props['idProduto']))? (string) $props['idProduto'] : null;
		$page		= (isset($props['page']))? (string) $props['page'] : null;
		$groupBy	= (isset($props['groupBy']))? (string) $props['groupBy'] : null;
		$orderBy	= (isset($props['orderBy']))? (string) $props['orderBy'] : null;
		$limit		= (isset($props['limit']))? (integer) $props['limit'] : 10;
		$index		= (isset($props['index']))? (integer) $props['index'] : 0;
		$sortDesc   = (isset($props['sortDesc']))? (boolean) $props['sortDesc'] : true;
		$format     = (isset($props['format']))? (string) $props['format'] : 'OBJECT';
		$print		= (isset($props['print']))? (boolean) $props['print'] : false;
		
		if ($format != "OBJECT"){
			require_once( $_SERVER["DOCUMENT_ROOT"] ."/jsAdmin/check.php");
		}
		
		$sqlQuery ="
			SELECT
				aa.id, u.nome, p.titulo, p.codigo, p.id AS idProduto, aa.page, aa.action, aa.name, aa.value, aa.ip, aa.referrer, aa.creation
			FROM
				analytics_adm aa
					INNER JOIN
						a744e89c3d u
							ON
								u.id = aa.id_user
					LEFT JOIN
						produtos p
							ON 
								p.id = aa.id_produto
		";
		
		$where = "";
		
		if (isset($idProduto)){
			$where = "
				WHERE p.id IN (" . splitJoin($idProduto) . ") "
			;
		}else{
			$where ="
				WHERE
					1 = 1 "
			;
		}
		if(isset($idUser)){
			$where .= "
				AND
					aa.id_user IN (" . splitJoin($idUser) . ") "
			;
		}
		if(isset($nomeUser)){
			$where .= "
				AND
					aa.id_user = (SELECT id_user FROM a744e89c3d WHERE nome = '" . $nomeUser . "') "
			;
		}
		if(isset($page)){
			$where .= "
				AND aa.page LIKE '%" . splitJoin($page, ',', ',', 'string') . "%' "
			;
		}
		if(isset($groupBy)){
			$groupBy = "
				GROUP BY " .
					splitJoin($groupBy, ',', ',', 'string')
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
		}
		if($limit){
			$limit =" 
				LIMIT " . $index . ", " . $limit
			;
		}
		
		//die("Instrução: " . $sqlQuery . $where . $groupBy . $orderBy . $limit);
		
		$result	= mysql_query($sqlQuery . $where . $groupBy . $orderBy . $limit) or die("Erro ao acessar o banco de dados! : " . mysql_error() );
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
							<th>nome</th>
							<th>titulo</th>
							<th>codigo</th>
							<th>idProduto</th>
							<th>page</th>
							<th>action</th>
							<th>name</th>
							<th>value</th>
							<th>ip</th>
							<th>referrer</th>
							<th>creation</th>
						</tr>
				';
				
				for($i=0; $i < count($rows); $i++){
					$tabela .='
						<tr>
							<td>'. $rows[$i]['id'] .'</td>
							<td>'. $rows[$i]['nome'] .'</td>
							<td>'. $rows[$i]['titulo'] .'</td>
							<td>'. $rows[$i]['codigo'] .'</td>
							<td>'. $rows[$i]['idProduto'] .'</td>
							<td>'. $rows[$i]['page'] .'</td>
							<td>'. $rows[$i]['action'] .'</td>
							<td>'. $rows[$i]['name'] .'</td>
							<td>'. $rows[$i]['value'] .'</td>
							<td>'. $rows[$i]['ip'] .'</td>
							<td>'. $rows[$i]['referrer'] .'</td>
							<td>'. $rows[$i]['creation'] .'</td>
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
							<Logs>
				';
				
				for($i=0;$i < count($rows); $i++){
					$xml .="
								<Log>
									<id>". $rows[$i]['id'] ."</id>
									<nome>". utf8_encode($rows[$i]['nome']) ."</nome>
									<titulo>". utf8_encode($rows[$i]['titulo']) ."</titulo>
									<codigo>". $rows[$i]['codigo'] ."</codigo>
									<idProduto>". $rows[$i]['idProduto'] ."</idProduto>
									<page>". $rows[$i]['page'] ."</page>
									<action>". $rows[$i]['action'] ."</action>
									<name>". utf8_encode($rows[$i]['name']) ."</name>
									<value>". $rows[$i]['value'] ."</value>
									<ip>". $rows[$i]['ip'] ."</ip>
									<referrer>". utf8_encode($rows[$i]['referrer']) ."</referrer>
									<creation>". $rows[$i]['creation'] ."</creation>
								</Log>
					";
				}
				$xml .= "
							</Logs>
				";
				
				if($print){
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
		print_r(getLog());
		echo '</pre>';
		exit;
    }
	if ( isset($_GET["print"])) {
		$props['idUser'] 		= $_GET['idUser'];
		$props['nomeUser']		= $_GET['nomeUser'];
		$props['idProduto']		= $_GET['idProduto'];
		$props['page']			= $_GET['page'];
		$props['groupBy']		= $_GET['groupBy'];
		$props['orderBy'] 		= $_GET['orderBy'];
		$props['limit'] 		= $_GET['limit'];
		$props['index'] 		= $_GET['index'];
		$props['sortDesc'] 		= $_GET['sortDesc'];
		$props['format'] 		= $_GET['format'];
		$props['print']			= $_GET['print'];
		getLog($props);
	}
?>