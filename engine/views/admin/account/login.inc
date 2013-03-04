<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Login | Admin</title>
		<link rel="stylesheet" type="text/css" href="{{AROOT}}css/main.css" />
		<link rel="stylesheet" type="text/css" href="{{AROOT}}account/css/login.css" />
		<script type="text/javascript" src="{{CROOT}}jCube/jCube.min.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/gt8/gt8.js" ></script>
		<script type="text/javascript" src="{{AROOT}}account/js/login.js" ></script>
		<script type="text/javascript" src="{{AROOT}}js/main.js" ></script>
	</head>
	<body>
		<div id="eMain" >
			<div id="eLoginWindow" >
				<div class="bg" >
					<h2>
						<a href="{{CROOT}}" class="logo" ><img alt="[logotipo]" src="{{CROOT}}imgs/gt8/gt8-logo-regular.png" /></a>
						<strong>Início de sessão</strong>
					</h2>
					<div class="stage line padding-top" >
						<div class="infos col-8bm" >
							<img class="spinner login-spinner-feedback opacity-0" src="{{CROOT}}imgs/gt8/spinner-large.gif" alt="carregando..." />
							<h3 class="login-info-feedback" >Por favor, preencha com seu nome e senha de acesso.</h3>
							<p>
								&nbsp;
							</p>
						</div>
						<div class="fields col-10 input-validation" >
							<label class="line name required" >
								<span class="col-9bm" ><input type="text" class="input-rounded-shadowed" value="" name="name" /><small>Nome de usuário</small></span>
								<em class="col-1" >&nbsp;</em>
							</label>
							<label class="line pass required" >
								<span class="col-9bm" ><input type="password" class="input-rounded-shadowed" value="" name="password" /><small>Senha</small></span>
								<em class="col-1" >&nbsp;</em>
							</label>
							<label class="line keepLogged" >
								<span class="col-1" >
									<input type="checkbox" name="save" />
								</span>
								<span class="col-7" >Manter conectado</span>
							</label>
							<label class="line keepLogged" style="margin-top: 10px" >
								<a class="a-button submit col-9bm" href="#" >
									<span>
										<em>
											<img src="{{CROOT}}imgs/gt8/password-medium.png" alt="[button]" />
										</em>
										<strong>Entrar</strong>
										<small>Clique para acessar a área privada</small>
									</span>
								</a>
							</label>
							<div class="clear" ></div>
							<div class="forgot" >
								<small><a href="{{CROOT}}{{GT8:account.root}}{{GT8:account.forgotPassword.root}}" >Esqueci a senha</a></small>
							</div>
						</div>
						<footer class="new" >
							<small><a href="{{CROOT}}{{GT8:account.root}}{{GT8:account.new.root}}" >Criar novo cadastro</a></small>
						</footer>
						<div class="clear" ></div>
					</div>
					<div class="clear height-30" >&nbsp;</div>
				</div>
				<footer class="margin-top" >
					<div class="top" ><small>O Sistema de Gerenciamento de Acesso {{GT8:title}} utiliza mecanismo de alta proteção para transmitir de forma confidencial seus dados.</small></div>
					<hr />
					<div class="bottom" ><small>{{GT8:title}} &copy; 2012 Todos os direitos reservados.</small></div>
				</footer>
			</div>
		</div>
	</body>
</html>