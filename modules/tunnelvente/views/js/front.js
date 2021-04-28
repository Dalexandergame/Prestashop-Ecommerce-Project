/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2015 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */
$(function ($) {
    $('.thirdCol').data('calPrice', {
        prices: [],
        currency_sign: $('.thirdCol').data('currency'),
        selectorPrice: '.thirdCol .total_prix',
        isHasRecyclage: false,
        rabaisPriceBoulePromo: 65,
        rabaisPricePotPromo: 5,
        price: {
            sapin: 0,
            recyclage: 0,
            boule: 0,
            pot: 0,
            pied: 0,
        },
        setPriceSapin: function (price, reset) {
            if (reset == undefined)
                reset = false;

            this.price.sapin = price;

            if (reset) {
                this.price.recyclage = 0;
                this.price.boule = 0;
                this.price.pot = 0;
                this.price.pied = 0;
            }

            this.cal();
        },
        setPriceRecyclage: function (price, id_recyclage, reset) {
            if (reset == undefined)
                reset = false;

            this.price.recyclage = price;

            if (reset) {
                this.price.boule = 0;
                this.price.pot = 0;
            }

            if (parseInt(id_recyclage) != 0) {
                this.isHasRecyclage = true;
            } else {
                this.isHasRecyclage = false;
            }

            this.cal();
        },
        setPriceBoule: function (price, reset) {
            if (reset == undefined)
                reset = false;

            if (this.isHasRecyclage && parseFloat(price) > 0) {
                this.price.boule = parseFloat(price) - this.rabaisPriceBoulePromo;
            } else {
                this.price.boule = price;
            }

            if (reset) {
                this.price.pot = 0;
            }

            this.cal();
        },
        setPricePot: function (price, reset) {
            if (reset == undefined)
                reset = false;

            if (this.isHasRecyclage && parseFloat(price) > 0) {
                this.price.pot = parseFloat(price) - this.rabaisPricePotPromo;
            } else {
                this.price.pot = price;
            }

            if (reset) {
                //TODO: autre product
            }

            this.cal();
        },
        setPricePied: function (price, reset) {
            if (reset == undefined)
                reset = false;

            this.price.pied = price;

            if (reset) {
                this.price.recyclage = 0;
                this.price.boule = 0;
                this.price.pot = 0;
            }

            this.cal();
        },
        addAutreSapin: function () {
            var total = 0;

            $.each(this.price, function (k, v) {
                total += parseFloat(v);
            });

            this.prices.push(total);
            this.setPriceSapin(0, true);
            this.cal();
        },
        cal: function () {
            var total = 0;

            $.each(this.prices, function (k, v) {
                total += parseFloat(v);
            });

            $.each(this.price, function (k1, v1) {
                total += parseFloat(v1);
            });

            $(this.selectorPrice).text(parseFloat(total).toFixed(2).toString().replace('.', ',') + ' ' + this.currency_sign);
        },
    });

    $('#form_type ul input').change(function () {
        alert('change');

        if ($('input#type_13').is(":checked")) {
            $('.cercle_taille').addClass("sap-coupe");
            alert('okay');
        } else {
            $('.cercle_taille').removeClass("sap-coupe");
        }
    });

    $(document).on("click", ".alert.alert-danger", null, function () {
        $(".alert.alert-danger").alert('close');
    });
});
