{foreach from=$blocks item=data}
    
    <div class="well col-md-3 m-r-15 text-center">
        {*/***}
        {** Pulse*}
        {** By: Abdelhafid El kadiri*}
        {**/*}
        <h3 style="background-color: {$data.color}">{$data.carrier_name}
            <a href="index.php?controller=AdminSuiviCommandes&pdf&date={$dateLivraison}&idc={$data.id_carrier}&wh={$wh}&token={$token}" class="btn btn-default _blank pull-right" style="margin: 4px;padding: 2px 4px;">
                <i class="icon-file-text"></i>
            </a>
        </h3>
        <h2>{$data.ncmd}</h2>
        <h4>
            <a href="#" class="texte" data-type="text" data-pk="{$data.id_carrier}" >{$data.text}</a>
        </h4>
    </div>
{/foreach}  

<script type="text/javascript">
    
$(document).ready(function() {
    
    {if !isset($restricted) || !$restricted}
    $('.texte').editable({
        url: 'index.php?controller=AdminSuiviCommandes&action=updateTextBlockCarrier&ajax=true&date={$dateLivraison}&wh={$wh}&token={$token}'
    });
    {/if}
});
</script>