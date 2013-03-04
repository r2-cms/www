jCube(function(){
	//turn Grid editable
	jCube(':.Grid').addClass('editable');
	jCube(':.Grid > .head .group > .g-col[title^=old|]').addClass('editable');
	jCube(':.Grid > .head .group > .g-col[title^=new|]').addClass('editable');
	
	jCube(':.Grid').addEvent('onGridCellChange', function(e, cell, main){
		var field	= main.getColHead(cell).title.substringIndex('|');
		var rowIndex	= cell.getNodeIndex();
		var id	= main.getColBody(jCube(':.Grid > .head .group > .g-col[title^=id|]')).query('::div > a')[rowIndex].textContent.toInteger();
		var value	= cell.innerHTML;
		GT8.Spinner.request(new jCube.Server.HttpRequest({
			url: '?action=update&id='+ id +'&field='+ field +'&value='+ escape(value),
			noCache: true,
			onComplete: function() {
				
			}
		}));
	});
	
});