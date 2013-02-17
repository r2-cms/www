<?php
	if ( !defined('SROOT')) {
		require_once( '../connect.php');
	}
	
	if ( !$_SESSION["login"]) {
		$_SESSION["login"]	= array(
			"logged"				=> $_SESSION["login"]["logged"],
			"name"					=> $_SESSION["login"]["name"],
			"minutesToKeepLogged"	=> 120,
			"daysToKeepLogged"		=> 3
		);
	}
	
	function CheckLogin( $login, $hpass, $keepLogged=false, $useMD5=true) {
		$huser	= md5(strtolower($login));
		$hpass	= RegExp($hpass, "[a-fA-F0-9]+");
		
		if ( !isset($_SESSION['analytics-page']) ) {
			//SUSPECT: DOS attack
			//Cookies não apresentam riscos, pois é feita validação antes. Mas se a procedência não for cookies, durma e morra
			if ( $useMD5 ) {
				sleep(30);
				die('-303');
			}
		}
		if ( isset($_SESSION['login']['last-try']) ) {
			if ( (time()-$_SESSION['login']['last-try']) > 300) {//deverá ser 300
				$_SESSION['login']['last-try']	= time();
				$_SESSION['login']['access-counter']	= -1;
			}
		}
		$_SESSION['login']['last-try']			= isset($_SESSION['login']['last-try'])? $_SESSION['login']['last-try']: time();
		$_SESSION['login']['access-counter']	= isset($_SESSION['login']['access-counter'])? $_SESSION['login']['access-counter']: -1;
		$_SESSION['login']['access-counter']	= $_SESSION['login']['access-counter'] + 1;
		
		$_SESSION['login']['access-counter']=1;
		if ( $_SESSION['login']['access-counter'] > 4) {
			
			if ( $_SESSION['login']['access-counter'] > 10) {
				sleep(300);
				die('//#error: Acesso negado!');
			}
			flush();
			sleep( pow( 2, $_SESSION['login']['access-counter']-4));
		}
		
		$result	= mysql_query("
			SELECT
				id, name, hlogin, login, level+0 AS level, enabled, pass, agent
			FROM
				gt8_users
			WHERE
				hlogin = '$huser' AND enabled = 1
			LIMIT
				1
		") OR die("//#error: Erro na consulta!<br />Por favor, contate o administrador do sistema.");
		
		//if user exists
		$error	= "";
		if ( ($row = mysql_fetch_assoc($result))) {
			//print( $hpass . PHP_EOL);
			//print( ($row["pass"] .'-'. $_SESSION['GT8']['tstart']) .PHP_EOL);
			//print( md5($row["pass"] .'-'. $_SESSION['GT8']['tstart']) .PHP_EOL);
			if ( ($useMD5==true&&md5($row["pass"] .'-'. $_SESSION['GT8']['tstart']) == $hpass) || ($useMD5==false&&$hpass==$row['pass']) ) {
				//ok
				//update the agent user
				mysql_query('UPDATE gt8_users SET agent = "'. $_SERVER["REMOTE_ADDR"] .'####'. date('Y/m/d H:i:s') .'####'. mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']) .'" WHERE id = '. $row['id']) or die('LOGIN UPDATE user agent error!');
				
				//update the a.id_users
				if ( isset($_SESSION['analytics-page']) && $_SESSION['analytics']['id']>1) {//1 é default e quer dizer que não existe no banco
					mysql_query("
						UPDATE
							gt8_analytics a
						SET
							a.id_users = {$row['id']}
						WHERE
							id = {$_SESSION['analytics']['id']}
					") or die('//#error: LOGIN::UPDATE user id error');
				}
			} else {
				$error	= "//#error: Nome de usuário ou senha inválidos!";
			}
		} else {
			$error	= "//#error: Nome de usuário ou senha inválidos! ";
		}
		
		$_SESSION['param-cache']		= array();
		
		if ( empty($error)) {
			$_SESSION["login"]["logged"]		= true;
			$_SESSION["login"]["login"]			= $row["login"];
			$_SESSION["login"]["id"]			= $row["id"];
			$_SESSION["login"]["name"]			= $row["name"];
			$_SESSION["login"]["level"]			= $row["level"];
			$_SESSION["login"]["I am Darth Vader"]	= $row["level"] == 11;
			$_SESSION["login"]["hpass"]			= md5($row["pass"]);
			unset($_SESSION['login']['access-counter']);
			unset($_SESSION['login']['last-try']);
			
			if ( $keepLogged) {
				LogCookie( $huser, md5($row["pass"]));
			}
			
			//update users info
			mysql_query("UPDATE gt8_users SET access_counter = access_counter+1 WHERE id = ". $row['id']);
			
			if ( $_GET["format"] === "JSON") {
				print("
					//#affected: 1
					//#message: Acesso garantido!
				");
				//####{$row['agent']}####{$_SERVER['REMOTE_ADDR']}####{$_SERVER['HTTP_USER_AGENT']}
			}
			return true;
		} else {
			//print($error); //debug porposes only
		}
		$_SESSION["login"]["logged"]		= false;
		
		LogOut();
		return false;
	}
	//unset($_SESSION['login']);
	function LogCookie( $huser, $hpass) {
		if ( !headers_sent()) {
			$plusTime	= 60 * 60 * 24 * (isset($_SESSION["login"]["daysToKeepLogged"])?$_SESSION["login"]["daysToKeepLogged"]:3);
			$expires	= time() + $plusTime;
			setcookie("cuser", $huser, $expires, "/");
			setcookie("cpass", md5($hpass . $plusTime), $expires, "/");
		}
		
	}
	function LogOut() {
		//eliminamos toda a sessão de login, exceto a propriedade 'last-try' que é usada para verificar a periodicidade de acesso
		$lastTry	= $_SESSION['login']['last-try'];
		$counter	= $_SESSION['login']['access-counter'];
		unset($_SESSION["login"]);
		$_SESSION['login']	= array(
			'last-try'	=> $lastTry,
			'access-counter'	=> $counter
		);
		
		if ( !headers_sent()) {
			if ( $_GET["format"] === "JSON") {
				die("//#error: Nome de usuário ou senha inválidos!");
			} else {
				return;
			}
		} else if ( $_GET["format"] === "JSON") {
			die("//#error: Nome de usuário ou senha inválidos!");
		}
	}
	//DEFAULT FLOW
	if ( isset($_GET["logout"])) {
		$_SESSION['param-cache']		= array();
		if ( !headers_sent()) {
			setcookie("cuser", $_COOKIE['cuser'], time()-(60*60*24*$_SESSION["login"]["daysToKeepLogged"]), "/");
			setcookie("cpass", $_COOKIE['cpass'], time()-(60*60*24*$_SESSION["login"]["daysToKeepLogged"]), "/");
		}
		LogOut();
		header('location: ./');
		die();
	} else if ( isset($_SESSION["login"]["logged"]) && $_SESSION["login"]["logged"] ) {//session
		
		if ( $_SESSION["login"]["keepLogged"]) {
			LogCookie( $_SESSION["login"]["huser"], $_SESSION["login"]["hpass"]);
		}
		
	} else if ( isset($_GET["user"]) && isset($_POST["pass"]) && $_GET["user"] && $_POST["pass"] ) {
		
		CheckLogin( $_GET["user"], $_POST["pass"], ($_GET["keepLogged"]==="1"||$_GET["keepLogged"]==="true"));
		
	} else if ( isset($_COOKIE['cuser']) && isset($_COOKIE["cpass"]) && $_COOKIE['cuser'] && $_COOKIE["cpass"] ) {
		//se o usuário não passou por nenhuma outra página, é suspeito. Durma por 30 segundos, por segurança
		if ( !isset($_SESSION['analytics-page'])) {
			sleep(30);
		}
		$cuser	= RegExp($_COOKIE["cuser"],"[a-zA-Z0-9]+");
		$cpass	= RegExp($_COOKIE["cpass"],"[a-zA-Z0-9]+");
		$plusTime	= 60 * 60 * 24 * (isset($_SESSION["login"]["daysToKeepLogged"])?$_SESSION["login"]["daysToKeepLogged"]:3);
		$result	= mysql_query("
			SELECT
				hlogin, login, pass
			FROM
				gt8_users
			WHERE
				hlogin	= '$cuser'
			LIMIT
				1
		") OR die("//#error: Erro ao acessar o banco de dados.<br />Por favor, contate o administrador do sistema. Cod: chk(2)");
		$row	= mysql_fetch_assoc($result);
		
		if ( md5(md5($row['pass']) . $plusTime) == $cpass) {
			CheckLogin($row["login"], $row["pass"], true, false);
		} else {
			LogOut();
		}
	} else {
		LogOut();
		if ( !isset($GT8)) {
			global $GT8;
		}
		require_once( SROOT .'engine/controllers/account/Login.php');
		$Login	= new Login();
		GT8::printView(
			(Login::$includeCustomView? Login::$includeCustomView: SROOT .'engine/views/'. ( strpos('#'.$_SERVER['REQUEST_URI'], '/'.$GT8['admin']['root'])>-1?'admin/':'' ) .'account/login.inc'),
			array(),
			null,
			$Index=$Login
		);
		die();
	}
?>