<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
use PrestaShop\PrestaShop\Adapter\ServiceLocator;

/**
 * Represents quantities available
 * It is either synchronized with Stock or manualy set by the seller.
 *
 * @since 1.5.0
 */
class StockAvailableCore extends ObjectModel
{
    /** @var int identifier of the current product */
    public $id_product;

    /** @var int identifier of product attribute if necessary */
    public $id_product_attribute;

    /** @var int the shop associated to the current product and corresponding quantity */
    public $id_shop;

    /** @var int the group shop associated to the current product and corresponding quantity */
    public $id_shop_group;

    /** @var int the quantity available for sale */
    public $quantity = 0;

    /** @var bool determine if the available stock value depends on physical stock */
    public $depends_on_stock = false;

    /** @var bool determine if a product is out of stock - it was previously in Product class */
    public $out_of_stock = false;

    /** @var string the location of the stock for this product / combination */
    public $location = '';

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'stock_available',
        'primary' => 'id_stock_available',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_product_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_shop_group' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'quantity' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'depends_on_stock' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'out_of_stock' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'location' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
        ],
    ];

    /**
     * @see ObjectModel::$webserviceParameters
     */
    protected $webserviceParameters = [
        'fields' => [
            'id_product' => ['xlink_resource' => 'products'],
            'id_product_attribute' => ['xlink_resource' => 'combinations'],
            'id_shop' => ['xlink_resource' => 'shops'],
            'id_shop_group' => ['xlink_resource' => 'shop_groups'],
        ],
        'hidden_fields' => [
        ],
        'objectMethods' => [
            'add' => 'addWs',
            'update' => 'updateWs',
        ],
    ];

    /**
     * For a given {id_product, id_product_attribute and id_shop}, gets the stock available id associated.
     *
     * @param int $id_product
     * @param int $id_product_attribute Optional
     * @param int $id_shop Optional
     *
     * @return int
     */
    public function updateWs()
    {
        if ($this->depends_on_stock) {
            return WebserviceRequest::getInstance()->setError(500, $this->trans('You cannot update the available stock when it depends on stock.', [], 'Admin.Catalog.Notification'), 133);
        }

        return $this->update();
    }

    public static function getStockAvailableIdByProductId($id_product, $id_product_attribute = null, $id_shop = null)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        $query = new DbQuery();
        $query->select('id_stock_available');
        $query->from('stock_available');
        $query->where('id_product = ' . (int) $id_product);

        if ($id_product_attribute !== null) {
            $query->where('id_product_attribute = ' . (int) $id_product_attribute);
        }

        $query = StockAvailable::addSqlShopRestriction($query, $id_shop);

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * For a given id_product, synchronizes StockAvailable::quantity with Stock::usable_quantity.
     *
     * @param int $id_product
     */
    public static function synchronize($id_product, $order_id_shop = null)
    {
        if (empty($_GET['cron-job'])) {
            return true;
        }

        // todo hacking stock
        $today = new \DateTime("now");
        $year  = $today->format("Y");
        $month = $today->format("m");

        // années d'acivité actuel
        $date_activity_start = $month > 6 ? "$year-07-01 00:00:00" : (intval($year) - 1) . "-07-01 00:00:00";
        $date_activity_end   = $month >= 6 ? (intval($year) + 1) . "-06-30 00:00:00" : "$year-06-30 00:00:00";

        $sql = "
            update ps_stock s
            set usable_quantity = GREATEST(physical_quantity - ifnull(
                (SELECT SUM(od.product_quantity) as qty_delivered
                 FROM ps_orders as o
                          JOIN ps_order_detail as od ON o.id_order = od.id_order
                          LEFT JOIN ps_warehouse_carrier as wc on wc.id_carrier = o.id_carrier
                 WHERE od.product_id = s.id_product
                   AND od.product_attribute_id = s.id_product_attribute
                   AND o.current_state in (2, 5, 10, 12, 18, 20, 21, 22, 23, 24, 29, 33, 34, 35)
                   AND o.date_add between '$date_activity_start' and '$date_activity_end'
                   AND wc.id_warehouse = s.id_warehouse
                ), 0), 0)
        ";
        $result = Db::getInstance()->execute($sql);
        die("synchronize result: $result");
    }

    /**
     * For a given id_product, sets if stock available depends on stock.
     *
     * @param int $id_product
     * @param int $depends_on_stock Optional : true by default
     * @param int $id_shop Optional : gets context by default
     */
    public static function setProductDependsOnStock($id_product, $depends_on_stock = true, $id_shop = null, $id_product_attribute = 0)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        $existing_id = StockAvailable::getStockAvailableIdByProductId((int) $id_product, (int) $id_product_attribute, $id_shop);
        if ($existing_id > 0) {
            Db::getInstance()->update('stock_available', [
                'depends_on_stock' => (int) $depends_on_stock,
            ], 'id_stock_available = ' . (int) $existing_id);
        } else {
            $params = [
                'depends_on_stock' => (int) $depends_on_stock,
                'id_product' => (int) $id_product,
                'id_product_attribute' => (int) $id_product_attribute,
            ];

            StockAvailable::addSqlShopParams($params, $id_shop);

            Db::getInstance()->insert('stock_available', $params);
        }

        // depends on stock.. hence synchronizes
        if ($depends_on_stock) {
            StockAvailable::synchronize($id_product);
        }
    }

    /**
     * For a given id_product, sets if product is available out of stocks.
     *
     * @param int $id_product
     * @param int $out_of_stock Optional false by default
     * @param int $id_shop Optional gets context by default
     */
    public static function setProductOutOfStock($id_product, $out_of_stock = false, $id_shop = null, $id_product_attribute = 0)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        $existing_id = (int) StockAvailable::getStockAvailableIdByProductId((int) $id_product, (int) $id_product_attribute, $id_shop);

        if ($existing_id > 0) {
            Db::getInstance()->update(
                'stock_available',
                ['out_of_stock' => (int) $out_of_stock],
                'id_product = ' . (int) $id_product .
                (($id_product_attribute) ? ' AND id_product_attribute = ' . (int) $id_product_attribute : '') .
                StockAvailable::addSqlShopRestriction(null, $id_shop)
            );
        } else {
            $params = [
                'out_of_stock' => (int) $out_of_stock,
                'id_product' => (int) $id_product,
                'id_product_attribute' => (int) $id_product_attribute,
            ];

            StockAvailable::addSqlShopParams($params, $id_shop);
            Db::getInstance()->insert('stock_available', $params, false, true, Db::ON_DUPLICATE_KEY);
        }
    }

    /**
     * @param int $id_product
     * @param string $location
     * @param int $id_shop Optional
     * @param int $id_product_attribute Optional
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     */
    public static function setLocation($id_product, $location, $id_shop = null, $id_product_attribute = 0)
    {
        if (
            false === Validate::isUnsignedId($id_product)
            || (((false === Validate::isUnsignedId($id_shop)) && (null !== $id_shop)))
            || (false === Validate::isUnsignedId($id_product_attribute))
            || (false === Validate::isString($location))
        ) {
            $serializedInputData = [
                'id_product' => $id_product,
                'id_shop' => $id_shop,
                'id_product_attribute' => $id_product_attribute,
                'location' => $location,
            ];

            throw new \InvalidArgumentException(sprintf('Could not update location as input data is not valid: %s', json_encode($serializedInputData)));
        }

        $existing_id = StockAvailable::getStockAvailableIdByProductId($id_product, $id_product_attribute, $id_shop);

        if ($existing_id > 0) {
            Db::getInstance()->update(
                'stock_available',
                ['location' => pSQL($location)],
                'id_product = ' . (int) $id_product .
                (($id_product_attribute) ? ' AND id_product_attribute = ' . (int) $id_product_attribute : '') .
                StockAvailable::addSqlShopRestriction(null, $id_shop)
            );
        } else {
            $params = [
                'location' => pSQL($location),
                'id_product' => (int) $id_product,
                'id_product_attribute' => (int) $id_product_attribute,
            ];

            StockAvailable::addSqlShopParams($params, $id_shop);
            Db::getInstance()->insert('stock_available', $params, false, true, Db::ON_DUPLICATE_KEY);
        }
    }

    /**
     * For a given id_product and id_product_attribute, gets its stock available.
     *
     * @param int $id_product
     * @param int $id_product_attribute Optional
     * @param int $id_shop Optional : gets context by default
     *
     * @param int $id_warehouse
     * @return int Quantity
     */
    public static function getQuantityAvailableByProduct($id_product = null, $id_product_attribute = null, $id_shop = null, $id_warehouse = null)
    {
        // if null, it's a product without attributes
        if ($id_product_attribute === null) {
            $id_product_attribute = 0;
        }

        if ($id_product === null && Tools::getValue('product_id')) {
            $id_product = Tools::getValue('product_id');
        }

        $query = 'SELECT IFNULL(stock.quantity, 0) as quantity
                    FROM ' . _DB_PREFIX_ . 'product p
                    ' . Product::sqlStock('p', $id_product_attribute, true, null, $id_warehouse) . '
                    WHERE p.id_product = ' . $id_product;

        $result = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

        return $result;
    }

    /**
     * Upgrades total_quantity_available after having saved.
     *
     * @see ObjectModel::add()
     */
    public function add($autodate = true, $null_values = false)
    {
        if (!$result = parent::add($autodate, $null_values)) {
            return false;
        }

        $result &= $this->postSave();

        return $result;
    }

    /**
     * Upgrades total_quantity_available after having update.
     *
     * @see ObjectModel::update()
     */
    public function update($null_values = false)
    {
        if (!$result = parent::update($null_values)) {
            return false;
        }

        $result &= $this->postSave();

        return $result;
    }

    /**
     * Upgrades total_quantity_available after having saved.
     *
     * @see StockAvailableCore::update()
     * @see StockAvailableCore::add()
     */
    public function postSave()
    {
        if ($this->id_product_attribute == 0) {
            return true;
        }

        $id_shop = (Shop::getContext() != Shop::CONTEXT_GROUP && $this->id_shop ? $this->id_shop : null);

        if (!Configuration::get('PS_DISP_UNAVAILABLE_ATTR')) {
            $combination = new Combination((int) $this->id_product_attribute);
            if ($colors = $combination->getColorsAttributes()) {
                $product = new Product((int) $this->id_product);
                foreach ($colors as $color) {
                    if ($product->isColorUnavailable((int) $color['id_attribute'], (int) $this->id_shop)) {
                        Tools::clearColorListCache($product->id);

                        break;
                    }
                }
            }
        }

        $total_quantity = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            '
			SELECT SUM(quantity) as quantity
			FROM ' . _DB_PREFIX_ . 'stock_available
			WHERE id_product = ' . (int) $this->id_product . '
			AND id_product_attribute <> 0 ' .
            StockAvailable::addSqlShopRestriction(null, $id_shop)
        );
        $this->setQuantity($this->id_product, 0, $total_quantity, $id_shop, false);

        return true;
    }

    /**
     * For a given id_product and id_product_attribute updates the quantity available
     * If $avoid_parent_pack_update is true, then packs containing the given product won't be updated.
     *
     * @param int $id_product
     * @param int $id_product_attribute Optional
     * @param int $delta_quantity The delta quantity to update
     * @param int $id_shop Optional
     * @param bool $add_movement Optional
     * @param array $params Optional
     */
    public static function updateQuantity($id_product, $id_product_attribute, $delta_quantity, $id_shop = null, $add_movement = false, $params = [])
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }
        $product = new Product((int) $id_product);
        if (!Validate::isLoadedObject($product)) {
            return false;
        }

        $stockManager = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\Stock\\StockManager');
        $stockManager->updateQuantity($product, $id_product_attribute, $delta_quantity, $id_shop, $add_movement, $params);

        return true;
    }

    /**
     * For a given id_product and id_product_attribute sets the quantity available.
     *
     * @param $id_product
     * @param $id_product_attribute
     * @param $quantity
     * @param null $id_shop
     * @param bool $add_movement
     *
     * @return bool
     */
    public static function setQuantity($id_product, $id_product_attribute, $quantity, $id_shop = null, $add_movement = true)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }
        $context = Context::getContext();
        // if there is no $id_shop, gets the context one
        if ($id_shop === null && Shop::getContext() != Shop::CONTEXT_GROUP) {
            $id_shop = (int) $context->shop->id;
        }
        $depends_on_stock = StockAvailable::dependsOnStock($id_product);
        //Try to set available quantity if product does not depend on physical stock
        if (!$depends_on_stock) {
            $stockManager = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\Stock\\StockManager');

            $id_stock_available = (int) StockAvailable::getStockAvailableIdByProductId($id_product, $id_product_attribute, $id_shop);
            if ($id_stock_available) {
                $stock_available = new StockAvailable($id_stock_available);

                $deltaQuantity = -1 * ((int) $stock_available->quantity - (int) $quantity);

                $stock_available->quantity = (int) $quantity;
                $stock_available->update();

                if (true === $add_movement && 0 != $deltaQuantity) {
                    $stockManager->saveMovement($id_product, $id_product_attribute, $deltaQuantity);
                }
            } else {
                $out_of_stock = StockAvailable::outOfStock($id_product, $id_shop);
                $stock_available = new StockAvailable();
                $stock_available->out_of_stock = (int) $out_of_stock;
                $stock_available->id_product = (int) $id_product;
                $stock_available->id_product_attribute = (int) $id_product_attribute;
                $stock_available->quantity = (int) $quantity;
                if ($id_shop === null) {
                    $shop_group = Shop::getContextShopGroup();
                } else {
                    $shop_group = new ShopGroup((int) Shop::getGroupFromShop((int) $id_shop));
                }
                // if quantities are shared between shops of the group
                if ($shop_group->share_stock) {
                    $stock_available->id_shop = 0;
                    $stock_available->id_shop_group = (int) $shop_group->id;
                } else {
                    $stock_available->id_shop = (int) $id_shop;
                    $stock_available->id_shop_group = 0;
                }
                $stock_available->add();

                if (true === $add_movement && 0 != $quantity) {
                    $stockManager->saveMovement($id_product, $id_product_attribute, (int) $quantity);
                }
            }

            Hook::exec(
                'actionUpdateQuantity',
                [
                    'id_product' => $id_product,
                    'id_product_attribute' => $id_product_attribute,
                    'quantity' => $stock_available->quantity,
                ]
            );
        }
        Cache::clean('StockAvailable::getQuantityAvailableByProduct_' . (int) $id_product . '*');
    }

    /**
     * Removes a given product from the stock available.
     *
     * @param int $id_product
     * @param int|null $id_product_attribute Optional
     * @param Shop|null $shop Shop id or shop object Optional
     *
     * @return bool
     */
    public static function removeProductFromStockAvailable($id_product, $id_product_attribute = null, $shop = null)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        if (Shop::getContext() == SHOP::CONTEXT_SHOP) {
            if (Shop::getContextShopGroup()->share_stock == 1) {
                $pa_sql = '';
                if ($id_product_attribute !== null) {
                    $pa_sql = '_attribute';
                    $id_product_attribute_sql = $id_product_attribute;
                } else {
                    $id_product_attribute_sql = $id_product;
                }

                if ((int) Db::getInstance()->getValue('SELECT COUNT(*)
						FROM ' . _DB_PREFIX_ . 'product' . $pa_sql . '_shop
						WHERE id_product' . $pa_sql . '=' . (int) $id_product_attribute_sql . '
							AND id_shop IN (' . implode(',', array_map('intval', Shop::getContextListShopID(SHOP::SHARE_STOCK))) . ')')) {
                    return true;
                }
            }
        }

        $res = Db::getInstance()->execute('
		DELETE FROM ' . _DB_PREFIX_ . 'stock_available
		WHERE id_product = ' . (int) $id_product .
        ($id_product_attribute ? ' AND id_product_attribute = ' . (int) $id_product_attribute : '') .
        StockAvailable::addSqlShopRestriction(null, $shop));

        if ($id_product_attribute) {
            if ($shop === null || !Validate::isLoadedObject($shop)) {
                $shop_datas = [];
                StockAvailable::addSqlShopParams($shop_datas);
                $id_shop = (int) $shop_datas['id_shop'];
            } else {
                $id_shop = (int) $shop->id;
            }

            $stock_available = new StockAvailable();
            $stock_available->id_product = (int) $id_product;
            $stock_available->id_product_attribute = (int) $id_product_attribute;
            $stock_available->id_shop = (int) $id_shop;
            $stock_available->postSave();
        }

        Cache::clean('StockAvailable::getQuantityAvailableByProduct_' . (int) $id_product . '*');

        return $res;
    }

    /**
     * Removes all product quantities from all a group of shops
     * If stocks are shared, remoe all old available quantities for all shops of the group
     * Else remove all available quantities for the current group.
     *
     * @param ShopGroup $shop_group the ShopGroup object
     */
    public static function resetProductFromStockAvailableByShopGroup(ShopGroup $shop_group)
    {
        if ($shop_group->share_stock) {
            $shop_list = Shop::getShops(false, $shop_group->id, true);
        }

        if (count($shop_list) > 0) {
            $id_shops_list = implode(', ', $shop_list);

            return Db::getInstance()->update('stock_available', ['quantity' => 0], 'id_shop IN (' . $id_shops_list . ')');
        } else {
            return Db::getInstance()->update('stock_available', ['quantity' => 0], 'id_shop_group = ' . $shop_group->id);
        }
    }

    /**
     * For a given product, tells if it depends on the physical (usable) stock.
     *
     * @param int $id_product
     * @param int $id_shop Optional : gets context if null @see Context::getContext()
     *
     * @return bool : depends on stock @see $depends_on_stock
     */
    public static function dependsOnStock($id_product, $id_shop = null)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        $query = new DbQuery();
        $query->select('depends_on_stock');
        $query->from('stock_available');
        $query->where('id_product = ' . (int) $id_product);
        $query->where('id_product_attribute = 0');

        $query = StockAvailable::addSqlShopRestriction($query, $id_shop);

        return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * For a given product, get its "out of stock" flag.
     *
     * @param int $id_product
     * @param int $id_shop Optional : gets context if null @see Context::getContext()
     *
     * @return bool : depends on stock @see $depends_on_stock
     */
    public static function outOfStock($id_product, $id_shop = null)
    {
        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }

        $query = new DbQuery();
        $query->select('out_of_stock');
        $query->from('stock_available');
        $query->where('id_product = ' . (int) $id_product);
        $query->where('id_product_attribute = 0');

        $query = StockAvailable::addSqlShopRestriction($query, $id_shop);

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * @param int $id_product
     * @param int id_product_attribute Optional
     * @param int $id_shop Optional
     *
     * @return bool|string
     */
    public static function getLocation($id_product, $id_product_attribute = null, $id_shop = null)
    {
        $id_product = (int) $id_product;

        if (null === $id_product_attribute) {
            $id_product_attribute = 0;
        } else {
            $id_product_attribute = (int) $id_product_attribute;
        }

        $query = new DbQuery();
        $query->select('location');
        $query->from('stock_available');
        $query->where('id_product = ' . $id_product);
        $query->where('id_product_attribute = ' . $id_product_attribute);

        $query = StockAvailable::addSqlShopRestriction($query, $id_shop);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * Add an sql restriction for shops fields - specific to StockAvailable.
     *
     * @param DbQuery|string|null $sql Reference to the query object
     * @param Shop|int|null $shop Optional : The shop ID
     * @param string|null $alias Optional : The current table alias
     *
     * @return string|DbQuery DbQuery object or the sql restriction string
     */
    public static function addSqlShopRestriction($sql = null, $shop = null, $alias = null)
    {
        $context = Context::getContext();

        if (!empty($alias)) {
            $alias .= '.';
        }

        // if there is no $id_shop, gets the context one
        // get shop group too
        if ($shop === null || $shop === $context->shop->id) {
            if (Shop::getContext() == Shop::CONTEXT_GROUP) {
                $shop_group = Shop::getContextShopGroup();
            } else {
                $shop_group = $context->shop->getGroup();
            }
            $shop = $context->shop;
        } elseif (is_object($shop)) {
            /** @var Shop $shop */
            $shop_group = $shop->getGroup();
        } else {
            $shop = new Shop($shop);
            $shop_group = $shop->getGroup();
        }

        // if quantities are shared between shops of the group
        if ($shop_group->share_stock) {
            if (is_object($sql)) {
                $sql->where(pSQL($alias) . 'id_shop_group = ' . (int) $shop_group->id);
                $sql->where(pSQL($alias) . 'id_shop = 0');
            } else {
                $sql = ' AND ' . pSQL($alias) . 'id_shop_group = ' . (int) $shop_group->id . ' ';
                $sql .= ' AND ' . pSQL($alias) . 'id_shop = 0 ';
            }
        } else {
            if (is_object($sql)) {
                $sql->where(pSQL($alias) . 'id_shop = ' . (int) $shop->id);
                $sql->where(pSQL($alias) . 'id_shop_group = 0');
            } else {
                $sql = ' AND ' . pSQL($alias) . 'id_shop = ' . (int) $shop->id . ' ';
                $sql .= ' AND ' . pSQL($alias) . 'id_shop_group = 0 ';
            }
        }

        return $sql;
    }

    /**
     * Add sql params for shops fields - specific to StockAvailable.
     *
     * @param array $params Reference to the params array
     * @param int $id_shop Optional : The shop ID
     */
    public static function addSqlShopParams(&$params, $id_shop = null)
    {
        $context = Context::getContext();
        $group_ok = false;

        // if there is no $id_shop, gets the context one
        // get shop group too
        if ($id_shop === null) {
            if (Shop::getContext() == Shop::CONTEXT_GROUP) {
                $shop_group = Shop::getContextShopGroup();
            } else {
                $shop_group = $context->shop->getGroup();
                $id_shop = $context->shop->id;
            }
        } else {
            $shop = new Shop($id_shop);
            $shop_group = $shop->getGroup();
        }

        // if quantities are shared between shops of the group
        if ($shop_group->share_stock) {
            $params['id_shop_group'] = (int) $shop_group->id;
            $params['id_shop'] = 0;

            $group_ok = true;
        } else {
            $params['id_shop_group'] = 0;
        }

        // if no group specific restriction, set simple shop restriction
        if (!$group_ok) {
            $params['id_shop'] = (int) $id_shop;
        }
    }

    /**
     * Copies stock available content table.
     *
     * @param int $src_shop_id
     * @param int $dst_shop_id
     *
     * @return bool
     */
    public static function copyStockAvailableFromShopToShop($src_shop_id, $dst_shop_id)
    {
        if (!$src_shop_id || !$dst_shop_id) {
            return false;
        }

        $query = '
			INSERT INTO ' . _DB_PREFIX_ . 'stock_available
			(
				id_product,
				id_product_attribute,
				id_shop,
				id_shop_group,
				quantity,
				depends_on_stock,
				out_of_stock,
				location
			)
			(
				SELECT id_product, id_product_attribute, ' . (int) $dst_shop_id . ', 0, quantity, depends_on_stock, out_of_stock, location
				FROM ' . _DB_PREFIX_ . 'stock_available
				WHERE id_shop = ' . (int) $src_shop_id .
            ')';

        return Db::getInstance()->execute($query);
    }
}
