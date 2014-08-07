//Styling
//Make clickable read more link.

jQuery(document).ready(function($){

	var geoSearch = new google.maps.Geocoder;

	//Map Options
	var config = {
			zoom: 12,
			mapTypeId: google.maps.MapTypeId[sc_location_object.options.map_mode],
			panControl: parseInt(sc_location_object.options.pan_control) ? true : false,
			zoomControl: parseInt(sc_location_object.options.zoom_control) ? true : false,
			mapTypeControl: parseInt(sc_location_object.options.map_type_control) ? true : false,
			scaleControl: parseInt(sc_location_object.options.scale_control) ? true : false,
			streetViewControl: parseInt(sc_location_object.options.street_view) ? true : false,
			rotateControl: false,
			overviewMapControl: false
		},
		//iconImage = '',
		$map = null,
		markers = [],
		infoWindows =[],
		_data = null;

	var ui = {

		buildMarkers: function( data, first ){

			var results = [];

			$.each( data, function(i, marker){

				var html = ui.buildHTML( marker.title, marker.address, marker.telephone );

				if(i == 0){
					results.push({
						lat: marker.lat,
						lng: marker.lng ,
						html: html,
						//icon: iconImage,
						popup: true
					});
				} else {
					results.push({
						lat: marker.lat,
						lng: marker.lng,
						html: html,
						//icon: iconImage,
					});
				}
			});

			//Switch item to first
			if( first ){
				var temp = results[first];
				results.splice(first, 1);[first];
				results.unshift(temp);
			}

			return results;

		},

		buildHTML: function( title, address, telephone ){

			var string = "";

			string += '<strong>' + title + '</strong><br />';
			string += '<em style="font-size:0.9em;">';
				string += address;
				string += '<br />Tel: ' + telephone;
			string += '</em>';

			return string;

		},

		buildMap: function( markers, zoom, locationSet ){

			$map = null;

			if(!markers.length) return false;

			//Set center and zoom
			config.center = new google.maps.LatLng( markers[0].lat, markers[0].lng);
			config.zoom = zoom ? zoom : config.zoom;

			//Build map
			$map = new google.maps.Map( document.getElementById('sc-location-map'), config )

			$.each( markers, function(i, marker){

				//Add Markers
				markers[i] = new google.maps.Marker({
						position: new google.maps.LatLng(marker.lat, marker.lng),
						map: $map
				});

				//Add Info Windows
				infoWindows[i] = new google.maps.InfoWindow({
					content: marker.html
				});

				//Add Mrker Events
				markers[i].addListener('click', function(){
					infoWindows[i].open( $map, markers[i] );
				});


				if( i == 0 && locationSet ){
					infoWindows[i].open( $map, markers[i] );
				}

			});

		}

	}


	//Get Search Term
	var	address = $('#sc-postcode').val(),
		distanceLimit = $('#sc-distance').val(),
		locationSet = address ? true : false;

	if( $.type( address ) === "string" ){

		var region = sc_location_object.countrycode,
        	address = address + ', ' + region;

		geoSearch.geocode({'address':address,'region':region},function(results, status){

			if( status == google.maps.GeocoderStatus.OK ){

				var point = results[0].geometry.location,
				    data = {
						action: 'sc_location_ajax',
						lat: point.lat(),
						lng: point.lng(),
						distance: distanceLimit,
						location_set: locationSet
					};

				$.ajax({
					url: sc_location_object.ajaxurl,
					data: data,
					type: 'post',
					dataType: 'json'
				}).done(function(data, status, response){

					console.log(data);

					var $resultsList = $('#sc-location-list'),
						$template = $($resultsList.find('.sc-location-result-template').html().trim());

					//Hide Spinner
					$resultsList.find('.sc-loading-spinner').hide();

					if( data.has_results_under_distance && locationSet ){

						//Build HTML list
						$.each( data.results, function(i, marker){
							$item = $template.clone();
							$item.children('.sc-location-list-title').html(marker.title);
							$item.children('.sc-location-list-address').html(marker.address);
							$item.children('.sc-location-list-distance').html('Distance ' + marker.distance + ' ' + marker.distance_units);
							$resultsList.append($item.attr('data-id', i));
						});


					} else {

						if( locationSet ){
							$item = $template.clone();
							$item.children('.sc-location-list-title').html('No results Found');
							$resultsList.append($item);
						}

						config.zoom = 8;

					}

					//Build map
					ui.buildMap( ui.buildMarkers(data.results), config.zoom, locationSet );

					//Store Data
					_data = data;


				}).fail(function(data, status, response){
					console.log('Error: Something Went Wrong');
				});

				return false;

			} else {

				alert("Geocode was not successful for the following reason: " + status);

			}

		});

	}

	//Select Location From List
	$( "#sc-location-list" ).on( "click", ".sc-location-list-item", function() {

		$clicked = $(this);

		ui.buildMap( ui.buildMarkers( _data.results, $clicked.attr('data-id')), config.zoom, true );

		$clicked.addClass('active').siblings().removeClass('active');

		return false;

	});

});





