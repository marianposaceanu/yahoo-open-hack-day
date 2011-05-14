function initialize() {
  
  var maps = [];
  // intial map
  maps.push( new TravelMap(51.7532410,-0.3546090,data_in,'map','message') );
  
  // make types
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
  
  // generates custom map
  // from types
  $('#show_custom_map').click(function() {
    console.log(select_places_types);
    event.preventDefault();
    $("#dialog_map_inside").html("");
    $("#dialog_map").dialog({ height: 900, width: 480, position: [0,0] });
    if( select_places_types.length == 0 ) {
      $("#dialog_map_inside").html("<p>Please select something !</p>");
    } else {
      // gen. selected stuff
      $("#ss").remove();
      $("#dialog_map").append("<ul id='ss'>Selected types:</ul>");
      for (var i = select_places_types.length - 1; i >= 0; i--){
        $("#ss").append("<li>"+select_places_types[i]+"</li>");
      };
      // harta
      var map = maps.push( new TravelMap(51.7532410,-0.3546090,data_in,'dialog_map_inside','message_dialog') );          
    }
  });
  
  // le close for mini-windows
  $("#message span, #message_dialog span").live('click',function(){
    $(this).parent().fadeOut();
  });

  // le tabs
  $("#tabs").tabs();

}

$(document).ready(function() {
  initialize(); 
});