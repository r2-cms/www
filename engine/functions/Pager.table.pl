<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
		<title>Pager</title>
		<script type="text/javascript" src="/jCube/jCube.js" ></script>
		<script type="text/javascript" >
			jCube.Include('Array.each');
			jCube.Include('Array.map');
			jCube.Include('Document.getHttpVariables');
			jCube.Include('String.contains');
			jCube.Include('String.startsWith');
			jCube.Include('String.substringIndex');
			
			var Pager = {
				parse: function( name, value, resetIndex) {
					var loc	= window.location +'';
					
					if ( loc.contains('?') ) {
						var vars	= jCube.Document.getHttpVariables();
						var found	= false;
						vars.map( function() {
							if ( resetIndex && this[0] =='index') {
								return ['index', 1];
							}
							if ( this[0] == name) {
								found	= true;
								return [name, value];
							} else {
								return this;
							}
						}, null, true);
						if ( !found) {
							vars.push([name, value]);
						}
						var sVars	= '?';
						vars.each(function(){
							sVars	+= '&'+ this[0] +'='+ this[1];
						});
						
						loc	= loc.substringIndex('?') + sVars;
					} else {
						loc	= '?'+ name +'='+ value;
						if ( resetIndex) {
							loc	+= '&index=1';
						}
					}
					window.location	= loc.replace('?&', '?');
				},
				goTo: function( index) {
					this.parse('index', index);
				},
				goPrevious: function() {
					this.goTo( Number(jCube.Document.getHttpVariables().get('index') || 1) - 1);
				},
				goNext: function() {
					this.goTo( Number(jCube.Document.getHttpVariables().get('index') || 1) + 1);
				},
				orderBy: function( obj) {
					var field	= jCube(obj).textContent || jCube(obj).innerText;
					var position	= jCube.Document.getHttpVariables().get('order') + '';
					
					if ( position.startsWith(field)) {
						field	= field +' '+ (position.contains('DESC')? 'ASC': 'DESC');
					} else {
						field	= field +' ASC';
					}
					
					this.parse( 'order', field, true);
				}
			}
			window.onload	= function() {
				document.getElementsByTagName('TABLE')[0].cellPadding	=
				document.getElementsByTagName('TABLE')[0].cellSpacing	= 0;
			}
		</script>
		<style type="text/css" >
			html, body {
				margin: 0px;
			}
			div, table {
				font-size: 14px;
				line-height: 20px;
				color: #444;
			}
			th {
				background: #CCC;
				color: #000;
				line-height: 30px;
				cursor: default;
			}
			th, td {
				padding: 0 10px;
			}
			th.selected {
				background: #FCC;
			}
			div.pager {
				clear: both;
				height: 25px;
			}
			div.pager span {
				float: left;
				display: block;
				line-height: 25px;
				width: 35px;
				text-align: center;
				cursor: pointer;
			}
			div.pager strong {
				float: left;
				display: block;
				line-height: 25px;
				width: 50px;
				text-align: center;
				cursor: default;
				border: 1px solid #ccc;
				border-radius: 5px;
				background: #eee;
			}
			div.pager small {
				float: left;
				display: block;
				line-height: 25px;
				width: 40px;
				text-align: center;
				cursor: pointer;
			}
			div.pager small.disabled {
				float: left;
				display: block;
				line-height: 25px;
				width: 40px;
				text-align: center;
				cursor: default;
				color: #999;
			}
		</style>
	</head>
	<body>
		<div id="eContent" >
			<?php
				Print($results['rows']);
				Print('<div class="pager" >'. $results['page'] .'</div>');
			?>
		</div>
	</body>
</html>
