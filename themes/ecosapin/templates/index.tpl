{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
{extends file='page.tpl'}

{block name='page_content_container'}
    {block name='page_content_top'}{/block}

    {block name='page_content'}
        {block name='hook_home'}
            <div id="editorial_block_center" class="editorial_block container"><a
                        href="https://www.youtube.com/watch?v=Co6Q6u8JDxQ&amp;feature=plcp"
                        title="Decorate your home with a beautiful living Christmas tree that will go back to nature."> </a>
                <h1>Decorate your home with a beautiful living Christmas tree that will go back to nature.</h1>
                <div class="rte"><p>Enjoy the company of a living, fine quality Christmas tree that&nbsp;will be delivered and picked up on the
                        day of your choice.</p>
                    <p>After spending the Holidays with you, your Ecosapin will go back to nature where he belongs.</p>
                    <div class="image-sapins"><img src="https://ecosapin.ch/img/cms/etape-sapin_EN.png" alt="" width="679" height="301"></div>
                </div>
            </div>
            <div id="popup-pays">
                <div class="content-ouv-new">
                    <a class="site-fr" href="//ecosapin.fr">fr</a>
                    <a class="site-ch" href="//ecosapin.ch">ch</a>
                    <a class="site-de" href="//ecosapin.de">de</a>
                    <a class="site-ap" href="//ecosapin.ch/content/22-join-us">ap</a>

                    <div class="newStyle">
                        <p class="txtIcons">{l s="France"}</p>
                        <p class="txtIcons">{l s="Suisse"}</p>
                        <p class="txtIcons">{l s="Allemagne"}</p>
                        <p class="txtIcons">{l s="Autre pays"}</p>
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <div class="btns container">
                    <a href="{$urls.base_url}module/tunnelvente/type" class="choix_spain_tunn"> {l s="Choisir mon sapin" mod='tunnelvente'}</a>
                </div>
                <div id="video_eco">
                    <div class="container">
                        <h1>{l s="Découvrez écosapin en video" mod='tunnelvente'}</h1>
                        <a href="{l s="https://www.youtube.com/watch?v=Co6Q6u8JDxQ" mod='tunnelvente'}" class="play"
                           title="Ecosapin bous présente sont concept en video...">Play</a>
                    </div>
                </div>

                <div id="my_littel_ecosapin">
                    <div class="container">
                        <h1>{l s="little ecosapin" mod='tunnelvente'}</h1>
                        <p>
                            {l s="Votre petit sapin en pot, idéal pour décorer votre table ou votre bureau" mod='tunnelvente'}
                        </p>
                        <a href="{$urls.base_url}stock-pack/93-my-little-ecosapin.html" class="little_ecosapin"
                           title="{l s="Choisir mon little ecosapin " mod='tunnelvente'}">{l s="Choisir mon little ecosapin " mod='tunnelvente'} </a>
                    </div>
                </div>
            </div>
        {literal}
            <script type="text/javascript">
                $(function ($) {
                    var texteOutStock2 = 'Sold out! Tous les  Ecosapins ont trouvé preneur cette année. Revenez nous voir en 2016';
                    $('<a href="#" class="close-tunnel"><i class="icon-remove"></i></a>').appendTo('.thirdCol');
                    $('.close-tunnel').on("click", function (e) {
                        $('#steps').slideUp("slow", function () {
                            $('#video_eco,#my_littel_ecosapin').slideDown();
                        });
                        $('html, body').animate({
                            scrollTop: 0
                        }, 1500, 'easeInOutQuart');
                        e.preventDefault();
                    });
                    //click Choisir mon sapin
                    $('.btns .choix_spain').on("click", function () {
                        // $('#test-popup strong').text(texteOutStock2);
                        //$('.popup-link').magnificPopup('open');
                        //return false;
                        $('#steps').slideDown("slow", function () {
                            $('#video_eco,#my_littel_ecosapin').slideUp();
                        });
                        if ($('#header').hasClass('header-fixed')) {
                            $('html, body').animate({
                                scrollTop: $("#steps").offset().top - 69
                            }, 1500, 'easeInOutQuart');
                        } else {
                            $('html, body').animate({
                                scrollTop: $("#steps").offset().top - 237
                            }, 1500, 'easeInOutQuart');
                        }


                    } /*, function() {
                     $('#steps').slideToggle("slow",function(){
                     $('#video_eco,#my_littel_ecosapin').slideToggle();
                     });
                     $('html, body').animate({
                     scrollTop: 0
                     }, 700);
                     }*/);


                    $('.play').magnificPopup({
                        disableOn: 700,
                        type: 'iframe',
                        mainClass: 'mfp-fade',
                        removalDelay: 160,
                        preloader: false,

                        fixedContentPos: true
                    });

                    $(".input_npa").numeric()

                    $.getJSON('https://ipapi.co/json/', function (data) {
                        let desired_code = 'ch';
                        let country_code = data.country.toLowerCase();

                        $(".site-" + desired_code).click(function (e) {
                            e.preventDefault();
                            $("#popup-pays").removeClass("popup-show");
                        });

                        if (get("redirect") != 1 && !document.referrer.includes("ecosapin." + desired_code)) {
                            if (country_code != desired_code) {
                                $("#popup-pays").addClass("popup-show");
                            }
                        }
                    });

                    $("body").on("click", ".popup-show", function () {
                        $("#popup-pays").removeClass("popup-show");
                    });

                    if (localStorage.choixsapin && localStorage.choixsapin == "yes") {
                        $('.btns .choix_spain').trigger("click");
                        localStorage.removeItem("choixsapin");
                    }

                });

                function get(name) {
                    if (name = (new RegExp('[?&]' + encodeURIComponent(name) + '=([^&]*)')).exec(location.search))
                        return decodeURIComponent(name[1]);
                }
            </script>
        {/literal}
        {/block}
    {/block}
{/block}