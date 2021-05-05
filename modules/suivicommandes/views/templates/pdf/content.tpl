<style type="text/css">
.border{
    border-top-width: 1px;
    border-bottom-width: 1px;
    border-left-width: 1px;
    border-right-width: 1px;
}

.odd{
    background-color: #D4D4D4;
}

</style>
{$sum = 0}
{foreach key=key item=item from=$total}
    {$sum = $sum + $item}
{/foreach}

<h3>Nombre des produits {$sum}</h3>
{foreach key=key item=item from=$total}
    {$key}: {$item}<br/>
{/foreach}
<br>
<table class="border">
    <tr>
        <th border="1" width="3%">N°</th> 
        <th border="1" width="3%"></th> 
        <th border="1" width="7%">Client</th> 
        <th border="1" width="10%">Adresse</th>
        <th border="1" width="30%">Commande</th>
        <th border="1" width="40%">Message</th>
        <th border="1" width="10%">Tel</th>
    </tr>
  
    {foreach $infos as $info} 
        <tr class="{cycle values="odd,even"}">
            {if $isRetour eq '1'}
                <td border="1">{$info.position_retour}</td>
            {else}
                <td border="1">{$info.position}</td>
            {/if}
            <td border="1">{$info.type}</td>
            <td border="1">{$info.customer}</td>
            <td border="1">{$info.address}</td>
            <td border="1">{$info.commande}</td>  
            <td border="1">{$info.message}</td> 
            <td border="1">{$info.tel}</td> 
        </tr>
    {/foreach}
 
</table>

{*

<img src="http://maps.googleapis.com/maps/api/staticmap?
    autoscale=1
    &size=800x400
    &maptype=roadmap
    &format=png
    &visual_refresh=true
    {$markers}
    &key={$gkey}"/>
 *}
