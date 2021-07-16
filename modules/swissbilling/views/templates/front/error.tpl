{*
* NOTICE OF LICENSE
*
* Module for Prestashop
*
* 100% Swiss development
* @author Webbax <contact@webbax.ch>
* @copyright -
* @license   -
*}

{if $ps_version=='1.6'}
    
    {literal}
        <style> .breadcrumb {display:none;}</style>
    {/literal}
    
    <h1 class="page-heading">{l s='Error' mod='swissbilling'}</h1>
    
    {if !empty($msg_error)}
        <div class="alert alert-danger">
            {$msg_error|escape:'htmlall':'UTF-8'}
        </div>
    {/if}
    
    {if !empty($error)}
        <div class="alert alert-danger">
            {$error|escape:'htmlall':'UTF-8'}
        </div>
    {/if}

    <div class="alert alert-warning">
        {l s='In case of problems, thank you to contact us via the' mod='swissbilling'} : 
        <a href="{$link->getPageLink('contact-form.php',false)|escape:'htmlall':'UTF-8'}">{l s='Contact form' mod='swissbilling'}</a>
    </div>
    <div align="center">
        {l s='If payment by invoice has been refused' mod='swissbilling'} 
        <a href="{$base_dir|escape:'htmlall':'UTF-8'}" class="link">
            <u>{l s='click here to choose another method of payment' mod='swissbilling'}</u>.
        </a>
    </div>
    
{else}
    
    <h1>{l s='Error' mod='swissbilling'}</h1>
   {include file="$tpl_dir../../modules/swissbilling/views/templates/front/styles.tpl"}
    
    {if !empty($msg_error)}
        <div align="center">
            <img src="{$base_dir|escape:'htmlall':'UTF-8'}img/admin/warning.gif" style="vertical-align:bottom"/>&nbsp;{$msg_error|escape:'htmlall':'UTF-8'}<br/>
        </div>
    {/if}
    
    {if !empty($error)}
        <div class="error">
              {$error|escape:'htmlall':'UTF-8'}
        </div>
    {/if}

    <br/>
    <div align="center">{l s='If payment by invoice has been refused' mod='swissbilling'} 
        <a href="{$base_dir|escape:'htmlall':'UTF-8'}" class="link">
            {l s='click here to choose another method of payment' mod='swissbilling'}.
        </a>
    </div>
    <br/>
    <div class="warning">
        {l s='In case of problems, thank you to contact us via the' mod='swissbilling'}&nbsp;
        <a href="{$link->getPageLink('contact-form.php',false)|escape:'htmlall':'UTF-8'}">
            {l s='Contact form' mod='swissbilling'}
        </a>
    </div>

{/if}