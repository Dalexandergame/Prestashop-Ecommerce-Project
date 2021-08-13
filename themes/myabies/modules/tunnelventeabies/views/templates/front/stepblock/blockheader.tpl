<div class="col-md-12 block-header">
    <div class="container-header-tunnel">
        <a class="link-back3" href="{$urls.base_url}"
           title="{l s='Home'}">
            <span>
                <img src="{$urls.img_url}arrow-left.png" alt="back" class="icon-back"/>
                {if $language.iso_code == 'fr' }
                    <span class="back-text">{l s='Retour au site'}</span>
                {elseif $language.iso_code == 'en'}
                <span class="back-text">{l s='Back to website'}</span>
                {elseif $language.iso_code == 'de'}
                <span class="back-text">{l s='Zurück zur Website'}</span>
                {/if}
            </span>
        </a>
        {if $language.iso_code == 'fr' }
            <span class="head-tunnel font-serif-title">{l s='Je commande mon sapin.'}</span>
        {elseif $language.iso_code == 'en'}
            <span class="head-tunnel font-serif-title">{l s='I order my tree.'}</span>
        {elseif $language.iso_code == 'de'}
            <span class="head-tunnel font-serif-title">{l s='Ich bestelle meinen Baum.'}</span>
        {/if}

    </div>

</div>