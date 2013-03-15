<?php
	if ( !defined('SROOT')) {
		die('//#error: Chamada inválida em classes.upload!'. PHP_EOL);
	}
	
	require_once( SROOT .'engine/functions/CheckLogin.php');
	require_once( SROOT .'engine/queries/explorer/SaveData.php');
	
	class Index	extends SaveData {
		public $windowWidth		= 150;
		public $windowHeight	= 130;
		
		function __construct() {
			$options	= array(
				'id'	=> isset($_GET["id"])? (integer)$_GET["id"]: $this->id,
				'file'		=> $_FILES["img"],
				'action'	=> 'upload'
			);
			parent::SaveData($options);
			
			$this->windowWidth		= isset($_GET["W"])? (integer)$_GET["W"]: $this->windowWidth;
			$this->windowHeight		= isset($_GET["H"])? (integer)$_GET["H"]: $this->windowHeight;
		}
	}
?>