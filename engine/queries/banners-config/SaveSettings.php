<?php
	if ( !defined('CROOT')) {
		die('Undefined connection! (Q40->A114e00::U91a1e)');
	}
	require_once( SROOT .'engine/classes/Update.php');
	/*
			Options:
				page
				name		page|limit|source (forbidden: page, creation, modification)
				value		String ids ([0-9]+,[0-9]+,...)
				format
	*/
	class SaveSettings extends Update {
		public $name	= 'banners_config';
		public $privilegeName	= 'banners-config/';
		private $isContact	= false;
		
		public function __construct($options) {
			$page	= mysql_real_escape_string($options['page']);
			$format		= isset($options['format'])? $options['format']: 'OBJECT';
			
			if ( $options['field'] == 'page') {
				if ( $options['format'] == 'JSON') {
					print('//#error: Não é possível alterar o nome da página. Por favor, contate o administrador.'. PHP_EOL);
				}
				return 'ERROR: "page" is an invalid parameter!';
			}
			$this->name				= $this->name;
			$this->privilegeName	= $this->privilegeName . $page .'/';
			$this->id				= 0;
			require_once( SROOT .'engine/functions/Pager.php');
			$Pager	= Pager(array(
				'sql'	=> 'banners-config.list',
				'required'	=> array(
					array('page', $page)
				)
			));
			$Pager	= $Pager['rows'][0];
			$this->id	= $Pager['id'];
			$this->Update( $options);
			
		}
		public function getValue( $field, $value) {
			if ( $field == 'source') {
				$value	= RegExp($value, '[0-9\,]+');
				$ids	= array();
				if ( $value) {
					$value	= explode(',', $value);
					for( $i=0; $i<count($value); $i++) {
						$crr	= (integer)$value[$i];
						if ( $crr) {
							$ids[]	= $crr;
						}
					}
				}
				if ( count($ids)) {
					$value	= join(',', $ids);
				} else {
					$value	= '';
				}
			}
			return $value;
		}
	}
?>