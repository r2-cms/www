<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Painel Administrativo | GT8</title>
		{{VIEW:admin.header-default}}
		<link rel="stylesheet" type="text/css" href="css/home.css" />
		{{tag:script|inline:{{JS_SERVER_VARS}}}}
		<script type="text/javascript" src="js/home.js" ></script>
	</head>
	<body>
		<div id="eMain" >
			{{VIEW:admin.header}}
			<div id="eBtGoPreviousPage" class="go-page-button" ></div>
			<div class="body" >
				<div class="overflow-view" >
				{{METHOD:getCards}}
					<div id="eModule-{{card_index}}-{{page_index}}" class="card" >
						<div class="spacing bg-linear-gray" >
							<a href="{{url}}" >
								<img src="{{img}}" alt="##sumary##" />
							</a>
							<span>##sumary##</span>
							<span class="hidden module-name" >{{module}}</span>
							<div>&nbsp;</div>
						</div>
					</div>
				{{/METHOD}}
				</div>
			</div>
			<div id="eBtGoNextPage" class="go-page-button" ></div>
			<footer class="main" >
				<div class="pages-holder" ></div>
				<a id="eBtAddPages" href="#nova-pagina" class="button user" title="Adicionar nova página" ><span><img alt="[adicionar página]" src="../imgs/gt8/add-small.png" width="16" height="16" /></span></a>
			</footer>
		</div>
	</body>
</html>