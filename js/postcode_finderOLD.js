var geoSearch = new google.maps.Geocoder;

//Map Options
var zoom = 12,
	controls = {
		panControl: true,
		zoomControl: true,
		mapTypeControl: true,
		scaleControl: true,
		//streetViewControl: true,
		overviewMapControl: false
	},
	icon = { //60px
		//image: '/wp-content/plugins/location_finder/images/marker.png',
		//iconsize:[60,56],
		//iconsize:[37,60],
		//iconanchor:[18, 60]
	};

jQuery(document).ready(function() {


	//Get Search Term
	var $map = jQuery('#FYN_map'),
		address = jQuery('#fyn_postalcode').val(),
		distanceLimit = jQuery('#fyn_distance_limit').val(),
		locationSet = address ? true : false;

	if(jQuery.type( address ) === "string"){

		var region = FYN_search.countrycode,
        	address = address + ', ' + region;

		geoSearch.geocode({'address':address,'region':region},function(results, status){
			if (status == google.maps.GeocoderStatus.OK) {
				var point = results[0].geometry.location;
				var data = {
					action:'aphs_FYN_search_ajax',
					lat: point.lat(),
					lng: point.lng(),
					distance: distanceLimit
				}
				if (jQuery('#aphsfyntagsearch').length > 0){
					data['aphsfyntagsearch']=jQuery('#aphsfyntagsearch').val()
				}
				if (jQuery('#aphsfyntaglistsearch').length > 0){
					data['aphsfyntaglistsearch']=jQuery('#aphsfyntaglistsearch').val()
				}
				if (jQuery('#fyn_show_within_distance').length > 0){
					data['fyn_show_within_distance']=jQuery('#fyn_show_within_distance').val()
				}

				jQuery.post(FYN_search.ajaxurl, data, function(returndata){

					jQuery('#search_results').html(returndata);
						//Create Marker Array
						var results = [];
						if( locationSet && !jQuery( "li.FYN_viewmap" ).first().hasClass('no-results-found') ){

							jQuery( "li.FYN_viewmap" ).each(function( index ) {

								var distance = parseInt(jQuery(this).attr('data-distance')),
									html = '<strong>'+jQuery(this).attr('data-title') + '</strong><br /><em style="font-size:0.9em;">' + jQuery(this).attr('data-address') + '<br />Tel: ' + jQuery(this).attr('data-telephone') + '</em>';
								if(index == 0 && distance <= distanceLimit){
									results.push({latitude: jQuery(this).attr('data-lat'), longitude: jQuery(this).attr('data-lng'), html: html, popup:true});
									//Add Active Class
									jQuery(this).addClass('active');
								} else {
									results.push({latitude: jQuery(this).attr('data-lat'), longitude: jQuery(this).attr('data-lng'), html: html});
								}

								//Limit Displayed Results
								if(index > 6){
									jQuery(this).hide();
								}

							});

						} else {

							jQuery( "li.FYN_viewmap" ).each(function( index ) {

								if( !$(this).hasClass('no-results-found') ){

									var distance = parseInt(jQuery(this).attr('data-distance')),
										html = '<strong>'+jQuery(this).attr('data-title') + '</strong><br /><em style="font-size:0.9em;">' + jQuery(this).attr('data-address') + '<br />Tel: ' + jQuery(this).attr('data-telephone') + '</em>';

									results.push({latitude: jQuery(this).attr('data-lat'), longitude: jQuery(this).attr('data-lng'), html: html});

									//Hide Location List
									jQuery(this).hide();

									//Zoom Out on Map
									zoom = 8;

								}

							});

						}

						//Create Map
						$map.gMap({
							zoom: zoom,
							markers: results,
							controls:  controls,
							//icon: icon
						});

				});

			} else {
				alert("Geocode was not successful for the following reason: " + status);
			}
		});
	}

	//Select Location From List
	jQuery( "#search_results" ).on( "click",".FYN_viewmap", function() {

		//Determine Selected Location
		var results = [{
			latitude: jQuery(this).attr('data-lat'),
			longitude: jQuery(this).attr('data-lng'),
			html: '<strong>'+jQuery(this).attr('data-title') + '</strong><br /><em style="font-size:0.9em;">' + jQuery(this).attr('data-address') + '<br />Tel: ' + jQuery(this).attr('data-telephone') + '</em>',
			popup:true
		}],
		setTitle = jQuery(this).attr('data-title');

		//Gather Other Lopcations
		jQuery('li.FYN_viewmap').each(function(index){
			var title = jQuery(this).attr('data-title');
			if( title != setTitle){
				results.push({
					latitude: jQuery(this).attr('data-lat'),
					longitude: jQuery(this).attr('data-lng'),
					html: '<strong>'+jQuery(this).attr('data-title') + '</strong><br /><em style="font-size:0.9em;">' + jQuery(this).attr('data-address') + '<br />Tel: ' + jQuery(this).attr('data-telephone') + '</em>'
				});
			}
		});

		//Create Map
		$map.gMap({
			zoom: zoom,
			markers: results,
			controls: controls,
			//icon: icon
		});

		//Add Active Class
		jQuery(this).addClass('active').siblings().removeClass('active');

		return false;

	});

});
