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
{block name='header_banner'}
  <div class="header-banner">
    {hook h='displayBanner'}
  </div>
{/block}

{block name='header_nav'}
  <nav class="header-nav">
    <div class="container">
      <div class="row">
        <div class="hidden-sm-down">
          <div class="col-md-5 col-xs-12">
            {hook h='displayNav1'}
          </div>
          <div class="col-md-7 right-nav">
              {hook h='displayNav2'}
          </div>
        </div>
        <div class="hidden-md-up text-sm-center mobile">
          <div class="float-xs-left" id="menu-icon">
            <i class="material-icons d-inline">&#xE5D2;</i>
          </div>
          <div class="float-xs-right" id="_mobile_cart"></div>
          <div class="float-xs-right" id="_mobile_user_info"></div>
          <div class="top-logo" id="_mobile_logo"></div>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
  </nav>
{/block}

{block name='header_top'}
  <div class="header-top">
    <div class="container">
       <div class="row">
        <div class="col-md-2 hidden-sm-down" id="_desktop_logo">
            {if $page.page_name == 'index'}
              <h1>
                <a href="{$urls.base_url}">
                  <img class="logo img-responsive" src="{$shop.logo}" alt="{$shop.name}">
                </a>
              </h1>
            {else}
                <a href="{$urls.base_url}">
                  <img class="logo img-responsive" src="{$shop.logo}" alt="{$shop.name}">
                </a>
            {/if}
        </div>
        <div class="col-md-10 col-sm-12 position-static">
          {hook h='displayTop'}
          <div class="clearfix"></div>
        </div>
          <div class="container col-md-12 titles">
              <div class="titles-header home">
                  <div class="title-header"><img src="{$urls.img_url}drapeau_suisse.png" alt="pecto-suisse"/></div>
                  <p class="text-header">{l s='Mon sapin local' d='Shop.Theme.Myabies'},<br>{l s='livré chez moi.' d='Shop.Theme.Myabies'}
                  </p>
                  <div class="btn-header">
                      <a href="{$urls.base_url}module/tunnelventeabies/type" class="btn-sapin-header">
                          {l s='Acheter mon sapin' d='Shop.Theme.Myabies'}
                      </a>
                  </div>
              </div>
              <div class="titles-header contact" style="display: none">
                  <h2 class="title-header">{l s='Contact'} </h2>
                  <p class="text-header">{l s='Pour toute question, notre équipe se fera un plaisir de vous répondre dans les meilleurs délais.' d='Shop.Theme.Myabies'}
                  </p>
              </div>
              <div class="titles-header profil" style="display: none">
                  <h2 class="title-header">{l s='Les producteurs' d='Shop.Theme.Myabies'} </h2>
                  <p class="text-header">{l s='Découvrez les producteurs qui travaillent avec My Abies' d='Shop.Theme.Myabies'}
                  </p>
              </div>
              <div class="titles-header sapins" style="display: none">
                  <h2 class="title-header">{l s='Les sapins' d='Shop.Theme.Myabies'} </h2>
                  <p class="text-header">{l s='Découvrez tous les sapins proposés sur my abies' d='Shop.Theme.Myabies'}
                  </p>
              </div>
              <div class="titles-header profil-details1" style="display: none">
                  <h2 class="title-header">{l s='Agriculteur'} </h2>
                  <p class="text-header">{l s='Famille Castella'}
                  </p>
              </div>
              <div class="titles-header profil-details2" style="display: none">
                  <h2 class="title-header">{l s='Agriculteur'} </h2>
                  <p class="text-header">{l s='Adrian Kuhn'}
                  </p>
              </div>
              <div class="titles-header profil-details3" style="display: none">
                  <h2 class="title-header">{l s='Agriculteur'} </h2>
                  <p class="text-header">{l s='Nicolas Dolder'}
                  </p>
              </div>
              <div class="titles-header profil-details4" style="display: none">
                  <h2 class="title-header">{l s='Agriculteur'} </h2>
                  <p class="text-header">{l s='Famille Gäumann'}
                  </p>
              </div>
          </div>        
      </div>
      <div id="mobile_top_menu_wrapper" class="row hidden-md-up" style="display:none;">
        <div class="js-top-menu mobile" id="_mobile_top_menu"></div>
        <div class="js-top-menu-bottom">
          <div id="_mobile_currency_selector"></div>
          <div id="_mobile_language_selector"></div>
          <div id="_mobile_contact_link"></div>
        </div>
      </div>
    </div>
  </div>
  {hook h='displayNavFullWidth'}
{/block}
