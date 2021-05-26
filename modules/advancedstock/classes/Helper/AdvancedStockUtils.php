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

class AdvancedStockUtils
{
    /**
     * @param $name
     * @param $url
     * @return mixed
     */
    public static function getLink($name, $url)
    {
        $context = Context::getContext();
        $tpl = $context->smarty->createTemplate(_PS_MODULE_DIR_.'advancedstock/views/templates/admin/link.tpl', $context->smarty);
        $tpl->assign('name', $name);
        $tpl->assign('linkUrl', $url);

        return $tpl->fetch();
    }

    public static function getStockMovementIcon($icon)
    {
        $context = Context::getContext();
        $path = Context::getContext()->shop->getBaseURL(true).'modules/advancedstock/views/img/';
        $tpl = $context->smarty->createTemplate(_PS_MODULE_DIR_.'advancedstock/views/templates/admin/stock_movement/stock_movement.tpl');
        $tpl->assign('icon', $icon);
        $tpl->assign('path', $path);

        return $tpl->fetch();
    }
}
