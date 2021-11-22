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
<section class="contact-form">
    <div class="row container-contact">
      <div class="col-md-6 contact-us-myabies">
          <h2 class="title-contact">{l s="Formulaire de contact" d='Modules.ContactForm.Admin'}</h2>
          <form action="{$urls.base_url}nous-contacter" method="post" class="contact-form-box" enctype="multipart/form-data">
          
          {if $notifications}
            <div class="col-xs-12 alert {if $notifications.nw_error}alert-danger{else}alert-success{/if}">
              <ul>
                {foreach $notifications.messages as $notif}
                  <li>{$notif}</li>
                {/foreach}
              </ul>
            </div>
          {/if}
      
          {if !$notifications || $notifications.nw_error}
            <div class="clearfix">
                <div class="row">
                  <div class="form-group col-md-12 col-xs-12">
                    <select name="id_contact" class="form-control form-control-select">
                      {foreach from=$contact.contacts item=contact_elt}
                        <option value="{$contact_elt.id_contact}">{$contact_elt.name}</option>
                      {/foreach}
                    </select>
                  </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12 col-xs-12">
                      <input class="form-control grey validate sapin-nom" type="text" id="nom" name="nom" value="" />
                    </div>
                    <div class="form-group col-md-12 col-xs-12">
                      <input class="form-control grey validate sapin-email" type="text" id="email" name="from" value="" />
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12 col-xs-12">
                      <textarea class="form-control grey sapin-message" id="message" name="message" cols="22" rows="5"></textarea>
                    </div>
                </div>
                <div class="row">
                  <button type="submit" name="submitMessage" id="submitMessage" class="link-home btn btn-"> <span>Send</span> </button>
                </div>
            </div>
          {/if}
          </form>
      </div>
      <div class="col-md-6 contact-us-myabies">
          <h2 class="title-contact">{l s="Nous trouvez" d='Modules.ContactForm.Shop'}</h2>
          <div class="col-md-12 hidden-sm">
              <div class="bl_right">
                <p class="font-serif-text text-contact"><span class="text-tel-mail">+41 21 539 13 14</span><br /><span class="text-tel-mail">info@myabies.ch</span></p>
              </div>
              <div class="mt-3">
              <p class="font-serif-text text-contact"><span class="text-address">My Abies</span><br /><span class="text-address">Chemin du Vieux Réservoir 7</span><br /><span class="text-address">1116 Cottens</span><br /><span class="text-address">Suisse</span></p>
              </div>
          </div>
      </div>
    </div>  
</section>