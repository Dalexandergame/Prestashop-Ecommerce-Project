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

{if $status == 'ok'}
    <p>
        {l s='Your order' mod='swissbilling'} <span class="bold">{$shop_name|escape:'htmlall':'UTF-8'}</span> {l s='has been done.' mod='swissbilling'}<br/>
        {l s='Amount of your order' mod='swissbilling'} : <span class="price">{$total_to_pay|escape:'htmlall':'UTF-8'}</span><br/>
        <br />{l s='Your order will now be prepared for shipping.' mod='swissbilling'}
        <br/> {l s='You then get the bill separately transmitted by' mod='swissbilling'} <a href="http://www.swissbilling.ch" target="_blank">Swissbilling</a>.
    </p>
{else}
    <p class="warning">
        {l s='One problem seems to have occurred with your order. If you think that there has been a mistake thank you to contact the' mod='swissbilling'}&nbsp;
        <a href="{$link->getPageLink('contact-form.php', true)|escape:'htmlall':'UTF-8'}">{l s='customer support' mod='swissbilling'}</a>.
    </p>
{/if}
