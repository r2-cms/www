<?php
	require_once("../../engine/connect.php");
	
	
	if ( $_GET['opt'] == 'post') {
		//security: o seguro morreu de velho
		if ( !isset($_SESSION['analytics-page']) ) {
			sleep(30);
			die('//#affected rows: 0');
		}
		
		$_GET['message']	= $_POST['message'];
		
		//validação dos campos
		$name		= str_replace( str_split('<>%$&;{}[]\'"()*'), '-', $_GET['name']);
		$site		= $_GET['site']? RegExp($_GET['site'], '[a-zA-Z0-9_\.\-\+\=\,\:\/]+'): '';
		$mail		= $_GET['mail']? RegExp($_GET['mail'], '[a-zA-Z0-9\.\-\_\@]+'): '';
		$idReplay		= (integer)$_GET['idReplay'];
		$message		= substr(mysql_real_escape_string($_GET['message']), 0, 1024);
		$idAnalytics	= $_SESSION['analytics-page'];
		$idUser			= isset($_SESSION['login'])? $_SESSION['login']['id']: 0;
		
		if ( $name && $message && $idAnalytics) {
			$ar	= explode( '\n', $message);
			$message	= '';
			foreach($ar AS $value) {
				$message	.= '<p>'. $value .'</p>';
			}
			
			mysql_query("
				INSERT INTO
					gt8_co33e210(
						id_users,
						id_replay,
						id_analytics,
						nm,
						ml,
						st,
						cmmnt,
						creation
					)
					VALUES(
						$idUser,
						$idReplay,
						$idAnalytics,
						'$name',
						'$mail',
						'$site',
						'$message',
						NOW()
					)
			") or die("//#error: DB query error!". (isset($_SESSION['login'])&&$_SESSION['login']['level']>6? mysql_error(): ''). PHP_EOL);
			
			print('//#affected rows: 1'. PHP_EOL);
			
			require_once( SROOT . 'engine/mail/Mail.php');
			$m	= new Mail(200);
			$m->printAfterSending	= false;
			$m->copyOnDb	= false;
			$m->send($_GET);
			
		} else {
			die('//#error: invalid field'. PHP_EOL);
		}
	} else if ( $_GET['opt'] == 'get') {
		require_once( SROOT .'engine/functions/Pager.php');
		$Pager	= Pager(array(
			'sql'	=> 'comments.list',
			'limit'	=> 10,
			'format'	=> 'CARD',
			'card8'	=> '
									<div class="comment" >
										<img class="icon" src="imgs/comments/user-default.png" alt="" width="50" height="50"/>
										<div class="line" >@nm@</div>
										<div class="comment" >
											@cmmnt@
										</div>
										<div class="clear" ></div>
									</div>
			'
		));
		print($Pager['rows']);
	}
?>