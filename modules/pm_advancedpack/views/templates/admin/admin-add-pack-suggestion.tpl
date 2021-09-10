{if (version_compare($ps_version, '1.6.0.0', '>='))}
    <div class="alert alert-info fade in">
{else}
    <div class="info fade in">
{/if}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <p>{l s='Do you want to add a new pack ?' mod='pm_advancedpack'}</p>
        <p><a id="add-pack-hint" data-header-label="{$addNewPackLabel|escape:'html':'UTF-8'}" class="alert-link" href="{$adminPacksLink}">{l s='Please go to Catalog > Packs, or click here' mod='pm_advancedpack'}</a></p>
</div>
