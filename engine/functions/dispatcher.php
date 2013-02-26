<?php
	
	global $GT8;
	$spath	= $_GET['path'];
	
	$base	= '';
	$obj	= $GT8;
	$paths	= explode('/', substr($spath, 0, (strpos($spath, '?')?substr($spath, 0, strpos($spath, '?')):strlen($spath))));
	$found	= false;
	//////////////////////// EXPLORER //////////////////////////////////////
	if ( ($paths[0].'/') == $GT8['explorer']['root']) {
		//se a requisição for imagem (endereço virtual e !admin), apenas verifique os privilégios e carregue a imagem
		if ( isset($_GET['path']) && (strpos($_GET['path'], $GT8['admin']['root'] . $GT8['explorer']['root'])===false) ) {
			require( SROOT .'engine/controllers/admin/explorer/LoadPhisic.php');
			die();
		}
		print("<h1>Não funcionou :(dispatcher.php)</h1>".PHP_EOL);
		print("<pre>". print_r(222222, 1) ."</pre>". PHP_EOL);
		die();
		//require( SROOT .'engine/controllers/admin/explorer/Index.php');
		die();
	}
	
	for ( $ipath=0; $ipath<count($paths); $ipath++) {
		$path	= $paths[$ipath];
		$found	= false;
		foreach( $obj as $name=>$value) {
			if ( isset($value['root']) && strtolower($value['root'])== strtolower($path.'/')) {
				$base	.= $name.'/';
				$obj	= $value;
				$found	= true;
				break;
			}
		}
		
		if ( $found) {
			
		} else {
			//print("<h1>f: ". (SROOT.'engine/views/'.$base.$path) ."</h1>".PHP_EOL);
			if ( file_exists(SROOT.'engine/views/'.$base.$path)) {
				$base	.= $path .'/';
				$obj	= array();
				$found	= true;
			} else {//NOT FOUND :(
				//print("<h1>Not found: ". SROOT ."engine/views/$base$path</h1>".PHP_EOL);
				break;
			}
		}
	}
	
	$base	= substr($base, 0, -1);
	require_once(SROOT.'engine/classes/GT8.php');
	$fileviewer	= $base;
	$controller	= $base;
	//experimental
	$Index	= null;
	$Data	= array(
		'path'		=> RegExp($_GET['path'], '[a-z-A-Z0-9_\-\+\.\:\/\\\|\%\&\?\@]+'),
		'login'		=> isset($_SESSION['login']['login'])? $_SESSION['login']['login']: '',
		'login-id'	=> isset($_SESSION['login']['id'])? $_SESSION['login']['id']: ''
	);
	if ( isset($_GET['action']) && $_GET['action'] === 'login') {
		require_once( SROOT ."engine/functions/CheckLogin.php");
	}
	if ( $found && substr($base, -1)=='/') {//DIR
		
		//se houver um arquivo com o mesmo nome do diretório, iniciando em maísculo, é classe com auto inicialização. Carreguemo-la!
		$baseClassName	= strpos($controller, '/')+1<strlen($controller)? substr( $controller, strpos($controller, '/')+1): $controller;
		//$baseClassName	= strtoupper(substr($baseClassName, 0, 1)) . substr($baseClassName, 1, (strlen($baseClassName)-2));
		$baseClassName	= explode('/', $baseClassName);
		if ( empty($baseClassName[count($baseClassName)-1]) ) {
			array_pop($baseClassName);
		}
		$baseClassName	= ucfirst($baseClassName[count($baseClassName)-1]);
		$fileController	= '';
		if ( file_exists(SROOT.'engine/controllers/'. $controller . $baseClassName .'.php')) {
			$fileController	= SROOT.'engine/controllers/'. $controller . $baseClassName .'.php';
		} else {
			$fileController	= explode('/', $controller);
			if ( empty($fileController[count($fileController)-1])) {
				array_pop( $fileController);
			}
			array_pop( $fileController);
			$fileController	= join('/', $fileController) .'/'. $baseClassName;
			
			if ( file_exists( SROOT .'engine/controllers/'. $fileController .'.php')) {
				$fileController	= SROOT .'engine/controllers/'. $fileController .'.php';
			} else if ( strpos('#'.$baseClassName, '-')>0) {
				while ( ($pos=strpos('#'.$baseClassName, '-')) > 0) {
					$baseClassName	= substr($baseClassName, 0, $pos-1) . substr(strtoupper($baseClassName), $pos, 1) . substr($baseClassName, $pos+1);
				}
				$fileController	= SROOT .'engine/controllers'. strtolower($fileController) .'/'. $baseClassName .'.php';
				if ( !file_exists($fileController)) {
					$fileController	= '';
				}
				
			} else {
				$fileController	= '';
			}
		}
		
		//camel case class name
		
		if ( $fileController ) {
			require( $fileController);
			$Index	= new $baseClassName;
			$Data;
			if ( isset($Index->data)) {
				$Data	= array_merge($Index->data, $Data);
			}
			$Index->printView(
				file_exists(SROOT .'engine/views/'. $fileviewer . strtolower($baseClassName) .'.inc')? SROOT .'engine/views/'. $fileviewer . strtolower($baseClassName) .'.inc': SROOT .'engine/views/'. $fileviewer.'index.inc',
				$Data,
				null,
				$Index
			);
			die();
		}
		$fileviewer	.= 'index.inc';
		$controller	.= 'Index.php';
	} else if ($found) {//FILE
		
	} else {//NOT FOUND
		//has custom?
		$virtualPath	= array();
		for( $ipath; $ipath<count($paths); $ipath++) {
			$virtualPath[]	= $paths[$ipath];
		}
		$virtualPath	= join('/', $virtualPath);
		$pathLevels		= count(explode('/', $virtualPath));
		
		//se houver índices de controles...(eg: level-2, level-3, etc)
		for ( $i=$pathLevels; $i>0; $i--) {
			if ( file_exists(SROOT."engine/controllers/$controller/Level-$i.php") ) {
				require_once(SROOT."engine/controllers/$controller/Level-$i.php");
				if ( file_exists(SROOT."engine/views/$controller/level-$i.inc") ) {
					if ( !isset($Data)) {
						$Data	= array();
					}
					$Data['path']	= isset($Data['path'])? $Data['path']: RegExp($_GET['path'], '[a-z-A-Z0-9_\-\+\.\:\/\\\|\%\&\?\@]+');
					$Data['login']	= isset($_SESSION['login']['login'])? $_SESSION['login']['login']: '';
					$Data['login-id']	= isset($_SESSION['login']['id'])? $_SESSION['login']['id']: '';
					
					$Index	= null;
					if ( class_exists('Index')) {
						$Index	= new Index();
						if ( isset($Index->data)) {
							$Data	= array_merge($Index->data, $Data);
						}
					}
					GT8::printView(
						SROOT ."engine/views/$controller/level-$i.inc",
						$Data,
						null,
						$Index
					);
				}
				die();
			}
		}
		
		$fileV;
		$baseClassName	= strtoupper(substr($controller, 0, 1)) . substr($controller, 1);
		//camel case class name
		while ( ($pos=strpos('#'.$baseClassName, '-')) > 0) {
			$baseClassName	= substr($baseClassName, 0, $pos-1) . substr(strtoupper($baseClassName), $pos, 1) . substr($baseClassName, $pos+1);
		}
		if ( file_exists(SROOT.'engine/controllers/'.$controller .'/Editor.php') ) {//o Editor.php deve ter precedência sobre o Index.php
			require(SROOT.'engine/controllers/'.$controller .'/Editor.php');
			if ( file_exists(SROOT.'engine/views/'.$controller .'/editor.inc') ) {
				$fileV	= SROOT .'engine/views/'. $controller .'/editor.inc';
			}
		} else if ( file_exists(SROOT.'engine/controllers/'.$controller .'/Index.php') ) {
			require(SROOT.'engine/controllers/'.$controller .'/Index.php');
			if ( file_exists(SROOT.'engine/views/'.$controller .'/index.inc') ) {
				$fileV	= SROOT .'engine/views/'. $controller .'/index.inc';
			}
		} else if ( file_exists(SROOT.'engine/controllers/'. $controller .'/'. $baseClassName .'.php') ) {
			require(SROOT.'engine/controllers/'. $controller .'/'. $baseClassName .'.php');
			if ( file_exists(SROOT.'engine/views/'.$controller .'/index.inc') ) {
				$fileV	= SROOT .'engine/views/'. $controller .'/index.inc';
			} else if ( file_exists(SROOT.'engine/views/'.$controller .'/'. strtolower($baseClassName) .'.inc') ) {
				$fileV	= SROOT.'engine/views/'.$controller .'/'. strtolower($baseClassName) .'.inc';
			}
			$Index	= new $baseClassName;
			if ( method_exists($Index, 'on404')) {
				$Index->on404();
			}
		} else if ( file_exists(SROOT.'engine/controllers/'.$controller .'/404.php') ) {
			require(SROOT.'engine/controllers/'.$controller .'/404.php');
			if ( file_exists(SROOT.'engine/views/'.$controller .'/404/index.inc') ) {
				$fileV	= SROOT .'engine/views/'. $controller .'/404/index.inc';
			}
		} else {
			$fileV	= SROOT .'engine/views/404/index.inc';
		}
		
		if ( class_exists('AdminEditor')) {
			$Editor	= new AdminEditor();
			if ( method_exists($Editor, 'on404')) {
				$Editor->on404();
			}
		} else if ( class_exists('Index')) {
			$Index	= new Index();
			if ( method_exists($Index, 'on404')) {
				$Index->on404();
			}
		} else if ( class_exists('Editor')) {
			$Editor	= new Editor();
			if ( method_exists($Editor, 'on404')) {
				$Editor->on404();
			}
		}
		if ( $Editor && isset($Editor->data)) {
			$Data	= array_merge($Editor->data, $Data);
		} else if ( $Index && isset($Index->data)) {
			$Data	= array_merge($Index->data, $Data);
		}
		
		if ( $Editor) {
			$Editor->printView(
				$fileV,
				$Data,
				$Editor,
				$Index
			);
		} else if ( $Index) {
			$Index->printView(
				$fileV,
				$Data,
				$Editor,
				$Index
			);
		} else {
			GT8::printView(
				$fileV,
				$Data,
				$Editor,
				$Index
			);
		}
		die();
	}
	if ( file_exists(SROOT.'engine/controllers/'.$controller) && is_dir(SROOT.'engine/controllers/'.$controller)) {
		header('location: '. $paths[count($paths)-1] .'/');
		die();
	} else if ( file_exists(SROOT.'engine/controllers/'.$controller) ) {
		require_once(SROOT.'engine/controllers/'.$controller);
	} else {
		$mime	= '';
		$useView	= false;
		switch( substr($fileviewer, -4)) {
			case '.css':
				$mime	= 'text/css';
				$useView	= true;
				break;
			case '.jpg':
				$mime	= 'image/jpg';
				break;
			case '.jpeg':
				$mime	= 'image/jpg';
				break;
			case '.png':
				$mime	= 'image/png';
				break;
			case '.gif':
				$mime	= 'image/gif';
				break;
		}
		if ( !$mime) {
			switch( substr($fileviewer, -3)) {
				case '.js':
					$mime	= 'text/javascript';
					$useView	= true;
					break;
			}
		}
		if ( $mime) {
			header("Content-Type: $mime");
			if ( $useView) {
				$gt8	= new GT8();
				$gt8->printView(SROOT.'engine/views/'.$fileviewer);
			} else {
				print(file_get_contents(SROOT.'engine/views/'.$fileviewer));
			}
			die();
		}
	}
	//require(SROOT.'engine/views/'.$fileviewer);
	//die();
	if ( class_exists('Index')) {
		$Index	= new Index();
		if ( isset($Index->data)) {
			$Data	= array_merge($Index->data, $Data);
		}
	}
	if ( file_exists(SROOT .'engine/views/'. $fileviewer) && !is_dir(SROOT .'engine/views/'. $fileviewer) ) {
		if ( $Index) {
			$Index->printView(
				SROOT .'engine/views/'. $fileviewer,
				$Data,
				null,
				$Index
			);
		} else {
			GT8::printView(
				SROOT .'engine/views/'. $fileviewer,
				$Data,
				$Editor
			);
		}
		die();
	}
?>