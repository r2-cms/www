<?php
	/*
		Boolean deleteFile( id)
			Properties:
				id			Required file/directory id
	*/
	if ( !defined('CROOT')) {
		die('//#error: Undefined GT8: Explorer.delete!'. PHP_EOL);
	}
	require_once( SROOT .'engine/functions/CheckLogin.php');
	require_once( SROOT .'engine/functions/Pager.php');
	function deleteFile( $id) {
		$id	= (integer)$id;
		if ( !$id) {
			die('//#error: ID is missing!'. PHP_EOL);
		}
		
		$Pager	= Pager(array(
			'sql'	=> 'explorer.list',
			'ids'	=> array(
				array('e.id', $id)
			)
		));
		
		if ( !isset($Pager['rows'][0])) {
			print('//#error: O caminho para este arquivo não foi encontrado!<br />Ele pode ter sido já excluído.'. PHP_EOL);
			return null;
		}
		$idFile	= $id;
		$idDir	= $Pager['rows'][0]['id_dir'];
		$idUser	= (integer)$_SESSION['login']['id'];
		
		if ( $Pager['rows'][0]['locked']) {
			die('//#error: O arquivo está bloqueado!'. PHP_EOL);
		}
		
		$level	= (isset($_SESSION['login']['level'])? $_SESSION['login']['level']: 0);
		
		$affecteds	= 0;
		$filesDel	= 0;
		$foldersDel	= 0;
		$isDirectory	= $Pager['rows'][0]['type'] == 'directory';
		
		$path	= $Pager['rows'][0]['path'] . $Pager['rows'][0]['filename'] .'/';
		
		/***********************************************************************
		 *                                                                    *
		 *                     COUNT TOTAL                                    *
		 *                                                                    *
		***********************************************************************/
		$total	= mysql_query("
			SELECT
				COUNT(*) AS total
			FROM 
				gt8_explorer 
			WHERE
				1	= 1
				AND path LIKE '$path%'
		") or die('que4ie0->explorer->deleteFile::Directory SELECT (total) Error: '. mysql_error());
		$total	= mysql_fetch_array($total);
		$total	= isset($total[0])? $total[0]: 0;
		$total++;//este adicional refere-se a si mesmo
		
		/***********************************************************************
		 *                                                                    *
		 *                     LISTA OS DIRETÓRIOS                            *
		 *                                                                    *
		***********************************************************************/
		$result	= mysql_query("
			SELECT
				e.id, e.path, e.filename
			FROM 
				gt8_explorer e
				JOIN gt8_users u ON u.id = e.id_users
			WHERE
				1	= 1
				AND e.type = 'directory'
				AND e.path LIKE '$path%'
				AND e.locked = 0
				AND (u.id = ". $_SESSION['login']['id'] ." || e.write_privilege <= $level)
		") or die('que4ie0->explorer->deleteFile::Directory SELECT (dirs) Error: '. mysql_error());
		$dirs	= array();
		while( $row = mysql_fetch_assoc($result)) {
			$dirs[]	= $row;
		}
		
		/***********************************************************************
		 *                                                                    *
		 *                     LISTA OS ARQUIVOS                              *
		 *                                                                    *
		***********************************************************************/
		$result	= mysql_query("
			SELECT
				e.id
			FROM 
				gt8_explorer e
				JOIN gt8_users u ON u.id = e.id_users
			WHERE
				1	= 1
				AND e.path LIKE '$path%' 
				AND e.locked = 0
				AND e.type	= 'file'
				AND (u.id = ". $_SESSION['login']['id'] ." || e.write_privilege <=$level)
		") or die('que4ie0->explorer->deleteFile::ids selection Error: '. mysql_error());
		$ids	= array();
		while($row=mysql_fetch_array($result)) {
			$ids[]	= $row[0];
		}
		$affecteds	= 0;
		$filesDel	= $affecteds;
		if ( count($ids)) {
			/*******************************************************************
			 *                                                                 *
			 *                     EXCLUI EXPLORER                             *
			 *                                                                 *
			*******************************************************************/
			mysql_query("
				DELETE
					e.*
				FROM 
					gt8_explorer e
				WHERE
					id IN (". join(',', $ids) .")
			") or die('que4ie0->explorer->deleteFile::Directory DELETE (1) Error: '. mysql_error());
			$affecteds	= mysql_affected_rows() + 1;//este um refere-se ao diretório pai
			$filesDel	= $affecteds;
			
			/*******************************************************************
			 *                                                                 *
			 *                     EXCLUI DATA                                 *
			 *                                                                 *
			*******************************************************************/
			mysql_query("
				DELETE
					d.*
				FROM
					gt8_explorer_data d
				WHERE
					d.id IN (". join(',', $ids) .")
			") or die('que4ie0->explorer->deleteFile::Data DELETE Error: '. mysql_error());
			
			/*******************************************************************
			 *                                                                 *
			 *                     EXCLUI ATTRIBUTES                           *
			 *                                                                 *
			*******************************************************************/
			mysql_query("
				DELETE
					v.*
				FROM
					gt8_explorer_attributes_value v
				WHERE
					v.id_explorer IN (". join(',', $ids) .")
			") or die('que4ie0->explorer->multiDeleteAttribute::File DELETE Error: '. mysql_error());
		}
		
		/***********************************************************************
		 *                                                                    *
		 *                     EXCLUI DIRECTORY (EMPTY AND UNLOCKED)          *
		 *                                                                    *
		 *   Não precisa checar permissão, pois isto já foi feito             *
		***********************************************************************/
		for ( $i=0; $i<count($dirs); $i++) {
			$crr	= $dirs[$i];
			$path	= $crr['path'] . $crr['filename'] .'/';
			$id		= $crr['id'];
			
			/*******************************************************************
			 *                     EXCLUI DATA                                 *
			 *      Please, do not delete with join d.*, e.*.                  *
			 *       The result may lead to a different match on affected rows *
			*******************************************************************/
			mysql_query("
				DELETE
					d.*
				FROM 
					gt8_explorer e
					LEFT JOIN gt8_explorer_data d ON e.id = d.id
					JOIN gt8_users u ON u.id = e.id_users
					LEFT JOIN( SELECT $id AS id, COUNT(*) AS total FROM gt8_explorer WHERE path LIKE '$path%') z ON e.id = z.id
				WHERE
					1	= 1
					AND e.id = $id
					AND total = 0
			") or die('que4ie0->explorer->deleteFile::Directory Data DELETE (2) Error: '. mysql_error());
			
			/*******************************************************************
			 *                     EXCLUI FILE                                 *
			*******************************************************************/
			mysql_query("
				DELETE
					e.*
				FROM 
					gt8_explorer e
					JOIN gt8_users u ON u.id = e.id_users
					LEFT JOIN( SELECT $id AS id, COUNT(*) AS total FROM gt8_explorer WHERE path LIKE '$path%') z ON e.id = z.id
				WHERE
					1	= 1
					AND e.id = $id
					AND total = 0
			") or die('que4ie0->explorer->deleteFile::Directory DELETE (2) Error: '. mysql_error());
			$affecteds	+= mysql_affected_rows();
			$foldersDel	+= mysql_affected_rows();
		}
		
		require_once( SROOT.'engine/functions/LogAdmActivity.php');
		LogAdmActivity( array(
			"action"	=> "delete",
			"page"		=> 'explorer/',
			"name"		=> 'row',
			"value"		=> $id,
			"idRef"		=> $id
		));
		
		/*******************************************************************
		 *                                                                 *
		 *                     EXCLUI DATA                                 *
		 *                                                                 *
		 *      As exclusões acima eram apenas dos arquivos dentro deste   *
		 *        diretório. Esta exclusão, é do próprio arquivo (data)    *
		*******************************************************************/
		mysql_query("
			DELETE
				d.*
			FROM
				gt8_explorer e
				LEFT JOIN gt8_explorer_data d ON e.id = d.id
				JOIN gt8_users u ON u.id = e.id_users
			WHERE
				e.id = $idFile
				AND (u.id = ". $_SESSION['login']['id'] ." || e.write_privilege <= $level)
		") or die('que4ie0->explorer->deleteFile::Could not perform a self exclusion. ERROR: '. mysql_error());
		
		/*******************************************************************
		 *                     EXCLUI FILE                                 *
		*******************************************************************/
		mysql_query("
			DELETE
				e.*
			FROM
				gt8_explorer e
				JOIN gt8_users u ON u.id = e.id_users
			WHERE
				e.id = $idFile
				AND (u.id = ". $_SESSION['login']['id'] ." || e.write_privilege <= $level)
		") or die('que4ie0->explorer->deleteData::Could not perform a self exclusion. ERROR: '. mysql_error());
		$affecteds	+= mysql_affected_rows();
		
		if ( $affecteds < $total) {
			print("//#error: Alguns Alguns arquivos não puderam ser excluídos!<br />Verifique as permissões e tente novamente.". PHP_EOL);
		} else {
			print("//#affected rows: $affecteds". PHP_EOL);
			print("//#message: $total arquivos excluídos com sucesso!". PHP_EOL);
		}
		
		/***********************************************************************
		 *                     UPDATE COUNT                                    *
		***********************************************************************/
		while(
			($row = mysql_fetch_array(mysql_query("
				SELECT id_dir FROM gt8_explorer WHERE id = $idDir
			")))
		) {
			mysql_query("
				UPDATE
					gt8_explorer
				SET
					folders = folders - $foldersDel,
					files = files - $filesDel
				WHERE
					id = $idDir
			");
			$idDir	= $row[0];
		}
	}
?>
