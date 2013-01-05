<?php
	
	$sql	= array(
		"select"	=> "
				a.id,
				a.zip,
				t.type,
				a.stt, a.city, a.district,
				a.street, a.number, a.complement,
				a.reference, a.creation, a.modification
		",
		"from"	=> "
				gt8_address a
				INNER JOIN gt8_address_type t ON t.id = a.id_type
		",
		'gridConf'	=> array(
			//format: label|width|type|length|minwidth|maxwidth
			/*0*/ 'id|50|integer||20|100',
			/*1*/ 'Cep|80|integer|8|40|100',
			/*2*/ 'Tipo|100|enum|Home,Office',
			/*3*/ 'Estado|20|enum|SP,AC,AM,BA,DF,ES,GO,MA,MN,PA,PE,PR,PN,RJ,RN,RO,SC,SP,TO',
			/*4*/ 'Cidade|150|string|30',
			/*5*/ 'Bairro|150|string|20',
			/*6*/ 'Logradouro|150|string',
			/*7*/ 'N.|50|string|10',
			/*8*/ 'Complemento|60|string|30',
			/*9*/ 'Referência|200|string|200',
			/*10*/ 'Criação|80|datetime',
			/*11*/ 'Modificação|80|datetime'
		),
		'gridState'	=> '0|100{{}}1|160{{}}Região(())3|40(())4|300{{}}Localização(())5|300(())6|300(())7|100{{}}Data(())10|160(())11|160',
		'card8'		=> '<a id="address-@id@" class="address card card-border col-7" href="@id@/" title="address-@id@" >
							<em class="zip" >@zip@</em>
							<span class="imgC" ><img src="'. CROOT .'imgs/gt8/address/@type@.png" alt="[imagem]" /></span>
							<strong class="estado" >@stt@ - @city@</strong>
							<span class="title" >@street@, @number@. @district@</span>
							<span class="id_users hidden" >@id_users@</span>
							<span class="stt hidden" >@stt@</span>
							<span class="city hidden" >@city@</span>
							<span class="street hidden" >@street@</span>
							<span class="number hidden" >@number@</span>
							<span class="district hidden" >@district@</span>
						</a>'
	);
	
?>