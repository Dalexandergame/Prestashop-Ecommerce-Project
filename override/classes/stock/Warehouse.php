<?php
/*
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
class Warehouse extends WarehouseCore {
   
    /**
     * Sets the regions associated to the current warehouse
     *
     * @param array $ids_regions
     */
    /*
    * module: gszonevente
    * date: 2021-10-13 10:32:32
    * version: 1.0.0
    */
    public function setRegions($ids_regions) {
        if (!is_array($ids_regions))
            $ids_regions = array();
        $row_to_insert = array();
        foreach ($ids_regions as $id_gszonevente_region)
            $row_to_insert[] = array($this->def['primary'] => $this->id, 'id_gszonevente_region' => (int) $id_gszonevente_region);
        Db::getInstance()->execute('
			DELETE FROM ' . _DB_PREFIX_ . 'gszonevente_region_warehouse
			WHERE ' . $this->def['primary'] . ' = ' . (int) $this->id);
        if ($row_to_insert)
            Db::getInstance()->insert('gszonevente_region_warehouse', $row_to_insert);
    }
}
