<?php
/*
Plugin Name: Location Finder
Plugin URI: http://www.flintriver.co.uk
Description: Find a location relative to a postcode or place name. Google Maps API based.
Version: 1.0
Author: Scott Carmichael
Author URI: http://www.scottcarmichael.co.uk
Credit: Originally based on Adam Sargant's "Find Your Nearest" plugin - http://www.sargant.net/projects/wordpress-plug-ins/wp-find-your-nearest/
License: GPL2
*/

define('SC_LOCATION_PLUGIN_URL',$plugin_url=plugin_dir_url(__FILE__));

//Country Codes Array
include_once('includes/countrycodes.php');

include_once('includes/location_finder.php');
new sc_location_finder();

//Location Post Type
include_once('includes/location_post_type.php');
new sc_location_post_type();

//Settings Page
include_once('includes/location_finder_settings_page.php');
new sc_location_finder_settings_page();

//Location Ajax Query
include_once('includes/location_finder_ajax_query.php');
new sc_location_finder_ajax();


?>