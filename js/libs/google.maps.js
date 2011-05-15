/*
*  lat / lnt
*  data_in
*  container_id
*/

	function get_photos(title, lat, lon, msg_id) {
	  
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
	}
function TravelMap(lat,lnt,data_in,container_id,msg_id) {
  
  var latlng = new google.maps.LatLng(lat, lnt);
  var options = {
   zoom: 12,
   center: latlng,
   mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  
  var map = new google.maps.Map(document.getElementById(container_id), options);
  this.map = map;
  var OverLayMap = new MyOverlay( { map: this.map } );  
  
  for( var i = 0; i < data_in.results.length; i++ ) {
    
    var name, lat, lng, marker;

	
    name = data_in.results[i].name;
    lat = data_in.results[i].geometry.location.lat;
    lng = data_in.results[i].geometry.location.lng;
    le_types = data_in.results[i].types;
     
    this.marker = new google.maps.Marker({
        position: new google.maps.LatLng(lat, lng), 
        map: map, 
        title: name,
        tags: le_types
    });
            
    google.maps.event.addListener(this.marker, 'click', function() {
      
      $("#"+msg_id).html('<span>close</span>');
      var buff = this.tags[this.tags.length - 1];
      for (var i = this.tags.length - 2; i >= 0; i--) {
        buff += ", " + le_types[i];
      };
		get_photos(this.title, this.position.Ia, this.position.Ja, msg_id);     
      
      //this.map.panTo(this.position);
      var markerOffset = OverLayMap.fromLatLngToDivPixel(this.position);
      $("#"+msg_id).append("<p>Name: <span style='font-weight:bolder;'>" + this.title +"</span><br/>Type: "+ buff + "</p>").show().css({ top:markerOffset.y, left:markerOffset.x });
      $("#"+msg_id).append("<div class='spinner clear' align='center'><img src='images/spinner.gif' /></div>");
      //this.map.panTo(this.position);
      //$("#"+msg_id).hide(); 
      //var marker = this;
      //console.log("test");
      ////var moveEnd = google.maps.event.addListener(this.map,"moveend", function(){ 
      //  console.log("test");
      //  console.log(marker);
      //  var markerOffset = OverLayMap.fromLatLngToDivPixel(this.map.getCenter());//marker.position); 
      //  $("#"+msg_id).fadeIn().append("<p>Nume: " + this.title + buff + "</p>").css({ top:markerOffset.y, left:markerOffset.x }); 
      ////  google.maps.event.removeListener(moveEnd); 
      ////});
      
    });

  }  
  
}