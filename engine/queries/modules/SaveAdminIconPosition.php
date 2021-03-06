<?php
	/*
		SaveAdminIconPosition
			module		Required module name
			page_index	Required page index
			card_index	Required card
			format
	*/
	if ( !defined('CROOT')) {
		die('Undefined connection! (Q40->Mo1u5e0::Sa8e)');
	}
	require_once( SROOT ."engine/classes/Update.php");
	class SaveAdminIconPosition extends Update {
		public $name	= 'modules';
		public $privilegeName	= 'modules/';
		
		public function SaveAdminIconPosition($options) {
			$options['id_users']	= $_SESSION['login']['id'];
			$options['card_index']	= (integer)$options['card_index'];
			$options['page_index']	= (integer)$options['page_index'];
			$options['module']	= RegExp($options['module'], '[a-zA-Z0-9\-\.\/]+');
			$options['id']	= mysql_query("SELECT id FROM gt8_modules WHERE id_users = {$options['id_users']} AND module = '{$options['module']}'");
			if ( !$options['id']) {
				die('invalid!');
			}
			$options['id']	= mysql_fetch_array($options['id']);
			$options['id']	= $options['id'][0];
			
			if ( !$options['module']) {
				die('//#error: Nome do módulo ausente!'. PHP_EOL);
			}
			
			if ( !$options['id']) {
				mysql_query("
					INSERT INTO
						gt8_modules( id_users, module, card_index, page_index)
					SELECT
						{$options['id_users']}, '{$options['module']}', {$options['card_index']}, {$options['page_index']}
					FROM
						gt8_modules
					WHERE
						id_users = {$options['id_users']} AND
						module = '{$options['module']}'
					HAVING
						COUNT(*) = 0
				");
				$options['id']	= mysql_insert_id();
			}
			
			if ( !$options['id']) {
				die('Could not create a new ID!');
			}
			$options['field']	= 'page_index';
			$options['value']	= $options['page_index'];
			
			mysql_query("
				UPDATE
					gt8_modules
				SET
					page_index	= {$options['page_index']},
					card_index	= {$options['card_index']}
				WHERE
					id = {$options['id']} AND
					id_users = {$options['id_users']}
					
			") or die("SQL Update Error on Qr::SAIP");
			
			if ( (isset($options['format']) && $options['format'] === 'JSON') || ( isset($_GET['format']) && $_GET['format'] === 'JSON')) {
				print("//#affected rows: ". mysql_affected_rows() . PHP_EOL);
			}
		}
		public function getValue( $field, $value) {
			if ( $field == 'stt') {
				//$value	= strtoupper(RegExp($value, '[A-Za-z]{2}'));
			}
			return $value;
		}
	}
?>