function initialize() {
  
  var maps = [];
  // intial map
  maps.push( new TravelMap(51.7532410,-0.3546090,data_in,'map','message') );
  
  // make types
  for (var i = place_types.length - 1; i >= 0; i--){
    $("#places_types").append('<li><a href="'+place_types[i]+'">'+place_types[i]+'</a></li>');
  };
  
  // places types select
  $("#places_types li a").click(function(event) {
    event.preventDefault();
    $("#dialog_map_inside").html("");
    $("#dialog_map").dialog({ height: 900, width: 480, position: [0,0] });
    var map = maps.push( new TravelMap(51.7532410,-0.3546090,data_in,'dialog_map_inside','message_dialog') );
  });

  // le tabs
  $("#tabs").tabs();

}

$(document).ready(function() {
  initialize(); 
});


  
  //$('#tabs').bind('tabsselect', function(event, ui) {
  //  for (var i = maps.length - 1; i >= 0; i--){
  //    google.maps.event.trigger(maps[i].map, 'resize');
  //    maps[i].map.setZoom( maps[i].map.getZoom() );
  //  };
  //});  
  //$("#accordion").accordion();
