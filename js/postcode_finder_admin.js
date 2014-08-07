//Lookup long/lat of location in Wp Admin
var geo = new google.maps.Geocoder;

jQuery(document).ready(function($){
	$('#update_latlong').on('click', function() {
		var address = $('#sc-location-postcode').val(),
		    region = countrycode;

        address = address + ', ' + region;
		geo.geocode({'address':address,'region':region},function(results, status){

			if (status == google.maps.GeocoderStatus.OK) {
				var point = results[0].geometry.location;
				$('#sc-location-lat').val(point.lat());
				$('#sc-location-lng').val(point.lng());
			} else {
				alert("Geocode was not successful for the following reason: " + status);
			}

		});
		return false;
	});
});