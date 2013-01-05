<?php require_once(SROOT.'engine/functions/includeif.php'); ?><!DOCTYPE html>
<html lang="en">
	<?php GT8::printHead('File Manager | GT8', '', array(CROOT.'js/Editor.js', CROOT.$GT8['admin']['root'].'explorer/js/fileEditor.js'), array(CROOT.$GT8['admin']['root'].'explorer/css/fileEditor.css')); ?>
		<div id="eMain" >
			<?php
				global $Users;
				$Users->printHeader(array(
					"h1" 		=> 'Usuários',
					'directory'	=> $Users->getDirLocation(),
					'toolbar'	=> $Users->toolbarItems
				));
			?>
			<div class="wrapper" >
				<div class="row" >
					<h2 class="block" >Desculpe-nos o transtorno, mas não encontramos a seguinte página que você tentou acessar:</h2>
					<div class="block" >
						<small class="text-error" >"{{path}}"</small> 
					</div>
					<p class="block" >
						Se você digitou o endereço, verifique novamente a ortografia ou contate o administrador deste sistema.
					</p>
					<div class="clear" ></div>
				</div>
			</div>
			<?php GT8::printFooter(); ?>
		</div>
	</body>
</html>