<?php require_once(SROOT.'engine/functions/includeif.php'); ?><!DOCTYPE html>
<html lang="en">
	<?php GT8::printHead('Sem permissão | GT8', '', array(), array()); ?>
		<div id="eMain" >
			<?php GT8::printHeader(); ?>
			<div class="wrapper" >
				<div class="row padding-top" >
					<h2 class="block" >Você não tem permissão para acessar este diretório!</h2>
					<div class="block" >
						<small class="text-error" >"{{path}}"</small> 
					</div>
					<p class="block" >
						Se realmente precisa acesssar este arquivo, por favor, contate o administrador do sistema.
					</p>
					<div class="clear" ></div>
				</div>
			</div>
			<?php GT8::printFooter(); ?>
		</div>
	</body>
</html>