function initialize() {
  
  var paths = [];
  
  var latlng = new google.maps.LatLng(51.7532410, -0.3546090);
  var options = {
   zoom: 12,
   center: latlng,
   mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  var map = new google.maps.Map(document.getElementById("map"), options);
  var OverLayMap = new MyOverlay( { map: map } );  
  
  for( var i = 0; i < data_in.results.length; i++ ) {
    
    var name, lat, lng, marker;
  
    name = data_in.results[i].name;
    lat = data_in.results[i].geometry.location.lat;
    lng = data_in.results[i].geometry.location.lng;
    le_types = data_in.results[i].types;
    
    // paths
    paths.push(new google.maps.LatLng(lat,lng));
    
    marker = new google.maps.Marker({
        position: new google.maps.LatLng(lat, lng), 
        map: map, 
        title: name,
        tags: le_types
    }); 
    
    
    google.maps.event.addListener(marker, 'click', function() {
      
      $("#message").html('');
      
      var buff = "";
      for (var i = this.tags.length - 1; i >= 0; i--) {
        buff = " " + buff + " " + le_types[i];
      };
      
      YUI().use('node', 'yql', function(Y) {
        var res = Y.one('#message'),
        url = '<a href="http://flickr.com/photos/{owner}/{id}"><img src="http://farm{farm}.static.flickr.com/{server}/{id}_{secret}_t.jpg"></a>';
        var q = Y.YQL('select * from flickr.photos.search(5) where has_geo="true" and (tags="%'+le_types[0]+'%") and (lat, lon) in (select centroid.latitude, centroid.longitude from geo.places where text="London")', function(r) {
          Y.each(r.query.results.photo, function(v) {
            res.append(Y.Lang.sub(url, v));
          });
        });
      });      
      
      var markerOffset = OverLayMap.fromLatLngToDivPixel(this.position);
      $("#message").append("Nume: " + this.title + buff).show().css({ top:markerOffset.y, left:markerOffset.x });
    });

  }
  /*
  var paths = [
      new google.maps.LatLng(37.772323, -122.214897),
      new google.maps.LatLng(21.291982, -157.821856),
      new google.maps.LatLng(-18.142599, 178.431),
      new google.maps.LatLng(-27.46758, 153.027892)
    ];
    */

  
  var flightPath = new google.maps.Polyline({
      path: paths,
      strokeColor: "#FF0000",
      strokeOpacity: 1.0,
      strokeWeight: 2
  });
  
  flightPath.setMap(map);

}

$(document).ready(function() {
  initialize();
});
