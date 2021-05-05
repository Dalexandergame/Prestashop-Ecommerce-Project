
<form id="form" method="post" action="index.php?controller=AdminStockSapinsVendusRestants&token={$token}" class="form-inline">
    <div class="form-group">
    {l s='Date début de ventes' mod='suivicommandes'} : 
    <input type="text" id="dateDebut" class="datepicker" name="dateDebut" class="form-control"/>
    
    {l s='Date prévue pour livraison' mod='suivicommandes'} : 
    <input type="text" id="dateFin" class="datepicker" name="dateFin" class="form-control"/>
    {l s='Entrepôt' mod='suivicommandes'} : 
    </div>
    
    <div class="form-group">
    <select name="warehouse_selected" id="warehouse_selected" class="form-control">
        {foreach from=$warehouses item=warehouse}
            <option value="{$warehouse.id}" {$warehouse.selected}> {$warehouse.name}</option>
        {/foreach}
    </select>
    </div>
    
    <input type="submit" class="btn btn-info" name="submit" value="{l s='OK' mod='suivicommandes'}" />

</form>
    
<br><br>
{if isset($listVendus)}
    <h1>Liste des produits vendus</h1>
    {$listVendus}
{/if}  

<br><br>
{if isset($listNonVendus)}
    <h1>Liste des produits non vendus</h1>
    {$listNonVendus}
{/if}  
<script type="text/javascript">
    
$(document).ready(function() {
    
    $(".datepicker").datepicker({
            prevText: '',
            nextText: '',
            currentText: "Now",
            dateFormat: 'yy-mm-dd'
    });
    {if $dateDebut}
        $("#dateDebut").datepicker("setDate", "{$dateDebut}"); 
    {/if}
    {if $dateFin}
        $("#dateFin").datepicker("setDate", "{$dateFin}"); 
    {/if}

});
</script>