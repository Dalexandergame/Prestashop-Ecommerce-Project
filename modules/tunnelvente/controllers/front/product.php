<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__) . "/Front.php";

class TunnelVenteProductModuleFrontController extends Front {

    protected static $TEMPLATE = "product.tpl";

    public function init() {
        $this->page_name = 'product';
        parent::init();
        $this->display_column_left = false;
        $this->display_column_right = false;
        if ($this->ajax && $this->isXmlHttpRequest()) {

            if (Tools::isSubmit('product')) {
                $id_product = (int) Tools::getValue("product");

                $product = new Product($id_product, true, $this->context->language->id);
                $smarty = $this->context->smarty;
                if ($product) {

                    // Assign template vars related to the price and tax
                    $this->assignPriceAndTax($product);
                    
                    // Assign template vars related to the images
                    $this->assignImages($product);
                    // Assign attribute groups to the template
                    $this->assignAttributesGroups($product);

                    // Assign attributes combinations to the template
                    $this->assignAttributesCombinations($product);

                    $smarty->assign(array(
                        "product" => $product,
                        "myLittelEcosapin"=> ((int) $product->id == (int) Configuration::get('TUNNELVENTE_ID_PRODUCT_MYLITTELECOSAPIN'))? true:false,
                    ));
                } else {
                    $this->errors[] = Tools::displayError("erreur : produit introuvable !");
                }
            } else {
                $this->errors[] = Tools::displayError("erreur : Choisissez un produit !");
            }
            $return = array(
                'hasError' => !empty($this->errors),
                'errors' => $this->errors,
                'html' => $this->getHtmlProduct($product),
                'numStep' => 8,
                "myLittelEcosapin"=> ((int) Tools::getValue("product") == (int) Configuration::get('TUNNELVENTE_ID_PRODUCT_MYLITTELECOSAPIN'))? true:false,
                'sup' => $this->getValuesTunnelVent(),
            );
            die(Tools::jsonEncode($return));
        }
    }

    private function getHtmlProduct($product) {
        $js_tag = 'js_def';
        $this->context->smarty->assign($js_tag, $js_tag);
        // Pack management
        $pack_items = Pack::isPack($product->id) ? Pack::getItemTable($product->id, $this->context->language->id, true) : array();
        $this->context->smarty->assign('packItems', $pack_items);
        $this->context->smarty->assign('packs', Pack::getPacksTable($product->id, $this->context->language->id, true, 1));

        $this->context->smarty->assign(array(
            'allow_oosp' => $product->isAvailableWhenOutOfStock((int) $product->out_of_stock),
            'customizationFields' => $product->customizable ? $product->getCustomizationFields($this->context->language->id) : false,
            'jqZoomEnabled' => Configuration::get('PS_DISPLAY_JQZOOM'),
            'last_qties' => (int) Configuration::get('PS_LAST_QTIES'),
            'display_qties' => (int) Configuration::get('PS_DISPLAY_QTIES'),
            'content_only' => true,//test
            'static_token' => Tools::getToken(false),
            'token' => Tools::getToken(),
            'js_def' => Media::getJsDef(),
            'display_discount_price' => Configuration::get('PS_DISPLAY_DISCOUNT_PRICE'),
            'PS_CATALOG_MODE' => Configuration::get('PS_CATALOG_MODE'),
            'PS_STOCK_MANAGEMENT' => Configuration::get('PS_STOCK_MANAGEMENT'),
            'currency' => $this->context->currency,
            'tools' => new Tools(),
            'link' => new Link()
        ));

        $html = $this->context->smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/" . self::$TEMPLATE);
        $html .= $this->getJs($js_tag);
        return $html;
    }

    protected function getJs($js_tag) {
        $dom_available = extension_loaded('dom') ? true : false;
        $defer = (bool) Configuration::get('PS_JS_DEFER');
        $this->context->smarty->assign(array(
            $js_tag => Media::getJsDef(),
            'js_files' => $defer ? array_unique($this->js_files) : array(),
            'js_inline' => ($defer && $dom_available) ? Media::getInlineScript() : array()
        ));
        $javascript = $this->context->smarty->fetch(_PS_ALL_THEMES_DIR_ . 'javascript.tpl');
        $javascript .= $this->context->smarty->fetch(dirname(__FILE__) . "/../../views/templates/front/jsAttributes.tpl");
        return $javascript;
    }

