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
    {include file="$tpl_dir../../modules/swissbilling/views/templates/front/styles_1.6.tpl"}
    <div class="row">
        <div class="col-xs-12">
            <p class="payment_module">
                <a href="{$link->getModuleLink('swissbilling','payment')|escape:'htmlall':'UTF-8'}" class="swissbilling" title="{l s='Pay by invoice' mod='swissbilling'}">
                    {l s='Payment by invoice' mod='swissbilling'} <span>{l s='(by Swissbilling)' mod='swissbilling'}</span>
                </a>
            </p>
        </div>
    </div>
{else}
    <p class="payment_module">
        <a href="{$link->getModuleLink('swissbilling','payment')|escape:'htmlall':'UTF-8'}" title="{l s='Pay by invoice' mod='swissbilling'}">
                <img src="{$this_path|escape:'htmlall':'UTF-8'}views/img/swissbilling.jpg" alt="{l s='Pay by invoice' mod='swissbilling'}" />
                {l s='Payment by invoice' mod='swissbilling'} {l s='(by Swissbilling)' mod='swissbilling'}
        </a>
    </p>
{/if}