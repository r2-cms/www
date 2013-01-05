<?php
// +----------------------------------------------------------------------+
// | Licensa original                                                     |
// | BoletoPhp - Versão Beta                                              |
// | alterado para se adaptar à framework GT8                             |
// +----------------------------------------------------------------------+
// | Este arquivo está disponível sob a Licença GPL disponível pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Você deve ter recebido uma cópia da GNU Public License junto com     |
// | esse pacote; se não, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colaborações de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de João Prado Maia e Pablo Martins F. Costa				  |
// | 																	  |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto Itaú: Glauber Portella		                  |
// +----------------------------------------------------------------------+
	
	function Boletor( $options) {
		$codigobanco = "341";
		$codigo_banco_com_dv = geraCodigoBanco($codigobanco);
		$nummoeda = "9";
		$fator_vencimento = fator_vencimento($options["vencimento"]);
		
		//valor tem 10 digitos, sem virgula
		$valor = formata_numero($options["valor-boleto"],10,0,"valor");
		//agencia é 4 digitos
		$agencia = formata_numero($options["agencia"],4,0);
		//conta é 5 digitos + 1 do dv
		$conta = formata_numero($options["conta"],5,0);
		$conta_dv = formata_numero($options["conta-dv"],1,0);
		//carteira 175
		$carteira = $options["carteira"];
		//nosso_numero no maximo 8 digitos
		$nnum = formata_numero($options["nosso-numero"],8,0);
		$codigo_barras = $codigobanco.$nummoeda.$fator_vencimento.$valor.$carteira.$nnum.modulo_10($agencia.$conta.$carteira.$nnum).$agencia.$conta.modulo_10($agencia.$conta).'000';
		
		// 43 numeros para o calculo do digito verificador
		$dv = digitoVerificador_barra($codigo_barras);
		// Numero para o codigo de barras com 44 digitos
		$linha = substr($codigo_barras,0,4).$dv.substr($codigo_barras,4,43);
		
		$options["linha-digitavel"] = monta_linha_digitavel($linha); // verificar. EG: 34191.75124 34567.861561 51387.710000 1 55410000295295
		$options["agencia-codigo"] = $agencia." / ". $conta."-".modulo_10($agencia.$conta);
		$options["nosso-numero"] = $carteira.'/'.$nnum.'-'.modulo_10($agencia.$conta.$carteira.$nnum);;
		$options["codigo-banco-com-dv"] = $codigo_banco_com_dv;
		$options["codigo_barras"] = $linha;
		
		//default values
		$options["aceite"]			= isset($options["aceite"]) && $options["aceite"]? $options["aceite"]: '';
		$options["especie-doc"]		= isset($options["especie-doc"]) && $options["especie-doc"]? $options["especie-doc"]: '';
		$options["quantidade"]		= isset($options["quantidade"]) && $options["quantidade"]? $options["quantidade"]: '';
		$options["valor-documento"]	= isset($options["valor-documento"]) && $options["valor-documento"]? $options["valor-documento"]: '';
		$options["instructions"]	= isset($options["instructions"]) && $options["instructions"]? $options["instructions"]: '<p>Não receber após vencimento.<br />Cheques não são aceitos como pagamento deste boleto.</p>';
		$options["quantidade"]		= isset($options["quantidade"]) && $options["quantidade"]? $options["quantidade"]: '';
		$options["quantidade"]		= isset($options["quantidade"]) && $options["quantidade"]? $options["quantidade"]: '';
		$options["quantidade"]		= isset($options["quantidade"]) && $options["quantidade"]? $options["quantidade"]: '';
		$options["quantidade"]		= isset($options["quantidade"]) && $options["quantidade"]? $options["quantidade"]: '';
		$options["quantidade"]		= isset($options["quantidade"]) && $options["quantidade"]? $options["quantidade"]: '';
		$options['barcode']	= fbarcode($options["codigo_barras"]);
		return $options;
	}
	function digitoVerificador_barra($numero) {
		$resto2 = modulo_11($numero, 9, 1);
		$digito = 11 - $resto2;
		 if ($digito == 0 || $digito == 1 || $digito == 10  || $digito == 11) {
			$dv = 1;
		 } else {
			$dv = $digito;
		 }
		 return $dv;
	}
	function formata_numero($numero,$loop,$insert,$tipo = "geral") {
		if ($tipo == "geral") {
			$numero = str_replace(",","",$numero);
			while(strlen($numero)<$loop){
				$numero = $insert . $numero;
			}
		}
		if ($tipo == "valor") {
			/*
			retira as virgulas
			formata o numero
			preenche com zeros
			*/
			$numero = str_replace(array('.',','),"",$numero);
			while(strlen($numero)<$loop){
				$numero = $insert . $numero;
			}
		}
		if ($tipo == "convenio") {
			while(strlen($numero)<$loop){
				$numero = $numero . $insert;
			}
		}
		return $numero;
	}
	function direita($entra,$comp){
		return substr($entra,strlen($entra)-$comp,$comp);
	}
	function fator_vencimento($data) {
		$data = explode("/",$data);
		$ano = $data[2];
		$mes = $data[1];
		$dia = $data[0];
		return(abs((_dateToDays("1997","10","07")) - (_dateToDays($ano, $mes, $dia))));
	}
	function _dateToDays($year,$month,$day) {
		$century = substr($year, 0, 2);
		$year = substr($year, 2, 2);
		if ($month > 2) {
			$month -= 3;
		} else {
			$month += 9;
			if ($year) {
				$year--;
			} else {
				$year = 99;
				$century --;
			}
		}
		return ( floor((  146097 * $century)    /  4 ) +
				floor(( 1461 * $year)        /  4 ) +
				floor(( 153 * $month +  2) /  5 ) +
					$day +  1721119);
	}
	function modulo_10($num) { 
			$numtotal10 = 0;
			$fator = 2;
	
			// Separacao dos numeros
			for ($i = strlen($num); $i > 0; $i--) {
				// pega cada numero isoladamente
				$numeros[$i] = substr($num,$i-1,1);
				// Efetua multiplicacao do numero pelo (falor 10)
				// 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Itaú
				$temp = $numeros[$i] * $fator; 
				$temp0=0;
				foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v){ $temp0+=$v; }
				$parcial10[$i] = $temp0; //$numeros[$i] * $fator;
				// monta sequencia para soma dos digitos no (modulo 10)
				$numtotal10 += $parcial10[$i];
				if ($fator == 2) {
					$fator = 1;
				} else {
					$fator = 2; // intercala fator de multiplicacao (modulo 10)
				}
			}
			
			// várias linhas removidas, vide função original
			// Calculo do modulo 10
			$resto = $numtotal10 % 10;
			$digito = 10 - $resto;
			if ($resto == 0) {
				$digito = 0;
			}
			
			return $digito;
			
	}
	function modulo_11($num, $base=9, $r=0)  {
		/**
		 *   Autor:
		 *           Pablo Costa <pablo@users.sourceforge.net>
		 *
		 *   Função:
		 *    Calculo do Modulo 11 para geracao do digito verificador 
		 *    de boletos bancarios conforme documentos obtidos 
		 *    da Febraban - www.febraban.org.br 
		 *
		 *   Entrada:
		 *     $num: string numérica para a qual se deseja calcularo digito verificador;
		 *     $base: valor maximo de multiplicacao [2-$base]
		 *     $r: quando especificado um devolve somente o resto
		 *
		 *   Saída:
		 *     Retorna o Digito verificador.
		 *
		 *   Observações:
		 *     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
		 *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
		 */                                        
	
		$soma = 0;
		$fator = 2;
	
		/* Separacao dos numeros */
		for ($i = strlen($num); $i > 0; $i--) {
			// pega cada numero isoladamente
			$numeros[$i] = substr($num,$i-1,1);
			// Efetua multiplicacao do numero pelo falor
			$parcial[$i] = $numeros[$i] * $fator;
			// Soma dos digitos
			$soma += $parcial[$i];
			if ($fator == $base) {
				// restaura fator de multiplicacao para 2 
				$fator = 1;
			}
			$fator++;
		}
	
		/* Calculo do modulo 11 */
		if ($r == 0) {
			$soma *= 10;
			$digito = $soma % 11;
			if ($digito == 10) {
				$digito = 0;
			}
			return $digito;
		} elseif ($r == 1){
			$resto = $soma % 11;
			return $resto;
		}
	}
	function fbarcode($valor){
		
		$fino = 1 ;
		$largo = 3 ;
		$altura = 50 ;
		
		$barcodes[0] = "00110" ;
		$barcodes[1] = "10001" ;
		$barcodes[2] = "01001" ;
		$barcodes[3] = "11000" ;
		$barcodes[4] = "00101" ;
		$barcodes[5] = "10100" ;
		$barcodes[6] = "01100" ;
		$barcodes[7] = "00011" ;
		$barcodes[8] = "10010" ;
		$barcodes[9] = "01010" ;
		for($f1=9;$f1>=0;$f1--){ 
			for($f2=9;$f2>=0;$f2--){  
				$f = ($f1 * 10) + $f2 ;
				$texto = "" ;
				for($i=1;$i<6;$i++){ 
					$texto .=  substr($barcodes[$f1],($i-1),1) . substr($barcodes[$f2],($i-1),1);
				}
				$barcodes[$f] = $texto;
			}
		}
		//Desenho da barra
		//Guarda inicial
		$bar	= '';
		$bar	.= '<div class="p1" ></div>';
		$bar	.= '<div class="b1" ></div>';
		$bar	.= '<div class="p1" ></div>';
		$bar	.= '<div class="b1" ></div>';
		
		$texto = $valor ;
		if((strlen($texto) % 2) <> 0){
			$texto = "0" . $texto;
		}
		// Draw dos dados
		while (strlen($texto) > 0) {
			$i = round(substr($texto,0,2));
			$texto = direita($texto,strlen($texto)-2);
			$f	= $barcodes[$i];
			for($i=1;$i<11;$i+=2){
			
				if (substr($f,($i-1),1) == "0") {
					$f1	= $fino ;
				} else {
					$f1	= $largo ;
				}
				$bar	.= '<div class="p'. $f1 .'" ></div>';
				
				if (substr($f,$i,1) == "0") {
					$f2 = $fino;
				} else {
					$f2 = $largo;
				}
				$bar	.= '<div class="b'. $f2 .'" ></div>';
			}
		}
	
		$bar	.= '<div class="p3" ></div>';
		$bar	.= '<div class="b1" ></div>';
		$bar	.= '<div class="p1" ></div>';
		
		return $bar;
	}
	function geraCodigoBanco($numero) {
		$parte1 = substr($numero, 0, 3);
		$parte2 = modulo_11($parte1);
		return $parte1 . "-" . $parte2;
	}
	function monta_linha_digitavel($codigo) {
		// campo 1
		$banco    = substr($codigo,0,3);
		$moeda    = substr($codigo,3,1);
		$ccc      = substr($codigo,19,3);
		$ddnnum   = substr($codigo,22,2);
		$dv1      = modulo_10($banco.$moeda.$ccc.$ddnnum);
		// campo 2
		$resnnum  = substr($codigo,24,6);
		$dac1     = substr($codigo,30,1);//modulo_10($agencia.$conta.$carteira.$nnum);
		$dddag    = substr($codigo,31,3);
		$dv2      = modulo_10($resnnum.$dac1.$dddag);
		// campo 3
		$resag    = substr($codigo,34,1);
		$contadac = substr($codigo,35,6); //substr($codigo,35,5).modulo_10(substr($codigo,35,5));
		$zeros    = substr($codigo,41,3);
		$dv3      = modulo_10($resag.$contadac.$zeros);
		// campo 4
		$dv4      = substr($codigo,4,1);
		// campo 5
		$fator    = substr($codigo,5,4);
		$valor    = substr($codigo,9,10);
		
		$campo1 = substr($banco.$moeda.$ccc.$ddnnum.$dv1,0,5) . '.' . substr($banco.$moeda.$ccc.$ddnnum.$dv1,5,5);
		$campo2 = substr($resnnum.$dac1.$dddag.$dv2,0,5) . '.' . substr($resnnum.$dac1.$dddag.$dv2,5,6);
		$campo3 = substr($resag.$contadac.$zeros.$dv3,0,5) . '.' . substr($resag.$contadac.$zeros.$dv3,5,6);
		$campo4 = $dv4;
		$campo5 = $fator.$valor;
		
		return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
	}

?>