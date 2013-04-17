<?php
	//define( 'SROOT', substr(__FILE__, 0, strlen(__FILE__)-strlen('engine/Util/Pager/Pager.php')) );
	//define( 'DS', DIRECTORY_SEPARATOR );
	//require_once( SROOT .'engine'.DS.'connect.php');
	/**
	 * @function Pager( options, &rows)
	 * @option index 0
	 * @option foundRows Integer|String Se for String, deve ser uma consulta que será executada para se obter o número total de registros encontrados. Se for Integer deve ser o total de registros encontrados, que foi obtido de alguma outra forma
	 * @option bts 7	quantos botãos clicáveis aparecerão. Deve ser sempre número ímpar
	 * @option limit 50
	 * @option format <OBJECT|TABLE|CARD|JSON|GRID|TEMPLATE>
	 * @option rows		Um array que receberá o resultado em formato OBJECT independente do parâmetro option.format
	 
	 * @option debug Boolean
	 
	 * @option select
	 * @option addSelect		o mesmo que select, mas adiciona colunas ao invés de substituir as já existentes
	 * @option from
	 * @option addFrom			o mesmo que from, mas adiciona tabelas ao invés de substituir as já existentes
	 * @option group
	 * @option addWhere
	 * @option order
	 * @option search			retorna resultados que contenham TODAS as procuras coincidentes
	 * 	search=columnName,columnName()keywords{}columnName,columnName()keywords
	 * 	'search'	=> array(
	 *		array('fieldName,fieldName,separated,by,comma', 'keywords separated by space')
	 * 	)
	 * @option searchR			O mesmo que search, mas coloca o caractere '%' somente no fim da palavra. (mais rápido)
	 * @option any				o mesmo que search, mas retorna resultados que contenham resultados de quaisquer das coincidências procuradas
	 * @option equal
	 * 	equal=columnName,columnName()keywords{}columnName,columnName()keywords
	 * 	'equal'	=> array(
	 *		array('fieldName,fieldName,separated,by,comma', 'keywords separated by space', trueOrNullToExecute)
	 * 	)
	 * @option required			O mesmo que search, mas sem '%' antes ou depois da palavra
	 * @option ids
	 * 	ids=columnName,columnName()ids{}columnName,columnName()ids
	 * 	'ids'	=> array(
	 *		array( column, idsSeparatedByCommas, force),		//force: quando o id for nulo, ele é ignorado. Ao usar o terceiro parâmetro, mesmo nulo ou zero o valor do id será usado
	 *		array('l.id', '1,2,3'),
	 *		array('p.id', '5,6,7')
	 *	)
	 * 
	 * @option replace
	 * 	replace=old()new{}old()new
	 * 	replace	=> array(
	 *		array('old', 'new'),
	 *		array('old', 'new')
	 * 	)
	 * @option cols					cols=columnName()columnType()columnFormat{}columnName()columnType()columnFormat
	 * @option card					estrutura html. Os nomes de colunas entre "@" serão substituídos pelos valores. Eg: 'card'=> '<span class="preco" >@preco@</span>' == <span class="preco" >12.09</span>
	 * @option card8				o mesmo que card, mas com encodificação utf8. Se ao invés de dois "@" forem encontrados dois sharps "#" (eg: #preco#), será aplicado encodificação de entidades antes: ut8_encode(htmlentities(field)).
	 * 
	 * @option grid					estrutura html, igual ao card. (a estrutura aplica-se somente às células do body. Eg: <div >Cell</div>).
	 * @option gridConf				Veja sql.address.list
	 * @option gridState			Configuração das colunas quando o formato GRID é utilizado
	 * 	Códigos:
	 * 		@name@		nome da variável
	 * 		$value$		valor raw da variável
	 * 		@value@		valor UTF8 da variável
	 * 		#value#		valor UTF8 com entidades da variável
	*/
	function Pager( $props, &$rows=null) {
		global $GT8;
		//PARAMS
		if ( !isset($props['format'])) {
			$props['format']	= 'OBJECT';
		}
		
		if ( isset($props['sql'])) {
			$props['sql']	= RegExp($props['sql'], '[a-zA-Z_0-9\-\.]+');
			include( SROOT .'engine/queries'. DS . str_replace('.', DS, $props['sql']) .".php");
		}	
		$props['select']	= isset($props['select'])? $props['select']: $sql['select'];
		$props['from']		= isset($props['from'])? $props['from']: $sql['from'];
		$props['where']		= isset($props['where'])? $props['where']: $sql['where'];
		$props['group']		= isset($props['group'])? $props['group']: $sql['group'];
		$props['order']		= isset($props['order'])? $props['order']: $sql['order'];
		$props['limit']		= isset($props['limit'])? $props['limit']: $sql['limit'];
		$props['card']		= isset($props['card'])? $props['card']: $sql['card'];
		$props['card8']		= isset($props['card8'])? $props['card8']: $sql['card8'];
		if ( isset($sql['foundRows'])) {
			$props['foundRows']	= isset($props['foundRows'])? $props['foundRows']: $sql['foundRows'];
		}
		if ( isset($sql['gridConf'])) {
			$props['gridConf']	= isset($props['gridConf'])? $props['gridConf']: $sql['gridConf'];
		}
		if ( isset($sql['gridState'])) {
			$props['gridState']	= isset($props['gridState'])? $props['gridState']: $sql['gridState'];
		}
		
		if ( isset($props['addSelect'])) {
			$props['select']	.= ' '. (substr(trim($props['addSelect']), 0, 1)==','? $props['addSelect']: ', '. $props['addSelect']);
		}
		if ( isset($props['addFrom'])) {
			$props['from']	.= ' '. $props['addFrom'];
		}
		
		//LIMIT
		$props['limit']	= (integer)$props['limit'];
		if ( !$props['limit']) {
			$props['limit']	= 50;
		}
		$props['index']	= max((integer)$props['index'], 1);
		$limit	= array(($props['index']-1) * $props['limit'], $props['limit']);
		
		//validating fields. Este trabalho deve ser minucioso e gradativo.
		if ( isset($props['foundRows'])) {
			$props['foundRows']	= (integer)$props['foundRows'];
		}
		
		
		switch ( $props['format']) {
			case "OBJECT":			break;
			case "TABLE":			break;
			case "GRID":			break;
			case "CARD":			break;
			case "LIST":			break;
			case "XML":				break;
			case "JSON":			break;
			case "TEMPLATE":		break;
			default: $props['format']	= "OBJECT"; break;
		}
		
		/***************************** WHERE **********************************/
		$where	= '1 = 1'. PHP_EOL;
		if ( !empty($props['search'])) {
			$fullsearchClause	= "";
			
			$props['search']	= ( gettype($props['search']) == 'array')? $props['search']: explode('{}', $props['search']);
			for ($i=0; $i<count($props['search']); $i++) {
				$crr	= ( gettype($props['search'][$i]) == 'array')? $props['search'][$i]: explode('()', $props['search'][$i]);
				
				$columns	= explode(',', $crr[0]);
				
				$words	= explode( ' ', $crr[1]);
				$len	= count($words);
				for ( $j=0; $j<$len; $j++) {
					if ( strtolower(substr($words[$j], -1)) == "s") {
						$words[$j]	= substr($words[$j], 0, -1);
					}
				}
				for ( $j=0; $j<$len; $j++) {
					$fullsearchClause	.= '				AND ('. PHP_EOL .'					1 = 0'. PHP_EOL;
					foreach( $columns as $col) {
						if ( !empty($words[$j]) || $crr[2]==null || $crr[2]) {
							$fullsearchClause	.= "					OR ". $col ." LIKE '%". $words[$j] ."%'". PHP_EOL;
						}
					}
					$fullsearchClause	.= '				)'. PHP_EOL;
				}
				$where	.= $fullsearchClause;
				$fullsearchClause	= '';
			}
		}
		if ( !empty($props['searchR'])) {
			$fullsearchClause	= "";
			
			$props['searchR']	= ( gettype($props['searchR']) == 'array')? $props['searchR']: explode('{}', $props['searchR']);
			for ($i=0; $i<count($props['searchR']); $i++) {
				$crr	= ( gettype($props['searchR'][$i]) == 'array')? $props['searchR'][$i]: explode('()', $props['searchR'][$i]);
				
				$columns	= explode(',', $crr[0]);
				
				$words	= explode( ' ', $crr[1]);
				$len	= count($words);
				for ( $j=0; $j<$len; $j++) {
					if ( strtolower(substr($words[$j], -1)) == "s") {
						$words[$j]	= substr($words[$j], 0, -1);
					}
				}
				for ( $j=0; $j<$len; $j++) {
					$fullsearchClause	.= '				AND ('. PHP_EOL .'					1 = 0'. PHP_EOL;
					foreach( $columns as $col) {
						if ( !empty($words[$j]) || $crr[2]===null || $crr[2]) {
							$fullsearchClause	.= "					OR ". $col ." LIKE '". $words[$j] ."%'". PHP_EOL;
						}
					}
					$fullsearchClause	.= '				)'. PHP_EOL;
				}
				$where	.= $fullsearchClause;
				$fullsearchClause	= '';
			}
		}
		if ( !empty($props['any'])) {
			$fullsearchClause	= "				AND (
					1 = 0". PHP_EOL
			;
			
			$props['any']	= ( gettype($props['any']) == 'array')? $props['any']: explode('{}', $props['any']);
			for ($i=0; $i<count($props['any']); $i++) {
				$crr	= ( gettype($props['any'][$i]) == 'array')? $props['any'][$i]: explode('()', $props['any'][$i]);
				
				$columns	= explode(',', $crr[0]);
				
				$words	= explode( ' ', $crr[1]);
				$len	= count($words);
				for ( $j=0; $j<$len; $j++) {
					if ( strtolower(substr($words[$j], -1)) == "s") {
						$words[$j]	= substr($words[$j], 0, -1);
					}
				}
				for ( $j=0; $j<$len; $j++) {
					$fullsearchClause	.= '					OR ('. PHP_EOL .'						1 = 0'. PHP_EOL;
					foreach( $columns as $col) {
						if ( !empty($words[$j]) || $crr[2]===null || $crr[2]) {
							$fullsearchClause	.= "						OR ". $col ." LIKE '%". $words[$j] ."%'". PHP_EOL;
						}
					}
					$fullsearchClause	.= '					)'. PHP_EOL;
				}
				$where	.= $fullsearchClause;
				$fullsearchClause	= '';
			}
			$where	.= '				)';
		}
		if ( !empty($props['equal'])) {
			$fullsearchClause	= "";
			
			$props['equal']	= (gettype($props['equal']) == 'array')? $props['equal']: explode('{}', $props['equal']);
			for ($i=0; $i<count($props['equal']); $i++) {
				$crr	= (gettype($props['equal'][$i]) == 'array')? $props['equal'][$i]: explode('()', $props['equal'][$i]);
				
				$columns	= explode(',', $crr[0]);
				
				$word	= $crr[1];
				$word	= gettype($crr[1])=='array'? $crr[1]: explode(',', $crr[1]);
				if ( $crr[2]===null || $crr[2]) {
					foreach( $columns as $col) {
						$fullsearchClause	.= "		OR ". $col ." IN ('". join("','", $word) ."' )". PHP_EOL ."			";
					}
				}
			}
			if ( strlen($fullsearchClause) > 3) {
				$where	.= "				AND (
						1=0
						$fullsearchClause
					)";
			}
		}
		if ( !empty($props['ids'])) {
			$fullsearchClause	= "";
			$props['ids']	= gettype($props['ids']) == 'array'? $props['ids']: explode('{}', $props['ids']);
			for ($i=0; $i<count($props['ids']); $i++) {
				$crr	= gettype($props['ids'][$i]) == 'array'? $props['ids'][$i]: explode('()', $props['ids'][$i]);
				
				$columns	= explode(',', $crr[0]);
				$ids		= explode(',', $crr[1]);
				$force		= $crr[2];
				$idsLibrary	= array();
				for( $j=0; $j<count($ids); $j++) {
					settype($ids[$j], 'integer');
					if ( $ids[$j] !== null) {
						$idsLibrary[]	= $ids[$j];
					} else if ( $force) {
						$idsLibrary[]	= 0;
					}
				}
				if ( count($idsLibrary)) {
					foreach( $columns as $col) {
						$fullsearchClause	.= '				AND '. $col .' IN ('. join(',', $idsLibrary) .')';
					}
				}
				$where	.= $fullsearchClause;
			}
		}
		if ( !empty($props['required'])) {
			$fullsearchClause	= "";
			
			$props['required']	= (gettype($props['required']) == 'array')? $props['required']: explode('{}', $props['required']);
			for ($i=0; $i<count($props['required']); $i++) {
				$crr	= (gettype($props['required'][$i]) == 'array')? $props['required'][$i]: explode('()', $props['required'][$i]);
				
				$columns	= explode(',', $crr[0]);
				
				$word	= $crr[1];
				$word	= gettype($crr[1])=='array'? $crr[1]: explode(',', $crr[1]);
				if ( $crr[2]===null || $crr[2]) {
					foreach( $columns as $col) {
						$fullsearchClause	.= "		AND ". $col ." IN ('". join("','", $word) ."' )". PHP_EOL ."			";
					}
				}
			}
			if ( strlen($fullsearchClause) > 3) {
				$where	.= "				AND (
						1=1
						$fullsearchClause
					)";
			}
		}
		if ( !empty($props['order'])) {
			$props['order']	= "ORDER BY
				". $props['order'];
		}
		if ( !empty($props['where'])) {
			$where	= $where . $props['where'];
		}
		if ( !empty($props['group'])) {
			$props['group']	= '
			GROUP BY
				'. $props['group'] .'
			';
		}
		
		
		if ( isset($props['addWhere'])) {
			$where	.= $props['addWhere'];
		}
		/***************************** SQL ************************************/
		$sql	= "
			SELECT
				". $props['select'] ."
			FROM
				". $props['from'] ."
			WHERE
				". $where ."
				". $props['group'] ."
			". $props['order'] ."
			LIMIT
				{$limit[0]}, {$limit[1]}
		";
		
		/***************************** COUNT **********************************/
		if ( gettype($props['foundRows']) == 'integer' ) {
			
		} else {
			if ( isset($props['group']) && !empty($props['group'])) {
				$sqlFR	= "
					SELECT
						COUNT(*) AS total
					FROM (
						SELECT
							COUNT(*)
						FROM
							". $props['from'] ."
						WHERE
							". $where ."
							". $props['group'] ."
					) sql_products_sumary
				";
			} else {
				$sqlFR	= "
					SELECT
						COUNT(*) AS total
					FROM
						". $props['from'] ."
					WHERE
						". $where ."
						". $props['group'] ."
				";
			}
		}
		if ( !empty($props['replace'])) {
			$props['replace']	= gettype($props['replace']) == 'array'? $props['replace']: explode('{}', $props['replace']);
			for ($i=0; $i<count($props['replace']); $i++) {
				$crr	= gettype($props['replace'][$i]) == 'array'? $props['replace'][$i]: explode('()', $props['replace'][$i]);
				$sql	= str_replace($crr[0], $crr[1], $sql);
				if ( isset($sqlFR)) {
					$sqlFR	= str_replace($crr[0], $crr[1], $sqlFR);
				}
			}
		}
		if ( isset($props['debug'])) {
			print("<pre>$sql</pre>");
			print("<h1>;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;</h1>");
			print("<pre>$sqlFR</pre>");
		}
		//die($sql);
		$tstart	= microtime(true);
		if ( isset($sqlFR)) {
			$rowFR		= mysql_fetch_array(mysql_query($sqlFR));
			$props['foundRows']	= $rowFR[0];
		}
		
		$result	= mysql_query( $sql) or $error=mysql_error();// or die("SQL SELECT Error (1)". mysql_error());
		
		if ( $error) {
			print("<h1>". $props['sql'] ."</h1>");
			print("<pre>". $sql ."</pre>");
			die('ERROR:'. $error);
		}
		/***************************** RESULTS ********************************/
		$Return	= array();
		$stats	= array();
		$rows	= array();
		if ( $props['format'] == "TABLE") {
			$stats	= '<table border="1" class="cellpadding" >';
			$firstLoop	= true;
		} else if ( $props['format'] == "CARD") {
			$stats	= '';
			$firstLoop	= true;
		} else if ( $props['format'] == 'JSON' ) {
			$stats	= '';
		} else if ( $props['format'] == 'TEMPLATE' ) {
			$stats	= '';
		} else if ( $props['format'] == 'GRID') {
			$stats	= '
				<div class="Grid" >
					<div class="head" style="width:@@fullwidth@@px;" >
						<div class="overflow" >
			';
			$gridCols	= array();
			$firstLoop	= true;
		}
		//when format is GRID, we ensure the min row found is at least one. So, we can create the header and an empty body
		$atLeastOne	= $props['format'] == 'GRID';
		while( ($row = mysql_fetch_assoc( $result))||$atLeastOne) {
			$atLeastOne	= false;
			$rows[]	= $row;
			if ( $props['format'] == "TABLE") {
				if ( $firstLoop) {
					$stats	.= '<tr>';
					foreach( $row as $name=>$value) {
						$class	= '';
						if ( strpos($props['order'], " $name ") > 0) {
							$class	= 'class="selected"';
						}
						$stats	.= "<th onclick='Pager.orderBy(this)' $class >". $name ."</th>";
					}
					$stats	.= "</tr>";
				}
				
				$s2	= '<tr>';
				foreach( $row as $name=>$value) {
					$s2	.= "<td>". $value ."</td>";
				}
				$stats	= "$stats$s2</tr>". PHP_EOL;
			} else if ( $props['format'] == 'GRID') {
				if ( $firstLoop) {
					//cookie simulation
					if ( $props['gridState']) {
						$gridState	= $props['gridState'];
						$gridState	= explode('{{}}', $gridState);
						for( $i=0; $i<max(1,count($gridState)); $i++) {
							$gridState[$i]	= explode('(())', $gridState[$i]);
						}
					} else {
						$gridState	= array();
						$i	= 0;
						foreach( $row AS $name=>$value) {
							$gridState[]	= array( $i .'|80');
							$i++;
						}
						$props['gridState']	= $gridState;
					}
					
					$columns	= array();
					if ( $row) {
						foreach( $row AS $name=>$value) {
							$columns[]	= $name;
						}
					} else {
						//grid precisa de algumas informações contidas no header.
						//Assim, fazemos um select sem WHERE somente com o objetivo de obter o nome das colunas.
						$__row	= mysql_fetch_assoc(mysql_query("SELECT {$props['select']} FROM {$props['from']} LIMIT 1"));
						foreach( $__row AS $name=>$value) {
							$columns[]	= $name;
						}
					}
					$stats	.= '				';
					$bodyStats	= array();
					$fullWidth	= 0;
					for( $i=0; $i<count($gridState); $i++) {
						$crr	= $gridState[$i];
						$stats	.= '<div class="group" >'. PHP_EOL .'						';
						for( $j=0; $j<max(1, count($crr)); $j++) {
							$crrGS	= explode('|', $crr[$j]);
							$col	= str_replace(array('&', '>', '<'), array('&amp;',''), $crrGS[0]);
							$crrGC	= explode('|', $props['gridConf'][$col]);
							$label	= $crrGC[0];
							$type	= isset($crrGC[2])? $crrGC[2]: 'string';
							$width	= isset($crrGS[1])? $crrGS[1]: $crrGC[1];
							$typeLen= isset($crrGC[3])? $crrGC[3]: 0;
							$minW	= isset($crrGC[4])? ' minwidth-'.$crrGC[4]: '';
							$maxW	= isset($crrGC[5])? ' maxwidth-'.$crrGC[5]: '';
							$dataA	= '';
							
							if ( $type == 'enum') {
								$arrEnum	= explode(',', $crrGC[3]);
								$dataA	= '
									<span class="hidden e-select">
										<select>
											'
								;
								foreach( $arrEnum as $name) {
									$expl	= explode('|', $name);
									$dataA	.= '<option'. (isset($expl[1])? ' value="'. addslashes($expl[1]) .'"': '') .'>'. $expl[0] .'</option>';
								}
								$dataA	.= '
										</select>
										<span class="button group-button"><strong>&nbsp;</strong><img src="'. CROOT .'imgs/arrow-down-mini.png" alt=""></span>
									</span>								
								';
							}
							//print("<h1>:". $columns[$col] .":</h1>".PHP_EOL);
							if ( count($crr) == 1) {
								$stats	.= '		<div class="g-col double border '.$type.'" style="width:'. ($width-1) .'px;" title="'.$columns[$col].'|'.$col.'" >'. $label . $dataA.'</div>'. PHP_EOL .'						';
								$gridCols[]		= array($columns[$col], $type);
								$bodyStats[]	= '<div class="g-col" style="width:'.$width.'px;" >'. PHP_EOL.'								';
								$fullWidth		+= $width+1;
							} else {
								if ( $j==0) {
									$label	= $col;
									$stats	.= '		<div class="g-col g-title '.$type.'" title="'.$columns[$col].'|'.$col.'" ><div class="border" >'. $label .'</div></div>'. PHP_EOL .'						';
								} else {
									$stats	.= '		<div class="g-col border '.$type.'" style="width:'. ($width-1) .'px;" title="'.$columns[$col].'|'.$col.'" >'. $label . $dataA .'</div>'. PHP_EOL .'						';
									$gridCols[]		= array($columns[$col], $type);
									$bodyStats[]	= '<div class="g-col" style="width:'.$width.'px;" title="'.$columns[$col].'|'.$col.'" >'. PHP_EOL.'								';
									$fullWidth		+= $width+1;
								}
							}
						}
						$stats	.= '	</div>'. PHP_EOL .'							';
					}
					$stats	= substr($stats, 0, -1) .'</div>'. PHP_EOL .'					';
					$stats	.= '</div>'. PHP_EOL .'					';
					$stats	.= '<div class="body" style="width:@@fullwidth@@px;" >'. PHP_EOL .'						';
					$stats	.= '<div class="overflow" >'. PHP_EOL .'							';
					$stats	= str_replace('@@fullwidth@@', $fullWidth, $stats);
				}
				
				//certifique-se que props.grid contenha o mesmo n. de colunas que gridCols
				if ( !isset($props['grid']) ) {
					$props['grid']	= array();
					$props['grid']['id']		= '<a href="?id=$value$" >#value#</a>'. PHP_EOL .'								';
				}
				if ( !$props['grid']['default']) {
					$props['grid']['default']	= '<div>#value#</div>'. PHP_EOL .'								';
				}
				foreach ( $gridCols as $name=>$value) {
					if ( !$props['grid'][$value[0]] ) {
						$props['grid'][$value[0]]	= $props['grid']['default'];
					}
				}
				for ( $i=0, $len=count($gridCols); $i<$len; $i++) {
					$name	= $gridCols[$i][0];
					$raw	= $row[$name];
					$utf	= utf8_encode($raw);
					$ett	= utf8_encode((($raw)));
					$type	= $gridCols[$i][1];
					
					if ( $type == 'string') {
						$utf	= $utf;
						$ett	= $ett;
						$value	= $raw;
					} else if ( $type == 'integer') {
						$utf	=
						$ett	= 
						$value	= '<small>'. $raw .'</small>';
					} else if ( $type == 'datetime') {
						$utf	=
						$ett	= 
						$value	= '<small>'. $raw .'</small>';
					} else if ( $type == 'timestamp') {
						$utf	=
						$ett	= 
						$value	= '<small>'. $raw .'</small>';
					} else if ( $type == 'float') {
						$utf	=
						$ett	= 
						$value	= '<small>'. number_format($raw, $typeLen, '.', ',') .'</small>';
					} else if ( $type == 'currency') {
						$utf	=
						$ett	= 
						$value	= '<small><span>R$</span>'. number_format($raw, 2, '.', ',') .'</small>';
					}
					$bodyStats[$i]	.= str_replace( array('@name@', '$value$', '@value@', '#value#'), array( $name, $raw, $utf, $ett), $props['grid'][$name]);
					//$bodyStats[$i]	.= '<a href="?'. urlencode($raw) .'" >'. $name .'</a>'. PHP_EOL .'								';
				}
			} else if ( $props['format'] == 'CARD') {
				if ( $firstLoop ) {
					if ( !$props['cols']) {
						$props['cols']	= array();
						foreach( $row as $name=>$value) {
							$props['cols'][]	= array($name);
						}
					}
					if ( !isset($props['card']) && !isset($props['card8'])) {
						$props['card']	= '<a href="" >';
						for ($i=0, $len=count($props['cols']); $i<$len; $i++) {
							$props['card']	.= '<span class="'. $props['cols'][$i][0] .'" >@'. $props['cols'][$i][0] .'@</span>';
						}
						$props['card']	.= '</a>';
					}
				}
				$s2	= isset($props['card8'])? $props['card8']: $props['card'];
				for ($i=0, $len=count($props['cols']); $i<$len; $i++) {
					$crr	= $row[$props['cols'][$i][0]];
					$s2	= str_replace('@'. $props['cols'][$i][0] .'@', (isset($props['card8'])? utf8_encode($crr): $crr), $s2);
					$s2	= str_replace('#'. $props['cols'][$i][0] .'#', (isset($props['card8'])? utf8_encode(htmlentities($crr)): $crr), $s2);
				}
				$stats	= "$stats$s2". PHP_EOL;
			} else if ( $props['format'] == "JSON") {
				$s2	= "";
				foreach( $row as $name=>$value) {
					$s2	.= ",'$name':'". utf8_encode(addslashes($value)) ."'";
				}
				$stats	= $stats .",". PHP_EOL ."{". substr( $s2, 1) ."}";
			} else if ( $props['format'] == 'TEMPLATE' && $props['template']) {
				$row['CROOT']	= CROOT;
				
				if ( strpos('#'.$props['template'], '{{all-fields}}') > 0 ) {
					$row['all-fields']	= '';
					foreach( $row as $name=>$value) {
						$row['all-fields']	.= '<span class="'. $name .'" >'.utf8_encode($value).'</span>';
					}
				}
				
				$stats	.= GT8::getHTML( $props['template'], $row);
			} else {
				$s2	= array();
				foreach( $row as $name=>$value) {
					$s2[$name]	= $value;
				}
				$stats[]	= $s2;
			}
			$firstLoop	 = false;
		}
		
		if ( $props['format'] == "TABLE") {
			$stats	.= "</table>";
		} else if ( $props['format'] == 'JSON') {
			$stats	= "var results = [". substr( $stats, 1) ."];";
		} else if ( $props['format'] == 'GRID') {
			for ( $i=0, $len=count($bodyStats); $i<$len; $i++) {
				if ( $i==0) {
					$stats	= $stats . $bodyStats[$i];
				} else {
					$stats	= substr($stats, 0, -1) . '</div>'. PHP_EOL.'								';
					$stats	= substr($stats, 0, -1) . $bodyStats[$i];
				}
			}
			$stats	= substr($stats, 0, -1) .'</div>'. PHP_EOL .'						';
			$stats	.= '</div>'. PHP_EOL .'					';
			$stats	.= '</div>'. PHP_EOL .'				';
			$stats	.= '</div>'. PHP_EOL .'			';
		} else if ( $props['format'] == 'TEMPLATE') {
			
		}
		
		//performance analytics
		if ( $props['sql'] && $GT8['log-performance']) {
			$tend	= (microtime(true)-$tstart) * 1000;
			mysql_query('
				INSERT INTO gt8_analytics_performance(que4y, de5ay)
				VALUES("'. $props['sql'] .'", '. $tend .')
			') or die('Performance error: '. mysql_error());
		}
		
		$Return['rows']	= $stats;
		$Return['format']	= $props['format'];
		$Return['foundRows']	= $props['foundRows'];
		
		if ( $props['format'] != 'OBJECT') {
			$Return['raw']	= $rows;
		}
		
		//PAGING
		if ( 1) {
			$bts	= (integer)$props['bts']? (integer)$props['bts']: 7;
			$prvUrl		= $props['index'] - 1;
			$nxtUrl		= $props['index'] + 1;
			
			if ( $props['index'] > 1 ) {
				$prvUrl	= "<a class='button prvnxt' href='?index=". ($props['index']-1) ."' onclick='if ( window.Pager) {return Pager.goPrevious(this); }' ><span>&lt;&lt;</span></a>";
			} else {
				$prvUrl	= "<small class='disabled' >&lt;&lt;</small>";
			}
			
			if ( ($props['index'])*$props['limit'] < $props['foundRows'] ) {
				$nxtUrl		= "<a class='button prvnxt' href='?index=". ($props['index']+1) ."' onclick='if ( window.Pager) {return Pager.goNext(this); }' ><span>&gt;&gt;</span></a>";
			} else {
				$nxtUrl		= "<small class='disabled' >&gt;&gt;</small>";
			}
			
			$prv	= $props['index'] - ($bts-1)/2;
			$nxt	= $props['index'] + ($bts-1)/2 + 1;
			if ( $prv < 1) {
				$nxt	= $nxt + (($prv-1)*(-1));
				$prv	= 1;
			}
			if ( $nxt*$props['limit'] > $props['foundRows']) {
				$nxt	= ceil($props['foundRows']/$props['limit']) + 1;
				$prv	= max(1, $nxt - $bts);
			}
			$pagesUrl	= "";
			for( $i=$prv; $i<$nxt; $i++) {
				if ( $i==$props['index']) {
					$pagesUrl	.= "<strong class='disabled' >". $i ."</strong>";
				} else {
					$pagesUrl	.= "<a class='button' href='?index=$i' onclick='if ( window.Pager) {return Pager.goTo( this, $i); }' ><span>$i</span></a>";
				}
			}
			if ( $prv == $nxt) {
				$pagesUrl	.= "<strong>1</strong>";
			}
			$Return['page']	= ($prvUrl . $pagesUrl . $nxtUrl);
		}
		
		return $Return;
	}
	
	
	if ( isset($_GET['print']) && strpos("#". $_SERVER["PHP_SELF"], "/engine/Util/Pager/Pager.php") ) {
		require_once( $GT8['admin']['root'] ."check.php");
		
		$results	= Pager( array(
			"index"		=> $_GET['index'],
			"bts"		=> $_GET['bts'],
			"limit"		=> $_GET['limit'],
			"format"	=> isset($_GET['format'])? $_GET['format']: 'TABLE',
			"sql"		=> $_GET['sql'],
			"debug"		=> $_GET['debug'],
			
			"select"	=> $_GET['select'],
			"from"		=> $_GET['from'],
			"group"		=> $_GET['group'],
			"order"		=> $_GET['order'],
			"search"	=> $_GET['search'],
			"any"		=> $_GET['any'],
			"equal"		=> $_GET['equal'],
			"required"		=> $_GET['required'],
			"ids"		=> $_GET['ids'],
			"replace"	=> $_GET['replace']
		));
		//include();
		
		if ( $_GET['format'] == 'TABLE' || !isset($_GET['format'])) {
			include( SROOT .'engine/Util/Pager/table.pl');
		} else if ( $_GET['format'] == 'CARD') {
			print($results['rows']);
		} else if ( $_GET['format'] == 'JSON') {
			print($results['rows']);
		}
	}
?>