{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

<!-- fix for JS issue with gamification module -->
<div style="display: none"><div id="header_notifs_icon_wrapper"></div></div>

<div class="row" xmlns="http://www.w3.org/1999/html">
    <div class="col-md-6">
        <h2>{l s='Import' mod='advancedstock'}</h2>

        <div class="alert alert-warning col-md-10" role="alert">
            <p>{l s='Importing new quantities on existing products will replace their existing quantities.' mod='advancedstock'}</p>
        </div>

        <div class="alert alert-info col-md-10" role="alert">
            <h4>{l s='CSV file sample' mod='advancedstock'}</h4>
            <p>wi_product_id;wi_attribute_id;wi_physical_quantity;wi_shelf_location</p>
            <p>8;0;106;A1</p>
            <p>14;1;107;B2</p>
            <p>174;0;100;A4</p>
            <p>222;5;100;C1</p>
            <p>26;40;100;B3</p>
            <p>26;46;100;A2</p>
        </div>

		<form method="POST" enctype="multipart/form-data" action="{Context::getContext()->link->getAdminLink('AdminWarehousesImportExportTab')|escape:'htmlall':'UTF-8'}&w_id={$w_id|escape:'htmlall':'UTF-8'}&action=import#tabActive=ImportExport">
			<input id="stock_id" name="stock_id" type="hidden" value="">
            <div class="col-md-12">
                <input type="file" name="import_stock" id="import_stock" />
			</div>
            <div class="col-md-12">
                <input type="submit" value="{l s='Import' mod='advancedstock'}" />
            </div>
        </form>
	</div>

    <div class="col-md-6">
        <h2>{l s='Export Products' mod='advancedstock'}</h2>
        <div class="col-md-6">
            <a target="_blank" href="{Context::getContext()->link->getAdminLink('AdminWarehousesProductTab')|escape:'htmlall':'UTF-8'}&w_id={$w_id|escape:'htmlall':'UTF-8'}&action=export">{l s='Export CSV' mod='advancedstock'}</a>
        </div>
    </div>

</div>
