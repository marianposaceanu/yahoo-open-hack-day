<?php
	
	//require_once( 'mockup.php' ) ;

	// flush ranked locations
	function getRankedLocations() {
		global $mockup ;
		$rankedLocations = $mockup ; 
		print( json_encode( $rankedLocations ) ) ;
	}

	// proxy for directions api
	function getTour( $origin, $destination,$waypoints, $mode = 'driving' ) {
		$googleMapsApiUrl = 'http://maps.googleapis.com/maps/api/directions/json'.
							'?origin='.$origin.
							'&destination='.$destination.
							'&waypoints=optimize:true|'.$waypoints.
							'&mode='.$mode ;
		//die( $googleMapsApiUrl ) ;
		$stuff = file_get_contents( $googleMapsApiUrl ) ;
		print( json_encode( $stuff ) ) ;
	}


	$get = $_GET['get'] ;
	switch( $get ){
		case 'rankedLocations' : 
			getRankedLocations() ;
			break ;
		case 'tour' :
			$origin = $_GET['origin'] ;
			$destination = $_GET['destination'] ;
			$waypoints = json_decode( $_GET['waypoints'] ) ;
			$mode = $_GET['mode'] ;
			die( $origin . '-' . $waypoints . '-' . $destination ) ;
			getTour( $origin, $destination, $waypoints, $mode ) ;
			break ;
	}

