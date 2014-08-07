<?php

class sc_location_finder {

	public $options = null;

	public $api_key = "";

	public $scripts = array(
			'googlemapapi' => 'http://maps.google.com/maps/api/js?sensor=false',
			'googleapi' => 'http://www.google.com/uds/api?file=uds.js&v=1.0',
			'postcode_finder_admin' => "js/postcode_finder_admin.js",
			'jquery_gmap' => "js/jquery.gmap.min.js",
			'postcode_finder' => "js/postcode_finder.js"
		);

	//Admin JS Scripts
	public function scripts_admin(){

		global $countrycodes_array;

		if( get_post_type() == 'location' ){

            wp_register_script( 'googlemapapi', $this->scripts['googlemapapi'].$this->api_key, FALSE );
			wp_register_script( 'googleapi', $this->scripts['googleapi'], FALSE );
			wp_register_script( 'postcode_finder_admin', SC_LOCATION_PLUGIN_URL.$this->scripts['postcode_finder_admin'], null, "", FALSE );

			wp_enqueue_script( 'googlemapapi' );
			wp_enqueue_script( 'googleapi' );
			wp_enqueue_script( 'postcode_finder_admin' );

			wp_localize_script( 'postcode_finder_admin', 'countrycode', $countrycodes_array[$this->options['countrycode']] );

		}

	}

	//Public JS Scripts
	public function scripts_public(){

		if( is_page( $this->options['searchresults_page_id'] ) ){

	        global $countrycodes_array;

			wp_register_script( 'googlemapapi', $this->scripts['googlemapapi'].$this->api_key, FALSE );
			wp_register_script( 'googleapi', $this->scripts['googleapi'], FALSE );
			wp_register_script( 'postcode_finder', SC_LOCATION_PLUGIN_URL.$this->scripts['postcode_finder'], false, "", true );

			wp_enqueue_script( 'googlemapapi' );
			wp_enqueue_script( 'googleapi' );
			wp_enqueue_script( 'postcode_finder' );

			$params = array(
				'ajaxurl' => admin_url( 'admin-ajax.php', ( $_SERVER["HTTPS"] )? 'https://':'http://' ),
				'options' => $this->options,
				'countrycode' => $countrycodes_array[$this->options['countrycode']]
			);
			wp_localize_script( 'postcode_finder', 'sc_location_object', $params );

			//Load CSS
        	wp_enqueue_style( 'sc_mockingbird_style', SC_LOCATION_PLUGIN_URL.'/css/style.css' );

		}

	}

	//Results Content
	function filter_search_results( $content ){

		if( $this->options['searchresults_page_id'] && is_page( $this->options['searchresults_page_id'] ) ){

		    include_once('page_location_results.php');
			return false;

		} else if( $this->options['directory_page_id'] && is_page( $this->options['directory_page_id'] ) ){

			//return 'Directory Page Here';
			include_once('page_location_directory.php');
			return false;

		} else {

			return $content;

		}

	}

	public function get_options(){
		$this->options = get_option('aphs_FYN_options');
	}

	public function get_locations( $include_meta = false ){

		//Location Query
		$args = array(
					'post_type' => 'location',
					'posts_per_page' => -1
				);
		$query = new WP_Query( $args );
		wp_reset_query();

		//Include Meta Data
		if( $include_meta && $query->posts ){

			$locations = array();

			foreach( $query->posts as $location ){

				$location->meta = (object)array();

				//Get Meta
				//Location Coordinates
				$location->meta->lng = get_post_meta ( $location->ID, '_aphs_FYN_longitude', true );
				$location->meta->lat = get_post_meta ( $location->ID, '_aphs_FYN_latitude', true );
				$location->meta->postcode = get_post_meta ( $location->ID, '_aphs_FYN_postcode', true );

				//Location Meta
				$location->meta->title = ( $title = get_post_meta( $location->ID, '_aphs_FYN_location_name', true ) ) ? $title : $location->post_title ;
				$location->meta->address = get_post_meta( $location->ID, '_aphs_FYN_location_address', true );
				$location->meta->telephone = get_post_meta( $location->ID, '_aphs_FYN_location_telephone', true );

				$locations[] = $location;
			}

			return $locations;

		}

		return $query->posts;

	}

	public function __construct(){

		$this->get_options();

		//Google Maps API Key
		if( $Google_Maps_API_Key = $this->options['Google_Maps_API_Key'] ){
			$this->api_key = "&key=$Google_Maps_API_Key";
		}

		if( is_admin() ) {

			//Load Admin Scripts
			add_action( 'admin_print_scripts-post-new.php', array( &$this, 'scripts_admin' ) , 11 );
			add_action( 'admin_print_scripts-post.php', array( &$this, 'scripts_admin' ), 11 );

		} else {

			//Load Public Scripts
			add_action( 'wp_enqueue_scripts', array( &$this, 'scripts_public' ) );

			//Output Results in Content
			add_filter( 'the_content', array( &$this, 'filter_search_results' ) );

		}

	}


}

?>