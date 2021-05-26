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

require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockWarehousesProducts.php';
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockExtendedOrderDetail.php';

class AdminOrdersController extends AdminOrdersControllerCore
{
    /**
     * @return string
     * @throws \PrestaShopException
     */
    public function renderView()
    {
        parent::renderView();
        $warehouses = AdvancedStockWarehouses::getAllWarehouses();
        $this->tpl_view_vars['warehouses'] = $warehouses;
        foreach ($this->tpl_view_vars['products'] as $index => $item) {
            $eod = AdvancedStockExtendedOrderDetail::getEodFromOdId($item['id_order_detail']);

            if ($eod->eod_id) {
                $this->tpl_view_vars['products'][$index]['eod_id'] = $eod->eod_id;
                $this->tpl_view_vars['products'][$index]['eod_warehouse_id'] = $eod->eod_warehouse_id;
                $this->tpl_view_vars['products'][$index]['eod_reserved_qty'] = $eod->eod_reserved_qty;
                $this->tpl_view_vars['products'][$index]['stock_detail'] =
                    AdvancedStockWarehousesProducts::getProductStockDetail($item['product_id'], $item['product_attribute_id']);
            }
        }

        return AdminController::renderView();
    }

    public function ajaxPreProcess()
    {
        if (!Tools::getIsset('method') || Tools::getValue('method') != 'updatePreparationWarehouse') {
            return;
        }

        try {
            $errors = array();
            $data = Tools::getValue('warehouseId_eodId');

            if (!$data) {
                throw new PrestaShopException('Missing parameters: warehouseId and eodId undefined.');
            }

            $data = explode('_', $data);
            $warehouseId = (int)$data[0];
            $eodId = (int)$data[1];

            $eod = new AdvancedStockExtendedOrderDetail($eodId);
            $eod->assignWarehouse($warehouseId);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }

        if (count($errors) > 0) {
            header($_SERVER['SERVER_PROTOCOL']. ' 500 Internal Server');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(
                array(
                    'errors' => $errors
                )
            ));
        }
    }

    /**
     * @param OrderDetail $order_detail
     * @param int $qty_cancel_product
     * @param bool $delete
     * @throws Exception
     * @throws PrestashopException
     * @throws PrestashopDataBaseException
     */
    public function reinjectQuantity($order_detail, $qty_cancel_product, $delete = false)
    {
        $order = new Order($order_detail->id_order);
        $state = new OrderState($order->getCurrentState());
        //do not re-stock product if order hasn't been shipped yet
        if(!$state->shipped)
            return;

        //credit memo stock movement creation
        $preparationWarehouse = Tools::getValue('preparationWarehouse');
        $data = explode('_', $preparationWarehouse[$order_detail->id_order_detail]);
        $targetWarehouseId = (int)$data[0];

        $reinjectableQuantity = (int)$order_detail->product_quantity - (int)$order_detail->product_quantity_reinjected;
        $quantityToReinject = $qty_cancel_product > $reinjectableQuantity ? $reinjectableQuantity : $qty_cancel_product;

        AdvancedStockStockMovements::create(
            $order_detail->product_id,
            $order_detail->product_attribute_id,
            0,
            $targetWarehouseId,
            $quantityToReinject,
            AdvancedStockStockMovements::TYPE_CREDITMEMO,
            $this->l('Product return')
        );
    }

    /**
     * @param bool $isNewTheme
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();
        $this->addJS(_PS_MODULE_DIR_ . 'advancedstock/views/js/orderView.js');
        Media::addJsDef(array(
            'ajaxUpdatePreparationWarehouse' => $this->context->link->getAdminLink(
                'AdminOrders',
                true
            ),
        ));
    }
}
