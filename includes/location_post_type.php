<?php

class sc_location_post_type {

	public $post_type_name = 'location';

	public $options = null;

	public function post_type_options() {
		return array(
			'labels' => array(
				'name' => __( 'Locations' ),
				'singular_name' => __( 'Location' ),
				'add_new' => __( 'Add New' ),
				'add_new_item' => __( 'Add New Location' ),
				'edit_item' => __( 'Edit Location' ),
				'new_item' => __( 'New Location' ),
				'all_items' => __( 'All Locations' ),
				'view_item' => __( 'View Location' ),
				'search_items'  => __( 'Search Locations' ),
				'not_found' => __( 'No locations found.' ),
				'not_found_in_trash' => __( 'No locations found in the trash.' ),
				'parent_item_colon' => '',
				'menu_name' => 'Locations'
			),
			'description' => 'Holds our testimonials and testimonial specific data',
			'public' => true,
			'menu_position' => 20,
			//'menu_icon' => admin_url().'images/press-this.png',
			'supports' => array( 'title', 'editor' ), // title, editor, thumbnail, excerpt, comments
			'has_archive'   => true,
			'rewrite' => array( 'slug' => 'testimonials', 'with_front' => true ),
			'menu_icon' => 'dashicons-location'
		);
	}

	public function post_type_setup() {
		register_post_type( $this->post_type_name, $this->post_type_options() );
	}

	//Build 'Defaults' Object
	public function build_options() {
		return (object)array(
			'post_type' => $this->post_type_name,
			'unique_id'  => "sc_{$this->post_type_name}_details", //unique prefix
			'title'		 => 'Location Details', //title
			'context'	 => 'side', //normal, advanced, side
			'priority'	 => 'default' //default, core, high, low
		);
	}

//Custom Meta Boxes

	public function custom_meta_add() {

		add_meta_box(
			$this->options->unique_id, // Unique ID
			esc_html__( $this->options->title, 'example' ), //Title
			array(&$this, 'custom_meta_render' ), // Callback (builds html)
			$this->options->post_type, // Admin page (or post type)
			$this->options->context, // Context
			$this->options->priority, // Priority
			$callback_args = null
		);

	}

	public function custom_meta_render($post){

		wp_nonce_field( basename( __FILE__ ), $this->options->unique_id.'_nonce' );

		$location_postcode = get_post_meta ($post->ID, '_aphs_FYN_postcode', true);
		$location_lat = get_post_meta ($post->ID, '_aphs_FYN_latitude', true);
		$location_lng = get_post_meta ($post->ID, '_aphs_FYN_longitude', true);
		$location_title = get_post_meta ($post->ID, '_aphs_FYN_location_name', true);
		$location_address = get_post_meta ($post->ID, '_aphs_FYN_location_address', true);
		$location_telephone = get_post_meta ($post->ID, '_aphs_FYN_location_telephone', true);

		?>

		<label for="sc-location-postcode">Postal Code:</label>
		<input name="aphs_FYN_postcode" type="text" class="widefat" id="sc-location-postcode" value="<?php echo $location_postcode;?>" size="16" />

		<button class="button button-primary" id="update_latlong">Update Coordinates</button>
		<br />

		<label for="sc-location-lat">Latitude:</label>
		<input name="aphs_FYN_latitude" type="text" class="widefat" id="sc-location-lat" value="<?php echo $location_lat;?>" size="16" />

		<label for="sc-location-lng">Longitude:</label>
		<input name="aphs_FYN_longitude" type="text" class="widefat" id="sc-location-lng" value="<?php echo $location_lng;?>" size="16" />

		<br />
		<hr />

		<label for="sc-location-title">Location Name:</label>
		<input name="aphs_FYN_location_name" type="text" class="widefat" id="sc-location-title" value="<?php echo $location_title;?>" />

		<label for="sc-location-address">Address:</label>
		<textarea name="aphs_FYN_location_address" class="widefat" id="sc-location-address"><?php echo $location_address;?></textarea>

		<label for="sc-location-telephone">Telephone:</label>
		<input name="aphs_FYN_location_telephone" type="text" class="widefat" id="sc-location-telephone" value="<?php echo $location_telephone;?>" />

	<?php }

	public function custom_meta_save($post_id, $post = false){

		//Verify the nonce before proceeding.
		if ( !isset( $_POST[$this->options->unique_id.'_nonce'] ) || !wp_verify_nonce( $_POST[$this->options->unique_id.'_nonce'], basename( __FILE__ ) ) ){
			return $post_id;
		}

		//Save Location Data
		if(isset($_POST['aphs_FYN_postcode']) && isset($_POST['aphs_FYN_latitude']) && isset($_POST['aphs_FYN_longitude'])){
			update_post_meta( $post_id, '_aphs_FYN_postcode', $_POST['aphs_FYN_postcode']);
			update_post_meta( $post_id, '_aphs_FYN_latitude', $_POST['aphs_FYN_latitude']);
			update_post_meta( $post_id, '_aphs_FYN_longitude', $_POST['aphs_FYN_longitude']);
		}

		//Save Location Meta
		if(isset($_POST['aphs_FYN_location_name']) || isset($_POST['aphs_FYN_location_address']) || isset($_POST['aphs_FYN_location_telephone'])){
			update_post_meta( $post_id, '_aphs_FYN_location_name', $_POST['aphs_FYN_location_name']);
			update_post_meta( $post_id, '_aphs_FYN_location_address', $_POST['aphs_FYN_location_address']);
			update_post_meta( $post_id, '_aphs_FYN_location_telephone', $_POST['aphs_FYN_location_telephone']);
		}

	}

	public function custom_meta_setup() {

		//Add Box
		add_action( 'add_meta_boxes', array(&$this, 'custom_meta_add' ));
		//Save Box
		add_action( 'save_post', array(&$this, 'custom_meta_save'));

	}

	public function __construct(){

		//Add Post Custom Type
		add_action( 'init', array(&$this, 'post_type_setup') );

		//Create 'Options' Object
		$this->options = $this->build_options();

		//Add Custom Meta
		add_action( 'init', array(&$this, 'custom_meta_setup'));

	}

}

?>