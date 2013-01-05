<?php
	session_start();
	print("<h1>Session Object:</h1>".PHP_EOL);
	
	if ( isset($spath[2]) && $spath[2]) {
		print("<pre>". print_r($_SESSION[$spath[2]], 1) ."</pre>". PHP_EOL);
	} else {
		print("<pre>". print_r($_SESSION, 1) ."</pre>". PHP_EOL);
	}
?>