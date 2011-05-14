window.log = function(){
  log.history = log.history || [];  
  log.history.push(arguments);
  arguments.callee = arguments.callee.caller;  
  if(this.console) console.log( Array.prototype.slice.call(arguments) );
};
(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();)b[a]=b[a]||c})(window.console=window.console||{});


// my function

function MyOverlay(options) {
    this.setValues(options);
    var div = this.div_= document.createElement('div');
    div.className = "overlay";
};

// MyOverlay is derived from google.maps.OverlayView
MyOverlay.prototype = new google.maps.OverlayView;

MyOverlay.prototype.onAdd = function() {

    var pane = this.getPanes().overlayLayer;
    pane.appendChild(this.div_);

}

MyOverlay.prototype.onRemove = function() {
    this.div_.parentNode.removeChild(this.div_);
}

MyOverlay.prototype.fromLatLngToDivPixel = function(where_point) {
  var projection = this.getProjection();
  return projection.fromLatLngToDivPixel(where_point);
}

MyOverlay.prototype.draw = function() {
    //var projection = this.getProjection();
    //var position = projection.fromLatLngToDivPixel(this.getMap().getCenter());

    //var div = this.div_;
    //div.style.left = position.x + 'px';
    //div.style.top = position.y + 'px';
    //div.style.display = 'block';
};