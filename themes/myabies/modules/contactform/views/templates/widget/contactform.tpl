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
<section class="contact-form m-auto" style="width:90%">
  <div class="col-md-6 contact-us-myabies">
    <form action="{$urls.pages.contact}" method="post" {if $contact.allow_file_upload}enctype="multipart/form-data"{/if}>

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
        <section class="form-fields">

          <h2 class="title-contact">{l s="Contact Form" d='Shop.Theme.Global'}</h2>

          <div class="form-group row" style="display: none">
            <select name="id_contact" class="form-control form-control-select">
              {foreach from=$contact.contacts item=contact_elt}
                <option value="{$contact_elt.id_contact}">{$contact_elt.name}</option>
              {/foreach}
            </select>
          </div>

          <div class="form-group row">
            <input
                    class="form-control"
                    name="name"
                    placeholder="{l s='Name' d='Shop.Forms.Help'}"
            >
          </div>

          <div class="form-group row">
            <input
                    class="form-control"
                    name="from"
                    type="email"
                    value="{$contact.email}"
                    placeholder="{l s='E-mail address' d='Shop.Forms.Help'}"
            >
          </div>

          {if $contact.orders}
            <div class="form-group row" style="display: none">
              <select name="id_order" class="form-control form-control-select">
                <option value="">{l s='Select reference' d='Shop.Forms.Help'}</option>
                {foreach from=$contact.orders item=order}
                  <option value="{$order.id_order}">{$order.reference}</option>
                {/foreach}
              </select>
              <span class="col-md-3 form-control-comment">
                {l s='optional' d='Shop.Forms.Help'}
              </span>
            </div>
          {/if}

          {if $contact.allow_file_upload}
            <div class="form-group row" style="display: none">
              <input type="file" name="fileUpload" class="filestyle" data-buttonText="{l s='Choose file' d='Shop.Theme.Actions'}">
              <span class="col-md-3 form-control-comment">
              {l s='optional' d='Shop.Forms.Help'}
            </span>
            </div>
          {/if}

          <div class="form-group row">
            <textarea
                    class="form-control"
                    name="message"
                    placeholder="{l s='Message' d='Shop.Forms.Help'}"
                    rows="5"
            >{if $contact.message}{$contact.message}{/if}</textarea>
          </div>

          {*{if isset($id_module)}
            <div class="form-group row">
              <div class="offset-md-3">
                {hook h='displayGDPRConsent' id_module=$id_module}
              </div>
            </div>
          {/if}*}

        </section>

        <footer class="form-footer">
          <style>
            input[name=url] {
              display: none !important;
            }
          </style>
          <input type="text" name="url" value=""/>
          <input type="hidden" name="token" value="{$token}" />
          <input class="btn contact-submit" type="submit" name="submitMessage" value="{l s='Send' d='Shop.Theme.Actions'}">
        </footer>
      {/if}

    </form>
  </div>
  <div class="col-md-5 contact-us-myabies">
    <h2 class="title-contact">{l s="Find us" d='Modules.ContactForm.Shop'}</h2>
    <div class="col-md-12">
      <div class="bl_right">
        <p class="font-serif-text text-contact"><span class="text-tel-mail">+41 21 539 18 13</span><br /><span class="text-tel-mail">info@myabies.ch</span></p>
      </div>
      <div class="mt-3">
        <p class="font-serif-text text-contact"><span class="text-address">My Abies</span><br /><span class="text-address">Chemin du Vieux Réservoir 7</span><br /><span class="text-address">1116 Cottens</span><br /><span class="text-address">Suisse</span></p>
      </div>
    </div>
  </div>
</section>
