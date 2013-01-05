jCube(function(){
	Editor.onBeforeUpdate	= function(name, value, obj, req) {
		req.url	= '?action=update.privileges&format=JSON';
		
		var eParent	= jCube(obj).getParent('tr');
		var id	= ((eParent||{}).id+'').substringIndex('-', -1);
		if ( isNaN(Number(id))) {
			alert('Por favor, contate o administrador.\nEsta estrutura foi alterada e não é possível alterar as informações deste contato!');
			return false;
		} else {
			req.addGet('idPrivilege', id);
		}
		return [ name, value];
	};
});
