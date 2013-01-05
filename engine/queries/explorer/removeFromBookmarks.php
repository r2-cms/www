<?php
	if ( !defined('CROOT')) {
		die('//#error: Undefined GT8: Explorer.removeFromBookmarks!'. PHP_EOL);
	}
	require_once( SROOT .'engine/functions/CheckLogin.php');
	/*
		Boolean removeFromBookmarks( id)
			Properties:
				id			Required file/directory id
	*/
	function removeFromBookmarks( $id) {
		
		$id	= (integer)$id;
		
		if ( !$id) {
			die('//id is missing!'. PHP_EOL);
		}
		
		if ( !isset($_SESSION['login']['id'])) {
			sleep(30);
			die();
		}
		
		require_once( SROOT .'engine/functions/Pager.php');
		$Pager	= Pager(array(
			'sql'	=> 'explorer.list',
			'ids'	=> array(
				array('e.id', $id)
			)
		));
		
		$idUser	= (integer)$_SESSION['login']['id'];
		
		//what's the last new file index?
		mysql_query("
			DELETE
			FROM
				gt8_explorer_bookmarks
			WHERE
				id_dir = $id AND
				id_users = $idUser
		") or die('que4ie0->explorer->removeFromBookmarks::DELETE Error: '. mysql_error());
		
		print('//bookmarks deleted successfully! ('. mysql_affected_rows() .')');
	}
?>
