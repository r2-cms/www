<?php
	print("<h1>Starting explorer</h1>".PHP_EOL);
	$_Fields	= array(
		//Field	Type	Null	Key	Default	Extra
		array('Field'=>'id',	'Type'=>'int(10) unsigned',	'Null'=>'NO',	'Key'=>'PRI',	'Default'=>'\N',	'Extra'=>'auto_increment'),
		array("Field"=>"id",	"Type"=>"int(10) unsigned",	"Null"=>"NO",	"Key"=>"PRI",	"Default"=>"\N",	"Extra"=>"auto_increment"),
		array("Field"=>"id_explorer_storage",	"Type"=>"int(10) unsigned",	"Null"=>"NO",	"Key"=>"",	"Default"=>"\N",	"Extra"=>""),
		array("Field"=>"id_explorer_product",	"Type"=>"int(10) unsigned",	"Null"=>"NO",	"Key"=>"UNI",	"Default"=>"\N",	"Extra"=>""),
		array("Field"=>"creation",	"Type"=>"timestamp",	"Null"=>"NO",	"Key"=>"",	"Default"=>"0000-00-00 00:00:00",	"Extra"=>""),
		array("Field"=>"modification",	"Type"=>"timestamp",	"Null"=>"NO",	"Key"=>"",	"Default"=>"CURRENT_TIMESTAMP",	"Extra"=>"on update CURRENT_TIMESTAMP"),
	);
	require_once( '../../../engine/connect.php');
	require_once( SROOT.'engine/functions/Pager.php');
	$table	= 'gt8_explorer';
	$result	= mysql_query('
		DESCRIBE '. $table .'
	') or die('Can not get table data type in SQL Update');
	
	$Fields	= array();
	while( $row = mysql_fetch_assoc($result)) {
		$Fields[]	= $row;
	}
	for ( $i=0; $i<count($Fields); $i++) {
		$crr	= $Fields[$i];
		$found	= false;
		for ( $j=0; $j<count($_Fields); $j++) {
			if ( $crr['Field'] === $_Fields[$j]['Field'] ) {
				if ( $_Fields[$j]['Default'] === '\N') {
					$_Fields[$j]['Default']	= NULL;
				}
				$hasDif	= false;
				if ( $crr['Type'] !== $_Fields[$j]['Type']) {
					print("`$table`.`{$crr['Field']}` Type field has diferences".PHP_EOL);
					$hasDif	= true;
				}
				if ( $crr['Null'] !== $_Fields[$j]['Null']) {
					print("`$table`.`{$crr['Field']}` Null field has diferences".PHP_EOL);
					$hasDif	= true;
				}
				if ( $crr['Key'] !== $_Fields[$j]['Key']) {
					print("`$table`.`{$crr['Field']}` Key field has diferences".PHP_EOL);
					$hasDif	= true;
				}
				if ( $crr['Default'] !== $_Fields[$j]['Default']) {
					print("`$table`.`{$crr['Field']}` Default field has diferences".PHP_EOL);
					$hasDif	= true;
				}
				if ( strtolower($crr['Extra']) !== strtolower($_Fields[$j]['Extra'])) {
					print("`$table`.`{$crr['Field']}` Extra field has diferences".PHP_EOL);
					$hasDif	= true;
				}
				$found	= true;
				
				if ( $hasDif) {
					$default	= $_Fields[$j]['Default'];
					if ( RegExp($default, '[0-9\.\-\+]+') === $default && !strpos('#'.$default, '..')) {
						
					} else if ( strpos('#'.$default, 'CURRENT_TIMESTAMP')) {
						
					} else {
						$default	= "'". $default ."'";
					}
					print("
						ALTER TABLE `$table`
							CHANGE `{$crr['Field']}` `{$crr['Field']}`
							{$_Fields[$j]['Type']}
							DEFAULT $default
							". ($_Fields[$j]['Null']==='NO'? 'NOT NULL': 'YES') ."
							{$_Fields[$j]['Extra']}
					");// or die("UPGRADE ERROR (GT8::explorer): 0x3");
					print(";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;");
				}
				break;
			}
		}
		if ( $found === false) {
			print("`$table`.`{$crr['Field']}` created".PHP_EOL);
		}
	}
	print("<h1>########################################################################</h1>".PHP_EOL);
?>