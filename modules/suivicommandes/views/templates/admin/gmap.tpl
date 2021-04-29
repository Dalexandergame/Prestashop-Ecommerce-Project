{if $lists}
    <link rel="stylesheet" href="/modules/suivicommandes/css/gmap.css"/>
    
<div id="map_canvas" ></div>
    <script type="text/javascript"
            src="https://maps.google.com/maps/api/js?key=AIzaSyCTZea67jn4YSPIGu0dNTHRyB1jnvo1Q00"></script>
<script type="text/javascript">
    var directionDisplay;
    var directionsService = new google.maps.DirectionsService();
    var map;

    function initialize() {
        directionsDisplay = new google.maps.DirectionsRenderer();

       var myOptions = {
            zoom: 6,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
        directionsDisplay.setMap(map);
        calcRoute();
    }

    function calcRoute() {
        
        myCoord =[];
        {foreach from=$lists key=carrier item=list}
            
            {foreach from=$list key=k item=data}
                myCoord.push( { location: new google.maps.LatLng({$data.lat}, {$data.long}) } );
            {/foreach} 
          
        {/foreach}  
       
       
        start = myCoord[0];
        end = myCoord[myCoord.length - 1];
        myCoord.shift();
        myCoord.pop();
            
        var request = {
            origin: start,
            destination: end,
            waypoints: myCoord,
            optimizeWaypoints: true,
            provideRouteAlternatives: false,
            travelMode: google.maps.DirectionsTravelMode.DRIVING
        };
        directionsService.route(request, function (response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(response);
            } else {
                alert("directions response " + status);
            }
        });
    };
    
    initialize();
</script>

{else}
    <div class="alert alert-warning">{l s='Aucune donnée trouvée dans la base' mod='suivicommandes'}</div>   
{/if}