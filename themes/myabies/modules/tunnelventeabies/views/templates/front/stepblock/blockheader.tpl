<div class="col-md-12 block-header">
    <div class="container-header-tunnel">
        <a class="link-back3" href="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}"
           title="{l s='Home'}">
            <span>
                <img src="{$img_dir}arrow-left.png" alt="back" class="icon-back"/>
                {if $lang_iso == 'fr' }
                    <span class="back-text">{l s='Retour au site'}</span>
                {elseif $lang_iso == 'en'}
                <span class="back-text">{l s='Back to website'}</span>
                {elseif $lang_iso == 'de'}
                <span class="back-text">{l s='Zurück zur Website'}</span>
                {/if}
            </span>
        </a>
        {if $lang_iso == 'fr' }
            <span class="head-tunnel font-serif-title">{l s='Je commande mon sapin.'}</span>
        {elseif $lang_iso == 'en'}
            <span class="head-tunnel font-serif-title">{l s='I order my tree.'}</span>
        {elseif $lang_iso == 'de'}
            <span class="head-tunnel font-serif-title">{l s='Ich bestelle meinen Baum.'}</span>
        {/if}

    </div>

</div>