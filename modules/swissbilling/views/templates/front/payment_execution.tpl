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
    {capture name=path}{l s='Payment by invoice' mod='swissbilling'}{/capture}
    {include file="$tpl_dir./breadcrumb.tpl"}

    <div class="row">
        <div id="center_column" class="center_column col-xs-12 col-sm-12">
            
            <h1 class="page-heading">{l s='Order Summary' mod='swissbilling'}</h1>
            <script type="text/javascript">
                {literal}
                function goBack()
                  {
                  window.history.back()
                  }
                {/literal}
            </script>

            {assign var='current_step' value='payment'}
            {include file="$tpl_dir./order-steps.tpl"}

            <div class="box"> 
                {if $nbProducts <= 0}
                    <p class="warning">{l s='Your cart is empty.' mod='swissbilling'}</p>
                {else}         
                    <h3 class="page-subheading">{l s='Payment by invoice' mod='swissbilling'}</h3>
                    <form action="{$this_path_ssl|escape:'htmlall':'UTF-8'}validation.php" method="post" data-ajax="false">
                    <p>
                       {l s='You have chosen to pay by invoice.' mod='swissbilling'}<br/>
                       {l s='The total amount of your order is' mod='swissbilling'} <span id="amount" class="price">{displayPrice price=$total}</span> {if $use_taxes == 1}{l s='(tax incl.)' mod='swissbilling'}{/if}
                    </p>
                    <br/>
                    
                    {* html autorisé *}
                    {$info_prepmt}  

                    <p id="cart_navigation" class="cart_navigation clearfix">
                        <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'htmlall':'UTF-8'}" class="button-exclusive btn btn-default"><i class="icon-chevron-left"></i>{l s='Other payment methods' mod='swissbilling'}</a>
                        <button type="submit" class="button btn btn-default button-medium"> 
                            <span>{l s='I confirm my order' mod='swissbilling'} <i class="icon-chevron-right right"></i></span>
                        </button>
                    </p>
                    </form>
                {/if}
            </div>
            
        </div>
    </div>

{else}
    
    {capture name=path}{l s='Payment by invoice' mod='swissbilling'}{/capture}
    {include file="$tpl_dir./breadcrumb.tpl"}

    <h2>{l s='Order Summary' mod='swissbilling'}</h2>

    <script type="text/javascript">
        {literal}
        function goBack()
          {
          window.history.back()
          }
        {/literal}
    </script>

    {include file="$tpl_dir../../modules/swissbilling/views/templates/front/styles.tpl"}
    {assign var='current_step' value='payment'}
    {include file="$tpl_dir./order-steps.tpl"}

    {if $nbProducts <= 0}
            <p class="warning">{l s='Your cart is empty.' mod='swissbilling'}</p>
    {else}

    <h3>{l s='Payment by invoice' mod='swissbilling'}</h3>
    <form action="{$this_path_ssl|escape:'htmlall':'UTF-8'}validation.php" method="post" data-ajax="false">
    <p>
            <img src="{$this_path|escape:'htmlall':'UTF-8'}views/img/swissbilling.jpg" alt="Swissbilling" style="float:left; margin: 0px 10px 5px 0px;" />
            <br/>{l s='You have chosen to pay by invoice.' mod='swissbilling'}<br/>
    </p>
    <br/>
    <p style="margin-top:20px;">
            {l s='Here is a summary of your order' mod='swissbilling'} : <br/>
            <br/>
            - {l s='The total amount of your order is' mod='swissbilling'}
            <span id="amount" class="price">{displayPrice price=$total}</span>
            {if $use_taxes == 1}
            {l s='(tax incl.)' mod='swissbilling'}
        {/if}
    </p>
    <input type="hidden" name="currency_payement" value="{$currencies.0.id_currency|escape:'htmlall':'UTF-8'}" />

    {$info_prepmt}

    <p class="cart_navigation">
            <a href="{$link->getPageLink('order.php', true)|escape:'htmlall':'UTF-8'}?step=3" class="button_back button_large hideOnSubmit">{l s='Other payment methods' mod='swissbilling'}</a>
            <input type="submit" name="submit" value="{l s='I confirm my order' mod='swissbilling'}" class="exclusive_large hideOnSubmit" />
    </p>
    </form>
    {/if}
    
{/if}
