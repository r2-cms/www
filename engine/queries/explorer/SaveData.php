<?php
	if ( !defined('SROOT')) {
		die('//#error: Chamada inválida em classes.upload!'. PHP_EOL);
	}
	
	require_once( SROOT .'engine/classes/SimpleImage.php');
	require_once( SROOT .'engine/functions/Pager.php');
	
	class SaveData extends SimpleImage {
		public $crrPath		= '';
		public $imgPath;
		public $pathBaseless	= '';
		
		//se desejar que não haja redimensionamento, defina estes valores como zero (0)
		public $maxImageWidth	= 2560;
		public $maxImageHeight	= 1440;
		
		//db settings
		public $id			= 0;
		public $fileType	= '';
		public $isUpload	= false;
		public $mimeType;
		public $htmlTemplate	= '';
		public $fileExtension	= '';
		
		protected $Pager;
		protected $hasImage	= false;
		
		static protected $sizeData	= array( 2560, 1440);
		static protected $sizePreview	= array( 640, 480);
		static protected $sizeRegular	= array( 256, 256);
		static protected $sizeSmall		= array( 128, 128);
		static protected $sizeThumb		= array( 32, 32);
		
		function SaveData($options) {
			global $GT8;
			
			$this->id				= $options['id'];
			
			
			//obtendo endereço da imagem do banco de dados, se configurado
			if ( !$this->id ) {
				die('//missing id');
			}
			$this->Pager	= Pager(array(
				'sql'		=> 'explorer.list',
				'ids'		=> array(
					array('e.id', $this->id)
				)
			));
			$this->Pager	= $this->Pager['rows'][0];
			$this->crrPath	= CROOT . $GT8['explorer']['root'] . $this->Pager['path'] . $this->Pager['filename'];
			$this->pathBaseless	= $GT8['explorer']['root'] . $this->Pager['path'] . $this->Pager['filename'];
			
			$this->hasImage	= true;
			
			$sizeRequested	= 'regular';
			switch( $_GET['s']) {
				case 'thumb':	$sizeRequested	= 'thumb';		break;
				case 'small':	$sizeRequested	= 'small';		break;
				case 'regular':	$sizeRequested	= 'regular';	break;
				case 'preview':	$sizeRequested	= 'preview';	break;
				case 'large':	$sizeRequested	= 'large';		break;
				default:		$sizeRequested	= 'regular';	break;
			}
			$this->imgPath	= $this->crrPath.'?'. ($sizeRequested) .'&amp;nocache='. time();
			
 			if ( $options['action'] == "upload" && ($img = $options['file'])) {
				
				$this->mimeType	= $img['type'];
				
				//update file extension
				$type	= $this->mimeType;
				$ext	= substr($type, strpos('#'. $type, '/'));
				$fileName	= $this->Pager['filename'];
				if ( $ext ) {
					$this->fileExtension		= $ext;
				}
				$this->SaveImageOnDB($img);
				$this->onUpload();
				$this->crrPath	= $this->crrPath;
				$this->isUpload	= true;
			} else if ( $_GET["opt"] == "delete" ) {
				
				mysql_query("
					UPDATE
						gt8_explorer_data d
						JOIN gt8_explorer e ON e.id = d.id
					SET
						d.thumb			= '',
						d.small			= '',
						d.regular		= '',
						d.preview		= '',
						d.data			= '',
						e.modification	= NOW(),
						e.mime			= '',
						e.size			= 0
					WHERE
						d.id = {$this->id}
				") or die('SQL UPDATE ERROR: '. mysql_error() .'.'. PHP_EOL.'Probaly the row does not exists.');
				
				//some updates
				$this->Pager['size']			= 0;
				$this->Pager['modification']	= date('Y/m/d H:i:s');
				exit();
			} else {
				$this->onBeforeLoad();
				$this->crrPath	= $this->crrPath;
			}
		}
		public function getServerJSVars() {
			global $GT8;
			$this->jsVars[]	= array('id', $this->id, true);
			$this->jsVars[]	= array('hasImage', $this->hasImage? 'true': 'false', true);
			$this->jsVars[]	= array('windowWidth', $this->windowWidth, true);
			$this->jsVars[]	= array('windowHeight', $this->windowHeight, true);
			$this->jsVars[]	= array('fileSize', $this->Pager['size'], true);
			$this->jsVars[]	= array('fileName', $this->Pager['filename']);
			$this->jsVars[]	= array('fileExtension', $this->fileExtension);
			$this->jsVars[]	= array('width', $this->Pager['width']);
			$this->jsVars[]	= array('height', $this->Pager['height']);
			$this->jsVars[]	= array('locked', $this->Pager['locked'], true);
			$this->jsVars[]	= array('src', $this->pathBaseless);
			$this->jsVars[]	= array('CROOT', CROOT);
			$this->jsVars[]	= array('AROOT', CROOT.$GT8['admin']['root']);
			
			return parent::getServerJSVars();
		}
		public function SaveImageOnDB( $file) {
			$datas	= array(
				'thumb'		=> '',
				'small'		=> '',
				'regular'	=> '',
				'preview'	=> '',
				'data'		=> '',
				'width'		=> 0,
				'height'	=> 0
			);
			if ( strpos('#'. $this->mimeType, 'image/')) {
				$this->load( $file['tmp_name']);
				$w	= $this->getWidth();
				$h	= $this->getHeight();
				
				$datas['width']		= $w;
				$datas['height']	= $h;
				
				if ( count(self::$sizeData) > 0 && (self::$sizeData[0] < $w || self::$sizeData[1] < $h)) {
					if ( $w > $h) {
						$this->resizeToWidth( self::$sizeData[0]);
					} else {
						$this->resizeToHeight( self::$sizeData[1]);
					}
					//print("<h1>Data resized</h1>");
					$datas['data'] = $this->output( null, true);
				} else {
					$datas['data']	= file_get_contents($file['tmp_name']);
				}
				if ( self::$sizePreview && (self::$sizePreview[0] < $w || self::$sizePreview[1] < $h)) {
					if ( $w > $h ) {
						$this->resizeToWidth( self::$sizePreview[0]);
					} else {
						$this->resizeToHeight( self::$sizePreview[1]);
					}
					//print("<h1>Preview resized</h1>");
					$datas['preview'] = $this->output( null, true);
				} else {
					$datas['preview']	= file_get_contents($file['tmp_name']);
				}
				if ( self::$sizeRegular && (self::$sizeRegular[0] < $w || self::$sizeRegular[1] < $h)) {
					if ( $w > $h ) {
						$this->resizeToWidth( self::$sizeRegular[0]);
					} else {
						$this->resizeToHeight( self::$sizeRegular[1]);
					}
					//print("<h1>Regular resized</h1>");
					$datas['regular'] = $this->output( null, true);
				} else {
					$datas['regular']	= file_get_contents($file['tmp_name']);
				}
				if ( self::$sizeSmall && (self::$sizeSmall[0] < $w || self::$sizeSmall[1] < $h) ) {
					if ( $w > $h ) {
						$this->resizeToWidth( self::$sizeSmall[0]);
					} else {
						$this->resizeToHeight( self::$sizeSmall[1]);
					}
					//print("<h1>Small resized</h1>");
					$datas['small'] = $this->output( null, true);
				} else {
					$datas['small']	= file_get_contents($file['tmp_name']);
				}
				if ( self::$sizeThumb && (self::$sizeThumb[0] < $w || self::$sizeThumb[1] < $h)) {
					if ( $w > $h ) {
						$this->resizeToWidth( self::$sizeThumb[0]);
					} else {
						$this->resizeToHeight( self::$sizeThumb[1]);
					}
					//print("<h1>Thumb resized</h1>");
					$datas['thumb'] = $this->output( null, true);
				} else {
					$datas['thumb']	= file_get_contents($file['tmp_name']);
				}
			} else {
				$datas['data']	= file_get_contents($file['tmp_name']);
				unlink($file['tmp_name']);
			}
			
			//create if not exists
			mysql_query("
				INSERT INTO
					gt8_explorer_data( id, thumb, small, regular, preview, data)
				SELECT
					{$this->id}, '', '', '', '', ''
				FROM
					gt8_explorer_data
				WHERE
					id = {$this->id}
				HAVING
					COUNT(*) = 0
			") or die('Explorer.upload::SQL CREATE ERROR!');
			
			//atualiza e zera os campos
			mysql_query("
				UPDATE
					gt8_explorer_data d
					JOIN gt8_explorer e ON e.id = d.id
				SET
					e.width			= '{$datas['width']}',
					e.height		= '{$datas['height']}',
					e.modification	= NOW(),
					d.data			= '',
					d.preview		= '',
					d.regular		= '',
					d.small			= '',
					d.thumb			= '',
					e.mime			= '{$this->mimeType}',
					e.size			= ". strlen($datas['data']) ."
				WHERE
					d.id = {$this->id}
			") or die('Explorer.upload::SQL INSERT (2) ERROR: '. mysql_error() .'.'. PHP_EOL.'Probaly the row does not exists.');
			
			//insere em chunks para não ser barrado pelo max_allowed_packets
			$db	= array('data', 'preview', 'regular', 'small', 'thumb');
			for ( $i=0; $i<count($db); $i++) {
				$crr	= $db[$i];
				
				$data	= $datas[$crr];
				
				$chunk	= 200000;//200K
				$pos0	= 0;
				$pos1	= $chunk;
				while( $pos0 < strlen($data)) {
					$buffer	= substr($data, $pos0, $chunk);
					$pos0	= $pos1;
					$pos1	+= $chunk;
					
					if ( $buffer) {
						$sql	= ("
							UPDATE
								gt8_explorer_data d
								JOIN gt8_explorer e ON e.id = d.id
							SET
								d.$crr			= CONCAT(d.$crr, '". mysql_real_escape_string($buffer) ."')
							WHERE
								d.id = {$this->id}
						");
						mysql_query($sql) or die('Explorer.upload::SQL INSERT (2) ERROR!');
					}
				}
			}
		}
		public function onBeforeSubmit( $img) {
			print("<h1>submiting on extended class</h1>");
		}
		public function onBeforeLoad() {
			//CheckPrivileges( null, null, 'explorer/', 1);
		}
		public function onUpload() {
			return;
			//exemplo
			LogAdmActivity( array(
				"page"		=> "produtos/",
				"action"	=> "upload",
				"idRef"		=> $this->id,
				"name"		=> $this->field,
				"value"		=> $this->crrPath
			));
		}
	}
?>