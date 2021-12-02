
{if $lists}
    <link rel="stylesheet" href="/modules/suivicommandes/css/gmap.css"/>
    <script type="text/javascript" src="//maps.google.com/maps/api/js?key={$gkey}"></script>
     
 <div id="info"></div>
<div id="map_canvas"></div>
 
<script>

var directionsDisplay = [];
var directionsService = [];
var map = null;
var bounds = new google.maps.LatLngBounds();
var infowindow = new google.maps.InfoWindow(
  { 
    size: new google.maps.Size(150,50)
  });
  
var color = ['blue','red','green','orange','purple','coral','deeppink','grey','black','navy'];
var pathcolor;

function init() {
    var mapOptions = {
        // center: locations[0],
        zoom: 10,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
     
    var i = 0;
    {foreach from=$lists key=carrier item=list}
        stops = [];
        {foreach from=$list key=k item=data}
            stops.push( ' {$data.lat} , {$data.long}' );
        {/foreach} 
            
        if(typeof color[i] != 'undefined'){ pathcolor=color[i];}
        else { pathcolor = "blue"; }
        
        calcRoute(stops,pathcolor);
        i = i+1;
    {/foreach} 
        
     createMarkers();   
}

function calcRoute(f,pathcolor) {
    var input_msg = f;
    var locations = new Array();
    //alert(f);
    for (var i = 0; i < input_msg.length; i++) {
        var tmp_lat_lng = input_msg[i].split(",");
        locations.push(new google.maps.LatLng(parseFloat(tmp_lat_lng[0]), parseFloat(tmp_lat_lng[1])));
        bounds.extend(locations[locations.length - 1]);
    }

    map.fitBounds(bounds);

    i = locations.length;
    var index = 0;

    while (i != 0) {

        if (i < 3) {
            var tmp_locations = new Array();
            for (var j = index; j < locations.length; j++) {
                tmp_locations.push(locations[j]);
            }
            drawRouteMap(tmp_locations,pathcolor);
            i = 0;
            index = locations.length;
        }

        if (i >= 3 && i <= 10) {
            var tmp_locations = new Array();
            for (var j = index; j < locations.length; j++) {
                tmp_locations.push(locations[j]);
            }
            drawRouteMap(tmp_locations,pathcolor);
            i = 0;
            index = locations.length;
        }

        if (i >= 10) {
            var tmp_locations = new Array();
            for (var j = index; j < index + 10; j++) {
                tmp_locations.push(locations[j]);
            }
            drawRouteMap(tmp_locations,pathcolor);
            i = i - 9;
            index = index + 9;
        }
    }


}
j = 0;

function drawRouteMap(locations,pathcolor) {
    j++;
    var start, end;
    var waypts = [];

    for (var k = 0; k < locations.length; k++) {
        if (k >= 1 && k <= locations.length - 2) {
            waypts.push({
                location: locations[k],
                stopover: true
            });
        }
        if (k == 0) { start = locations[k]; }

        if (k == locations.length - 1) { end = locations[k]; }

    }
    var request = {
        origin: start,
        destination: end,
        waypoints: waypts,
        provideRouteAlternatives: false,
        travelMode: google.maps.TravelMode.DRIVING
    };

    directionsService.push(new google.maps.DirectionsService());
    var instance = directionsService.length - 1;
    directionsDisplay.push(new google.maps.DirectionsRenderer({
        preserveViewport: true,
        suppressMarkers: true,
        polylineOptions: {
            strokeColor: pathcolor
        }
    }));
    // var instance = directionsDisplay.length - 1;
    //  directionsDisplay[instance].setMap(map);
    directionsService[instance].route(request, function (response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            // alert(status);
            if (directionsDisplay && directionsDisplay[instance]) {
                directionsDisplay[instance].setMap(map);
                directionsDisplay[instance].setDirections(response);
            } else {
                document.getElementById('info').innerHTML += "instance=" + instance + " doesn't exist<br>";
            }
        } else {
            document.getElementsById('info').innerHTML += "instance=" + instance + " status=" + status + "<br>";
        }
    });
    // alert(instance);

}

function createMarkers() {
    
    var point;
    var marker=[];
    
    {foreach from=$lists key=carrier item=list}
            
        {foreach from=$list key=k item=data}
            point = { lat: {$data.lat}, lng: {$data.long} } ;
            
            marker[{$k}] = new google.maps.Marker({
                position: point,
                map: map,
                icon : "//chart.apis.google.com/chart?chst=d_map_pin_letter&chld={$data.marker}|F75C54|000000"
            });
          
        {/foreach} 
    {/foreach} 
}



google.maps.event.addDomListener(window, 'load', init);
    </script>
{else}
    <div class="alert alert-warning">{l s='Aucune donnée trouvée dans la base , Vérifiez que les adresses sont correctes.' mod='suivicommandes'}</div>   
{/if}