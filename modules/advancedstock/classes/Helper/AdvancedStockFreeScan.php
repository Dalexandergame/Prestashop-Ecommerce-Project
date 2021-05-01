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
require_once _PS_MODULE_DIR_ . 'advancedstock/classes/Model/AdvancedStockStockMovements.php';

class AdvancedStockFreeScan
{
    const ACTION_RECEPTION = 'reception';
    const ACTION_SHIPPING = 'shipping';
    const ACTION_STOCK_CONTROL = 'stock_control';

    public static $redirect_errors;
    public static $redirect_confirmations;

    public static function getActionsOptions()
    {
        return array(
            array('code' => self::ACTION_RECEPTION, 'action' => 'Reception'),
            array('code' => self::ACTION_SHIPPING, 'action' => 'Shipping'),
            array('code' => self::ACTION_STOCK_CONTROL, 'action' => 'Stock Control')
        );
    }

    public static function getActionName($actionCode)
    {
        $actions = self::getActionsOptions();
        foreach ($actions as $action) {
            if ($action['code'] === $actionCode) {
                return $action['action'];
            }
        }

        return '';
    }

    /**
     * @param string $barcode
     * @param int $warehouseId
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getProductInformation($barcode, $warehouseId)
    {
        $productIds = self::findProductIdsFromBarcode($barcode);

        if ($productIds === null) {
            throw new \Exception('No product found with barcode ' . $barcode);
        }

        $data = array();
        $data['product_id'] = $productIds['product_id'];
        $data['product_attribute_id'] = $productIds['product_attribute_id'];
        $data['barcode'] = $barcode;
        $data['name'] = Product::getProductName(
            $productIds['product_id'],
            $productIds['product_attribute_id'],
            Context::getContext()->language->id
        );
        $data['qty'] = AdvancedStockWarehousesProducts::getPhysicalQtyForProduct(
            $productIds['product_id'],
            $productIds['product_attribute_id'],
            $warehouseId
        );
        $data['image_url'] = self::getImageUrl($productIds['product_id']);

        $product = new Product($productIds['product_id']);
        if ($productIds['product_attribute_id'] !== 0) {
            $data['sku'] = self::getAttributeReference($productIds['product_id'], $productIds['product_attribute_id']);
        } else {
            $data['sku'] = $product->reference;
        }

        return $data;
    }

    /**
     * @param string $barcode
     * @return array|null
     * @throws PrestaShopDatabaseException
     */
    public static function findProductIdsFromBarcode($barcode)
    {
        $query = new DbQuery();
        $query->select('p.id_product, pa.id_product_attribute')
            ->from(Product::$definition['table'], 'p')
            ->leftJoin('product_attribute', 'pa', 'pa.id_product = p.id_product')
            ->where(
                'p.isbn = "' . pSQL($barcode) . '" OR ' .
                'p.ean13 = "' . pSQL($barcode) . '" OR ' .
                'p.upc = "' . pSQL($barcode) . '" OR ' .
                'pa.isbn = "' . pSQL($barcode) . '" OR ' .
                'pa.ean13 = "' . pSQL($barcode) . '" OR ' .
                'pa.upc = "' . pSQL($barcode) . '"'
            );

        $ids = Db::getInstance()->executeS($query);

        if (!$ids) {
            return null;
        }

        $res = array();
        $ids = $ids[0];
        $res['product_id'] = (int)$ids['id_product'];
        $res['product_attribute_id'] = (int)$ids['id_product_attribute'];

        return $res;
    }

    /**
     * @param array $data
     * @param int $warehouseId
     * @param string $action
     * @param string $label
     * @throws Exception
     */
    public static function applyChanges($data, $warehouseId, $action, $label)
    {
        foreach ($data as $productId => $attribute) {
            foreach ($attribute as $attributeId => $scannedQty) {
                if ($scannedQty <= 0) {
                    continue;
                }
                switch ($action) {
                    case AdvancedStockFreeScan::ACTION_SHIPPING:
                        $from = $warehouseId;
                        $to = null;
                        $qty = $scannedQty;
                        $type = AdvancedStockStockMovements::TYPE_SHIPMENT;
                        break;
                    case AdvancedStockFreeScan::ACTION_RECEPTION:
                        $from = null;
                        $to = $warehouseId;
                        $qty = $scannedQty;
                        $type = AdvancedStockStockMovements::TYPE_RECEPTION;
                        break;
                    case AdvancedStockFreeScan::ACTION_STOCK_CONTROL:
                        $type = AdvancedStockStockMovements::TYPE_ADJUSTMENT;
                        $expectedQty = AdvancedStockWarehousesProducts::getPhysicalQtyForProduct(
                            $productId,
                            $attributeId,
                            $warehouseId
                        );
                        if ($scannedQty < $expectedQty) {
                            $from = $warehouseId;
                            $to = null;
                        } elseif ($scannedQty > $expectedQty) {
                            $from = null;
                            $to = $warehouseId;
                        } else {
                            continue 2;
                        }
                        $qty = abs($scannedQty - $expectedQty);
                        break;
                    default:
                        continue 2;
                }
                AdvancedStockStockMovements::create(
                    $productId,
                    $attributeId,
                    $from,
                    $to,
                    $qty,
                    $type,
                    $label,
                    (int) Context::getContext()->employee->id
                );
            }
        }
    }

    /**
     * @param int $productId
     * @return null|string
     */
    private static function getImageUrl($productId)
    {
        $image = Product::getCover($productId);

        if (!$image) {
            return null;
        }
        $imageId = $image['id_image'];
        $imageObj = new Image($imageId);
        return _PS_BASE_URL_._THEME_PROD_DIR_.$imageObj->getExistingImgPath(). '.jpg';
    }

    /**
     * @param int $productId
     * @param int $productAttributeId
     * @return null|string
     * @throws PrestaShopDatabaseException
     */
    public static function getAttributeReference($productId, $productAttributeId)
    {
        $query = new DbQuery();
        $query->select('pa.reference')
            ->from(Product::$definition['table'], 'p')
            ->leftJoin('product_attribute', 'pa', 'pa.id_product = p.id_product')
            ->where('p.id_product = ' . (int)$productId)
            ->where('pa.id_product_attribute = ' . (int)$productAttributeId);

        $res = Db::getInstance()->executeS($query);

        if (!$res) {
            return null;
        }

        return $res[0]['reference'];
    }
}
