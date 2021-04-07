<div id="opc_new_account" class="opc-main-block">
	<div id="opc_new_account-overlay" class="opc-overlay" style="display: none;"></div>
	<h1 class="page-heading step-num"><b><span>01 / </span> {l s='Account'}</b></h1>
	<form action="{$link->getPageLink('authentication', true, NULL, "back=order-opc")|escape:'html':'UTF-8'}" method="post" id="login_form" class="box row">
		<fieldset>
			<h3 class="page-subheading col-md-12">{l s='Already registered?'}</h3>
			<!-- <p><a href="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}" id="openLoginFormBlock">&raquo; {l s='Click here'}</a></p> -->
			<div id="login_form_content">
				<!-- Error return block -->
				<div id="opc_login_errors" class="alert alert-danger" style="display:none;"></div>
				<!-- END Error return block -->
				<p class="form-group col-md-6">
					<!-- <label for="login_email">{l s='Email address'}</label> -->
					<input placeholder="{l s='Email address'}" type="text" class="form-control validate" id="email" name="email" data-validate="isEmail" />
				</p>
				<p class="form-group col-md-6">
					<!-- <label for="login_passwd">{l s='Password'}</label> -->
					<input placeholder="{l s='Password'}" class="form-control validate" type="password" id="login_passwd" name="passwd" data-validate="isPasswd" />
				</p>
				<div class="col-md-12 text-right" id="lostPass">
					<a href="{$link->getPageLink('password', true)|escape:'html':'UTF-8'}" class="lost_password">{l s='Forgot your password?'}</a>
				<p class="submit" style="display:inline-flex">
					{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'html':'UTF-8'}" />{/if}
					<button type="submit" id="SubmitLogin" name="SubmitLogin" class="button btn btn-default"><span><!-- i class="icon-lock left"></i> -->{l s='Sign in'}</span></button>
				</p>
				</div>
				
			</div>
		</fieldset>
	</form>
	<form action="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}" method="post" id="new_account_form" class="std box row" autocomplete="on" autofill="on">
		<fieldset>
				<h3 id="new_account_title" class="page-subheading col-md-12">{l s='New Customer'}</h3>
				<div id="opc_account_choice" class="row">
					<div class="col-xs-12 col-md-6">
						<p class="title_block">{l s='Instant Checkout'}</p>
						<p class="opc-button">
							<button type="submit" class="btn btn-default button button-medium exclusive" id="opc_guestCheckout"><span>{l s='Guest checkout'}</span></button>
						</p>
					</div>
					<div class="col-xs-12 col-md-6">
						<p class="title_block">{l s='Create your account today and enjoy:'}</p>
						<ul class="bullet">
							<li>- {l s='Personalized and secure access'}</li>
							<li>- {l s='A fast and easy check out process'}</li>
							<li>- {l s='Separate billing and shipping addresses'}</li>
						</ul>
						<p class="opc-button">
							<button type="submit" class="btn btn-default button button-medium exclusive" id="opc_createAccount"><span><i class="icon-user left"></i>{l s='Create an account'}</span></button>
						</p>
					</div>
				</div>
				<div id="opc_account_form" class="unvisible">
					{$HOOK_CREATE_ACCOUNT_TOP}
					<!-- Error return block -->
					<div id="opc_account_errors" class="alert alert-danger" style="display:none;"></div>
					<!-- END Error return block -->
					<!-- Account -->
					<input type="hidden" id="is_new_customer" name="is_new_customer" value="0" />
					<input type="hidden" id="opc_id_customer" name="opc_id_customer" value="{if isset($guestInformations) && isset($guestInformations.id_customer) && $guestInformations.id_customer}{$guestInformations.id_customer}{else}0{/if}" />
					<input type="hidden" id="opc_id_address_delivery" name="opc_id_address_delivery" value="{if isset($guestInformations) && isset($guestInformations.id_address_delivery) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />
					<input type="hidden" id="opc_id_address_invoice" name="opc_id_address_invoice" value="{if isset($guestInformations) && isset($guestInformations.id_address_delivery) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />
					<div class="row">
						<div class="required text form-group col-md-6">
							<!-- <label for="email">{l s='Email'} <sup>*</sup></label> -->
							<input placeholder="{l s='Email'}*" type="text" class="text form-control validate" id="email" name="email" data-validate="isEmail" value="{if isset($guestInformations) && isset($guestInformations.email) && $guestInformations.email}{$guestInformations.email}{/if}" />
						</div>
						<div class="required password is_customer_param form-group col-md-6">
							<!-- <label for="passwd">{l s='Password'} <sup>*</sup></label> -->
							<input placeholder="{l s='Password'}*" type="password" class="text form-control validate" name="passwd" id="passwd" data-validate="isPasswd" />
							<!-- <span class="form_info">{l s='(five characters min.)'}</span> -->
						</div>
					</div>
					<div class="row">
						<div class="required clearfix gender-line col-md-12">
							<label>{l s='Social title'}</label>
							{foreach from=$genders key=k item=gender}
							<div class="radio-inline">
								<label for="id_gender{$gender->id_gender}" class="top">
								<input type="radio" name="id_gender" id="id_gender{$gender->id_gender}" value="{$gender->id_gender}"{if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id_gender || (isset($guestInformations) && $guestInformations.id_gender == $gender->id_gender)} checked="checked"{/if} />
								{$gender->name}</label></div>
							{/foreach}
						</div>
					</div>
					<div class="row">
						<div class="required form-group col-md-6">
							<!-- <label for="firstname">{l s='First name'} <sup>*</sup></label> -->
							<input placeholder="{l s='First name'}*" type="text" class="text form-control validate" id="customer_firstname" name="customer_firstname" onblur="$('#firstname').val($(this).val());" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.customer_firstname) && $guestInformations.customer_firstname}{$guestInformations.customer_firstname}{/if}" />
						</div>
						<div class="required form-group col-md-6">
							<!-- <label for="lastname">{l s='Last name'} <sup>*</sup></label> -->
							<input placeholder="{l s='Last name'}*" type="text" class="form-control validate" id="customer_lastname" name="customer_lastname" onblur="$('#lastname').val($(this).val());" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.customer_lastname) && $guestInformations.customer_lastname}{$guestInformations.customer_lastname}{/if}" />
						</div>
					</div>
					<div class="row">
						<div class="select date-select col-md-12">
							
							<div class="form-group" id="date_birth">
							<label class="col-xs-4">{l s='Date of Birth'}</label>
								<div class="col-md-4 col-xs-12">
									<select id="days" name="days" class="form-control">
										<option value="">-</option>
										{foreach from=$days item=day}
										<option value="{$day|escape:'html':'UTF-8'}" {if isset($guestInformations) && isset($guestInformations.sl_day) && ($guestInformations.sl_day == $day)} selected="selected"{/if}>{$day|escape:'html':'UTF-8'}&nbsp;&nbsp;</option>
										{/foreach}
									</select>
									{*
									{l s='January'}
									{l s='February'}
									{l s='March'}
									{l s='April'}
									{l s='May'}
									{l s='June'}
									{l s='July'}
									{l s='August'}
									{l s='September'}
									{l s='October'}
									{l s='November'}
									{l s='December'}
									*}
								</div>
								<div class="col-md-4 col-xs-12">
									<select id="months" name="months" class="form-control">
										<option value="">-</option>
										{foreach from=$months key=k item=month}
										<option value="{$k|escape:'html':'UTF-8'}" {if isset($guestInformations) && isset($guestInformations.sl_month) && ($guestInformations.sl_month == $k)} selected="selected"{/if}>{l s=$month}&nbsp;</option>
										{/foreach}
									</select>
								</div>
								<div class="col-md-4 col-xs-12">
									<select id="years" name="years" class="form-control">
										<option value="">-</option>
										{foreach from=$years item=year}
										<option value="{$year|escape:'html':'UTF-8'}" {if isset($guestInformations) && isset($guestInformations.sl_year) && ($guestInformations.sl_year == $year)} selected="selected"{/if}>{$year|escape:'html':'UTF-8'}&nbsp;&nbsp;</option>
										{/foreach}
									</select>
								</div>
							</div>
						</div>
					</div>


					<div class="row checkbox-wrap" id="">
						{if isset($newsletter) && $newsletter}
						<div class="checkbox col-md-12">
							<label for="newsletter">
							<input type="checkbox" name="newsletter" id="newsletter" value="1"{if isset($guestInformations) && isset($guestInformations.newsletter) && $guestInformations.newsletter} checked="checked"{/if} autocomplete="off"/>
							{l s='Sign up for our newsletter!'}</label>
							{if array_key_exists('newsletter', $field_required)}
								<sup> *</sup>
							{/if}
						</div>
						{/if}
						{if isset($optin) && $optin}
						<div class="checkbox col-md-12">
							<label for="optin">
							<input type="checkbox" name="optin" id="optin" value="1"{if isset($guestInformations) && isset($guestInformations.optin) && $guestInformations.optin} checked="checked"{/if} autocomplete="off"/>
							{l s='Receive special offers from our partners!'}</label>
							{if array_key_exists('optin', $field_required)}
								<sup> *</sup>
							{/if}
						</div>
						{/if}</div>
					<h3 class="page-subheading top-indent col-md-12">{l s='Delivery address'}</h3>
					{$stateExist = false}
					{$postCodeExist = false}
					{$dniExist = false}
					{foreach from=$dlv_all_fields item=field_name}
					
					{* {elseif $field_name eq "vat_number"}
					<div id="vat_number_block" class="col-md-6" style="display:none;">
						<div class="form-group">
							<label for="vat_number">{l s='VAT number'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
							<input placeholder="{l s='VAT number'}{if in_array($field_name, $required_fields)}*{/if}" type="text" class="text form-control" name="vat_number" id="vat_number" value="{if isset($guestInformations) && isset($guestInformations.vat_number) && $guestInformations.vat_number}{$guestInformations.vat_number}{/if}" />
						</div>
					</div> *}
					{if $field_name eq "dni"}
					{assign var='dniExist' value=true}
					<div class="required dni form-group">
						<label for="dni">{l s='Identification number'} <sup>*</sup></label>
						<input type="text" class="text form-control validate" name="dni" id="dni" data-validate="isDniLite" value="{if isset($guestInformations) && isset($guestInformations.dni) && $guestInformations.dni}{$guestInformations.dni}{/if}" />
						<span class="form_info">{l s='DNI / NIF / NIE'}</span>
					</div>

					
						{elseif $field_name eq "firstname"}
						<div class="row">
						<div class="required text form-group col-md-6">
							<!-- <label for="firstname">{l s='First name'} <sup>*</sup></label> -->
							<input placeholder="{l s='First name'} *" type="text" class="text form-control validate" id="firstname" name="firstname" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.firstname) && $guestInformations.firstname}{$guestInformations.firstname}{/if}" />
						</div>
						{elseif $field_name eq "lastname"}
						<div class="required text form-group col-md-6">
							<!-- <label for="lastname">{l s='Last name'} <sup>*</sup></label> -->
							<input placeholder="{l s='Last name'} *" type="text" class="text form-control validate" id="lastname" name="lastname" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.lastname) && $guestInformations.lastname}{$guestInformations.lastname}{/if}" />
						</div>
						</div>
						{elseif $field_name eq "company"}
						<div class="row">
						<div class="text form-group col-md-6">
							<!-- <label for="company">{l s='Company'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label> -->
							<input placeholder="{l s='Company'}{if in_array($field_name, $required_fields)} *{/if}" type="text" class="text form-control validate" id="company" name="company" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.company) && $guestInformations.company}{$guestInformations.company}{/if}" />
						</div>
						{elseif $field_name eq "address1"}
						<div class="required text form-group col-md-6">
							<!-- <label for="address1">{l s='Address'} <sup>*</sup></label> -->
							<input placeholder="{l s='Address'}" type="text" class="text form-control validate" name="address1" id="address1" data-validate="isAddress" value="{if isset($guestInformations) && isset($guestInformations.address1) && isset($guestInformations) && isset($guestInformations.address1) && $guestInformations.address1}{$guestInformations.address1}{/if}" />
							<span style="color:red">{l s='Ne pas abréger "route" par "rte"'}</span>
						</div>
						</div>
						{elseif $field_name eq "address2"}
						<div class="row">
						<div class="text{if !in_array($field_name, $required_fields)} is_customer_param{/if} form-group col-md-6">
							<!-- <label for="address2">{l s='Address (Line 2)'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label> -->
							<input placeholder="{l s='Address (Line 2)'}{if in_array($field_name, $required_fields)} *{/if}" type="text" class="text form-control validate" name="address2" id="address2" data-validate="isAddress" value="{if isset($guestInformations) && isset($guestInformations.address2) && isset($guestInformations) && isset($guestInformations.address2) && $guestInformations.address2}{$guestInformations.address2}{/if}" />
						</div>
					{elseif $field_name eq "postcode"}
					{$postCodeExist = true}
					<div class="required postcode text form-group  col-md-6">
						<!-- <label for="postcode">{l s='Zip/Postal code'} <sup>*</sup></label> -->
                                            <input placeholder="{l s='Zip/Postal code'} *" type="text" class="text form-control validate" maxlength="4" name="postcode" id="postcode" data-validate="isPostCode" value="{if isset($guestInformations) && isset($guestInformations.postcode) && $guestInformations.postcode}{$guestInformations.postcode}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
					</div>
					</div>
					{elseif $field_name eq "city"}
					<div class="row">
					<div class="required text form-group col-md-6">
						<!-- <label for="city">{l s='City'} <sup>*</sup></label> -->
						<input placeholder="{l s='City'} *" type="text" class="text form-control validate" name="city" id="city" data-validate="isCityName" value="{if isset($guestInformations) && isset($guestInformations.city) && $guestInformations.city}{$guestInformations.city}{/if}" />
					</div>
					{elseif $field_name eq "country" || $field_name eq "Country:name"}
					<div class="required select form-group col-md-6">
						<!-- <label for="id_country">{l s='Country'} <sup>*</sup></label> -->
						<select name="id_country" id="id_country" class="form-control">
							<option value="">{l s='Country'} <sup>*</sup></option>
							{foreach from=$countries item=v}
							<option value="{$v.id_country}"{if (isset($guestInformations) && isset($guestInformations.id_country) && $guestInformations.id_country == $v.id_country) || (!isset($guestInformations) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'html':'UTF-8'}</option>
							{/foreach}
						</select>
					</div>
					</div>
					{elseif $field_name eq "state" || $field_name eq 'State:name'}
					{$stateExist = true}
					<div class="required id_state form-group" style="display:none;">
						<label for="id_state">{l s='State'} <sup>*</sup></label>
						<select name="id_state" id="id_state" class="form-control">
							<option value="">-</option>
						</select>
					</div>
					{/if}
					{/foreach}
					{if !$postCodeExist}
					<div class="required postcode form-group unvisible">
						<label for="postcode">{l s='Zip/Postal code'} <sup>*</sup></label>
						<input type="text" class="text form-control validate" name="postcode" id="postcode" data-validate="isPostCode" value="{if isset($guestInformations) && isset($guestInformations.postcode) && $guestInformations.postcode}{$guestInformations.postcode}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
					</div>
					{/if}<div class="row">
					{if !$stateExist}
                                            
                                            <div class="required id_state form-group unvisible col-md-6">
                                                    
                                                    <select name="id_state" id="id_state" class="form-control">
                                                            <option value="">{l s='State'} *</option>
                                                    </select>
                                            </div>
                                            
                                        {/if}
<div class="form-group col-md-6 is_customer_param">
								<!-- <label for="other">{l s='Additional information'}</label> -->
					{*			<input placeholder="{l s='Additional information'}" class="validate form-control" type="text" data-validate="{$address_validation.other.validate}" id="other" name="other" value="{if isset($smarty.post.other)}{$smarty.post.other}{else}{if isset($address->other)}{$address->other|escape:'html':'UTF-8'}{/if}{/if}" />*}
					                        <span class="col-md-3 span-other">
					                                       		<label for="other" class="label-other">{l s='Livraison à l\'étage'} :</label>
					                                       </span>
					                                        <span class="col-md-3" style="padding-left:0"><select id="other" name="other" class="form-control">
					                                        	        <option value="">-</option>
					                                        	        <option value="Oui">{l s='Oui'}</option>
					                                        	        <option value="Non">{l s='Non'}</option>
					                                        	        {if isset($smarty.post.other) AND $smarty.post.other !==''}
					                                        	                <option value="{$smarty.post.other}" selected="selected">{$smarty.post.other}</option>
					                                        	        {else}
					                                        	                {if isset($address->other) AND $address->other !==''}
					                                        	                <option value="{$address->other|escape:'htmlall':'UTF-8'}" selected="selected">{$address->other|escape:'htmlall':'UTF-8'}</option>
					                                        	                {/if}
					                                        	        {/if}
					                                        	</select></span>
					                                        <small class="label-other">{l s='(Si vous habitez dans un immeuble uniquement)'}</small>
							</div>
                                        </div>
					{if !$dniExist}
					<div class="required dni form-group">
						<label for="dni">{l s='Identification number'} <sup>*</sup></label>
						<input type="text" class="text form-control validate" name="dni" id="dni" data-validate="isDniLite" value="{if isset($guestInformations) && isset($guestInformations.dni) && $guestInformations.dni}{$guestInformations.dni}{/if}" />
						<span class="form_info">{l s='DNI / NIF / NIE'}</span>
					</div>
					{/if}
					
					<hr class="clear">
					{if isset($one_phone_at_least) && $one_phone_at_least}
						<p class="inline-infos required is_customer_param text-center">{l s='You must register at least one phone number.'}</p>
					{/if}
					<div class="row">
						<div class="form-group is_customer_param col-md-6">
							<!-- <label for="phone">{l s='Home phone'}</label> -->
							<input placeholder="{l s='Home phone'}" type="text" class="text form-control validate" name="phone" id="phone"  data-validate="isPhoneNumber" value="{if isset($guestInformations) && isset($guestInformations.phone) && $guestInformations.phone}{$guestInformations.phone}{/if}" />
						</div>
						<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group col-md-6">
							<!-- <label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>*</sup>{/if}</label> -->
							<input placeholder="{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} *{/if}" type="text" class="text form-control validate" name="phone_mobile" id="phone_mobile" data-validate="isPhoneNumber" value="{if isset($guestInformations) && isset($guestInformations.phone_mobile) && $guestInformations.phone_mobile}{$guestInformations.phone_mobile}{/if}" />
						</div>
						<input type="hidden" name="alias" id="alias" value="{l s='My address'}"/>
					</div>
										<hr>
					<div class="row mb-0">
						<div class="checkbox col-md-12 mt-10 mb-0">
							<label for="invoice_address">
							<input type="checkbox" name="invoice_address" id="invoice_address"{if (isset($smarty.post.invoice_address) && $smarty.post.invoice_address) || (isset($guestInformations) && isset($guestInformations.invoice_address) && $guestInformations.invoice_address)} checked="checked"{/if} autocomplete="off"/>
							{l s='Please use another address for invoice'}</label>
						</div>
					</div>

					<div id="opc_invoice_address" class="is_customer_param" style="display:block">
						{assign var=stateExist value=false}
						{assign var=postCodeExist value=false}
						{assign var='dniExist' value=false}
						<h3 class="page-subheading top-indent col-md-12">{l s='Invoice address'}</h3>
						{foreach from=$inv_all_fields item=field_name}
						{*{if $field_name eq "company"}
												<div class="form-group">
													<label for="company_invoice">{l s='Company'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
													<input type="text" class="text form-control validate" id="company_invoice" name="company_invoice"  data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.company_invoice) && $guestInformations.company_invoice}{$guestInformations.company_invoice}{/if}" />
												</div>*}
						{*{elseif $field_name eq "vat_number"}
						<div id="vat_number_block_invoice" class="is_customer_param" style="display:none;">
							<div class="form-group">
								<label for="vat_number_invoice">{l s='VAT number'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
								<input type="text" class="form-control" id="vat_number_invoice" name="vat_number_invoice" value="{if isset($guestInformations) && isset($guestInformations.vat_number_invoice) && $guestInformations.vat_number_invoice}{$guestInformations.vat_number_invoice}{/if}" />
							</div>
						</div>*}
						{if $field_name eq "dni"}
						{assign var='dniExist' value=true}
						<div class="required form-group dni_invoice">
							<label for="dni">{l s='Identification number'} <sup>*</sup></label>
							<input type="text" class="text form-control validate" name="dni_invoice" id="dni_invoice" data-validate="isDniLite" value="{if isset($guestInformations) && isset($guestInformations.dni_invoice) && $guestInformations.dni_invoice}{$guestInformations.dni_invoice}{/if}" />
							<span class="form_info">{l s='DNI / NIF / NIE'}</span>
						</div>
						{elseif $field_name eq "firstname"}
						<div class="row">
						<div class="required form-group col-md-6">
							<!-- <label for="firstname_invoice">{l s='First name'} <sup>*</sup></label> -->
							<input placeholder="{l s='First name'} *" type="text" class="form-control validate" id="firstname_invoice" name="firstname_invoice" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.firstname_invoice) && $guestInformations.firstname_invoice}{$guestInformations.firstname_invoice}{/if}" />
						</div>
						{elseif $field_name eq "lastname"}
						<div class="required form-group col-md-6">
							<!-- <label for="lastname_invoice">{l s='Last name'} <sup>*</sup></label> -->
							<input placeholder="{l s='Last name'} *" type="text" class="form-control validate" id="lastname_invoice" name="lastname_invoice" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.lastname_invoice) && $guestInformations.lastname_invoice}{$guestInformations.lastname_invoice}{/if}" />
						</div>
						</div>
						{elseif $field_name eq "company"}
						<div class="row">
						<div class="form-group col-md-6">
							<!-- <label for="company_invoice">{l s='Company'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label> -->
							<input placeholder="{l s='Company'}{if in_array($field_name, $required_fields)} *{/if}" type="text" class="text form-control validate" id="company_invoice" name="company_invoice"  data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.company_invoice) && $guestInformations.company_invoice}{$guestInformations.company_invoice}{/if}" />
						</div>
						{elseif $field_name eq "address1"}
						<div class="required form-group col-md-6">
							<!-- <label for="address1_invoice">{l s='Address'} <sup>*</sup></label> -->
							<input placeholder="{l s='Address'} *" type="text" class="form-control validate" name="address1_invoice" id="address1_invoice" data-validate="isAddress" value="{if isset($guestInformations) && isset($guestInformations.address1_invoice) && isset($guestInformations) && isset($guestInformations.address1_invoice) && $guestInformations.address1_invoice}{$guestInformations.address1_invoice}{/if}" />
						</div>
						</div>
						{elseif $field_name eq "address2"}
						<div class="row">
						<div class="form-group{if !in_array($field_name, $required_fields)} is_customer_param{/if} col-md-6">
							<!-- <label for="address2_invoice">{l s='Address (Line 2)'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label> -->
							<input placeholder="{l s='Address (Line 2)'}{if in_array($field_name, $required_fields)} *{/if}" type="text" class="form-control address" name="address2_invoice" id="address2_invoice" data-validate="isAddress" value="{if isset($guestInformations) && isset($guestInformations.address2_invoice) && isset($guestInformations) && isset($guestInformations.address2_invoice) && $guestInformations.address2_invoice}{$guestInformations.address2_invoice}{/if}" />
						</div>
						{elseif $field_name eq "postcode"}
						{$postCodeExist = true}
						<div class="required postcode_invoice form-group  col-md-6">
							<!-- <label for="postcode_invoice">{l s='Zip/Postal Code'} <sup>*</sup></label> -->
							<input placeholder="{l s='Zip/Postal Code'} *" type="text" class="form-control validate" name="postcode_invoice" id="postcode_invoice" data-validate="isPostCode" value="{if isset($guestInformations) && isset($guestInformations.postcode_invoice) && $guestInformations.postcode_invoice}{$guestInformations.postcode_invoice}{/if}" onkeyup="$('#postcode_invoice').val($('#postcode_invoice').val().toUpperCase());" />
						</div>
						</div>
						{elseif $field_name eq "city"}
						<div class="row">
						<div class="required form-group col-md-6">
							<!-- <label for="city_invoice"></label> -->
							<input placeholder="{l s='City'} *" type="text" class="form-control validate" name="city_invoice" id="city_invoice" data-validate="isCityName" value="{if isset($guestInformations) && isset($guestInformations.city_invoice) && $guestInformations.city_invoice}{$guestInformations.city_invoice}{/if}" />
						</div>
						{elseif $field_name eq "country" || $field_name eq "Country:name"}
						<div class="required form-group col-md-6">
							<!-- <label for="id_country_invoice">{l s='Country'} <sup>*</sup></label> -->
							<select name="id_country_invoice" id="id_country_invoice" class="form-control">
								<option value="">{l s='Country'} <sup>*</sup></option>
								{foreach from=$countries item=v}
								<option value="{$v.id_country}"{if (isset($guestInformations) && isset($guestInformations.id_country_invoice) && $guestInformations.id_country_invoice == $v.id_country) || (!isset($guestInformations) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'html':'UTF-8'}</option>
								{/foreach}
							</select>
						</div>
						</div>
						{elseif $field_name eq "state" || $field_name eq 'State:name'}
						{$stateExist = true}
						<div class="required id_state_invoice form-group" style="display:none;">
							<label for="id_state_invoice">{l s='State'} <sup>*</sup></label>
							<select name="id_state_invoice" id="id_state_invoice" class="form-control">
								<option value="">-</option>
							</select>
						</div>
						{/if}
						{/foreach}
						{if !$postCodeExist}
						<div class="required postcode_invoice form-group unvisible">
							<label for="postcode_invoice">{l s='Zip/Postal Code'} <sup>*</sup></label>
							<input type="text" class="form-control validate" name="postcode_invoice" id="postcode_invoice" data-validate="isPostCode" value="{if isset($guestInformations) && isset($guestInformations.postcode_invoice) && $guestInformations.postcode_invoice}{$guestInformations.postcode_invoice}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
						</div>
						{/if}
						{*{if !$stateExist}
												<div class="required id_state_invoice form-group unvisible">
													<label for="id_state_invoice">{l s='State'} <sup>*</sup></label>
													<select name="id_state_invoice" id="id_state_invoice" class="form-control">
														<option value="">-</option>
													</select>
												</div>
												{/if}*}
						{if !$dniExist}
						<div class="required form-group dni_invoice">
							<label for="dni">{l s='Identification number'} <sup>*</sup></label>
							<input type="text" class="text form-control validate" name="dni_invoice" id="dni_invoice" data-validate="isDniLite" value="{if isset($guestInformations) && isset($guestInformations.dni_invoice) && $guestInformations.dni_invoice}{$guestInformations.dni_invoice}{/if}" />
							<span class="form_info">{l s='DNI / NIF / NIE'}</span>
						</div>
						{/if}
						{*<div class="form-group is_customer_param">
													<label for="other_invoice">{l s='Additional information'}</label>
													<textarea class="form-control" name="other_invoice" id="other_invoice" cols="26" rows="3"></textarea>
												</div>*}
												<hr>
						{if isset($one_phone_at_least) && $one_phone_at_least}
							<p class="inline-infos required is_customer_param text-center">{l s='You must register at least one phone number.'}</p>
						{/if}
						<div class="row">
							<div class="form-group is_customer_param col-md-6">
								<!-- <label for="phone_invoice">{l s='Home phone'}</label> -->
								<input placeholder="{l s='Home phone'}" type="text" class="form-control validate" name="phone_invoice" id="phone_invoice" data-validate="isPhoneNumber" value="{if isset($guestInformations) && isset($guestInformations.phone_invoice) && $guestInformations.phone_invoice}{$guestInformations.phone_invoice}{/if}" />
							</div>
							<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group col-md-6">
								<!-- <label for="phone_mobile_invoice">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>*</sup>{/if}</label> -->
								<input placeholder="{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} *{/if}" type="text" class="form-control validate" name="phone_mobile_invoice" id="phone_mobile_invoice" data-validate="isPhoneNumber" value="{if isset($guestInformations) && isset($guestInformations.phone_mobile_invoice) && $guestInformations.phone_mobile_invoice}{$guestInformations.phone_mobile_invoice}{/if}" />
							</div>
							<input type="hidden" name="alias_invoice" id="alias_invoice" value="{l s='My Invoice address'}" />
						</div>
					</div>
					<hr>
					{$HOOK_CREATE_ACCOUNT_FORM}
					<div class="submit opc-add-save clearfix text-right">
						<p class="required opc-required oblig">
							<sup>*</sup>{l s='Required field'}
						</p>
						<button type="submit" name="submitAccount"  id="submitAccount" class="btn btn-default button"><span>{l s='Save'}<!-- <i class="icon-chevron-right right"></i> --></span></button>

					</div>
					<div style="display: none;" id="opc_account_saved" class="alert alert-success">
						{l s='Account information saved successfully'}
					</div>
				<!-- END Account -->
				</div>
		</fieldset>
	</form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var npa = "{$npa}";
        //NPA
    console.info($('#postcode'));
    if($('#postcode').length){
        $('#postcode').val(npa);
        if(npa.length == 4){
            $('#postcode').attr('readonly',true);
        }
    }
    });
