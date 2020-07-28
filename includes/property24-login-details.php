<?php
/**
 * Register a custom menu page
 */
add_action( 'admin_menu', 'property24_register_woo_admin_menu' );

function property24_register_woo_admin_menu() {
    add_menu_page(
		__( 'Property24 Account Settings', 'textdomain' ),
		'Property24 Details', 
		'manage_options', 
		'property24-acccounts-settings.php',
        'property24_theme_display',
        'dashicons-testimonial',
		15.5
	); 
}

/**
 * Renders a simple page to display for the theme menu defined above.
 */
function property24_theme_display() {
?>
	<!-- Create a header in the default WordPress 'wrap' container -->
	<div class="wrap">
	
		<div id="icon-themes" class="icon32"></div>
		<h2><?php _e( 'Property24 Account Settings.', 'Property24-automations' ); ?></h2>
		
		<?php settings_errors(); ?>
		
		<form method="post" action="options.php">
			<?php
				echo '<p>Details to connect to Property24.</p>';
				echo '<hr>';		

				settings_fields( 'property24_notifications_settings' );
				do_settings_sections( 'property24_notifications_settings' );
				submit_button();
			?>
		</form>
		
	</div><!-- /.wrap -->
<?php
} // end property24_theme_display

/* ------------------------------------------------------------------------ *
 * Setting Registration
 * ------------------------------------------------------------------------ */ 
/**
 * Provides default values for the Input Options.
 */
function property24_theme_default_input_options() {
	
	$defaults = array(
		// 25%
		'property24_user_email'        => '',
		'property24_user_password'     => '',
		'property24_agency_id'         => '',
		'auction_property_taxonomy_id' => '162',
	);
	
	return apply_filters( 'property24_theme_default_input_options', $defaults );
	
} // end property24_theme_default_input_options

/**
 * Initializes the theme's display options page by registering the Sections,
 * Fields, and Settings.
 *
 * This function is registered with the 'admin_init' hook.
 */ 
function property24_theme_initialize_inputs() {

	if( false == get_option( 'property24_notifications_settings' ) ) {	
		add_option( 'property24_notifications_settings', apply_filters( 'property24_theme_default_input_options', property24_theme_default_input_options() ) );
	} // end if

	add_settings_section(
		'property24_account_settings',
		__( 'Account Settings', 'Property24-automations' ),
		'property24_tax_accounts_callback',
		'property24_notifications_settings'
	);
	
	register_setting(
		'property24_notifications_settings',
		'property24_notifications_settings',
		'property24_theme_validate_inputs'
	);

} // end property24_theme_initialize_inputs
add_action( 'admin_init', 'property24_theme_initialize_inputs' );

/* ------------------------------------------------------------------------ *
 * Section Callbacks
 * 
 * This function provides a simple description for the Input Examples page.
 *
 * It's called from the 'property24_theme_initialize_inputs_options' function by being passed as a parameter
 * in the add_settings_section function.
 */
function property24_account_settings_callback() {
	echo '';
} // end property24_general_options_callback

/* ------------------------------------------------------------------------ *
 * Field Callbacks
 * ------------------------------------------------------------------------ */ 

function property24_tax_accounts_callback() {
	
	$options = get_option( 'property24_notifications_settings' );

	$html = '<p><label for="property24_user_email">Email:<br>';
		$property24_user_email = ! empty( $options['property24_user_email'] ) ? $options['property24_user_email'] : '';
		$html .= '<input type="text" id="property24_user_email" name="property24_notifications_settings[property24_user_email]" value="' . $property24_user_email . '" /></p>';

	$html .= '<p><label for="property24_user_password">Password:<br>';
		$property24_user_password = ! empty( $options['property24_user_password'] ) ? $options['property24_user_password'] : '';
		$html .= '<input type="text" id="property24_user_password" name="property24_notifications_settings[property24_user_password]" value="' . $property24_user_password . '" /></p>';

	$html .= '<p><label for="property24_agency_id">Agency ID:<br>';
		$property24_agency_id = ! empty( $options['property24_agency_id'] ) ? $options['property24_agency_id'] : '';
		$html .= '<input type="text" id="property24_agency_id" name="property24_notifications_settings[property24_agency_id]" value="' . $property24_agency_id . '" /></p>';
	
	$html .= '<p><label for="auction_property_taxonomy_id">Agency ID:<br>';
		$auction_property_taxonomy_id = ! empty( $options['auction_property_taxonomy_id'] ) ? $options['auction_property_taxonomy_id'] : '';
		$html .= '<input type="text" id="auction_property_taxonomy_id" name="property24_notifications_settings[auction_property_taxonomy_id]" value="' . $property24_agency_id . '" /></p>';
	
	echo $html;

} // end property24_woo_order_notes_sms_callback

/* ------------------------------------------------------------------------ *
 * Setting Callbacks
 * ------------------------------------------------------------------------ */ 
function property24_theme_validate_inputs( $input ) {

	// Create our array for storing the validated options
	$output = array();
	
	// Loop through each of the incoming options
	foreach( $input as $key => $value ) {
		
		// Check to se if the current option has a value. If so, process it.
		if( isset( $input[$key] ) ) {
			// Strip all HTML and PHP tags and properly handle quoted strings
			$output[$key] = wp_strip_all_tags( stripslashes( $input[ $key ] ) );
		}
		
	} // end foreach
	
	// Return the array processing any additional functions filtered by this action
	return apply_filters( 'property24_theme_validate_inputs', $output, $input );

} // end property24_theme_validate_inputs
