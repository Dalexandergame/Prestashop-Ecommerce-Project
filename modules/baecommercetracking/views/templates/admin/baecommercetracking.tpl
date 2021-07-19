{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@buy-addons.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    Buy-addons    <contact@buy-addons.com>
* @copyright 2007-2020 Buy-addons
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if $demoMode=="1"}
<div class="bootstrap ba_error">
	<div class="module_error alert alert-danger">
		{l s='You are use ' mod='baecommercetracking'}
		<strong>{l s='Demo Mode' mod='baecommercetracking'}</strong>
		{l s=', so some buttons, functions will be disabled because of security. ' mod='baecommercetracking'}<br />
		{l s='You can use them in Live mode after you puchase our module. ' mod='baecommercetracking'}<br />
		{l s='Thanks !' mod='baecommercetracking'}
	</div>
</div>
{/if}
<div class="bootstrap form_config">
	<form action="" method="POST">
		<input type="hidden" value="{$languageDefault|escape:'htmlall':'UTF-8'}" id="languageDefault"/>
		<input type="hidden" value="{$currencyDefault|escape:'htmlall':'UTF-8'}" id="currencyDefault"/>
		<div style="width: 100%;float: left;" class="panel">
			<h3 style="margin-top:-16px !important;" class="ba_title_selection"><i class="icon-bar-chart"></i> {l s='Google Analytics' mod='baecommercetracking'}</h3>
			<div class="col-md-12">
				<lable class="col-md-3 control-label">{l s='Tracking ID' mod='baecommercetracking'}: </lable>
				<div class="col-md-5">
					<input type="text" name="baIdAnalytics" value="{$baIdAnalytics|escape:'htmlall':'UTF-8'}">
				</div>
			</div>
			<div class="panel-footer">
				<button type="submit" value="1" name="baCancel" class="btn btn-default pull-left">
					<i class="process-icon-cancel"></i> {l s='Cancel' mod='baecommercetracking'}
				</button>
				<button type="submit" value="1" name="baSave" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Save' mod='baecommercetracking'}
				</button>
				<button type="submit" name="baSaveAndStay" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Save and stay' mod='baecommercetracking'}
				</button>
			</div>
		</div>
		<div style="width: 100%;float: left;" class="panel">
			<h3 class="ba_title_selection"><i class="icon-bar-chart"></i> {l s='Google Adwords Conversion Tracking' mod='baecommercetracking'}</h3>
			<div class="col-md-12" style="margin-bottom: 7px;">
				<lable class="col-md-3 control-label">{l s='Add your Google Conversion Tracking ID' mod='baecommercetracking'}: </lable>
				<div class="col-md-5">
					<input id="idAdword" type="text" name="baIdAdwords" value="{$baIdAdwords|escape:'htmlall':'UTF-8'}">
				</div>
			</div>
			<div class="col-md-12" style="margin-bottom: 7px;">
				<lable class="col-md-3 control-label">{l s='Add your Google Conversion Tracking Label' mod='baecommercetracking'}: </lable>
				<div class="col-md-5">
					<input id="LabelAdword" type="text" name="baIdAdwordsLabel" value="{$baIdAdwordsLabel|escape:'htmlall':'UTF-8'}">
				</div>
			</div>
			<div class="col-md-12" style="margin-bottom: 7px;">
				<lable class="col-md-3 control-label">{l s='Test your code' mod='baecommercetracking'}: </lable>
				<div class="col-md-5">
					<input type="button" value="{l s='Click Here' mod='baecommercetracking'}" class="btn btn-info" id="btnTestYourCodeAdword">
				</div>
			</div>
			<div class="col-md-12" style="margin-bottom: 7px;" id="wrapperViewCodeAdword">
				<lable class="col-md-3 control-label">{l s='View Code' mod='baecommercetracking'}: </lable>
				<div class="col-md-5">
					<pre id="viewCodeAdword">
						
					</pre>
				</div>
			</div>
			<div class="panel-footer">
				<button type="submit" value="1" name="baAdwordsCancel" class="btn btn-default pull-left">
					<i class="process-icon-cancel"></i> {l s='Cancel' mod='baecommercetracking'}
				</button>
				<button type="submit" value="1" name="baAdwordsSave" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Save' mod='baecommercetracking'}
				</button>
				<button type="submit" name="baAdwordsSaveAndStay" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Save and stay' mod='baecommercetracking'}
				</button>
			</div>
		</div>
		<div style="width: 100%;float: left;" class="panel">
			<h3 class="ba_title_selection"><i class="icon-bar-chart"></i> {l s='Facebook Pixel' mod='baecommercetracking'}</h3>
			<div class="col-md-12" style="margin-bottom: 7px;">
				<lable class="col-md-3 control-label">{l s='Facebook ID' mod='baecommercetracking'}: </lable>
				<div class="col-md-5">
					<input id="facebookId" type="text" name="facebookId" value="{$facebookId|escape:'htmlall':'UTF-8'}">
				</div>
			</div>
			<div class="col-md-12" style="margin-bottom: 7px;">
				<lable class="col-md-3 control-label">{l s='Test your code' mod='baecommercetracking'}: </lable>
				<div class="col-md-5">
					<input type="button" value="{l s='Click Here' mod='baecommercetracking'}" class="btn btn-info" id="btnTestYourCodeFacebook">
				</div>
			</div>
			<div class="col-md-12" style="margin-bottom: 7px;" id="wrapperViewCodeFacebook">
				<lable class="col-md-3 control-label">{l s='View Code' mod='baecommercetracking'}: </lable>
				<div class="col-md-5">
					<pre id="viewCodeFacebook">
						
					</pre>
				</div>
			</div>
			<div class="panel-footer">
				<button type="submit" value="1" name="baFacebookCancel" class="btn btn-default pull-left">
					<i class="process-icon-cancel"></i> {l s='Cancel' mod='baecommercetracking'}
				</button>
				<button type="submit" value="1" name="baFacebookSave" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Save' mod='baecommercetracking'}
				</button>
				<button type="submit" name="baFacebookSaveAndStay" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Save and stay' mod='baecommercetracking'}
				</button>
			</div>
		</div>
	</form>
</div>