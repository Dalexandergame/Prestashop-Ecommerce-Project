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

{literal}
<style>
    p.payment_module a.swissbilling{
        background: url("{/literal}{$this_path|escape:'htmlall':'UTF-8'}{literal}views/img/swissbilling.jpg") no-repeat scroll 15px 15px #FBFBFB;
        padding-left:115px;
    }
    p.payment_module a.swissbilling:after{
        display: block;
        content: "\f054";
        position: absolute;
        right: 15px;
        margin-top: -11px;
        top: 50%;
        font-family: "FontAwesome";
        font-size: 25px;
        height: 22px;
        width: 14px;
        color: #777777; 
    }
    #module-swissbilling-payment .breadcrumb{display:none;}
    #module-swissbilling-payment #center_column .breadcrumb{display:block;}
</style>
{/literal}