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

<form method="POST" action="{$saveUrl}" id="form_save" enctype="multipart/form-data" class="form-horizontal well">
    <input type="hidden" name="warehouse_id" value="{$warehouseId}"/>
    <input type="hidden" name="action" value="{$action}"/>
    <input type="hidden" name="label" value="{$label}"/>
    <div class="row">
        <div class="col-md-10">
            <input readonly id="barcode"
                   class="form-control"
                   style="border:0px;width:100%;font-size:21px;background-color: inherit"
                   placeholder="{l s='Please scan a barcode' mod='advancedstock'}" value=""/>
        </div>
        <div class="col-md-2">
            <button form="form_save" type="submit" class="btn btn-success btn-lg pull-right">{l s='Apply' mod='advancedstock'}</button>
        </div>

    </div>

    <div id="div_product">
        <div class="container">
            <div class="col-lg-2">
                <img src="" id="product_image" height="150">
            </div>
            <div class="col-lg-6">
                <span class="h2" id="product_name"></span> (<span class="h4" id="product_sku"></span>)
            </div>
            <div class="row">
                <div class="col-lg-2"></div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-12">
                            <p class="h3" id="expected_qty_block">{l s='Expecting' mod='advancedstock'} : <span id="expected_qty"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container" style="margin-bottom:2em;">
            <div class="row">
                <div class="col-lg-2"></div>
                <div class="col-lg-6">
                    <div class="col-lg-1">
                        <button type="button" class="btn btn-info btn-lg" onclick="dec()"> -</button>
                    </div>
                    <div class="col-lg-4">
                        <input type="text" id="product_stock" class="form-control" onchange="changeQte(this.value)">
                    </div>
                    <div class="col-lg-1">
                        <button type="button" class="btn btn-info btn-lg" onclick="inc()"> +</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="div_history"
         style="align: center; text-align: center; border: 1px solid #D6D6D6; background: #FAFAFA; margin-top: 10px;">
        <table class="table table-striped" id="table_history">
            <tr>
                <th scope="col" class="text-center">{l s='Image' mod='advancedstock'}</th>
                <th scope="col" class="text-center">{l s='Name' mod='advancedstock'}</th>
                <th scope="col" class="text-center">{l s='Barcode' mod='advancedstock'}</th>
                {if ($action == 'stock_control')}
                    <th scope="col" class="text-center">{l s='Expected quantity' mod='advancedstock'}</th>
                {/if}
                <th scope="col" class="text-center">{l s='Quantity scanned' mod='advancedstock'}</th>
                {if ($action == 'stock_control')}
                    <th scope="col" class="text-center">{l s='Status' mod='advancedstock'}</th>
                {/if}

            </tr>
        </table>
    </div>


</form>

<audio id="audio_nok" src="{$nokSoundUrl|escape:'htmlall':'UTF-8'}" ></audio>
<audio id="audio_ok" src="{$okSoundUrl|escape:'htmlall':'UTF-8'}" ></audio>