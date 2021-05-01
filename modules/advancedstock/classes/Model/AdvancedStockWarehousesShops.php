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

class AdvancedStockWarehousesShops extends ObjectModel
{
    const ROLE_SALES = 1;
    const ROLE_SHIPMENT = 2;

    public $ws_id;
    public $ws_warehouse_id;
    public $ws_shop_id;
    public $ws_role;

    public static $definition = array(
        'table' => 'bms_advancedstock_warehouse_shop',
        'primary' => 'ws_id',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'ws_warehouse_id' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'ws_shop_id' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'ws_role' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            )
        )
    );

    /**
     * @return array
     * array {
     *  role => int,
     *  label => string
     * }
     */
    public static function getRoles()
    {
        return array(
            array("role" => self::ROLE_SALES, 'label' => 'Use for sales'),
            array("role" => self::ROLE_SHIPMENT, 'label' => 'Use for shipment')
        );
    }

    /**
     * @return array
     * array {
     *  role => label
     * }
     */
    public static function getRolesArray()
    {
        $roles = array();
        foreach (self::getRoles() as $role) {
            $roles[$role['role']] = $role['label'];
        }
        return $roles;
    }

    /**
     * @param int $warehouseId
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public static function getWarehouseRolesForAllShops($warehouseId)
    {
        $sql = new DbQuery();
        $sql->select('ws_warehouse_id, ws_role, ws_shop_id, s.name as shop_name');
        $sql->from('bms_advancedstock_warehouse_shop', 'ws');
        $sql->leftJoin('shop', 's', 's.id_shop = ws.ws_shop_id');
        $sql->where('ws_warehouse_id = '.pSQL($warehouseId));
        return Db::getInstance()->executeS($sql);
    }

    public static function deleteWarehouseAssignments($warehouseId)
    {
        return Db::getInstance()->delete(self::$definition['table'], 'ws_warehouse_id ='.pSQL($warehouseId));
    }
}
