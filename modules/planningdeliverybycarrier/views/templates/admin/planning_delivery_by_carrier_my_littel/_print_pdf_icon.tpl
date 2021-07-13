{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 9589 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{* Generate HTML code for printing Invoice Icon with link *}
<span style="width:20px; margin-right:5px;">
{if ($order_state->invoice || $order->invoice_number)}
    <a target="_blank" href="{$link->getAdminLink('AdminPdf')|escape:'htmlall':'UTF-8'}&submitAction=generateInvoicePDF&id_order={$order->id}"><img src="/../img/admin/tab-invoice.gif" alt="invoice" /></a>
{else}
    -
{/if}
</span>

{* Generate HTML code for printing Delivery Icon with link *}
<span style="width:20px;">
{if ($order_state->delivery || $order->delivery_number)}
	<a target="_blank" href="{$link->getAdminLink('AdminPdf')|escape:'htmlall':'UTF-8'}&submitAction=generateDeliverySlipPDF&id_order={$order->id}"><img src="../img/admin/delivery.gif" alt="delivery" /></a>
{/if}
</span>
<span style="width:20px;">
    {if ($order_etiquette)}
        <a target="_blank"
           href="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}barcode/wsbc-generate-littel.php?order_ids[]={$order->id}">
            <i class="icon-barcode"></i>
        </a>
        <input type="checkbox" class="inp_id_order" checked="" name="id_order[]" value="{$order->id}" />
    {/if}
</span>