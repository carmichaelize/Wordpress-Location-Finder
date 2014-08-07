<?php


class sc_location_finder_settings_page extends sc_location_finder{

	//Add Admin Menu
	public function admin_menu() {
		add_submenu_page(
						  'edit.php?post_type=location',
						  'Location Finder Settings',
						  'Settings',
						  'manage_options',
						  '_settings',
						  array( &$this, 'page_layout' )
						);
	}

	public function admin_init() {

		//Register Settings
		register_setting( 'aphs_FYN_options', 'aphs_FYN_options', array( &$this, 'validate' ) );

		//Options Section
		add_settings_section( 'aphs_FYN_Options', 'Options', NULL, '_settings' );

		//Google Maps Key
		add_settings_field('aphs_FYN_Google_Maps_API_Key', 'Google API Key (optional)', array( &$this, 'text_input' ), '_settings', 'aphs_FYN_Options', array('Google_Maps_API_Key'));
		//Distance Unit
		add_settings_field( 'aphs_FYN_Distance_Units', 'Distance Units', array( &$this, 'select_distance_unit_input' ), '_settings', 'aphs_FYN_Options', '' );
		//Display Number
		add_settings_field('aphs_FYN_Display_Results', 'Display Results', array( &$this, 'text_input' ), '_settings', 'aphs_FYN_Options', array('Display_Results'));
		//Item Name
		add_settings_field('aphs_FYN_Item_Name', 'Item Name', array( &$this, 'text_input' ), '_settings', 'aphs_FYN_Options', array('Item_Name'));
		//Results Page
		add_settings_field( 'aphs_FYN_searchresults_page', 'Search Results Page', array( &$this, 'select_page_input' ), '_settings', 'aphs_FYN_Options', array('searchresults_page_id') );
		//Directory Page
		add_settings_field( 'aphs_FYN_directoryresults_page', 'Directory Page', array( &$this, 'select_page_input' ), '_settings', 'aphs_FYN_Options', array('directory_page_id') );
		//Country
		add_settings_field( 'aphs_FYN_country', 'Country', array( &$this, 'select_country_input' ), '_settings', 'aphs_FYN_Options', '' );

		$map_mode_options = array(
			'ROADMAP' => 'Road Map',
			'SATELLITE' => 'Satellite',
			'HYBRID' => 'Hybrid',
			'TERRAIN' => 'Terrain'
		);
		$no_yes = array( '0' => 'No', '1' => 'Yes' );

		add_settings_field( 'map_mode', 'Map Mode', array( &$this, 'select_input' ), '_settings', 'aphs_FYN_Options', array('map_mode', $map_mode_options) );
		add_settings_field( 'street_view', 'Street View', array( &$this, 'select_input' ), '_settings', 'aphs_FYN_Options', array('street_view', $no_yes) );
		add_settings_field( 'zoom_control', 'Zoom Control', array( &$this, 'select_input' ), '_settings', 'aphs_FYN_Options', array('zoom_control', $no_yes) );
		add_settings_field( 'pan_control', 'Pan Control', array( &$this, 'select_input' ), '_settings', 'aphs_FYN_Options', array('pan_control', $no_yes) );
		add_settings_field( 'scale_control', 'Scale Control', array( &$this, 'select_input' ), '_settings', 'aphs_FYN_Options', array('scale_control', $no_yes) );

	}

	//Standard Input
	public function text_input($args){
        echo "<input class='regular-text' type='text' name='aphs_FYN_options[{$args[0]}]' value='{$this->options[$args[0]]}' />";
    }

    //Standard Select
	public function select_input($args){

        echo "<select name='aphs_FYN_options[{$args[0]}]'>";
        	foreach($args[1] as $key => $value){
        		$selected = $key == $this->options[$args[0]] ? "selected='selected'" : "";
        		echo "<option value='{$key}' {$selected}>{$value}</option>";
        	}
        echo "</select>";
    }

    //Select Page
    public function select_page_input($args) {

		$pages = get_pages();

		?>

		<select name='aphs_FYN_options[<?php echo $args[0]; ?>]'>
			<option value=''><?php echo esc_attr( __( 'Select page' ) ); ?></option>
			<?php foreach ( $pages as $pagg ) : ?>
				<option value="<?php echo $pagg->ID; ?>" <?php echo $this->options[$args[0]] == $pagg->ID ? "selected='selected'" : "";?> >
					<?php echo $pagg->post_title ?>
				</option>
			<?php endforeach; ?>
		</select>

	<?php }

	//Select Distance Unit
	public function select_distance_unit_input() { ?>

		<select name="aphs_FYN_options[Distance_Units]">
			<option value="miles" <?php echo $this->options['Distance_Units'] == 'miles' ? 'selected="selected"' : ''; ?>>Miles</option>
			<option value="kilometres" <?php echo $this->options['Distance_Units'] == 'kilometres' ? 'selected="selected"' : ''; ?>>Kilometres</option>
		</select>

	<?php }

	//Select Country
	public function select_country_input() {

		global $countrycodes_array;

		?>

		<select name='aphs_FYN_options[countrycode]'>
			<option value=''><?php echo esc_attr( __( 'Select page' ) ); ?></option>
			<?php foreach ( $countrycodes_array as $countrycode=>$country ) : ?>
				<option value="<?php echo $countrycode; ?>" <?php echo $this->options['countrycode'] == $countrycode ? "selected='selected'" : "";?> ><?php echo $country; ?></option>
			<?php endforeach; ?>
		</select>

	<?php }

	//Page Structure
	public function page_layout() { ?>
		<div class="wrap">
			<?php screen_icon(); ?><h2>Location Finder Options</h2>
			<form action="options.php" method="post">
				<?php settings_fields('aphs_FYN_options'); ?>
				<?php do_settings_sections('_settings'); ?>
				<br />
				<button class="button button-primary">Save Options</button>
			</form>
		</div>
	<?php }

	//Validate Data
	public function validate($input){

		return $input;

	}

	public function __construct(){

		//Get Options
		$this->get_options();

		//Admin Menu
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );

		//Option Fields
		add_action('admin_init', array( &$this, 'admin_init' ) );


	}

}


?>