</script>
{strip}
{if isset($guestInformations) && isset($guestInformations.id_state) && $guestInformations.id_state}
	{addJsDef idSelectedState=$guestInformations.id_state|intval}
{else}
	{addJsDef idSelectedState=false}
{/if}
{if isset($guestInformations) && isset($guestInformations.id_state_invoice) && $guestInformations.id_state_invoice}
	{addJsDef idSelectedStateInvoice=$guestInformations.id_state_invoice|intval}
{else}
	{addJsDef idSelectedStateInvoice=false}
{/if}
{if isset($guestInformations) && isset($guestInformations.id_country) && $guestInformations.id_country}
	{addJsDef idSelectedCountry=$guestInformations.id_country|intval}
{else}
	{addJsDef idSelectedCountry=false}
{/if}
{if isset($guestInformations) && isset($guestInformations.id_country_invoice) && $guestInformations.id_country_invoice}
	{addJsDef idSelectedCountryInvoice=$guestInformations.id_country_invoice|intval}
{else}
	{addJsDef idSelectedCountryInvoice=false}
{/if}
{if isset($countries)}
	{addJsDef countries=$countries}
{/if}
{if isset($vatnumber_ajax_call) && $vatnumber_ajax_call}
	{addJsDef vatnumber_ajax_call=$vatnumber_ajax_call}
{/if}
{/strip}
