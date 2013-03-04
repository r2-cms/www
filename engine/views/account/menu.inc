					<div class="grid-4 marginless unfloat-phone" >
						<div class="padding-medium" >
							<h2 class="text-align-left margin-bottom" >
								<strong>Bem-vindo, {{SESSION:login.name}}. </strong>
							</h2>
							<p>
								Aqui você poderá editar sua conta e seus dados. Também poderá ver seus pedidos de compras, adicionar e editar endereços para entrega.
							</p>
						</div>
						<div class="links-menu padding-bottom" >
							<a href="{{CROOT}}{{GT8:account.root}}{{GT8:account.data.root}}" class="link dados" >Dados pessoais</a>
							<a href="{{CROOT}}{{GT8:account.root}}enderecos/" class="link enderecos" >Endereços</a>
							<a href="{{CROOT}}{{GT8:account.root}}contatos/" class="link contatos" >Contatos</a>
							<a href="{{CROOT}}{{GT8:account.root}}seguranca/" class="link seguranca" >Alterar Senha</a>
							<a href="{{CROOT}}{{GT8:account.root}}{{GT8:account.orders.root}}" class="link compras" >Compras</a>
							{{IF:$_SESSION['login']['level'] > 3}}<a href="{{CROOT}}{{GT8:admin.root}}" class="link gt8-admin" >Administrativo</a>{{/IF}}
						</div>
					</div>
