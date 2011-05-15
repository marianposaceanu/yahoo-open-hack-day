var directionDisplay;
var directionsService = new google.maps.DirectionsService();
var map;
var OverLayMap ;

var printLoc = function( loc ) {
	if( loc.lat === undefined ) {
		loc = loc.geometry.location ;
	}
	return loc.lat+','+loc.lng ;
} ;

var get_photos = function(title, lat, lon, msg_id) {	  
  YUI().use('node', 'yql', function(Y) {
	var res = Y.one('#'+msg_id),
	url = '<a href="http://flickr.com/photos/{owner}/{id}"><img src="http://farm{farm}.static.flickr.com/{server}/{id}_{secret}_s.jpg"></a>';
	
	var q = Y.YQL('select * from flickr.photos.search where has_geo="true" and tags="'+title.replace(/ /g,",")+'" and (lat, lon) in ('+lat+', '+lon+') and radius=4 LIMIT 4', function(r) {
	  Y.each(r.query.results.photo, function(v) {
		$(".spinner").html("");
		res.append(Y.Lang.sub(url, v));
	  });
	});
  });
} ;

var printMarkers = function( landmarks, map, OverLayMap ) {
	var data_in = landmarks ;
		data_in.results = landmarks.results.splice( 0, 8 ) ;
		
	for( var i = 0; i < data_in.results.length; i++ ) {
		var name = data_in.results[i].name ,
			lat = data_in.results[i].geometry.location.lat ,
			lng = data_in.results[i].geometry.location.lng ,
			le_types = data_in.results[i].types;
     
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng( lat, lng ) , 
			map: map, 
			title: name,
			tags: le_types
		} ) ;
         
		(function( marker ){
			google.maps.event.addListener(marker, 'click', function() {
			  
				$("#message").html('<span>close</span>');
				var buff = marker.tags[marker.tags.length - 1];
				for (var i = marker.tags.length - 2; i >= 0; i--) {
					buff += ", " + le_types[i];
				};
				get_photos(marker.title, marker.position.Ia, marker.position.Ja, 'message' ) ;     
			  
				var markerOffset = OverLayMap.fromLatLngToDivPixel(marker.position);
				$("#message").append("<p>Name: <span style='font-weight:bolder;'>" + marker.title +"</span><br/>Type: "+ buff + "</p>").show().css({ top:markerOffset.y-268, left:markerOffset.x+35 });
				$("#message").append("<div class='spinner clear' align='center'><img src='images/spinner.gif' /></div>");
			});
		})(marker);
	}
} ;

var onGetRankedLocations = function( tour ){
	var results = tour.results.splice(0,6) ,
		_start = results.shift() ,
		_end = results.pop() , 
		start = _start.geometry.location,
		end = _end.geometry.location,
		waypoints = $.map( results, function( wp ){
			return {
				location: printLoc( wp ),
				stopover: false
			} ;
		}) ,
		mode = google.maps.TravelMode.DRIVING ,
		request = {
			origin: printLoc( start ),
			destination: printLoc( end ),
			waypoints: waypoints,
			optimizeWaypoints: true,
			travelMode: mode
		} ;
	
	tour.results.unshift( _start ) ;
	for(var k=0;k<results.length;k++){
	tour.results.unshift( results[k] ) ;
	}
	tour.results.unshift( _end ) ;
	
		
	directionsService.route(request, function(response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
			directionsDisplay.setDirections(response);
		}
	});
}

function initialize() {
  directionsDisplay = new google.maps.DirectionsRenderer({
		polylineOptions	 : {
			strokeColor: '#FD0000' ,
			strokeOpacity: '0.7'
		}
  });
  var center = new google.maps.LatLng( 51.7532410 , -0.3546090 ) ;
  var options = {
	zoom: 12,
	center: center,
	mapTypeId: google.maps.MapTypeId.ROADMAP
  } ;
  map = new google.maps.Map(document.getElementById('map'), options ) ;
  OverLayMap = new MyOverlay( { map: map } );
  directionsDisplay.setMap( map ) ;
  
  onGetRankedLocations( data_in ) ;
  printMarkers( data_in , map, OverLayMap ) ;
    
  // make types
  var maps = [];
  for(var i = place_types.length - 1; i >= 0; i--) {
    $("#places_types").append('<li><a href="'+place_types[i]+'">'+place_types[i]+'</a></li>');
  };
  
  // the types
  select_places_types = [];
  
  // places types select
  $("#places_types li a").click(function(event) {
    event.preventDefault();
    var buff = $(this).attr("href");
    if( !$(this).hasClass('selected') ) {
      $(this).addClass('selected');
      select_places_types.push(buff);
    } else {
      $(this).removeClass('selected');
      select_places_types = $.map(select_places_types, function(n, i) {
        return ( n != buff ) ? n : null;
      });
    }
  });
    
  // le close for mini-windows
  $("#message span, #message_dialog span").live('click',function(){
    $(this).parent().slideUp(800);
  });

  $("#message img").live('hover',function() {
    $(this).css('border','3px solid #CFCFCF');
  }, function() {
    $(this).css('border','none');
  });
  
  $("#message img").live('mouseover', function() {
    $(this).css('border','3px solid #CFCFCF');
    $(this).css({width:"55px",height:"55px" }, 200);
  });
  
  $("#message img").live('mouseout', function() {
    $(this).css({width:"61px",height:"61px"}, 200);
    $(this).css('border','none');
  });  
  
  $("#message").hover(function(){},function() {
    $(this).fadeOut();
  });
  
  // le tabs
  $("#tabs").tabs();  

}

$(document).ready(function() {
  initialize(); 
});