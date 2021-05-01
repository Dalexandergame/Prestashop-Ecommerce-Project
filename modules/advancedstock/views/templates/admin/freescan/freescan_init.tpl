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

<form class="form-horizontal well" method="post" action="{$continueUrl}">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="warehouse">{l s='Warehouse' mod='advancedstock'}</label>
                <select class="form-control col-lg-4" name="warehouse" id="warehouse">
                    {foreach from=$warehouses item=warehouse}
                        <option value="{$warehouse['code']}">{$warehouse['warehouse']}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <label for="action">{l s='Action' mod='advancedstock'}</label>
                <select class="form-control col-lg-4" name="action" id="action">
                    {foreach from=$actions item=action}
                        <option value="{$action['code']}">{$action['action']}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <label for="label">{l s='Label' mod='advancedstock'}</label>
                <input class="form-control col-lg-4" type="text" name="label" id="label"/>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-default" name="submitFreeScanInitInfo">
                    <i class="icon-refresh"></i> {l s='Continue' mod='advancedstock'}
                </button>
            </div>
        </div>
    </div>
</form>