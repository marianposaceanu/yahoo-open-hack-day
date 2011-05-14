function initialize() {
  
  var map_01 = new TravelMap(51.7532410,-0.3546090,data_in,'museums');
  var map_02= new TravelMap(51.7532410,-0.3546090,data_in,'map');
  
  //$("#tabs").tabs();
  //$("#accordion").accordion();
}

$(document).ready(function() {
  initialize(); 
});
