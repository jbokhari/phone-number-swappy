(function($, doucment, undefined){


	jQuery(document).ready(function($){
		for (var i =  PNS.jsTarget.length - 1; i >= 0; i--) {
			var swappy1 = $( PNS.jsTarget[i] ).text( PNS.phoneNumbers[i] );
			$("a" + PNS.jsTarget[i] ).attr( "href", "tel:" + PNS.phoneNumbers[i] );
		};
	});

})(jQuery, document);