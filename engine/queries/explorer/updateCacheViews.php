<?php
	/*
		Boolean updateCacheViews( D, f)
			Properties:
				D			Required directory path
				f			Required filename
			
			ATENÇÃO:
				Como a tabela gt8_images_view deve ser acessada somente internamente
				 (motivo pelo qual não é feito include do /engine/connect.php),
				 não será feita verificação de segurança!
				Certifique-se de enviar o único argumento necessário de forma
				 correta.
				
	*/
	function updateCacheViews( $D, $f) {
		global $sizeRequested;
		
		if ( $sizeRequested != 'thumb' && $sizeRequested != 'small' && $sizeRequested != 'regular' ) {
			mysql_query("
				UPDATE
					gt8_explorer e
					JOIN gt8_explorer_view v ON e.id = v.id
				SET
					v.vtotal	= v.vtotal+1,
					v.vmonth	= v.vmonth+1,
					v.vweek		= v.vweek+1,
					v.vtoday	= v.vtoday+1
				WHERE
					e.filename = '$f' AND
					e.path = '$D'
			") or die('explorer.update: '. mysql_error());
			
			if ( !mysql_affected_rows()) {
				//insert into t the new family
				mysql_query("
					INSERT INTO
						gt8_explorer_view( id)
						SELECT
							id
						FROM
							gt8_explorer
						WHERE
							path = '$D' AND
							filename = '$f'
				") or die('explorer.insert: '. mysql_error());
			}
		}
		return;
	}
?>
