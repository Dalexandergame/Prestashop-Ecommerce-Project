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

import createOrderMap from './create-order-map';

const $ = window.$;

/**
 * Manipulates UI of Planning Delivery block in Order creation page
 */
export default class PlanningDeliveryRenderer {
    constructor() {
        this.$planningDeliveryContainer = $(createOrderMap.planningDeliveryBlock);
        this.$planningDeliveryForm = $(createOrderMap.planningDeliveryForm);
    }

    /**
     * @param {Object} planningDelivery
     * @param {Boolean} emptyCart
     */
    renderPlanningDelivery() {
        this._displayPlanningDelivery();
    }

    /**
     * Show form block with planning delivery date
     *
     * @param planningDelivery
     *
     * @private
     */
    _displayPlanningDelivery() {
        this._showPlanningDeliveryContainer();
        this._showPlanningDeliveryForm();
    }

    /**
     * Show whole planning Delivery container
     *
     * @private
     */
    _showPlanningDeliveryContainer() {
        this.$planningDeliveryContainer.removeClass('d-none');
    }

    /**
     * Hide whole planning Delivery container
     *
     * @private
     */
    _hidePlanningDeliveryContainer() {
        this.$planningDeliveryContainer.addClass('d-none');
    }

    /**
     * Show form block
     *
     * @private
     */
    _showPlanningDeliveryForm() {
        this.$planningDeliveryForm.removeClass('d-none');
    }

    /**
     * Hide form block
     *
     * @private
     */
    _hidePlanningDeliveryForm() {
        this.$planningDeliveryForm.addClass('d-none');
    }
}
