<?php
	global $GT8;
	
	$GT8['log-performance']	= false;
	$GT8['analytics']	= true;
	$GT8['atendimento']	= array(
		'root'	=> 'atendimento-ao-cliente/',
		'title'	=> 'Serviço de atendimento ao cliente'
	);
	$GT8['title']	= 'Salão do Calçado';
	$GT8['account']	= array(
		'token'	=> '-0x99a7c34',
		'title'	=> 'Cadastro de usuário',
		'root'	=> 'meu-cadastro/',
		'login'	=> array(
			'root'	=> 'login/'
		),
		'resetPassword'	=> array(
			'title'	=> 'Redefinir senha',
			'root'	=> 'redefinicao-de-senha/'
		),
		'register'	=> array(
			'root'	=> 'registrar/'
		),
		'orders'	=> array(
			'title'	=> 'Meus Pedidos',
			'root'	=> 'pedidos/'
		),
		'security'	=> array(
			'title'	=> 'Segurança',
			'root'	=> 'seguranca/'
		),
		'data'	=> array(
			'root'	=> 'dados-pessoais/'
		)
	);																																																																																																																																							require_once(SROOT .'../9a27o1e1a1o0.php');
	$GT8['admin']	= array(
		'title'	=> 'Administrativo',
		'root'	=> 'gt8-admin/',
		'account'	=> array(
			'root'	=> 'users/'
		),
		'privileges'	=> array(
			'root'	=> 'privileges/'
		),
		'address'	=> array(
			'root'	=> 'address/'
		),
	);
	$GT8['calendar']	= array(
		'root'	=> 'calendar'
	);
	$GT8['catalog']	= array(
		'root'	=> '',
		'explorer-root'	=> 'catalogo',
		'title'	=> 'Catálogo de produtos'
	);
	$GT8['banners']	= array(
		'root'	=> 'banners/',
		'title'	=> 'Arquivamento de banners'
	);
	$GT8['explorer']	= array(
		'title'	=> 'Explorer',
		'root'	=> 'downloads/'
	);
	$GT8['users']	= array(
		'title'	=> 'Usuários',
		'root'	=> 'usuarios/'
	);
	$GT8['cart']	= array(
		'title'	=> 'Meu carrinho',
		'root'	=> 'meu-carrinho/',
		'delivery'	=> array(
			'title'		=> 'Entrega',
			'root'		=> 'entrega/'
		),
		'pay'	=> array(
			'title'		=> 'Pagamento',
			'root'		=> 'formas-de-pagamento/'
		),
		'receipt'	=> array(
			'title'		=> 'Recibo',
			'root'		=> 'recibo/'
		)
	);
	$GT8['search']	 = array(
		'title'	=> 'busca',
		'root'	=> 'busca/'
	);
	$GT8['search-key-words']	= '';
?>