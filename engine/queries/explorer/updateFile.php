<?php
	if ( !defined('SROOT')) {
		die('//#error: Not allowed in queries.explorer.update!'. PHP_EOL);
	}
	/*
		Boolean updateFile( $id, $name, $value)
	*/
	require_once( SROOT .'engine/functions/Pager.php');
	function updateFile( $id, $name, $value, $format='OBJECT') {
		//validação
		$id	= (integer)$id;
		if (!$id) {
			die('//missing id!');
		}
		$_name	= RegExp($name, '[a-zA-Z0-9_\.\@\-]+');
		if ( !$_name || $_name != $name) {
			die('//invalid name');
		}
		$name	= $_name;
		
		$value	= mysql_real_escape_string($value);
		
		//obtém dados do arquivo
		$Pager	= Pager(array(
			'sql'	=> 'explorer.list',
			'ids'	=> array(
				array('e.id', $id)
			),
			'foundRows'=>1
		));
		$Pager	= $Pager['rows'][0];
		
		if ( $Pager['locked'] == 1 && $name != 'locked') {
			die('//#error: arquivo bloqueado!'. PHP_EOL);
		}
		
		//PRIVILÉGIOS
		$result	= mysql_query("SELECT e.id_users AS id_user, e.id, e.write_privilege FROM gt8_explorer e WHERE e.id = $id");
		$myLevel	= isset($_SESSION['login']['level'])? $_SESSION['login']['level']: 0;
		if ( ($rowE = mysql_fetch_assoc($result))) {
			if ( $myLevel >= $rowE['e.write_privilege'] || (isset($_SESSION['login']['id']) && $rowE['id_user'] == $_SESSION['login']['id'])) {
				//OK
			} else {
				die('//#error: Sem permissão! (4)'. PHP_EOL);
			}
		} else {
			//OK
			die('//#error: O arquivo não existe! Por favor, contate o administrador.'. PHP_EOL);
		}
		//usuário não pode definir privilégio superior ao seu próprio
		if ( $name == 'read_privilege' || $name == 'write_privilege') {
			$value	= min( $value, $myLevel);
		} else if ( $name == 'approved') {
			//somente usuários com privilégios de moderador ou superior podem alterar a propriedade approved
			$result	= mysql_query("SELECT u.approval_level_required+0 AS approval_level_required FROM gt8_users u WHERE u.id = ". $rowE['id_user']);
			$rowU	= mysql_fetch_assoc($result);
			if ( $myLevel < $rowU['approval_level_required'] ) {
				die('//#error: Privilégios de moderador exigidos para esta ação! ('. $rowU['approval_level_required'] .')'. PHP_EOL);
			}
		}
		//print("<h1>". $name ."</h1>");
		//print("<h1>". $value ."</h1>");
		$table	= 'gt8_explorer';
		if ( $name == 'description') {//if explorer_data
			$table	= 'gt8_explorer_data';
		}
		
		if ( $name == 'price_cost' || $name == 'price_selling' || $name == 'price_suggested') {
			$value	= str_replace(',', '.', $value.'');
		}
		
		mysql_query("
			UPDATE
				$table
			SET
				`$name`	= '". $value ."'
			WHERE
				id	= $id
		") or die('//#error: Erro no servidor!'. ($_SESSION['login']['level']>7? '<br /><br />Explorer.updateFile::UPDATE (2) Error: '. mysql_error(): '') . PHP_EOL);
		
		$return	= mysql_affected_rows();
		
		if ( $name == 'filename' ) {
			####################################################################
			#                    FILENAME                                      #
			#                                                                  #
			# Quando um nome de diretório é alterado, precisa ser alterado     #
			#  também todos os arquivos que estão dentro deste diretório       #
			#                                                                  #
			####################################################################
			
			$crrPath	= $Pager['filename'];
			$oldFull	= $Pager['path'] . $crrPath .'/';
			$newFull	= $Pager['path'] . $value .'/';
			
			$len	= strlen($oldFull);
			$result	= mysql_query("
				SELECT
					id, path, filename
				FROM
					 gt8_explorer e
				 WHERE
					path LIKE '$oldFull%'
			");
			$return	= 0;
			require_once( SROOT .'engine/queries/urlHistory/addNewUrlHistory.php');
			while( ($row=mysql_fetch_assoc($result))) {
				$id	= $row['id'];
				mysql_query("
					UPDATE
						 gt8_explorer e
						SET
							path	= CONCAT(REPLACE(SUBSTRING(LOWER(e.path), 1, $len), LOWER('$oldFull'), '$newFull'), SUBSTRING(e.path, $len+1))
					 WHERE
						id = $id
				") or die('//#error: Erro no servidor (1).'. ($_SESSION['login']['level']>8? ' Explorer.updateFile::UPDATE: '. mysql_error(): '') . PHP_EOL);;
				$return++;
				
				//url history
				addNewUrlHistory(array(
					'old'	=> $GT8['admin']['root'] . $GT8['explorer']['root'] . $Pager['path'] . $Pager['filename'],
					'new'	=> $GT8['admin']['root'] . $GT8['explorer']['root'] . $Pager['path'] . $value,
					'format'	=> $format
				));
				addNewUrlHistory(array(
					'old'	=> $GT8['explorer']['root'] . $Pager['path'] . $Pager['filename'],
					'new'	=> $GT8['explorer']['root'] . $Pager['path'] . $value,
					'format'	=> $format
				));
			}
			
		}
		if ( $name == 'filename') {
			####################################################################
			#                        URL HISTORY                               #
			#                                                                  #
			# Registre as alterações dos urls para que não se percam links     #
			#                                                                  #
			####################################################################
			require_once( SROOT .'engine/queries/urlHistory/addNewUrlHistory.php');
			addNewUrlHistory(array(
				'old'	=> $GT8['admin']['root'] . $GT8['explorer']['root'] . $Pager['path'] . $Pager['filename'],
				'new'	=> $GT8['admin']['root'] . $GT8['explorer']['root'] . $Pager['path'] . $value,
				'format'	=> $format
			));
			addNewUrlHistory(array(
				'old'	=> $GT8['explorer']['root'] . $Pager['path'] . $Pager['filename'],
				'new'	=> $GT8['explorer']['root'] . $Pager['path'] . $value,
				'format'	=> $format
			));
		}
		
		require_once(SROOT.'engine/functions/LogAdmActivity.php');
		LogAdmActivity( array(
			"action"	=> "update",
			"page"		=> 'explorer/',
			"name"		=> $name,
			"value"		=> $value,
			"idRef"		=> $id
		));
		
		if ( $format == 'JSON') {
			if ( $return) {
				print('//#message: Alteração realizada com sucesso!'. PHP_EOL);
			} else {
				print('//#error: Não foi possível concluir a operação!'. PHP_EOL);
			}
			print('//#affected rows: '. $return. PHP_EOL);
		}
		
		return $return;
	}
?>
