<?php
/**
 * *
 *  2007-2018 PrestaShop
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *   @author    PrestaShop SA <contact@prestashop.com>
 *   @copyright 2007-2018 PrestaShop SA
 *   @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *   International Registered Trademark & Property of PrestaShop SA
 * /
 */

class AdvanceStockCompatibility
{
    const CSV_DELIMITER = ";";

    public static function getProductLink($productId, $context)
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            return $context->link->getAdminLink('AdminProducts', true, array('id_product' => (int)$productId));
        }
        return $context->link->getAdminLink('AdminProducts', true).'&id_product='.$productId.'&updateproduct';
    }

    public static function getAdvancedStockProductLink($warehouseProductId, $context)
    {
        return $context->link->getAdminLink('AdminProductsHome', true).'&wi_id='.$warehouseProductId.'&updatebms_advancedstock_warehouse_product';
    }

    public static function getOrderLink($orderId, $context)
    {
        return $context->link->getAdminLink('AdminOrders', true).'&id_order='.$orderId.'&vieworder';
    }

    public static function procurementIsInstalled()
    {
        return (Module::isInstalled('bmsprocurement'));
    }
}
