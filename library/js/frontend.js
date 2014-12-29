(function($, doucment, undefined){


	jQuery(document).ready(function($){

		if ( PNS.jsTarget1 != "" && PNS.phoneNumbers[0] != "" ){

			var swappy1 = $( PNS.jsTarget1 ).html( PNS.phoneNumbers[0] );

			if (swappy1.attr( "href") != "" ){
				//in each case we assume if it has an href, it's a tel: link
				swappy1.attr( "href", "tel:" + PNS.phoneNumbers[0] );
			}

		}

		if ( PNS.jsTarget2 != "" && PNS.phoneNumbers[1] != "" ){

			var swappy2 = $( PNS.jsTarget2 ).html( PNS.phoneNumbers[1] );

			if (swappy2.attr("href") != ""){
				swappy2.attr("href", "tel:" + PNS.phoneNumbers[1] );
			}

		}
		if ( PNS.jsTarget3 != "" && PNS.phoneNumbers[2] != "" ){

			var swappy3 = $( PNS.jsTarget3 ).html( PNS.phoneNumbers[2] );

			if (swappy3.attr("href") != ""){
				swappy3.attr("href", "tel:" + PNS.phoneNumbers[2] );
			}

		}

	});

})(jQuery, document);