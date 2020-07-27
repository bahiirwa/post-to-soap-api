<?php
/**
 * Plugin Name: Admin Ali Kibao Create Agent
 * Plugin URI: https://omukiguy.com
 * Author: Ali Kibao
 * Author URI: https://omukiguy.com
 * Description: Create New Agent to API Extdev
 * Version: 0.1.0
 * License: GPL2 or Later.
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: prefix-plugin-name
*/


// If this file is access directly, abort!
defined( 'ABSPATH' ) || die( 'Unauthorized Access' );

// Constants.
if ( ! defined( 'PROPERTY24_API_PLUGIN_DIR' ) ) {
	define( 'PROPERTY24_API_PLUGIN_DIR', dirname( __FILE__ ) );
}

add_action( 'plugins_loaded', 'property24_api_register_init' );

/**
 * Initial functionality when the plugins are loaded.
 *
 * @return void
 */
function property24_api_register_init() {
	require PROPERTY24_API_PLUGIN_DIR . '/includes/post-api-login-details.php';
	require PROPERTY24_API_PLUGIN_DIR . '/includes/ali-kibao-create-agent.php';
}

function agent_register_meta_boxes() {
    add_meta_box( 
        'meta-box-id', 
        __( 'Agent Meta Box', 'textdomain' ), 'wpdocs_my_display_callback', 
		'agent', 
		'side',
		'high'
    );
}
add_action( 'add_meta_boxes', 'agent_register_meta_boxes' );

function wpdocs_my_display_callback(){
    global $post;

    echo '<pre>';
    var_dump( get_post_meta( $post->ID ) );
	echo '</pre>';
}