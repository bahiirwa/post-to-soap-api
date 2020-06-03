<?php
/**
 * Plugin Name: Admin Ali Kibao Create Property
 * Plugin URI: https://omukiguy.com
 * Author: Ali Kibao
 * Author URI: https://omukiguy.com
 * Description: Create New property to API Extdev
 * Version: 0.1.0
 * License: GPL2 or Later.
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: prefix-plugin-name
*/
/**
 * Register a custom menu page
 */
add_action( 'admin_menu', 'ali_kibao_create_property' );

function ali_kibao_create_property() {
	add_menu_page(
		__( 'Post Property Settings', 'textdomain' ),
		'Post Property',
		'manage_options',
		'ali-kibao-create-property.php',
		'ali_kibao_create_property_details',
		'dashicons-testimonial',
		16
	);
}

function ali_kibao_create_property_details() {

    // Query the propertys CPT
    $property_data = get_properties_to_add();

    // Make an XML_data
    $xml_data = make_property_xml_data( $property_data );

    // Run it to the API
    post_property_to_api_extdev( $xml_data );

}

function get_properties_to_add() {

    $property_data = [];
    // The Query
    $args = array(  
        'post_type' => 'property',
        'post_status' => 'publish',
        'posts_per_page' => 1,
    );

    $loop = new WP_Query( $args ); 
        
    while ( $loop->have_posts() ) {
        $loop->the_post();
        $property_data[] = $loop;
        // $property_data[] = array( 
        //     'name' => get_post_meta( get_the_ID(), 'property_contact_details_property_name', true ),
        //     'contact_number' => get_post_meta( get_the_ID(), 'property_contact_details_property_contact_number', true ),
        //     'email_address'  => get_post_meta( get_the_ID(), 'property_contact_details_property_email_address', true ),
        //     'profile_image'  => wp_get_attachment_url( get_post_meta( get_the_ID(), 'property_contact_details_property_profile_image', true ) ),
        // );
    }

    wp_reset_postdata(); 

    var_dump( $property_data );
    die;

    return $property_data;

}


function post_property_to_api_extdev( $xml_data ){

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

function make_property_xml_data( $property_data ) {

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
        <SaveListing xmlns="http://www.property24.com/Services/P24ListingService40">
          <listing>
            <AgencyId>4689</AgencyId>
            <ContactAgentIds>
              <int>69565</int>
            </ContactAgentIds>
            <ListingNumber>34</ListingNumber>
            <ListingType>Sale</ListingType>
            <Status>NewListing</Status>
            <Price>230000.00</Price>
            <ListingVisibility>Public</ListingVisibility>
            <OccupationDate>2020-05-30</OccupationDate>
            <ExpiryDate>2020-06-29</ExpiryDate>
            <Description>descritption</Description>
            <DescriptionHeader>Description header</DescriptionHeader>
            <ShowDays>
              <ShowDay>
                <StartDate>2020-05-30</StartDate>
                <EndDate>2020-06-29</EndDate>
              </ShowDay>
              <ShowDay>
                <StartDate>2020-05-30</StartDate>
                <EndDate>2020-06-01</EndDate>
              </ShowDay>
            </ShowDays>
            <PropertyInfo>
              <ShowOnMap>1</ShowOnMap>
              <ShowAddress>1</ShowAddress>
              <Age>3</Age>
              <SuburbId>1</SuburbId>
              <MunicipalRatesAndTaxes>
                <Amount>23000.00</Amount>
                <Unit>PerSquareMetre</Unit>
              </MunicipalRatesAndTaxes>
              <MonthlyLevy>
                <Amount>23000.00</Amount>
                <Unit>PerSquareMetre</Unit>
              </MonthlyLevy>
              <StreetNumber>100</StreetNumber>
              <StreetName>Heugh Road</StreetName>
              <StandNumber>4</StandNumber>
              <SourceReference>website</SourceReference>
              <GeographicLocation>
                <Longitude>25.601046</Longitude>
                <Latitude>-33.979551</Latitude>
              </GeographicLocation>
              <Erf>
                <Size>460.0</Size>
                <AreaUnit>SquareMetres</AreaUnit>
              </Erf>
              <FloorArea>
                <Size>460.0</Size>
                <AreaUnit>SquareMetres</AreaUnit>
              </FloorArea>
              <PropertyTypeId>4</PropertyTypeId>
              <OwnerType>Individual</OwnerType>
              <SpecialLevy>230</SpecialLevy>
              <FloorNumber>3</FloorNumber>
              <Coverage>255</Coverage>
              <PricePerParkingBay>300.00</PricePerParkingBay>
              <ZoneType>Rural</ZoneType>
            </PropertyInfo>
            <PropertyFeatures>
              <Bedrooms>2.00</Bedrooms>
              <Bathrooms>
                <Bathrooms>3.000</Bathrooms>
                <Description>Description</Description>
                <CleaningService>1</CleaningService>
                <UnisexBathrooms>1</UnisexBathrooms>
                <CommunalBathrooms>1</CommunalBathrooms>
                <ExecutiveBathrooms>1</ExecutiveBathrooms>
                <InUnitBathrooms>1</InUnitBathrooms>
              </Bathrooms>
              <Garages>3.000</Garages>
              <DomesticBathrooms>3</DomesticBathrooms>
              <HeightRestrictions>3</HeightRestrictions>
              <ReceptionRooms>2</ReceptionRooms>
              <Studies>1</Studies>
              <Kitchens>
                <Kitchens>1</Kitchens>
                <Description>Description</Description>
                <Dishwasher>1</Dishwasher>
                <CleaningService>1</CleaningService>
                <Sink>1</Sink>
                <CoffeeMachine>1</CoffeeMachine>
              </Kitchens>
              <IsWheelchairAccessible>1</IsWheelchairAccessible>
              <HasGenerator>1</HasGenerator>
              <HasBackupWater>1</HasBackupWater>
            </PropertyFeatures>
            <Tags>
                  <Tag>SingleStorey</Tag>
            </Tags>
          </listing>
        </SaveListing>
      </soap:Body>
    </soap:Envelope>';

    return $xml_data;
}
