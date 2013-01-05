jCube.Include("Pluggins.BackgroundEffect");

jCube(function(){
	(function(){//BACKGROUND-EFFECT
		var eIndicator	= jCube(':.background-effect .bar-indicator');
		jCube('::.background-effect').each(function(){
			new jCube.Pluggins.BackgroundEffect({
				container: this,
				wait: 10000,
				transition: jCube.Transition.LINEAR,
				onChangeComplete: function() {
					eIndicator.setStyle('width', '100%');
				},
				onTimeEllapsing: function(t) {
					eIndicator.setStyle('width', 100 - (t/this.wait) * 100 +'%')
				}
			}).start().getChron().fps	= 20;
		});
	})();
	
});