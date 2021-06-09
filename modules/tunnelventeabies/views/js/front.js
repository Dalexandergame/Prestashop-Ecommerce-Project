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
    $('.priceCalcContainer').data('calPrice', {
        prices: [],
        currency_sign: $('.priceCalcContainer').data('currency'),
        selectorPrice: '.list-tunnel .total_prix',
        selectorOverview : '.overview-tunnel-container',
        recapOverview: '.final-recap-container',
        isHasRecyclage: false,
        rabaisPriceBoulePromo: 65,
        rabaisPricePotPromo: 5,
        price: {
            sapin: {
                name: '',
                price: 0
            },
            recyclage: {
                name: '',
                price: 0
            },
            boule: {
                name: '',
                price: 0
            },
            pot: {
                name: '',
                price: 0
            },
            pied: {
                name: '',
                price: 0
            },
        },
        setPriceSapin: function (price, name, lang,reset) {
            if (reset == undefined)
                reset = false;
            this.price.sapin.price = price;
            if(lang=="fr")
            {
                this.price.sapin.name = 'Sapin ' + name;
            }
            else if(lang=="en")
            {
                this.price.sapin.name = 'Fir ' + name;
            }
            else if(lang=="de")
            {
                this.price.sapin.name = 'Tanne ' + name;
            }


            if (reset) {
                this.price.recyclage.price = 0;
                this.price.boule.price = 0;
                this.price.pot.price = 0;
                this.price.pied.price = 0;
            }

            this.cal(false);
        },
        setPriceRecyclage: function (price, id_recyclage, name, reset) {
            if (reset == undefined)
                reset = false;

            this.price.recyclage.price = price;
            this.price.recyclage.name = name;

            if (reset) {
                this.price.boule.price = 0;
                this.price.pot.price = 0;
            }

            if (parseInt(id_recyclage) != 0) {
                this.isHasRecyclage = true;
            } else {
                this.isHasRecyclage = false;
            }

            this.cal(true);
        },
        unsetPriceRecyclage: function (price, name, reset) {
            if (reset == undefined)
                reset = false;

            this.isHasRecyclage = false;

            this.price.recyclage.price = 0;
            this.price.recyclage.name = '';

            if (reset) {
                this.price.boule.price = 0;
                this.price.pot.price = 0;
            }

            this.cal(true);
        },
        setPriceBoule: function (price, reset) {
            if (reset == undefined)
                reset = false;

            if (this.isHasRecyclage && parseFloat(price) > 0) {
                this.price.boule = parseFloat(price) - this.rabaisPriceBoulePromo;
            } else {
                this.price.boule.price = price;
            }

            if (reset) {
                this.price.pot.price = 0;
            }

            this.cal();
        },
        setPricePot: function (price, reset) {
            if (reset == undefined)
                reset = false;

            if (this.isHasRecyclage && parseFloat(price) > 0) {
                this.price.pot.price = parseFloat(price) - this.rabaisPricePotPromo;
            } else {
                this.price.pot.price = price;
            }

            if (reset) {
                //TODO: autre product
            }

            this.cal();
        },
        setPricePied: function (price, name, reset) {
            if (reset == undefined)
                reset = false;

            this.price.pied.price = price;
            this.price.pied.name = name;

            if (reset) {
                this.price.recyclage.price = 0;
                this.price.boule.price = 0;
                this.price.pot.price = 0;
            }

            this.cal(true);
        },
        unsetPricePied: function (price, name, reset) {
            if (reset == undefined)
                reset = false;

            this.price.pied.price = 0;
            this.price.pied.name = '';

            if (reset) {
                this.price.recyclage.price = 0;
                this.price.boule.price = 0;
                this.price.pot.price = 0;
            }

            this.cal(true);
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
        cal: function (overview) {
            var total = 0,
                overviewRender = '',
                currency = this.currency_sign;

            $.each(this.prices, function (k, v) {
                total += parseFloat(v);
            });

            $.each(this.price, function (k1, v1) {
                var currentPrice = parseFloat(v1.price).toFixed(2).toString().replace('.', ',') + ' ' + currency;
                total += parseFloat(v1.price);
                if (v1.name !== '' && typeof v1.name !== typeof undefined) {
                    overviewRender += '<div class="price-row-container">\n' +
                        '    <div class="prcontainer-title">' + v1.name + '</div>\n' +
                        '    <div class="prcontainer-price">' + currentPrice + '</div>\n' +
                        '</div>';
                }
            });

            if (!overview) {
                $(this.selectorPrice).text(parseFloat(total).toFixed(2).toString().replace('.', ',') + ' ' + currency);
            } else {
                var totalRender = parseFloat(total).toFixed(2).toString().replace('.', ',') + ' ' + currency;
                overviewRender += '<div class="overview-container-separator"></div>' +
                    '<div class="price-row-container">\n' +
                    '    <div class="prcontainer-title-total">Total</div>\n' +
                    '    <div class="prcontainer-price"><span>' + totalRender + '</span></div>\n' +
                    '</div>';
                $(this.selectorOverview).empty().append(overviewRender);
            }
        },
        recap: function (overview) {
            var total = 0,
                overviewRender = '',
                currency = this.currency_sign;

            $.each(this.prices, function (k, v) {
                total += parseFloat(v);
            });

            $.each(this.price, function (k1, v1) {
                var currentPrice = parseFloat(v1.price).toFixed(2).toString().replace('.', ',') + ' ' + currency;
                total += parseFloat(v1.price);
                if (v1.name !== '' && typeof v1.name !== typeof undefined) {
                    overviewRender += '<div class="price-row-container">\n' +
                        '    <div class="prcontainer-title">' + v1.name + '</div>\n' +
                        '    <div class="prcontainer-price">' + currentPrice + '</div>\n' +
                        '</div>';

                    var str = v1.name;
                    $('.recap-' + k1 + ' .recap-description').text(str.replace('&amp;','&'));
                }
            });

            var totalRender = parseFloat(total).toFixed(2).toString().replace('.', ',') + ' ' + currency;
            overviewRender += '<div class="overview-container-separator"></div>' +
                '<div class="price-row-container">\n' +
                '    <div class="prcontainer-title-total">Total</div>\n' +
                '    <div class="prcontainer-price"><span>' + totalRender + '</span></div>\n' +
                '</div>';
            $(this.recapOverview).append(overviewRender);
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
