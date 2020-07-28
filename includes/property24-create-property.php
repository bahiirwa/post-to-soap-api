<?php
add_action( 'save_post', 'post_listed_property_24_website', 10, 2 );

function post_listed_property_24_website( $post_id, $post ) {
	
	// Makse sure it is a new Agent post.
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) return;

    if ( $post->post_type === 'multiple_auction' ) {

		// Check for auct_cat taxonomy.
		if ( taxonomy_exists( 'auct_cat' ) ) {

			$auction_property_taxonomy_id = ! empty( $options['auction_property_taxonomy_id'] ) ? $options['auction_property_taxonomy_id'] : '162';

			$post_array = $_POST['tax_input']['auct_cat'];
			
			// property-auctions has id 162.
			$tax_to_fetch = $auction_property_taxonomy_id;
			$found = false;
			$taxs = [];

			// Check if the property auction is selected.
			if( isset( $post_array ) && ! empty( $post_array ) ){
				foreach( $post_array as $array_id ) {
					$taxs[] = $array_id;
				}
				
				$found = in_array( $tax_to_fetch, $taxs );
			}

			// If the acution is property then do Property 24 stuff.
			if ( $found ) {
				// echo 'yes<br>';
				// echo $found;
				property24_create_property_details( $post_id, $post );
			}

		}

	}
	
}

function property24_create_property_details( $post_id, $post ) {

	$property_data = [];

	$property_data[] = array( 
		'name' => get_the_title( $post_id ),
	    'agent_id' => '69565', // Can be an object. Loop through IDs if many
	    'number' => $post_id,
	    'type' => 'Sale', // Get options available
	    'status' => 'NewListing', // Get options available
	    'price' => 230000.00, // Get options available
	    'visibility' => 'Public', // Get options available
	    'occupation_date' => '2020-05-30', // Get options available
	    'expiry_date' => '2020-05-30', // Get options available
	    'show_day_start' => '2020-05-30', // Get options available
	    'show_day_end' => '2020-05-30', // Get options available
	    'description' => 'description', // Get options available
	    'description_header' => 'Description header', // Get options available
	);

	// Make an XML_data
	$xml_data = make_property_xml_data( $property_data );

	// Run it to the API
	post_property_to_api_extdev( $xml_data );

	//die;

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
			<SaveListing xmlns="http://www.property24.com/Services/P24ListingService40">
				<listing>
					<AgencyId>' . $property24_agency_id . '</AgencyId>
					<ContactAgentIds>
						<int>' . $property_data['agent_id'] . '</int>
					</ContactAgentIds>
					<ListingNumber>' . $property_data['number'] . '</ListingNumber>
					<ListingType>' . $property_data['type'] . '</ListingType>
					<Status>' . $property_data['status'] . '</Status>
					<Price>' . $property_data['price'] . '</Price>
					<ListingVisibility>' . $property_data['visibility'] . '</ListingVisibility>
					<OccupationDate>' . $property_data['occupation_date'] . '</OccupationDate>
					<ExpiryDate>' . $property_data['expiry_date'] . '</ExpiryDate>
					<Description>' . $property_data['description'] . '</Description>
					<DescriptionHeader>' . $property_data['description_header'] . '</DescriptionHeader>
					<ShowDays>
						<ShowDay>
							<StartDate>' . $property_data['show_day_start'] . '</StartDate>
							<EndDate>' . $property_data['show_day_end'] . '</EndDate>
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
