<?php
	/*
		Boolean addToBookmarks( id)
			Properties:
				id			Required file/directory id
	*/
	if ( !defined('CROOT')) {
		die('//#error: Undefined GT8: Explorer.addToBookmarks!'. PHP_EOL);
	}
	require_once( SROOT .'engine/functions/CheckLogin.php');
	function addToBookmarks( $id) {
		
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
		
		$idUser	= $_SESSION['login']['id'];
		
		//what's the last new file index?
		mysql_query("
			INSERT INTO
				gt8_explorer_bookmarks( id_users, id_dir)
			SELECT
				$idUser, $id
			FROM
				gt8_explorer_bookmarks
			WHERE
				id_dir = $id AND
				id_users = $idUser
			HAVING
				COUNT(*) = 0
		") or die('que4ie0->explorer->addToBookmarks::INSERT-SELECT Error: '. mysql_error());
		
		print('//bookmarks inserted successfully! ('. mysql_insert_id() .')');
		return $id;
	}
?>
