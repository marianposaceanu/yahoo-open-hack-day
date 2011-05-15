	var backend = 'http://tg.code5.ro/api/xhr.php' ; 
	var proxy = 'http://tg.code5.ro/api/proxy.php' ;
	var fetchRankedLocations = function( callback ) {
		$.ajax({
			url : backend+'?get=rankedLocations' ,
			dataType : 'json' ,
			type : 'post' ,
			success : function( waypoints ) {
				callback( waypoints ) ;
			}
		}) ;
	} ;
	var printLoc = function( loc ) {
		loc = loc.geometry.location ;
		return loc.lat+','+loc.lng ;
	} ;
	var buildGuide = function( rankedLocations, origin, destination, mode ) {
		origin = origin.lat+','+origin.lng ;
		//printLoc( origin || rankedLocations.shift() ) ;
		var tmp = rankedLocations.pop() ;
		destination = printLoc( tmp ) ;
		mode = mode || 'walk' ;
		var sensor = 'false' ;
		var gpsPositions = $.map( rankedLocations, function( loc ){
			return printLoc( loc ) ;
		}) ;
		var urlWaypoints = gpsPositions.splice(0,6).join('|');
		var googleDirectionsUrl =  'maps/api/directions/json'+
								   '?origin='+encodeURIComponent( origin )+
								   '&destination='+encodeURIComponent( destination )+
								   '&waypoints=optimize:true|'+( urlWaypoints )+
								   '&mode='+encodeURIComponent( mode )+
								   '&sensor='+sensor ;
		var proxyUrl = proxy+'?yws_path='+encodeURIComponent( googleDirectionsUrl ) ;
		$.ajax({
			url : proxyUrl ,
			dataType : 'json' ,
			type : 'post' ,
			success : onGetRankedLocations
		}) ;
	} ;
	/*
	var onGetRankedLocations = function( whatever ){
		console.log( whatever ) ;
	} ;
	*/

	//fetchRankedLocations( buildGuide ) ;

