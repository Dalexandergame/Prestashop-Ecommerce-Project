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

require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Helper/AdvancedStockWarehouseProduct.php';

class AdvancedStockWarehousesProducts extends ObjectModel
{
    public $wi_id;
    public $wi_warehouse_id;
    public $wi_product_id;
    public $wi_physical_quantity;
    public $wi_available_quantity;
    public $wi_reserved_quantity;
    public $wi_shelf_location;
    public $wi_quantity_to_ship;
    public $wi_warning_stock_level;
    public $wi_use_config_warning_stock_level;
    public $wi_ideal_stock_level;
    public $wi_use_config_ideal_stock_level;
    public $wi_attribute_id;

    public static $definition = array(
        'table' => 'bms_advancedstock_warehouse_product',
        'primary' => 'wi_id',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(

            'wi_warehouse_id' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'wi_product_id' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
            'wi_physical_quantity' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
            'wi_available_quantity' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
            'wi_reserved_quantity' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
            'wi_shelf_location' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false
            ),
            'wi_quantity_to_ship' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
            'wi_warning_stock_level' => array(
                'type' => self::TYPE_INT,
                'required' => false
            ),
            'wi_use_config_warning_stock_level' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
            'wi_ideal_stock_level' => array(
                'type' => self::TYPE_INT,
                'required' => false
            ),
            'wi_use_config_ideal_stock_level' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
            'wi_attribute_id' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
        )
    );

    public static function create($productId, $productAttributeId, $warehouseId, $qty = 0, $shelfLocation = '', $save = true)
    {
        if (!self::checkProductExist($productId, $productAttributeId)) {
            throw new PrestaShopException('There is no product with Id: '.$productId.' and product attribute Id : '. $productAttributeId);
        }

        $warehouseProduct = new AdvancedStockWarehousesProducts();
        $warehouseProduct->wi_warehouse_id = $warehouseId;
        $warehouseProduct->wi_product_id = $productId;
        $warehouseProduct->wi_attribute_id = $productAttributeId;
        $warehouseProduct->wi_shelf_location = $shelfLocation;
        $warehouseProduct->wi_physical_quantity = $qty;
        $warehouseProduct->wi_reserved_quantity = 0;

        if ($save) {
            $warehouseProduct->save();
        }

        return $warehouseProduct;
    }

    public static function getAll()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(self::$definition['table']);
        return Db::getInstance()->executeS($sql);
    }

    public static function checkProductExist($productId, $productAttributeId)
    {
        $sql = new DbQuery();
        $sql->select('p.id_product');
        $sql->from(Product::$definition['table'], 'p');
        $sql->where('p.id_product = ' . (int)$productId);

        if ($productAttributeId !== 0) {
            $sql->innerJoin(Combination::$definition['table'], 'pa', 'pa.id_product = p.id_product');
            $sql->where('pa.id_product_attribute = ' . (int)$productAttributeId);
        }

        return Db::getInstance()->getValue($sql);
    }

    public function save($null_values = false, $auto_date = true)
    {
        $parent = parent::save($null_values, $auto_date);
        Hook::exec('warehouseProductAfterSave', array('object' => $this));
        return $parent;
    }

    public static function getProductWarehouses($productId, $attributeId)
    {
        $query = new DbQuery();
        $query->select('*')
            ->from('bms_advancedstock_warehouse_product', 'wi')
            ->leftJoin('bms_advancedstock_warehouse', 'w', 'w_id = wi_warehouse_id')
            ->where('wi_product_id = ' . (int)$productId . ' and wi_attribute_id = ' . (int)$attributeId);

        return Db::getInstance()->executeS($query);
    }

    public static function getTotalQuantity($warehouseId)
    {
        $query = new DbQuery();
        $query->select('SUM(wi_physical_quantity)')
            ->from('bms_advancedstock_warehouse_product', 'wi')
            ->where('wi_warehouse_id = ' . (int)$warehouseId);

        return Db::getInstance()->getValue($query);
    }

    public static function getSumOfProductReferencesInStock($warehouseId)
    {
        $query = new DbQuery();
        $query->select('count('.self::$definition['primary'].')')
            ->from('bms_advancedstock_warehouse_product', 'wi')
            ->where('wi_warehouse_id = ' . (int)$warehouseId)
            ->where('wi_physical_quantity > 0');

        return Db::getInstance()->getValue($query);
    }

    public static function getStockValue($warehouseId)
    {
        $query = new DbQuery();
        $query->select('SUM(wi_physical_quantity * wholesale_price)')
            ->from('bms_advancedstock_warehouse_product', 'wi')
            ->leftJoin('product', 'p', 'wi_product_id = id_product')
            ->where('wi_warehouse_id = ' . (int)$warehouseId);

        return Db::getInstance()->getValue($query);
    }

    /**
     * @param $wiId int
     * @return null|string
     * @throws \PrestaShopDatabaseException
     */
    public static function getProductFullName($wiId)
    {
        $sql =
            "SELECT IF(b.attribute_full_name IS NULL, pl.name, CONCAT(pl.name, ' ', b.attribute_full_name)) as product_full_name " .
            'FROM ' . _DB_PREFIX_ . 'bms_advancedstock_warehouse_product as a ' .
            'LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (wi_product_id = pl.id_product and id_shop = '.(int)Context::getContext()->shop->id.' and id_lang = '.(int)Context::getContext()->language->id.') '.
            'LEFT JOIN ( ' .
                "SELECT wp.wi_product_id as product_id, wp.wi_attribute_id as pa_id, GROUP_CONCAT(al.name SEPARATOR ' ') as attribute_full_name ".
                'FROM ' . _DB_PREFIX_ . 'bms_advancedstock_warehouse_product as wp ' .
                'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination as pac ON (pac.`id_product_attribute` = `wp`.`wi_attribute_id`) ' .
                'LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang as al ON al.`id_attribute` = pac.`id_attribute` and al.id_lang = '.(int)Context::getContext()->language->id .' '.
                'GROUP BY wp.wi_attribute_id, wp.wi_warehouse_id ' .
                'HAVING wp.wi_attribute_id > 0' .
            ') AS b ON b.product_id = a.wi_product_id AND b.pa_id = a.wi_attribute_id ' .
            'WHERE wi_id = ' . (int)$wiId;
        
        $res = Db::getInstance()->executeS($sql);

        if (!$res ||!isset($res[0])) {
            return null;
        }

        return $res[0]['product_full_name'];
    }

    /**
     * @param $sm AdvancedStockStockMovements
     * @throws \PrestaShopException
     */
    public static function updatePhysicalQuantityFromSm($sm)
    {
        $productId = $sm->sm_product_id;
        $attributeId = $sm->sm_attribute_id;
        $warehouseSourceId = $sm->sm_source_warehouse_id;
        $warehouseTargetId = $sm->sm_target_warehouse_id;

        $warehouses = array($warehouseSourceId, $warehouseTargetId);

        foreach ($warehouses as $warehouseId) {
            if (empty($warehouseId)) {
                continue;
            }
            $qty = $sm::calculateQuantityOnHand($productId, $attributeId, $warehouseId);
            self::updatePhysicalQuantity($productId, $attributeId, $warehouseId, $qty);
        }
    }

    /**
     * @param $productId
     * @param $productAttributeId
     * @param $warehouseId
     * @return AdvancedStockWarehousesProducts|null
     * @throws \PrestaShopException
     */
    public static function findProduct($productId, $productAttributeId, $warehouseId = null)
    {
        $warehouseCondition = $warehouseId ? 'wi_warehouse_id = ' . (int)$warehouseId : '';

        $query = new DbQuery();
        $query->select(self::$definition['primary']);
        $query->from(self::$definition['table']);
        $query->where('wi_product_id = ' . (int)$productId);
        $query->where('wi_attribute_id = ' . (int)$productAttributeId);

        if (!empty($warehouseCondition)) {
            $query->where($warehouseCondition);
        }

        $res = Db::getInstance()->getValue($query);

        if (!$res) {
            return null;
        }

        return new self($res);
    }

    public static function getProductIds()
    {
        $query = new DbQuery();
        $query->select('DISTINCT wi_product_id, wi_attribute_id');
        $query->from(self::$definition['table']);
        return Db::getInstance()->executeS($query);
    }

    /**
     * @param $productId
     * @param $productAttributeId
     * @return AdvancedStockWarehousesProducts|null
     * @throws \PrestaShopException
     */
    public static function getProductStockDetail($productId, $productAttributeId)
    {
        $query = new DbQuery();
        $query->select(self::$definition['primary']);
        $query->select('wi_warehouse_id');
        $query->select('w_name');
        $query->select('wi_available_quantity');
        $query->select('wi_physical_quantity');
        $query->select('wi_quantity_to_ship');
        $query->from(self::$definition['table']);
        $query->leftJoin(AdvancedStockWarehouses::$definition['table'], 'w', 'w_id = wi_warehouse_id');
        $query->where('wi_product_id = ' . (int)$productId);
        $query->where('wi_attribute_id = ' . (int)$productAttributeId);

        $res = Db::getInstance()->executeS($query);

        return !$res ? null : $res;
    }

    /**
     * @param $productId int
     * @param $attributeId int
     * @param $warehouseId int
     * @param $qty int
     * @return AdvancedStockWarehousesProducts
     * @throws \PrestaShopException
     */
    public static function updatePhysicalQuantity($productId, $attributeId, $warehouseId, $qty)
    {
        $wp = self::findProduct($productId, $attributeId, $warehouseId);

        if ($wp === null) {
            $wp = self::create($productId, $attributeId, $warehouseId, $qty);
            return $wp;
        }

        if ($wp->wi_physical_quantity === $qty) {
            return $wp;
        }

        $wp->wi_physical_quantity = $qty;
        $wp->save();

        Hook::exec('warehouseProductQtyOnHandChange', array('object' => $wp));

        return $wp;
    }

    /**
     * @param $productId int
     * @param $attributeId int
     * @param $warehouseId int
     * @param $qty int
     * @return AdvancedStockWarehousesProducts
     * @throws \PrestaShopException
     */
    public static function updateQuantityToShip($productId, $attributeId, $warehouseId, $qty = -1)
    {
        $wp = self::findProduct($productId, $attributeId, $warehouseId);

        if ($wp === null) {
            $wp = self::create($productId, $attributeId, $warehouseId);
        }

        if ($qty === -1) {
            $qty = AdvancedStockWarehouseProduct::getGlobalQuantityToShip($productId, $attributeId, $warehouseId);
        }

        if ($wp->wi_quantity_to_ship === $qty) {
            return $wp;
        }

        $wp->wi_quantity_to_ship = $qty;
        $wp->save();

        Hook::exec('warehouseProductQtyToShipChange', array('object' => $wp));

        return $wp;
    }

    /**
     * @param $productId int
     * @param $attributeId int
     * @param $warehouseId int
     * @param $qtyToAdd int
     * @return AdvancedStockWarehousesProducts|null
     * @throws \PrestaShopException
     */
    public static function updateReservedQuantity($productId, $attributeId, $warehouseId, $qtyToAdd)
    {
        $wp = self::findProduct($productId, $attributeId, $warehouseId);

        if ($wp === null) {
            $wp = self::create($productId, $attributeId, $warehouseId);
        }

        if ($qtyToAdd === 0) {
            return $wp;
        }

        if ($wp->wi_reserved_quantity === null) {
            $wp->wi_reserved_quantity = 0;
        }
        $wp->wi_reserved_quantity += $qtyToAdd;
        $wp->wi_reserved_quantity = max(0, $wp->wi_reserved_quantity);
        $wp->save();

        Hook::exec('warehouseProductQtyReservedChange', array('object' => $wp));

        return $wp;
    }

    public static function onPrestashopQuantityChange($productId, $productAttributeId, $qty)
    {
        /** @var PrestaShopBundle\Model\Product\ $product */
        $product = new Product($productId);
        if ($product->hasCombinations()) {
            return;
        }

        $primaryWarehouseId = (int)AdvancedStockWarehouses::getActivePrimaryWarehouse();
        $wp = self::findProduct($productId, $productAttributeId, $primaryWarehouseId);

        if (!$wp) {
            $wp =  self::create($productId, $productAttributeId, $primaryWarehouseId);
        }

        $diff = $wp->wi_available_quantity - $qty;
        $newPhysicalQty = $diff > 0 ? $wp->wi_physical_quantity - $diff : $wp->wi_physical_quantity + abs($diff);
        AdvancedStockWarehousesProducts::updatePhysicalQuantity($productId, $productAttributeId, $primaryWarehouseId, $newPhysicalQty);
    }

    /**
     * @return AdvancedStockWarehousesProducts
     * @throws \PrestaShopException
     */
    public function updateAvailableQuantity()
    {
        $qty = max(0, $this->wi_physical_quantity - $this->wi_quantity_to_ship);

        if ($qty === $this->wi_available_quantity) {
            return $this;
        }

        $this->wi_available_quantity = $qty;
        $this->save();

        Hook::exec('warehouseProductAvailableQtyChange', array('object' => $this));

        return $this;
    }

    /**
     * @param $productId int
     * @param $productAttributeId int
     * @return AdvancedStockWarehousesProducts|null
     * @throws PrestaShopException
     */
    public static function assignToAllWarehouses($productId, $productAttributeId = 0)
    {
        $wp = self::findProduct($productId, $productAttributeId);

        if ($wp !== null) {
            return;
        }

        $warehouses = AdvancedStockWarehouses::getAll();
        foreach ($warehouses as $warehouse) {
            self::create((int)$productId, (int)$productAttributeId, (int)$warehouse['w_id'], 0);
        }
    }

    /**
     * @param int $productId
     * @param int $productAttributeId
     * @param int $warehouseId
     * @return false|null|string
     */
    public static function getAvailableQtyForProduct($productId, $productAttributeId, $warehouseId)
    {
        $sql = new DbQuery();
        $sql->select('wi_available_quantity')
            ->from(AdvancedStockWarehousesProducts::$definition['table'])
            ->where('wi_warehouse_id = ' . (int)$warehouseId)
            ->where('wi_product_id = ' . (int)$productId)
            ->where('wi_attribute_id = ' . (int)$productAttributeId);

        return Db::getInstance()->getValue($sql);
    }

    public static function getPhysicalQtyForProduct($productId, $productAttributeId, $warehouseId)
    {
        $sql = new DbQuery();
        $sql->select('wi_physical_quantity')
            ->from(AdvancedStockWarehousesProducts::$definition['table'])
            ->where('wi_warehouse_id = ' . (int)$warehouseId)
            ->where('wi_product_id = ' . (int)$productId)
            ->where('wi_attribute_id = ' . (int)$productAttributeId);

        return Db::getInstance()->getValue($sql);
    }

    public static function removeWarehouseProductAfterDelete($productId)
    {
        return Db::getInstance()->delete(self::$definition['table'], 'wi_product_id ='.pSQL($productId));
    }

}
