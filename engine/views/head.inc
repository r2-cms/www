		<link rel="stylesheet" type="text/css" href="{{CROOT}}css/main.css" />
		<script type="text/javascript" src="{{CROOT}}jCube/jCube.min.js" ></script>
		<script type="text/javascript" src="{{CROOT}}js/main.js" ></script>
		<script type="text/javascript" src="{{CROOT}}distatic/info.js" ></script>
		<script type="text/javascript" >
		//<![CDATA[
			document.createElement('HEADER');
			document.createElement('NAV');
			document.createElement('FOOTER');
			document.createElement('ASIDE');
			document.createElement('SECTION');
			document.createElement('ARTICLE');
			
			if ( !window.ASP) {
				window.ASP	= {
					CROOT: '/'
				};
			}
		//]]>
		</script>
		<script type="text/javascript" >
		//<![CDATA[
			{{JS_SERVER_VARS}}
		//]]>
		</script>
		<!--<meta name="viewport" content="width=device-width,initial-scale=1" />-->
		