<?php

class sc_location_finder_ajax extends sc_location_finder {

	public function location_query(){

		$distance_units = $this->options['Distance_Units'];
		$display_results = $this->options['Display_Results'];
		$lat1 = $_POST['lat'];
		$lng1 = $_POST['lng'];
		$distance_limit = $_POST['distance'] ? (int)$_POST['distance'] : 1000000;
		$location_set = $_POST['location_set'];

		//Build Results Array
		$results = array();

		foreach( $this->get_locations(true) as $location ) {

			//Location Meta
			$lng2 = $location->meta->lng;
			$lat2 = $location->meta->lat;
			$title = $location->meta->title;
			$location_address = $location->meta->address;
			$location_telephone = $location->meta->telephone;

			//Calculate Distance
			$theta = $lng1 - $lng2;
			$dist = sin(deg2rad((float)$lat1)) * sin(deg2rad((float)$lat2)) + cos(deg2rad((float)$lat1)) * cos(deg2rad((float)$lat2)) * cos(deg2rad((float)$theta));
			$dist = acos($dist);
			$dist = rad2deg($dist);
			$miles = $dist * 60 * 1.1515;

			//Location Content
			$content = $location->post_content;
			$content = nl2br($content);
			$content = str_replace('><br />', '>', $content);

			$results["{$miles}"] = array(
					'title' => $title,
					'distance' => $distance_units == "kilometres" ? round($miles * 1.609344) : round($miles),
					'distance_units' => $distance_units,
					'lat' => $lat2,
					'lng' => $lng2,
					'address' => $location_address,
					'telephone' => $location_telephone,
					'content' => $content
				);

		}

		//Order By Distance
		ksort($results);

		//Return Set Limit
		//if( $location_set ){
			if( $display_results != 0 && $display_results < count($results) ){
				$results = array_slice($results, 0, $display_results);
			}
		//}

		//echo json_encode( $location_set );
		//die();

		//Check Distance Limit
		$results_processed = array();
		$has_results = true;
		foreach($results as $distance => $content){
			if( round((int)$distance) > $distance_limit ){
				break;
			} else {
				$results_processed[] = $results[$distance];
			}
		}

		//Create new array if no reults
		if( count($results_processed) == 0 ){
			$has_results = false;
			foreach($results as $distance => $content){
				$results_processed[] = $results[$distance];
			}
		}

		$results_processed = array(
			'has_results_under_distance' => $has_results ? true : false,
			'results' => $results_processed
		);

		echo json_encode($results_processed);

		die();

	}


	public function __construct(){

		$this->get_options();

		//Add Ajax Hooks
		add_action('wp_ajax_sc_location_ajax', array( &$this, 'location_query' ) );
		add_action('wp_ajax_nopriv_sc_location_ajax', array( &$this, 'location_query' ) );


	}


}

?>