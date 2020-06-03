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
/**
 * Register a custom menu page
 */
add_action( 'admin_menu', 'ali_kibao_create_agent' );

function ali_kibao_create_agent() {
	add_menu_page(
		__( 'Post Agent Settings', 'textdomain' ),
		'Post Agent',
		'manage_options',
		'ali-kibao-create-agent.php',
		'ali_kibao_create_agent_details',
		'dashicons-testimonial',
		16
	);
}

function ali_kibao_create_agent_details() {
    $agent_data = get_agents_to_add(); // Query the Agents CPT
    $xml_data = make_xml_data( $agent_data ); // Make an XML_data
    post_to_api_extdev( $xml_data ); // Run it to the API
}

function get_agents_to_add() {

    $agent_data = [];
    // The Query
    $args = array(  
        'post_type' => 'agent',
        'post_status' => 'publish',
        'posts_per_page' => 1,
    );

    $loop = new WP_Query( $args ); 
        
    while ( $loop->have_posts() ) {
        $loop->the_post();
        $agent_data[] = array( 
            'name'           => get_post_meta( get_the_ID(), 'agent_contact_details_agent_name', true ),
            'contact_number' => get_post_meta( get_the_ID(), 'agent_contact_details_agent_contact_number', true ),
            'email_address'  => get_post_meta( get_the_ID(), 'agent_contact_details_agent_email_address', true ),
            'profile_image'  => wp_get_attachment_url( get_post_meta( get_the_ID(), 'agent_contact_details_agent_profile_image', true ) ),
        );
    }

    wp_reset_postdata(); 

    return $agent_data;
}


function post_to_api_extdev( $xml_data ){

    $url = 'http://www.exdev.property24.com/services/P24ListingService40.asmx';
	
	$arguments = array(
        'method' => 'POST',
        'headers' => array(
            'content-type' => 'text/xml',
        ),
		'body' => $xml_data,
	);

    $response = wp_remote_post( $url, $arguments );
    
    echo '<pre>';
    var_dump( $response['body'] );
    echo '</pre>';

}

function make_xml_data( $agent_data ) {

    $xml_data = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Header>
        <CredentialsHeader xmlns="http://www.property24.com/Services/P24ListingService40">
            <EMail>exdev@michaeljames.co.za</EMail>
            <Password>Isol@tion3</Password>
            <UserGroupEmailId>1</UserGroupEmailId>
        </CredentialsHeader>
    </soap:Header>
    <soap:Body>
        <AddAgent xmlns="http://www.property24.com/Services/P24ListingService40">
        <agent>
            <ReceiveStatsMail>1</ReceiveStatsMail>
            <ReceiveGroupListingEmail>1</ReceiveGroupListingEmail>
            <Published>1</Published>
            <AgencyId>4689</AgencyId>
            <SourceReference>string</SourceReference>
            <MobileNumber>'. $agent_data[0]['contact_number'] . '</MobileNumber>
            <EmailAddress>'. $agent_data[0]['email_address'] . '</EmailAddress>
            <FaxNumber>string</FaxNumber>
            <WorkNumber>0415831100</WorkNumber>
            <Qualification>string</Qualification>
            <FidelityFundCertificationYear>string</FidelityFundCertificationYear>
            <FidelityFundCertificationNumber>string</FidelityFundCertificationNumber>
            <IDNumber>string</IDNumber>
            <IncomeTaxNumber>string</IncomeTaxNumber>
            <About>string</About>
            <IsBroker>1</IsBroker>
            <CountryId>1</CountryId>
            <Status>Active</Status>
            <IsPrivySealCertified>1</IsPrivySealCertified>
            <JobTitle>SalesAgent</JobTitle>
            <Firstname>'. $agent_data[0]['name'] . '</Firstname>
        </agent>
        </AddAgent>
    </soap:Body>
    </soap:Envelope>';

    return $xml_data;
}
