<?php

add_action( 'save_post', 'pms_post_published_notification', 10, 2 );

function pms_post_published_notification( $post_id, $post ) {
    
    // Makse sure it is a new Agent post.
    if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) return;
    
    if( 'agent' === $post->post_type ) {

        $agent_data = [];
   
        $agent_data[] = array(
            'id'             => $post_id,
            'first_name'     => get_post_meta( $post_id, 'agent_contact_details_agent_first_name', true ),
            'last_name'      => get_post_meta( $post_id, 'agent_contact_details_agent_last_name', true ),
            'contact_number' => get_post_meta( $post_id, 'agent_contact_details_agent_contact_number', true ),
            'email_address'  => get_post_meta( $post_id, 'agent_contact_details_agent_email_address', true ),
            'profile_image'  => wp_get_attachment_url( get_post_meta( $post_id, 'agent_contact_details_agent_profile_image', true ) ),
        );

        // Make an XML_data object
        $xml_data = make_xml_data( $agent_data ); 

        // Run it to the API
        $response = post_to_api_extdev( $xml_data );

        // Filter the string from XML.
        $pattern = "#<\s*?AddAgentResult\b[^>]*>(.*?)</AddAgentResult\b[^>]*>#s";
        preg_match( $pattern, (string)$response, $matches );

        $new_agent_id = $matches[1];

        // Make sure string is an ID.
        if ( intval( $new_agent_id ) ) {
            update_post_meta( $post_id, 'agent_contact_details_agent_property24_id', $new_agent_id );
            add_action( 'admin_notices', function() {
                ?>
                <div class="updated notice is-dismissible">
                    <p>Agent successfully added to Property 24</p>
                </div>
                <?php 
            });
        }

    }
}

/**
 * POST Data to the API.
 *
 * @param object $xml_data XML required Data.
 * @return string $response Body of the response.
 */
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

    if ( 200 !== $response['response']['code'] && 'OK' !== $response['response']['message'] ) {
        return;
    }

    return $response['body'];

}

/**
 * Make XML object to pass to the API.
 *
 * @param object $agent_data Variables for the XML object.
 * @return object $xml_data XML object to pass to the API.
 */
function make_xml_data( $agent_data ) {

    $options = get_option( 'property24_notifications_settings' );

    $property24_user_email    = ! empty( $options['property24_user_email'] ) ? $options['property24_user_email'] : '';
    $property24_user_password = ! empty( $options['property24_user_password'] ) ? $options['property24_user_password'] : '';
    $property24_agency_id     = ! empty( $options['property24_agency_id'] ) ? $options['property24_agency_id'] : '';

    $xml_data = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Header>
        <CredentialsHeader xmlns="http://www.property24.com/Services/P24ListingService40">
            <EMail>' . $property24_user_email . '</EMail>
            <Password>' . $property24_user_password . '</Password>
            <UserGroupEmailId>1</UserGroupEmailId>
        </CredentialsHeader>
    </soap:Header>
    <soap:Body>
        <AddAgent xmlns="http://www.property24.com/Services/P24ListingService40">
        <agent>
            <ReceiveStatsMail>1</ReceiveStatsMail>
            <ReceiveGroupListingEmail>1</ReceiveGroupListingEmail>
            <Published>1</Published>
            <AgencyId>' . $property24_agency_id . '</AgencyId>
            <SourceReference>string</SourceReference>
            <MobileNumber>'. $agent_data[0]['contact_number'] . '</MobileNumber>
            <EmailAddress>'. $agent_data[0]['email_address'] . '</EmailAddress>
            <FaxNumber>string</FaxNumber>
            <WorkNumber>'. $agent_data[0]['contact_number'] . '</WorkNumber>
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
            <Firstname>'. $agent_data[0]['first_name'] . '</Firstname>
        </agent>
        </AddAgent>
    </soap:Body>
    </soap:Envelope>';

    return $xml_data;
}
