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

class AdvancedStockStockMovements extends ObjectModel
{
    const TYPE_SYSTEM = 1;
    const TYPE_SHIPMENT = 2;
    const TYPE_ADJUSTMENT = 3;
    const TYPE_CREDITMEMO = 4;
    const TYPE_PURCHASEORDER = 5;
    const TYPE_RECEPTION = 6;

    public $sm_id;
    public $sm_date;
    public $sm_product_id;
    public $sm_attribute_id;
    public $sm_source_warehouse_id;
    public $sm_target_warehouse_id;
    public $sm_qty;
    public $sm_type;
    public $sm_comment;
    public $sm_employee_id;

    public static $definition = array(
        'table' => 'bms_advancedstock_stock_movement',
        'primary' => 'sm_id',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'sm_date' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'required' => true
            ),
            'sm_product_id' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'sm_attribute_id' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
            'sm_source_warehouse_id' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
            'sm_target_warehouse_id' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
            'sm_qty' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'sm_type' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'sm_comment' => array(
                'type' => self::TYPE_STRING,
                'size' => 200,
                'required' => false
            ),
            'sm_employee_id' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            )
        )
    );

    public static function l($string)
    {
        return Translate::getModuleTranslation('advancedstock', $string, 'advancedstockstockmovementtype');
    }

    /**
     * @return array
     * array{
     *  array{
     *    type => int,
     *    label => string
     *  }
     * }
     */
    public static function getTypes()
    {
        return array(
            array('type' => self::TYPE_SYSTEM, 'label' => self::l('System')),
            array('type' => self::TYPE_SHIPMENT, 'label' => self::l('Shipment')),
            array('type' => self::TYPE_ADJUSTMENT, 'label' => self::l('Adjustment')),
            array('type' => self::TYPE_CREDITMEMO, 'label' => self::l('Credit Memo')),
            array('type' => self::TYPE_PURCHASEORDER, 'label' => self::l('Purchase Order')),
            array('type' => self::TYPE_RECEPTION, 'label' => self::l('Reception'))
        );
    }

    /**
     * Dummy function for translations
     */
    public function getTypesForTranslations()
    {
        $all = array();

        $all[] = $this->l('System');
        $all[] = $this->l('Shipment');
        $all[] = $this->l('Adjustment');
        $all[] = $this->l('Credit Memo');
        $all[] = $this->l('Purchase Order');
        $all[] = $this->l('Reception');

        return $all;
    }

    /**
     * @return array
     * array {
     *  type => label
     * }
     */
    public static function getTypesArray()
    {
        $types = array();
        foreach (self::getTypes() as $type) {
            $types[$type['type']] = $type['label'];
        }
        return $types;
    }

    /**
     * @param $productId int
     * @param $attributeId int
     * @param $whIdFrom int
     * @param $whIdTo int
     * @param $quantity int
     * @param $type string
     * @param $comment string|null
     * @param $employeeId int|null
     * @throws \Exception
     */
    public static function create($productId, $attributeId, $whIdFrom, $whIdTo, $quantity, $type, $comment = null, $employeeId = null)
    {
        $sm = new self();
        $sm->sm_product_id = $productId;
        $sm->sm_attribute_id = $attributeId;
        $sm->sm_source_warehouse_id = $whIdFrom;
        $sm->sm_target_warehouse_id = $whIdTo;
        $sm->sm_qty = $quantity;
        $sm->sm_type = $type;
        $sm->sm_comment = $comment;
        $sm->sm_employee_id = $employeeId;

        $sm->save();
    }

    /**
     * @param bool $null_values
     * @param bool $auto_date
     * @return bool
     * @throws \Exception
     * @throws \PrestaShopException
     */
    public function save($null_values = false, $auto_date = true)
    {
        $this->checkValues();
        $this->sm_date = date('Y-m-d H:i:s');
        $res = parent::save($null_values, $auto_date);
        Hook::exec('newStockMovement', array('object' => $this));
        return $res;
    }

    /**
     * @throws PrestaShopException
     */
    protected function checkValues()
    {
        if ($this->sm_source_warehouse_id === $this->sm_target_warehouse_id) {
            throw new PrestaShopException('Source warehouse and target warehouse cannot be the same');
        }

        if (empty($this->sm_source_warehouse_id) && empty($this->sm_target_warehouse_id)) {
            throw new PrestaShopException('At least a source warehouse or a target warehouse must be specified');
        }

        if ($this->sm_qty <= 0 || !is_numeric($this->sm_qty)) {
            throw new PrestaShopException('Incorrect quantity. It must be a positive integer');
        }

        if (empty($this->sm_source_warehouse_id)) {
            return;
        }

        $productWarehouse = AdvancedStockWarehousesProducts::findProduct(
            $this->sm_product_id,
            $this->sm_attribute_id,
            $this->sm_source_warehouse_id
        );

        if ($productWarehouse === null) {
            throw new PrestaShopException('Wrong product id or it doesn\'t belong to the source warehouse');
        }

        //cannot use wi_physical_quantity, it may have already been updated (warehouseProductAfterSave)
        if (self::calculateQuantityOnHand($this->sm_product_id, $this->sm_attribute_id, $this->sm_source_warehouse_id) < $this->sm_qty) {
            throw new PrestaShopException('Not enough quantity in the source warehouse');
        }
    }

    public static function calculateQuantityOnHand($productId, $productAttributeId, $warehouseId)
    {
        $query = new DbQuery();
        $query->select('SUM(IF(sm_source_warehouse_id = '. (int)$warehouseId .', -sm_qty, sm_qty)) AS qty')
            ->from(self::$definition['table'])
            ->where(
                '(sm_source_warehouse_id = ' . (int)$warehouseId . ' OR sm_target_warehouse_id = ' . (int)$warehouseId . ')' .
                ' AND sm_product_id = ' . (int)$productId .
                ' AND sm_attribute_id = ' . (int)$productAttributeId
            );

        return (int)Db::getInstance()->getValue($query);
    }

    /**
     * @param $wp AdvancedStockWarehousesProducts
     * @throws Exception
     */
    public static function syncStockMovements($wp)
    {
        $totalQtyFromSm = self::calculateQuantityOnHand($wp->wi_product_id, $wp->wi_attribute_id, $wp->wi_warehouse_id);
        if ($totalQtyFromSm == $wp->wi_physical_quantity) {
            return;
        }
        $diff = $totalQtyFromSm - $wp->wi_physical_quantity;
        $from = $diff > 0 ? $wp->wi_warehouse_id : null;
        $to = $diff < 0 ? $wp->wi_warehouse_id : null;

        AdvancedStockStockMovements::create(
            $wp->wi_product_id,
            $wp->wi_attribute_id,
            $from,
            $to,
            abs($diff),
            AdvancedStockStockMovements::TYPE_ADJUSTMENT,
            'Automatic adjustment'
        );
    }
}
