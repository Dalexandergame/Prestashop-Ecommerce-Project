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

<input type="number" data-ajax="1"
        data-wi-id="{$wi_id|escape:'htmlall':'UTF-8'}"
        data-product-id="{$productId|escape:'htmlall':'UTF-8'}"
        data-name="wi_physical_quantity"
        data-warehouse-id = "{$warehouseId|escape:'htmlall':'UTF-8'}"
        name="wi_physical_quantity[{$productId|escape:'htmlall':'UTF-8'}][{$productAttributeId|escape:'htmlall':'UTF-8'}]"
        data-product-attribute-id="{$productAttributeId|escape:'htmlall':'UTF-8'}"
        value="{$wi_physical_quantity|escape:'htmlall':'UTF-8'}">
