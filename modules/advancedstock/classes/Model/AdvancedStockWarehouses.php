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

class AdvancedStockWarehouses extends ObjectModel
{
    const WH_STATUS_ACTIVE = '1';
    const WH_STATUS_INACTIVE = '0';

    public $w_id;
    public $w_name;
    public $w_email;
    public $w_contact;
    public $w_is_active;
    public $w_display_on_front;
    public $w_notes;
    public $w_company_name;
    public $w_street1;
    public $w_street2;
    public $w_postcode;
    public $w_city;
    public $w_state;
    public $w_country;
    public $w_telephone;
    public $w_fax;
    public $w_open_hours;
    public $w_is_primary;
    public $w_use_in_supplyneeds;

    public static $definition = array(
        'table' => 'bms_advancedstock_warehouse',
        'primary' => 'w_id',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(

            'w_name' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true
            ),
            'w_email' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isEmail',
                'required' => false
            ),
            'w_contact' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false
            ),
            'w_is_active' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
            'w_display_on_front' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
            'w_notes' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false
            ),
            'w_company_name' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false
            ),
            'w_street1' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false
            ),
            'w_street2' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false
            ),
            'w_postcode' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false
            ),
            'w_city' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false
            ),
            'w_state' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false
            ),
            'w_country' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false
            ),
            'w_telephone' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false
            ),
            'w_fax' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false
            ),
            'w_open_hours' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false
            ),
            'w_is_primary' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
            'w_use_in_supplyneeds' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
        )
    );

    /**
     * @return array
     * array{
     *  array{
     *    code => int,
     *    status => string
     *  }
     * }
     */
    public static function getWhStatuses()
    {
        return array(
                array("code" => self::WH_STATUS_ACTIVE, 'status' => 'Active'),
                array("code" => self::WH_STATUS_INACTIVE, 'status' => 'Inactive')
        );
    }

    /**
     * @param bool $addEmptyLine
     * @return array
     * @throws \PrestaShopDatabaseException
     * array{
     *   array{
     *     code => int,
     *     warehouse => string
     *   }
     * }
     */
    public static function getWarehousesOptions($addEmptyLine = false)
    {
        $warehouses = self::getAllWarehouses();
        $options = array();
        if ($addEmptyLine) {
            $options[] = array('code' => '', 'warehouse' => '');
        }
        foreach ($warehouses as $warehouseId => $warehouseName) {
            $options[] = array('code' => $warehouseId, 'warehouse' => $warehouseName);
        }

        return $options;
    }

    /**
     * @return array $warehouses
     * array{
     *  w_id => w_name
     * }
     * @throws PrestaShopDatabaseException
     */
    public static function getAllWarehouses()
    {
        $warehouses = array();
        $sql = new DbQuery();
        $sql->select('w_id, w_name');
        $sql->from('bms_advancedstock_warehouse', 'w');
        $results = Db::getInstance()->executeS($sql);
        foreach ($results as $result) {
            $warehouses[$result['w_id']] = $result['w_name'];
        }
        return $warehouses;
    }

    /**
     * @param int $warehouseId
     * @return string|false|null|string warehouse name
     */
    public static function getWarehouseNameById($warehouseId)
    {
        $sql = new DbQuery();
        $sql->select('w_name');
        $sql->from('bms_advancedstock_warehouse', 'w');
        $sql->where('w.w_id = '.pSQL($warehouseId));
        return Db::getInstance()->getValue($sql);
    }

    public static function getActivePrimaryWarehouse()
    {
        $sql = new DbQuery();
        $sql->select(self::$definition['primary']);
        $sql->from(self::$definition['table']);
        $sql->where('w_is_primary = 1 AND w_is_active = 1');
        return Db::getInstance()->getValue($sql);
    }

    public static function getAll()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(self::$definition['table']);
        return Db::getInstance()->executeS($sql);
    }
}
