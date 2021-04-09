{*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{capture name=path}{l s='Your addresses'}{/capture}
<h1 class="page-heading">{l s='Your addresses'}</h1>
<p class="info-title">
    {if isset($id_address) && (isset($smarty.post.alias) || isset($address->alias))}
        {l s='Modify address'}
        {if isset($smarty.post.alias)}
            <strong>"{$smarty.post.alias}"</strong>
        {else}
            <strong>{if isset($address->alias)}"{$address->alias|escape:'html':'UTF-8'}"{/if}</strong>
        {/if}
    {else}
        {l s='To add a new address, please fill out the form below.'}
    {/if}
</p>
<div class="box container">


    {include file="$tpl_dir./errors.tpl"}
    <p class="required hide"><sup>*</sup>{l s='Required field'}</p>
    <form action="{$link->getPageLink('address', true)|escape:'html':'UTF-8'}" method="post" class="std"
          id="add_address">
        <!--h3 class="page-subheading">{if isset($id_address)}{l s='Your address'}{else}{l s='New address'}{/if}</h3-->
        {assign var="stateExist" value=false}
        {assign var="postCodeExist" value=false}
        {assign var="dniExist" value=false}
        {assign var="homePhoneExist" value=false}
        {assign var="mobilePhoneExist" value=false}
        {assign var="atLeastOneExists" value=false}
        {foreach from=$ordered_adr_fields item=field_name}
            {if $field_name eq 'company'}
                <div class="form-group col-md-6">
                    <!-- <label for="company">{l s='Company'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label> -->
                    <input placeholder="{l s='Company'}{if in_array($field_name, $required_fields)} *{/if}"
                           class="form-control validate" data-validate="{$address_validation.$field_name.validate}"
                           type="text" id="company" name="company"
                           value="{if isset($smarty.post.company)}{$smarty.post.company}{else}{if isset($address->company)}{$address->company|escape:'html':'UTF-8'}{/if}{/if}"/>
                </div>
                <div class="form-group open_houre_input col-md-6">
                    <input placeholder="{l s='Horaire d\'ouverture'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}"
                           type="text" class="form-control validate"
                           data-validate="{$address_validation.$field_name.validate}" id="open_houre" name="open_houre"
                           value="{if isset($smarty.post.open_houre)}{$smarty.post.open_houre}{else}{if isset($address->open_houre)}{$address->open_houre|escape:'html':'UTF-8'}{/if}{/if}"/>
                </div>
            {/if}
            {if $field_name eq 'vat_number'}
                <div id="vat_area" class=" col-md-6">
                    <div id="vat_number">
                        <div class="form-group">
                            <!-- <label for="vat-number">{l s='VAT number'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label> -->
                            <input placeholder="{l s='VAT number'}{if in_array($field_name, $required_fields)} *{/if}"
                                   type="text" class="form-control validate"
                                   data-validate="{$address_validation.$field_name.validate}" id="vat-number"
                                   name="vat_number"
                                   value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number}{else}{if isset($address->vat_number)}{$address->vat_number|escape:'html':'UTF-8'}{/if}{/if}"/>
                        </div>
                    </div>
                </div>
            {/if}
            {if $field_name eq 'dni'}
                {assign var="dniExist" value=true}
                <div class="required form-group dni">
                    <label for="dni">{l s='Identification number'} <sup>*</sup></label>
                    <input class="form-control" data-validate="{$address_validation.$field_name.validate}" type="text"
                           name="dni" id="dni"
                           value="{if isset($smarty.post.dni)}{$smarty.post.dni}{else}{if isset($address->dni)}{$address->dni|escape:'html':'UTF-8'}{/if}{/if}"/>
                    <span class="form_info">{l s='DNI / NIF / NIE'}</span>
                </div>
            {/if}

            {if $field_name eq 'firstname'}
                <div class="required form-group col-md-6">
                    <!-- <label for="firstname">{l s='First name'} <sup>*</sup></label> -->
                    <input placeholder="{l s='First name'} *" class="is_required validate form-control"
                           data-validate="{$address_validation.$field_name.validate}" type="text" name="firstname"
                           id="firstname"
                           value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{else}{if isset($address->firstname)}{$address->firstname|escape:'html':'UTF-8'}{/if}{/if}"/>
                </div>
            {/if}
            {if $field_name eq 'lastname'}
                <div class="required form-group col-md-6">
                    <!-- <label for="lastname">{l s='Last name'} <sup>*</sup></label> -->
                    <input placeholder="{l s='Last name'} *" class="is_required validate form-control"
                           data-validate="{$address_validation.$field_name.validate}" type="text" id="lastname"
                           name="lastname"
                           value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{else}{if isset($address->lastname)}{$address->lastname|escape:'html':'UTF-8'}{/if}{/if}"/>
                </div>
            {/if}

            {if $field_name eq 'address1'}
                <div class="required form-group col-md-6">
                    <!-- <label for="address1">{l s='Address'} <sup>*</sup></label> -->
                    <input placeholder="{l s='Address'} *" class="is_required validate form-control"
                           data-validate="{$address_validation.$field_name.validate}" type="text" id="address1"
                           name="address1"
                           value="{if isset($smarty.post.address1)}{$smarty.post.address1}{else}{if isset($address->address1)}{$address->address1|escape:'html':'UTF-8'}{/if}{/if}"/>
                    <small style="color:red">{l s='Ne pas abréger "route" par "rte"'}</small>
                </div>
            {/if}
            {if $field_name eq 'address2'}
                <div class="required form-group col-md-6">
                    <!-- <label for="address2">{l s='Address (Line 2)'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label> -->
                    <input placeholder="{l s='Address (Line 2)'}{if in_array($field_name, $required_fields)} *{/if}"
                           class="validate form-control" data-validate="{$address_validation.$field_name.validate}"
                           type="text" id="address2" name="address2"
                           value="{if isset($smarty.post.address2)}{$smarty.post.address2}{else}{if isset($address->address2)}{$address->address2|escape:'html':'UTF-8'}{/if}{/if}"/>
                </div>
            {/if}
            {if $field_name eq 'postcode'}
                {assign var="postCodeExist" value=true}
                <div class="required postcode form-group unvisible col-md-6">
                    <!-- <label for="postcode">{l s='Zip/Postal Code'} <sup>*</sup></label> -->
                    <input placeholder="{l s='Zip/Postal Code'} *" class="is_required validate form-control"
                           data-validate="{$address_validation.$field_name.validate}" type="text" id="postcode"
                           name="postcode"
                           value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{else}{if isset($address->postcode)}{$address->postcode|escape:'html':'UTF-8'}{/if}{/if}"/>
                </div>
            {/if}
            {if $field_name eq 'city'}
                <div class="required form-group col-md-6">
                    <!-- <label for="city">{l s='City'} <sup>*</sup></label> -->
                    <input placeholder="{l s='City'} *" class="is_required validate form-control"
                           data-validate="{$address_validation.$field_name.validate}" type="text" name="city" id="city"
                           value="{if isset($smarty.post.city)}{$smarty.post.city}{else}{if isset($address->city)}{$address->city|escape:'html':'UTF-8'}{/if}{/if}"
                           maxlength="64"/>
                </div>
                {* if customer hasn't update his layout address, country has to be verified but it's deprecated *}
            {/if}
            {if $field_name eq 'Country:name' || $field_name eq 'country'}
                <div class="required form-group col-md-6">
                    <!-- <label for="id_country">{l s='Country'} <sup>*</sup></label> -->
                    <select id="id_country" class="form-control" name="id_country">{$countries_list}</select>
                </div>
            {/if}
            {if $field_name eq 'State:name'}
                {assign var="stateExist" value=true}
                <div class="required id_state form-group col-md-6">
                    <!-- <label for="id_state">{l s='State'} <sup>*</sup></label> -->
                    <select name="id_state" id="id_state" class="form-control">
                        <option value="">{l s='State'} *</option>
                    </select>
                </div>
            {/if}
            {if $field_name eq 'phone'}
                <hr class="clear">
                {if isset($one_phone_at_least) && $one_phone_at_least}
                    {assign var="atLeastOneExists" value=true}
                    <p class="inline-infos required text-center">
                        ** {l s='You must register at least one phone number.'}</p>
                {/if}
                {assign var="homePhoneExist" value=true}
                <div class="form-group phone-number col-md-6 mt-0">
                    <!-- <label for="phone">{l s='Home phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label> -->
                    <input placeholder="{l s='Home phone'}{if isset($one_phone_at_least) && $one_phone_at_least} **{/if}"
                           class="{if isset($one_phone_at_least) && $one_phone_at_least}is_required{/if} validate form-control"
                           data-validate="{$address_validation.phone.validate}" type="tel" id="phone" name="phone"
                           value="{if isset($smarty.post.phone)}{$smarty.post.phone}{else}{if isset($address->phone)}{$address->phone|escape:'html':'UTF-8'}{/if}{/if}"/>
                </div>
            {/if}
            {if $field_name eq 'phone_mobile'}
                {assign var="mobilePhoneExist" value=true}
                <div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group col-md-6 mt-0">
                    <!-- <label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label> -->
                    <input placeholder="{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} **{/if}"
                           class="validate form-control" data-validate="{$address_validation.phone_mobile.validate}"
                           type="tel" id="phone_mobile" name="phone_mobile"
                           value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{else}{if isset($address->phone_mobile)}{$address->phone_mobile|escape:'html':'UTF-8'}{/if}{/if}"/>
                </div>
            {/if}

        {/foreach}
        {if !$postCodeExist}
            <div class="required postcode form-group unvisible">
                <label for="postcode">{l s='Zip/Postal Code'} <sup>*</sup></label>
                <input class="is_required validate form-control" data-validate="{$address_validation.postcode.validate}"
                       type="text" id="postcode" name="postcode"
                       value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{else}{if isset($address->postcode)}{$address->postcode|escape:'html':'UTF-8'}{/if}{/if}"/>
            </div>
        {/if}
        {if !$stateExist}
            <div class="required id_state form-group unvisible col-md-6">

                <select name="id_state" id="id_state" class="form-control">
                    <option value="">{l s='State'} *</option>
                </select>
            </div>
        {/if}
        {if !$dniExist}
            <div class="required dni form-group unvisible">
                <label for="dni">{l s='Identification number'} <sup>*</sup></label>
                <input class="is_required form-control" data-validate="{$address_validation.dni.validate}" type="text"
                       name="dni" id="dni"
                       value="{if isset($smarty.post.dni)}{$smarty.post.dni}{else}{if isset($address->dni)}{$address->dni|escape:'html':'UTF-8'}{/if}{/if}"/>
                <span class="form_info">{l s='DNI / NIF / NIE'}</span>
            </div>
        {/if}
        {if !$homePhoneExist}
            <div class="form-group phone-number col-md-6">
                <label for="phone">{l s='Home phone'}</label>
                <input class="{if isset($one_phone_at_least) && $one_phone_at_least}is_required{/if} validate form-control"
                       data-validate="{$address_validation.phone.validate}" type="tel" id="phone" name="phone"
                       value="{if isset($smarty.post.phone)}{$smarty.post.phone}{else}{if isset($address->phone)}{$address->phone|escape:'html':'UTF-8'}{/if}{/if}"/>
            </div>
        {/if}
        {if !$mobilePhoneExist}
            <div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group col-md-6  mt-0">
                <!-- <label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label> -->
                <input placeholder="{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} **{/if}"
                       class="validate form-control" data-validate="{$address_validation.phone_mobile.validate}"
                       type="tel" id="phone_mobile" name="phone_mobile"
                       value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{else}{if isset($address->phone_mobile)}{$address->phone_mobile|escape:'html':'UTF-8'}{/if}{/if}"/>
                <small style="color:red">{l s='exemple de format du numéro 0041791234567, ou 0791234567'}</small>
            </div>
            <div class="checkbox form-group col-md-6">
                <label for="receive_sms">
                    <input type="checkbox" name="receive_sms" id="receive_sms" value="1"
                           {if (isset($smarty.post.receive_sms) && $smarty.post.receive_sms == '1') || $address->receive_sms == '1'}checked="checked"{/if}/>
                    {l s='Je souhaite recevoir un SMS m\'avertissant lors de la livraison de mon sapin (max. 3 SMS, aucune pub de sera envoyée sur votre téléphone)'}
                </label>
            </div>
        {/if}
        <hr class="clear">
        <div class="form-group col-md-6">
            <!-- <label for="other">{l s='Additional information'}</label> -->
            {*			<input placeholder="{l s='Additional information'}" class="validate form-control" type="text" data-validate="{$address_validation.other.validate}" id="other" name="other" value="{if isset($smarty.post.other)}{$smarty.post.other}{else}{if isset($address->other)}{$address->other|escape:'html':'UTF-8'}{/if}{/if}" />*}
            <span class="col-md-3 span-other">
                                       		<label for="other" class="label-other">{l s='Livraison à l\'étage'}
                                                :</label>
                                       </span>
            <span class="col-md-3" style="padding-left:0"><select id="other" name="other" class="form-control">
                                        	        <option value="">-</option>
                                        	        <option value="Oui">{l s='Oui'}</option>
                                        	        <option value="Non">{l s='Non'}</option>
                    {if isset($smarty.post.other) AND $smarty.post.other !==''}
                        <option value="{$smarty.post.other}" selected="selected">{$smarty.post.other}</option>

{else}
                                        	                {if isset($address->other) AND $address->other !==''}
                        <option value="{$address->other|escape:'htmlall':'UTF-8'}"
                                selected="selected">{$address->other|escape:'htmlall':'UTF-8'}</option>
                    {/if}
                    {/if}
                                        	</select></span>
            <small class="label-other">{l s='(Si vous habitez dans un immeuble uniquement)'}</small>
        </div>

        {if isset($one_phone_at_least) && $one_phone_at_least && !$atLeastOneExists}
            <p class="inline-infos required">{l s='You must register at least one phone number.'}</p>
        {/if}
        <!-- <div class="clearfix"></div> -->

        <div class="required form-group col-md-6" id="adress_alias">

            <input placeholder="{l s='Please assign an address title for future reference.'} *" type="text" id="alias"
                   class="is_required validate form-control" data-validate="{$address_validation.alias.validate}"
                   name="alias"
                   value="{if isset($smarty.post.alias)}{$smarty.post.alias}{else if isset($address->alias)}{$address->alias|escape:'html':'UTF-8'}{elseif !$select_address}{l s='My address'}{/if}"/>
            <!-- <label for="alias">{l s='Please assign an address title for future reference.'} <sup>*</sup></label> -->
            <small style="color:red" class="inline-infos clear text-address">
                {l s='Attribuer un alias d\'adresse pour référence future. *'}
                <span style="color:red">{l s='ex: Mon adresse'}</span>
            </small>
        </div>
        <p class="submit2 col-md-6 col-xs-12 mt-20">
            {if isset($id_address)}<input type="hidden" name="id_address" value="{$id_address|intval}" />{/if}
            {if isset($back)}<input type="hidden" name="back" value="{$back}" />{/if}
            {if isset($mod)}<input type="hidden" name="mod" value="{$mod}" />{/if}
            {if isset($select_address)}<input type="hidden" name="select_address"
                                              value="{$select_address|intval}" />{/if}
            <input type="hidden" name="token" value="{$token}"/>
            <button type="submit" name="submitAddress" id="submitAddress" class="btn btn-default btn-add">
				<span>
					{l s='Save'}
                    <!-- <i class="icon-chevron-right right"></i> -->
				</span>
            </button>
        </p>
    </form>
</div>
<ul class="footer_links clearfix">
    <li>
        <a class="btn btn-defaul link-back" href="{$link->getPageLink('addresses', true)|escape:'html':'UTF-8'}">
            <span><i class="icon-angle-left"></i> {l s='Back to your addresses'}</span>
        </a>
    </li>
</ul>
{strip}
    {if isset($smarty.post.id_state) && $smarty.post.id_state}
        {addJsDef idSelectedState=$smarty.post.id_state|intval}
    {else if isset($address->id_state) && $address->id_state}
        {addJsDef idSelectedState=$address->id_state|intval}
    {else}
        {addJsDef idSelectedState=false}
    {/if}
    {if isset($smarty.post.id_country) && $smarty.post.id_country}
        {addJsDef idSelectedCountry=$smarty.post.id_country|intval}
    {else if isset($address->id_country) && $address->id_country}
        {addJsDef idSelectedCountry=$address->id_country|intval}
    {else}
        {addJsDef idSelectedCountry=false}
    {/if}
    {if isset($countries)}
        {addJsDef countries=$countries}
    {/if}
    {if isset($vatnumber_ajax_call) && $vatnumber_ajax_call}
        {addJsDef vatnumber_ajax_call=$vatnumber_ajax_call}
    {/if}
{/strip}

<script>
    $(document).ready(function () {
        show_open_hour();

        $(document).on('input', '#company, #company_invoice', function () {
            show_open_hour();
        });
    });

    function show_open_hour() {
        if ($('#company').length && ($('#company').val() != ''))
            $('.open_houre_input').show();
        else
            $('.open_houre_input').hide();
    }
</script>