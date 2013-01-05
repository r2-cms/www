jCube.Include('Pluggins.showOverlay');

GT8.ShowModal	= function( options) {
	
	if ( options) {
		GT8.ShowModal.eLastModal	= options.eModal;
		options.eModal.appendTo( document.body);
		options.eModal.showOverlay({
			objRef: options.eRef,
			duration: options.showDuration,
			transition: options.transition,
			transitionIn: options.transitionIn || [0.3, 1.06, 0.5, 1.19],
			overlay: options.overlay || {
				border : 'none',
				background:'white',
				borderRadius: '7px',
				boxShadow: '0 0 80px 5px #333344',
				opacity: 0.8
			},
			glassPane: options.glassPane || {
				transition: true,
				background: 'black',
				opacity: 0.5
			},
			onShowOverlayComplete: options.onShowOverlayComplete
		});
	} else {
		GT8.ShowModal.eLastModal.showOverlay();
	}
}