    /**
     * Assign price and tax to the template
     */
    protected function assignPriceAndTax($product) {
        $id_customer = (isset($this->context->customer) ? (int) $this->context->customer->id : 0);
        $id_group = (int) Group::getCurrent()->id;
        $id_country = $id_customer ? (int) Customer::getCurrentCountry($id_customer) : (int) Tools::getCountry();

        $group_reduction = GroupReduction::getValueForProduct($product->id, $id_group);
        if ($group_reduction === false)
            $group_reduction = Group::getReduction((int) $this->context->cookie->id_customer) / 100;

        // Tax
        $tax = (float) $product->getTaxesRate(new Address((int) $this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
        $this->context->smarty->assign('tax_rate', $tax);

        $product_price_with_tax = Product::getPriceStatic($product->id, true, null, 6);
        if (Product::$_taxCalculationMethod == PS_TAX_INC)
            $product_price_with_tax = Tools::ps_round($product_price_with_tax, 2);
        $product_price_without_eco_tax = (float) $product_price_with_tax - $product->ecotax;

        $ecotax_rate = (float) Tax::getProductEcotaxRate($this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $ecotax_tax_amount = Tools::ps_round($product->ecotax, 2);
        if (Product::$_taxCalculationMethod == PS_TAX_INC && (int) Configuration::get('PS_TAX'))
            $ecotax_tax_amount = Tools::ps_round($ecotax_tax_amount * (1 + $ecotax_rate / 100), 2);

        $id_currency = (int) $this->context->cookie->id_currency;
        $id_product = (int) $product->id;
        $id_shop = $this->context->shop->id;

        $quantity_discounts = SpecificPrice::getQuantityDiscounts($id_product, $id_shop, $id_currency, $id_country, $id_group, null, true, (int) $this->context->customer->id);
        foreach ($quantity_discounts as &$quantity_discount) {
            if ($quantity_discount['id_product_attribute']) {
                $combination = new Combination((int) $quantity_discount['id_product_attribute']);
                $attributes = $combination->getAttributesName((int) $this->context->language->id);
                foreach ($attributes as $attribute)
                    $quantity_discount['attributes'] = $attribute['name'] . ' - ';
                $quantity_discount['attributes'] = rtrim($quantity_discount['attributes'], ' - ');
            }
            if ((int) $quantity_discount['id_currency'] == 0 && $quantity_discount['reduction_type'] == 'amount')
                $quantity_discount['reduction'] = Tools::convertPriceFull($quantity_discount['reduction'], null, Context::getContext()->currency);
        }

        $product_price = $product->getPrice(Product::$_taxCalculationMethod == PS_TAX_INC, false);
        $address = new Address($this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $this->context->smarty->assign(array(
            'quantity_discounts' => $this->formatQuantityDiscounts($quantity_discounts, $product_price, (float) $tax, $ecotax_tax_amount),
            'ecotax_tax_inc' => $ecotax_tax_amount,
            'ecotax_tax_exc' => Tools::ps_round($product->ecotax, 2),
            'ecotaxTax_rate' => $ecotax_rate,
            'productPriceWithoutEcoTax' => (float) $product_price_without_eco_tax,
            'group_reduction' => $group_reduction,
            'no_tax' => Tax::excludeTaxeOption() || !$product->getTaxesRate($address),
            'ecotax' => (!count($this->errors) && $product->ecotax > 0 ? Tools::convertPrice((float) $product->ecotax) : 0),
            'tax_enabled' => Configuration::get('PS_TAX'),
            'customer_group_without_tax' => Group::getPriceDisplayMethod($this->context->customer->id_default_group),
        ));
    }

    protected function formatQuantityDiscounts($specific_prices, $price, $tax_rate, $ecotax_amount) {
        foreach ($specific_prices as $key => &$row) {
            $row['quantity'] = &$row['from_quantity'];
            if ($row['price'] >= 0) { // The price may be directly set
                $cur_price = (Product::$_taxCalculationMethod == PS_TAX_EXC ? $row['price'] : $row['price'] * (1 + $tax_rate / 100)) + (float) $ecotax_amount;
                if ($row['reduction_type'] == 'amount')
                    $cur_price -= (Product::$_taxCalculationMethod == PS_TAX_INC ? $row['reduction'] : $row['reduction'] / (1 + $tax_rate / 100));
                else
                    $cur_price *= 1 - $row['reduction'];
                $row['real_value'] = $price - $cur_price;
            }
            else {
                if ($row['reduction_type'] == 'amount')
                    $row['real_value'] = Product::$_taxCalculationMethod == PS_TAX_INC ? $row['reduction'] : $row['reduction'] / (1 + $tax_rate / 100);
                else
                    $row['real_value'] = $row['reduction'] * 100;
            }
            $row['nextQuantity'] = (isset($specific_prices[$key + 1]) ? (int) $specific_prices[$key + 1]['from_quantity'] : - 1);
        }
        return $specific_prices;
    }

    /**
     * Assign template vars related to images
     */
    protected function assignImages($product) {
        $images = $product->getImages((int) $this->context->cookie->id_lang);
        $product_images = array();

        if (isset($images[0]))
            $this->context->smarty->assign('mainImage', $images[0]);
        foreach ($images as $k => $image) {
            if ($image['cover']) {
                $this->context->smarty->assign('mainImage', $image);
                $cover = $image;
                $cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($product->id . '-' . $image['id_image']) : $image['id_image']);
                $cover['id_image_only'] = (int) $image['id_image'];
            }
            $product_images[(int) $image['id_image']] = $image;
        }

        if (!isset($cover)) {
            if (isset($images[0])) {
                $cover = $images[0];
                $cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($product->id . '-' . $images[0]['id_image']) : $images[0]['id_image']);
                $cover['id_image_only'] = (int) $images[0]['id_image'];
            } else
                $cover = array(
                    'id_image' => $this->context->language->iso_code . '-default',
                    'legend' => 'No picture',
                    'title' => 'No picture'
                );
        }
        $size = Image::getSize(ImageType::getFormatedName('large'));
        $this->context->smarty->assign(array(
            'have_image' => (isset($cover['id_image']) && (int) $cover['id_image']) ? array((int) $cover['id_image']) : Product::getCover((int) Tools::getValue('id_product')),
            'cover' => $cover,
            'imgWidth' => (int) $size['width'],
            'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
            'largeSize' => Image::getSize(ImageType::getFormatedName('large')),
            'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
            'cartSize' => Image::getSize(ImageType::getFormatedName('cart')),
            'col_img_dir' => _PS_COL_IMG_DIR_));
        if (count($product_images))
            $this->context->smarty->assign('images', $product_images);
    }

    /**
     * Assign template vars related to attribute groups and colors
     */
    protected function assignAttributesGroups($product) {
        $colors = array();
        $groups = array();

        // @todo (RM) should only get groups and not all declination ?
        $attributes_groups = $product->getAttributesGroups($this->context->language->id);
        if (is_array($attributes_groups) && $attributes_groups) {
            $combination_images = $product->getCombinationImages($this->context->language->id);
            $combination_prices_set = array();
            foreach ($attributes_groups as $k => $row) {
                // Color management
                if (isset($row['is_color_group']) && $row['is_color_group'] && (isset($row['attribute_color']) && $row['attribute_color']) || (file_exists(_PS_COL_IMG_DIR_ . $row['id_attribute'] . '.jpg'))) {
                    $colors[$row['id_attribute']]['value'] = $row['attribute_color'];
                    $colors[$row['id_attribute']]['name'] = $row['attribute_name'];
                    if (!isset($colors[$row['id_attribute']]['attributes_quantity']))
                        $colors[$row['id_attribute']]['attributes_quantity'] = 0;
                    $colors[$row['id_attribute']]['attributes_quantity'] += (int) $row['quantity'];
                }
                if (!isset($groups[$row['id_attribute_group']]))
                    $groups[$row['id_attribute_group']] = array(
                        'group_name' => $row['group_name'],
                        'name' => $row['public_group_name'],
                        'group_type' => $row['group_type'],
                        'default' => -1,
                    );

                $groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = $row['attribute_name'];
                if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1)
                    $groups[$row['id_attribute_group']]['default'] = (int) $row['id_attribute'];
                if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']]))
                    $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
                $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int) $row['quantity'];

                $combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
                $combinations[$row['id_product_attribute']]['attributes'][] = (int) $row['id_attribute'];
                $combinations[$row['id_product_attribute']]['price'] = (float) $row['price'];

                // Call getPriceStatic in order to set $combination_specific_price
                if (!isset($combination_prices_set[(int) $row['id_product_attribute']])) {
                    Product::getPriceStatic((int) $product->id, false, $row['id_product_attribute'], 6, null, false, true, 1, false, null, null, null, $combination_specific_price);
                    $combination_prices_set[(int) $row['id_product_attribute']] = true;
                    $combinations[$row['id_product_attribute']]['specific_price'] = $combination_specific_price;
                }
                $combinations[$row['id_product_attribute']]['ecotax'] = (float) $row['ecotax'];
                $combinations[$row['id_product_attribute']]['weight'] = (float) $row['weight'];
                $combinations[$row['id_product_attribute']]['quantity'] = (int) $row['quantity'];
                $combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
                $combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
                $combinations[$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];
                if ($row['available_date'] != '0000-00-00') {
                    $combinations[$row['id_product_attribute']]['available_date'] = $row['available_date'];
                    $combinations[$row['id_product_attribute']]['date_formatted'] = Tools::displayDate($row['available_date']);
                } else
                    $combinations[$row['id_product_attribute']]['available_date'] = '';

                if (!isset($combination_images[$row['id_product_attribute']][0]['id_image']))
                    $combinations[$row['id_product_attribute']]['id_image'] = -1;
                else {
                    $combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int) $combination_images[$row['id_product_attribute']][0]['id_image'];
                    if ($row['default_on']) {
                        if (isset($this->context->smarty->tpl_vars['cover']->value))
                            $current_cover = $this->context->smarty->tpl_vars['cover']->value;

                        if (is_array($combination_images[$row['id_product_attribute']])) {
                            foreach ($combination_images[$row['id_product_attribute']] as $tmp)
                                if ($tmp['id_image'] == $current_cover['id_image']) {
                                    $combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int) $tmp['id_image'];
                                    break;
                                }
                        }

                        if ($id_image > 0) {
                            if (isset($this->context->smarty->tpl_vars['images']->value))
                                $product_images = $this->context->smarty->tpl_vars['images']->value;
                            if (isset($product_images) && is_array($product_images) && isset($product_images[$id_image])) {
                                $product_images[$id_image]['cover'] = 1;
                                $this->context->smarty->assign('mainImage', $product_images[$id_image]);
                                if (count($product_images))
                                    $this->context->smarty->assign('images', $product_images);
                            }
                            if (isset($this->context->smarty->tpl_vars['cover']->value))
                                $cover = $this->context->smarty->tpl_vars['cover']->value;
                            if (isset($cover) && is_array($cover) && isset($product_images) && is_array($product_images)) {
                                $product_images[$cover['id_image']]['cover'] = 0;
                                if (isset($product_images[$id_image]))
                                    $cover = $product_images[$id_image];
                                $cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($product->id . '-' . $id_image) : (int) $id_image);
                                $cover['id_image_only'] = (int) $id_image;
                                $this->context->smarty->assign('cover', $cover);
                            }
                        }
                    }
                }
            }

            // wash attributes list (if some attributes are unavailables and if allowed to wash it)
            if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && Configuration::get('PS_DISP_UNAVAILABLE_ATTR') == 0) {
                foreach ($groups as &$group)
                    foreach ($group['attributes_quantity'] as $key => &$quantity)
                        if ($quantity <= 0)
                            unset($group['attributes'][$key]);

                foreach ($colors as $key => $color)
                    if ($color['attributes_quantity'] <= 0)
                        unset($colors[$key]);
            }
            foreach ($combinations as $id_product_attribute => $comb) {
                $attribute_list = '';
                foreach ($comb['attributes'] as $id_attribute)
                    $attribute_list .= '\'' . (int) $id_attribute . '\',';
                $attribute_list = rtrim($attribute_list, ',');
                $combinations[$id_product_attribute]['list'] = $attribute_list;
            }

            $this->context->smarty->assign(array(
                'groups' => $groups,
                'colors' => (count($colors)) ? $colors : false,
                'combinations' => $combinations,
                'combinationImages' => $combination_images
            ));
        }
    }

    /**
     * Get and assign attributes combinations informations
     */
    protected function assignAttributesCombinations($product) {
        $attributes_combinations = Product::getAttributesInformationsByProduct($product->id);
        if (is_array($attributes_combinations) && count($attributes_combinations))
            foreach ($attributes_combinations as &$ac)
                foreach ($ac as &$val)
                    $val = str_replace(Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'), '_', Tools::link_rewrite(str_replace(array(',', '.'), '-', $val)));
        else
            $attributes_combinations = array();
        $this->context->smarty->assign(array(
            'attributesCombinations' => $attributes_combinations,
            'attribute_anchor_separator' => Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR')
                )
        );
    }

}
