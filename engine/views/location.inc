			<nav class="directory row-12 fluid-12 margin-bottom clearfix-child" >
				<h1 class="grid-12" >
					<?php
						$location	= $this->getDirLocation();
						$location	= strtolower($location);
						$location	= str_replace('calcados', 'calçados', $location);
						$location	= str_replace('conexao', 'conexão', $location);
						$location	= str_replace('sandalia', 'sandália', $location);
						$location	= str_replace('salao', 'salão', $location);
						print($location);
					?>
				</h1>
				<hr class="grid-12" />
			</nav>
