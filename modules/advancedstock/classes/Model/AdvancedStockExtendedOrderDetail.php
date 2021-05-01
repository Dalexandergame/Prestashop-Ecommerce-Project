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

require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Helper/AdvancedStockRouter.php';

class AdvancedStockExtendedOrderDetail extends ObjectModel
{
    public $eod_id;
    public $eod_order_detail_id;
    public $eod_warehouse_id;
    public $eod_qty_to_ship;
    public $eod_reserved_qty;

    /** @var Order */
    protected $order;
    /** @var OrderDetail */
    protected $orderDetail;

    public static $definition = array(
        'table' => 'bms_advancedstock_extended_order_detail',
        'primary' => 'eod_id',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'eod_order_detail_id' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'eod_warehouse_id' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
            'eod_qty_to_ship' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
            'eod_reserved_qty' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            )
        )
    );

    /**
     * @return bool
     * @throws \PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public function isOrderOpened()
    {
        $order = $this->getOrder();
        $state = new OrderState($order->getCurrentState());
        return $order->current_state !== Configuration::get('PS_OS_CANCELED')
            && $order->current_state !== Configuration::get('PS_OS_ERROR')
            && $order->current_state !== Configuration::get('PS_OS_DELIVERED')
            && $order->current_state !== Configuration::get('PS_OS_REFUND')
            && !$state->deleted
            && !$state->shipped;
    }

    /**
     * @param $orderId int
     * @param $status OrderState
     * @throws \PrestaShopException
     * @throws \PrestaShopDatabaseException
     * @throws \Exception
     */
    public static function beforeOrderStatusChange($orderId, $status)
    {
        $order = new Order($orderId);
        $odList = $order->getOrderDetailList();

        /** @var OrderDetail $orderDetailArray */
        foreach ($odList as $orderDetailArray) {
            $eod = self::getEodFromOdId($orderDetailArray['id_order_detail']);
            $qtyToShipBeforeUpdate = $eod->eod_qty_to_ship;
            $reservedQtyBeforeUpdate = $eod->eod_reserved_qty;

            if (!$status->shipped || $qtyToShipBeforeUpdate <= 0)
                continue;

            if($reservedQtyBeforeUpdate < $qtyToShipBeforeUpdate){
                throw new PrestaShopException('Not enough reserved qty to process shipment');
            }
        }
    }
    /**
     * @param $orderId int
     * @param $status OrderState
     * @throws \PrestaShopException
     * @throws \PrestaShopDatabaseException
     * @throws \Exception
     */
    public static function onOrderStatusChange($orderId, $status)
    {
        $order = new Order($orderId);
        $odList = $order->getOrderDetailList();

        /** @var OrderDetail $orderDetailArray */
        foreach ($odList as $orderDetailArray) {
            $eod = self::getEodFromOdId($orderDetailArray['id_order_detail']);
            $qtyToShipBeforeUpdate = $eod->eod_qty_to_ship;
            $eod->updateQtyToShip();
            
            if (!$status->shipped || $qtyToShipBeforeUpdate <= 0) {
                continue;
            }
            $orderDetail = $eod->getOrderDetail();

            AdvancedStockStockMovements::create(
                $orderDetail->product_id,
                $orderDetail->product_attribute_id,
                $eod->eod_warehouse_id,
                null,
                $orderDetail->product_quantity - $orderDetail->product_quantity_refunded,
                AdvancedStockStockMovements::TYPE_SHIPMENT,
                'Shipment Order #' . $order->getUniqReference()
            );
        }
    }

    /**
     * @param $orderDetailId
     * @return AdvancedStockExtendedOrderDetail
     * @throws \PrestaShopException
     */
    public static function getEodFromOdId($orderDetailId)
    {
        $query = new DbQuery();
        $query->select(self::$definition['primary'])
            ->from(self::$definition['table'])
            ->where('eod_order_detail_id = ' . (int)$orderDetailId);

        $eodId = Db::getInstance()->getValue($query);

        return new self($eodId);
    }

    /**
     * @param $order Order
     * @throws \PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public static function createDetailsForOrder($order)
    {
        $odList = $order->getOrderDetailList();

        foreach ($odList as $orderDetail) {
            self::createFromOrderDetail(new OrderDetail($orderDetail['id_order_detail']));
        }
    }

    /**
     * @param $orderDetail OrderDetail
     * @return AdvancedStockExtendedOrderDetail
     * @throws \PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public static function createFromOrderDetail($orderDetail)
    {
        $eod = new self();
        $eod->eod_order_detail_id = $orderDetail->id_order_detail;
        $eod->eod_warehouse_id = AdvancedStockRouter::getWarehouseForOrderDetail($orderDetail);

        $eod->updateQtyToShip();

        return $eod;
    }

    /**
     * @param $warehouseId
     * @throws \PrestaShopException
     */
    public function assignWarehouse($warehouseId)
    {
        $previousWarehouseId = $this->eod_warehouse_id;
        $this->eod_warehouse_id = $warehouseId;
        $this->save();
        Hook::exec('orderDetailWarehouseChange', array('object' => $this, 'previousWarehouseId' => $previousWarehouseId));
    }

    /**
     * @param $qtyToShip int
     * @return int
     * @throws \PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public function updateQtyToShip($qtyToShip = 0)
    {
        if ($this->isOrderOpened()) {
            $order = $this->getOrderDetail();
            $qtyToShip = $order->product_quantity;
        }

        if ($qtyToShip !== $this->eod_qty_to_ship) {
            $previousQuantity = $this->eod_qty_to_ship;
            $this->eod_qty_to_ship = $qtyToShip;
            $this->save();
            Hook::exec('orderDetailQtyToShipChange', array('object' => $this, 'previousQty' => $previousQuantity));
        }

        return $qtyToShip;
    }

    /**
     * @param $qtyToReserve int
     * @return int
     * @throws \PrestaShopException
     */
    public function updateReservedQty($qtyToReserve = -1)
    {
        //if order move from "open" state to "closed" one, readjust qty reserved to qty to ship
        if ($qtyToReserve === -1) {
            $qtyToReserve = $this->eod_reserved_qty > $this->eod_qty_to_ship ? $this->eod_qty_to_ship : $this->eod_reserved_qty;
        }

        if ($qtyToReserve === $this->eod_reserved_qty) {
            return $this;
        }

        $previousQty = $this->eod_reserved_qty;
        $this->eod_reserved_qty = $qtyToReserve;
        $this->save();
        Hook::exec('orderDetailQtyReservedChange', array('object' => $this, 'previousQty' => $previousQty));

        return $qtyToReserve;
    }

    /**
     * @return Order
     * @throws \PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public function getOrder()
    {
        if ($this->order === null) {
            $query = new DbQuery();
            $query->select('id_order')
                ->from('order_detail', 'od')
                ->innerJoin(self::$definition['table'], 'eod', 'eod.eod_order_detail_id = od.id_order_detail')
                ->where('eod_order_detail_id = ' . (int)$this->eod_order_detail_id);

            $eodId = Db::getInstance()->getValue($query);

            $this->order = new Order($eodId);
        }

        return $this->order;
    }

    /**
     * @return OrderDetail
     */
    public function getOrderDetail()
    {
        if ($this->orderDetail === null) {
            $this->orderDetail = new OrderDetail($this->eod_order_detail_id);
        }
        return $this->orderDetail;
    }

    /**
     * @param $productId
     * @param $attributeId
     * @param $warehouseId
     * @return array
     * @throws \PrestaShopDatabaseException
     */
    public static function getOpenedOrdersItemsForProduct($productId, $attributeId, $warehouseId)
    {
        $query = new DbQuery();
        $query->select(self::$definition['primary'])
            ->from(self::$definition['table'], 'eod')
            ->leftJoin(OrderDetail::$definition['table'], 'od', 'od.id_order_detail = eod.eod_order_detail_id')
            ->leftJoin(Order::$definition['table'], 'o', 'o.id_order = od.id_order')
            ->leftJoin(OrderState::$definition['table'], 'os', 'os.id_order_state = o.current_state')
            ->where('od.product_id = ' . (int)$productId)
            ->where('od.product_attribute_id = ' . (int)$attributeId)
            ->where('eod.eod_warehouse_id = ' . (int)$warehouseId)
            ->where('os.deleted = 0 OR o.current_state = 0')
            ->where('os.shipped = 0 OR o.current_state = 0')
            ->where('o.current_state NOT IN (' .
                (int)Configuration::get('PS_OS_REFUND') . ','
                .(int)Configuration::get('PS_OS_DELIVERED') . ','
                .(int)Configuration::get('PS_OS_CANCELED') . ','
                .(int)Configuration::get('PS_OS_ERROR') . ')')
            ->orderBy('o.date_add');

        $ids = Db::getInstance()->executeS($query);

        if (!$ids) {
            return array();
        }

        $idsAsArray = array();
        foreach ($ids as $id) {
            $idsAsArray[] = $id['eod_id'];
        }

        return $idsAsArray;
    }

    /**
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public static function getOpenedOrdersItems()
    {
        $sql = new DbQuery();
        $sql->select('eod.*, od.product_id, od.product_attribute_id')
            ->from(AdvancedStockExtendedOrderDetail::$definition['table'], 'eod')
            ->leftJoin(OrderDetail::$definition['table'], 'od', 'od.id_order_detail = eod.eod_order_detail_id')
            ->leftJoin(Order::$definition['table'], 'o', 'o.id_order = od.id_order')
            ->leftJoin(OrderState::$definition['table'], 'os', 'os.id_order_state = o.current_state')
            ->where('os.deleted = 0')
            ->where('os.shipped = 0')
            ->where('o.current_state NOT IN (' .
                (int)Configuration::get('PS_OS_REFUND') . ',' .
                (int)Configuration::get('PS_OS_DELIVERED') . ',' .
                (int)Configuration::get('PS_OS_CANCELED') . ',' .
                (int)Configuration::get('PS_OS_ERROR') . ')')
            ->orderBy('o.date_add');

        return Db::getInstance()->executeS($sql);
    }

    public function getQtyToReserve()
    {
        return max($this->eod_qty_to_ship - $this->eod_reserved_qty, 0);
    }
}
