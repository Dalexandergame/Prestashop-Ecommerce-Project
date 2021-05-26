{if $lists}
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.5/leaflet.css" />
<link rel="stylesheet" href="{$base_dir|escape:'htmlall'}modules/suivicommandes/css/leaflet-routing-machine.css" />
<link rel="stylesheet" href="{$base_dir|escape:'htmlall'}modules/suivicommandes/css/index.css" />
 
<div id="map" class="map"></div>
 
<script src="http://cdn.leafletjs.com/leaflet-0.7.5/leaflet.js"></script>
<script src="{$base_dir|escape:'htmlall'}modules/suivicommandes/js/leaflet-routing-machine.js"></script>
<script src="{$base_dir|escape:'htmlall'}modules/suivicommandes/js/Control.Geocoder.js"></script>

<script>

var color = ['blue','red','green','yellow','orange','purple','coral','deeppink','grey','black','navy'];

var map = L.map('map');

var carrierinfos = [];

L.tileLayer('{literal}http://{s}.tile.osm.org/{z}/{x}/{y}.png'{/literal}, {
	attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

var i = 0;
var marker = [];
var pathcolor;
{foreach from=$lists key=carrier item=list}
    var waypoints =[];
    {foreach from=$list key=k item=data}
        waypoints.push(L.latLng({$data.lat}, {$data.long}));
        marker.push(['{$data.address}',{$data.lat},{$data.long}]);
    {/foreach}   
        
    if(typeof color[i] != 'undefined'){ pathcolor=color[i];}
    else { pathcolor = "blue"; }
    carrierinfos.push(['{$carrier}',pathcolor]);
    
        L.Routing.control({
        waypoints: waypoints,
        showAlternatives : false,
        language: 'fr',
        lineOptions: {
        styles: [{literal}{color: pathcolor, opacity: 1, weight: 5}{/literal}]
        }
        }).addTo(map);
        
        i = i+1;
        
{/foreach}

for (var i = 0; i < marker.length; i++) {
    L.marker([marker[i][1],marker[i][2]]).bindPopup(marker[i][0]).addTo(map);
}

{*$(window).bind("load", function() {
    for (var i = 0; i < carrierinfos.length; i++) {
        $("div.leaflet-right").find(".leaflet-routing-alternatives-container:eq("+i+") .leaflet-routing-alt").prepend( "<span class='carrier' style='color:"+carrierinfos[i][1]+"'>"+carrierinfos[i][0]+"</span>" );
    }
});*}
    
</script>


{else}
    <div class="alert alert-warning">{l s='Aucune donnée trouvée dans la base' mod='suivicommandes'}</div>   
{/if}