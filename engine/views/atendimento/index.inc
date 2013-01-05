<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>{{GT8:atendimento.title}} | Salão do Calçado</title>
		{{VIEW:head}}
		<script type="text/javascript" src="{{CROOT}}js/institucional.js" ></script>
	</head>
	<body>
		<div id="eMain" >
			{{VIEW:header}}
			{{VIEW:location}}
			<div class="row-12" >
				<ul class="grid-3" id="eInstitucionalMenu" >
					{{VIEW:atendimento.menu}}
				</ul>
				<div class="grid-8" >
					{{$this->getCrrContent()}}
				</div>
				<div class="clearfix" ></div>
			</div>
			<div class="height-superlarge" ></div>
			{{VIEW:footer}}
		</div>
	</body>
</html>