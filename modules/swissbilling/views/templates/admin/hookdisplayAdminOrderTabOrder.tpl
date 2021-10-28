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

<style>
    {literal}
        #panel_swissbilling{
            margin-bottom:0px;
            z-index:1000;
            font-weight:bold;
            text-align:center;
        } 
        #panel_swissbilling .logo-swissbilling{width:250px;}
        #panel_swissbilling #block_swissbilling{
            text-align:center;
            padding:0px;
            display:inline-block;
        }
        #panel_swissbilling #block_swissbilling form{
            display:inline-block;
        }
        #panel_swissbilling #panel_swissbilling.valid:hover{
            background-color:#eb1d2d;
        }    
        #panel_swissbilling #block_swissbilling img{
            vertical-align:middle;
        }
    {/literal}
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            
            <div class="panel-heading">
                 <img src="{$_path|escape:'htmlall':'UTF-8'}logo.gif"> Swissbilling
            </div>
      
            <div id="panel_swissbilling">
                <span id="block_swissbilling">
                    <img src="{$_path|escape:'htmlall':'UTF-8'}views/img/swissbilling-large.png" class="logo-swissbilling"> 
                    {if $link_swissbilling_invoice}  
                        {if $impression_type=='post' || $impression_type=='all'}
                            <form method="post" action="{$link_invoice|escape:'htmlall':'UTF-8'}&reporttype=post">
                                <button class="btn btn-default"><i class="icon-print"></i> {l s='Generate the PDF Swisbilling invoice' mod='swissbilling'} ({l s='Post' mod='swissbilling'})</button>
                            </form>
                        {/if}
                        {if $impression_type=='mail' || $impression_type=='all'}
                            <form method="post" action="{$link_invoice|escape:'htmlall':'UTF-8'}&reporttype=mail">
                                <button class="btn btn-default"><i class="icon-print"></i> {l s='Generate the PDF Swisbilling invoice' mod='swissbilling'} ({l s='e-mail' mod='swissbilling'})</button>
                            </form>
                        {/if}
                    {else}
                        {l s='State' mod='swissbilling'} :
                        {if $status==='Canceled by merchant'}
                            {l s='Invoice canceled by seller' mod='swissbilling'}
                        {else}
                            {l s='This order Swissbilling has not been confirmed' mod='swissbilling'}
                        {/if}
                    {/if}
                <span>
            </div>
        
        </div>
        
    </div>
</div>