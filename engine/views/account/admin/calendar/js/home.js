jCube(function() {//CALENDAR SIZE
	var eCal	= jCube(':.Calendar');
	var eLeft	= jCube(':.SplitPane-horizontal .left-pane');
	var eKnob	= jCube(':.SplitPane-horizontal .knob-pane');
	var W		= window.getWidth();
	
	jCube(':.SplitPane-horizontal').addEvent('onSplitPaneResize', function(e) {
		eCal.setStyle({
			width: W - eLeft.offsetWidth - eKnob.offsetWidth
		});
	}).trigger('onSplitPaneResize');
});