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

{extends file='page.tpl'} 
{block name='page_content'}

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
        <a href="{$link->getPageLink('contact',false)|escape:'htmlall':'UTF-8'}">{l s='Contact form' mod='swissbilling'}</a>
    </div>
    
    <p>
        {l s='If payment by invoice has been refused' mod='swissbilling'} :
        <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'htmlall':'UTF-8'}" class="link">
            <u>{l s='click here to choose another method of payment' mod='swissbilling'}</u>.
        </a>
    </p>
    
{/block}