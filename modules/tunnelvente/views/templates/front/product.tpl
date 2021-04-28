
{if !isset($priceDisplayPrecision)}
        {assign var='priceDisplayPrecision' value=2}
{/if}
{if !$priceDisplay || $priceDisplay == 2}
        {assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)}
        {assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL, $priceDisplayPrecision)}
{elseif $priceDisplay == 1}
        {assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, $priceDisplayPrecision)}
        {assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL, $priceDisplayPrecision)}
{/if}
<!-- hidden datas -->
<p class="hidden">
        <input type="hidden" name="token" value="{$static_token}" />
        <input type="hidden" name="id_product" value="{$product->id|intval}" id="product_page_product_id" />
        <input type="hidden" name="add" value="1" />
        <input type="hidden" name="id_product_attribute" id="idCombination" value="" />
        <input type="hidden" name="little_tunnel" id="little_tunnel" value="1" />
</p>
<div class="image">
    {if $myLittelEcosapin}
        <p class="text-arrow-descp">{l s="Cliquez sur les flêches pour choisir la déco et la couleur du pot" mod='tunnelvente'}</p>
        <div class="plus_infos">
            <div class="deco text-center">
                <span>{l s='Décoration'}</span>
                <span class="name"></span>
            </div>                                        
        </div>
        <span class="prev prev_deco" data-numbre_attribute="0"><i class="icon-chevron-left"></i></span>
        <span class="prev prev_pot" data-numbre_attribute="1"><i class="icon-chevron-left"></i></span>
    {/if}
    <!-- product img-->
    <div id="image-block" class="clearfix">
            {if $product->new}
                    <span class="new-box">
                            <span class="new-label">{l s='New' mod='tunnelvente'}</span>
                    </span>
            {/if}
           
            {if $have_image}
                    <span id="view_full_size">
                            {if $jqZoomEnabled && $have_image && !$content_only}
                                    <a class="jqzoom" title="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}" rel="gal1" href="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'thickbox_default')|escape:'html':'UTF-8'}" itemprop="url">
                                            <img itemprop="image" src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large_default')|escape:'html':'UTF-8'}" title="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}" alt="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}"/>
                                    </a>
                            {else}
                                    <img id="bigpic" itemprop="image" src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large_default')|escape:'html':'UTF-8'}" title="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}" alt="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}" width="{$largeSize.width}" height="{$largeSize.height}"/>
                                    {if !$content_only}
                                            <span class="span_link no-print">{l s='View larger'}</span>
                                    {/if}
                            {/if}
                    </span>
            {else}
                    <span id="view_full_size">
                            <img itemprop="image" src="{$img_prod_dir}{$lang_iso}-default-large_default.jpg" id="bigpic" alt="" title="{$product->name|escape:'html':'UTF-8'}" width="{$largeSize.width}" height="{$largeSize.height}"/>
                            {if !$content_only}
                                    <span class="span_link">
                                            {l s='View larger'}
                                    </span>
                            {/if}
                    </span>
            {/if}
    </div> <!-- end image-block -->
    {if $myLittelEcosapin}
        <div class="plus_infos">                                        
            <div class="pot text-center">
                <span>{l s='Couleur de pot' mod='tunnelvente'}</span>
                <span class="name"></span>
            </div>                                        
        </div>
        <span class="next next_deco" data-numbre_attribute="0"><i class="icon-chevron-right"></i></span>
        <span class="next next_pot" data-numbre_attribute="1"><i class="icon-chevron-right"></i></span>
    {/if}
    {if isset($images) && count($images) > 0}
            <!-- thumbnails -->
            <div id="views_block" class="clearfix hidden {if isset($images) && count($images) < 2}hidden{/if}">
                    {if isset($images) && count($images) > 2}
                            <span class="view_scroll_spacer">
                                    <a id="view_scroll_left" class="" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">
                                            {l s='Previous'}
                                    </a>
                            </span>
                    {/if}
                    <div id="thumbs_list">
                            <ul id="thumbs_list_frame">
                            {if isset($images)}
                                    {foreach from=$images item=image name=thumbnails}
                                            {assign var=imageIds value="`$product->id`-`$image.id_image`"}
                                            {if !empty($image.legend)}
                                                    {assign var=imageTitle value=$image.legend|escape:'html':'UTF-8'}
                                            {else}
                                                    {assign var=imageTitle value=$product->name|escape:'html':'UTF-8'}
                                            {/if}
                                            <li id="thumbnail_{$image.id_image}"{if $smarty.foreach.thumbnails.last} class="last"{/if}>
                                                    <a{if $jqZoomEnabled && $have_image && !$content_only} href="javascript:void(0);" rel="{literal}{{/literal}gallery: 'gal1', smallimage: '{$link->getImageLink($product->link_rewrite, $imageIds, 'large_default')|escape:'html':'UTF-8'}',largeimage: '{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html':'UTF-8'}'{literal}}{/literal}"{else} href="{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html':'UTF-8'}"	data-fancybox-group="other-views" class="fancybox{if $image.id_image == $cover.id_image} shown{/if}"{/if} title="{$imageTitle}">
                                                            <img class="img-responsive" id="thumb_{$image.id_image}" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'cart_default')|escape:'html':'UTF-8'}" alt="{$imageTitle}" title="{$imageTitle}" height="{$cartSize.height}" width="{$cartSize.width}" itemprop="image" />
                                                    </a>
                                            </li>
                                    {/foreach}
                            {/if}
                            </ul>
                    </div> <!-- end thumbs_list -->
                    {if isset($images) && count($images) > 2}
                            <a id="view_scroll_right" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">
                                    {l s='Next'}
                            </a>
                    {/if}
            </div> <!-- end views-block -->
            <!-- end thumbnails -->
    {/if}
    {if isset($images) && count($images) > 1}
            <p class="resetimg hidden clear no-print">
                    <span id="wrapResetImages" style="display: none;">
                            <a href="{$link->getProductLink($product)|escape:'html':'UTF-8'}" data-id="resetImages">
                                    <i class="icon-repeat"></i>
                                    {l s='Display all pictures'}
                            </a>
                    </span>
            </p>
    {/if}
		 
</div>

<div class="content_prices clearfix">
    {if $product->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
            <!-- prices -->
            <div>
                     <p id="old_price"{if (!$product->specificPrice || !$product->specificPrice.reduction) && $group_reduction == 0} class="hidden"{/if}>
                        {strip}
                            {if $priceDisplay >= 0 && $priceDisplay <= 2}
                                    {hook h="displayProductPriceBlock" product=$product type="old_price"}
                                    <span id="old_price_display">{if $productPriceWithoutReduction > $productPrice}<span class="price">{convertPrice price=$productPriceWithoutReduction}</span>{/if}</span>
                            {/if}
                        {/strip}
                    </p>
                    <p class="our_price_display" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                        {strip}
                            {if $product->quantity > 0}<link itemprop="availability" href="http://schema.org/InStock"/>{/if}
                            {if $priceDisplay >= 0 && $priceDisplay <= 2}
                                    <span id="our_price_display" class="price" itemprop="price">{convertPrice price=$productPrice}</span>
                                    {*{if $tax_enabled  && ((isset($display_tax_label) && $display_tax_label == 1) || !isset($display_tax_label))}
                                            {if $priceDisplay == 1} {l s='tax excl.'}{else} {l s='tax incl.'}{/if}
                                    {/if}*}
                                    <meta itemprop="priceCurrency" content="{$currency->iso_code}" />
                                    {hook h="displayProductPriceBlock" product=$product type="price"}
                            {/if}
                        {/strip}
                    </p>
                   {* <p id="reduction_percent" {if !$product->specificPrice || $product->specificPrice.reduction_type != 'percentage'} style="display:none;"{/if}>
                    {strip}
                            <span id="reduction_percent_display">
                                    {if $product->specificPrice && $product->specificPrice.reduction_type == 'percentage'}-{$product->specificPrice.reduction*100}%{/if}
                            </span>
                    {/strip}
                    </p>
                    <p id="reduction_amount" {if !$product->specificPrice || $product->specificPrice.reduction_type != 'amount' || $product->specificPrice.reduction|floatval ==0} style="display:none"{/if}>
                    {strip}
                            <span id="reduction_amount_display">
                            {if $product->specificPrice && $product->specificPrice.reduction_type == 'amount' && $product->specificPrice.reduction|floatval !=0}
                                    -{convertPrice price=$productPriceWithoutReduction-$productPrice|floatval}
                            {/if}
                            </span>
                    {/strip}
                    </p>*}
                   
                    {if $priceDisplay == 2}
                            <br />
                            <span id="pretaxe_price">{strip}
                                    <span id="pretaxe_price_display">{convertPrice price=$product->getPrice(false, $smarty.const.NULL)}</span> {l s='tax excl.'}
                            {/strip}</span>
                    {/if}
            </div> <!-- end prices -->
            {if $packItems|@count && $productPrice < $product->getNoPackPrice()}
                    <p class="pack_price">{l s='Instead of'} <span style="text-decoration: line-through;">{convertPrice price=$product->getNoPackPrice()}</span></p>
            {/if}
            {if $product->ecotax != 0}
                    <p class="price-ecotax">{l s='Including'} <span id="ecotax_price_display">{if $priceDisplay == 2}{$ecotax_tax_exc|convertAndFormatPrice}{else}{$ecotax_tax_inc|convertAndFormatPrice}{/if}</span> {l s='for ecotax'}
                            {if $product->specificPrice && $product->specificPrice.reduction}
                            <br />{l s='(not impacted by the discount)'}
                            {/if}
                    </p>
            {/if}
            {if !empty($product->unity) && $product->unit_price_ratio > 0.000000}
                    {math equation="pprice / punit_price"  pprice=$productPrice  punit_price=$product->unit_price_ratio assign=unit_price}
                    <p class="unit-price"><span id="unit_price_display">{convertPrice price=$unit_price}</span> {l s='per'} {$product->unity|escape:'html':'UTF-8'}</p>
                    {hook h="displayProductPriceBlock" product=$product type="unit_price"}
            {/if}
    {/if} {*close if for show price*}
    {hook h="displayProductPriceBlock" product=$product type="weight"}
    <div class="clear"></div>
</div> <!-- end content_prices -->

<h3 class="name">{$product->name}</h3>
<!-- availability or doesntExist -->
<p id="availability_statut"{if !$PS_STOCK_MANAGEMENT || ($product->quantity <= 0 && !$product->available_later && $allow_oosp) || ($product->quantity > 0 && !$product->available_now) || !$product->available_for_order || $PS_CATALOG_MODE} style="display: none;"{/if}>
        {*<span id="availability_label">{l s='Availability:'}</span>*}
        <span id="availability_value" class="label{if $product->quantity <= 0 && !$allow_oosp} label-danger{elseif $product->quantity <= 0} label-warning{else} label-success{/if}">{if $product->quantity <= 0}{if $PS_STOCK_MANAGEMENT && $allow_oosp}{$product->available_later}{else}{l s='This product is no longer in stock'}{/if}{elseif $PS_STOCK_MANAGEMENT}{$product->available_now}{/if}</span>
</p>
<div class="box-cart-bottom">
        <div{if (!$allow_oosp && $product->quantity <= 0) || !$product->available_for_order || (isset($restricted_country_mode) && $restricted_country_mode) || $PS_CATALOG_MODE} class="unvisible"{/if}>
                <p id="add_to_cart" class="buttons_bottom_block no-print">
                        <button type="submit" name="Submit" class="exclusive">
                                <span>{if $content_only && (isset($product->customization_required) && $product->customization_required)}{l s='Customize' mod='tunnelvente'}{else}{l s='Add to cart' mod='tunnelvente'}{/if}</span>
                        </button>
                </p>
        </div>
        {if isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS}{$HOOK_PRODUCT_ACTIONS}{/if}
</div> <!-- end box-cart-bottom -->

<div class="product_attributes clearfix">
        <!-- quantity wanted -->
        {if !$PS_CATALOG_MODE}
        <p id="quantity_wanted_p"{if (!$allow_oosp && $product->quantity <= 0) || !$product->available_for_order || $PS_CATALOG_MODE} style="display: none;"{/if}>                
                 <a href="#" data-field-qty="qty" class="btn btn-default button-plus product_quantity_up">
                        <span><i class="icon-plus"></i></span>
                </a>
            <input type="text" name="qty" id="quantity_wanted" readonly="" class="text" value="{if isset($quantityBackup)}{$quantityBackup|intval}{else}{if $product->minimal_quantity > 1}{$product->minimal_quantity}{else}1{/if}{/if}" />
               
               <a href="#" data-field-qty="qty" class="btn btn-default button-minus product_quantity_down">
                        <span><i class="icon-minus"></i></span>
                </a>
                <span class="clearfix"></span>
        </p>
        {/if}
        <!-- minimal quantity wanted -->
        <p id="minimal_quantity_wanted_p"{if $product->minimal_quantity <= 1 || !$product->available_for_order || $PS_CATALOG_MODE} style="display: none;"{/if}>
                {l s='The minimum purchase order quantity for the product is'} <b id="minimal_quantity_label">{$product->minimal_quantity}</b>
        </p>
        {if isset($groups)}
                <!-- attributes -->
                <div id="attributes" class=" {if $myLittelEcosapin}hidden {/if}">
                        <div class="clearfix"></div>
                        {foreach from=$groups key=id_attribute_group item=group}
                                {if $group.attributes|@count}
                                        <fieldset class="attribute_fieldset">
                                                <label class="attribute_label" {if $group.group_type != 'color' && $group.group_type != 'radio'}for="group_{$id_attribute_group|intval}"{/if}>{$group.name|escape:'html':'UTF-8'}&nbsp;</label>
                                                {assign var="groupName" value="group_$id_attribute_group"}
                                                <div class="attribute_list">
                                                        {if ($group.group_type == 'select')}
                                                                <select name="{$groupName}" id="group_{$id_attribute_group|intval}" class="form-control attribute_select no-print">
                                                                        {foreach from=$group.attributes key=id_attribute item=group_attribute}
                                                                                <option value="{$id_attribute|intval}"{if (isset($smarty.get.$groupName) && $smarty.get.$groupName|intval == $id_attribute) || $group.default == $id_attribute} selected="selected"{/if} title="{$group_attribute|escape:'html':'UTF-8'}">{$group_attribute|escape:'html':'UTF-8'}</option>
                                                                        {/foreach}
                                                                </select>
                                                        {elseif ($group.group_type == 'color')}
                                                                <ul id="color_to_pick_list" class="clearfix">
                                                                        {assign var="default_colorpicker" value=""}
                                                                        {foreach from=$group.attributes key=id_attribute item=group_attribute}
                                                                                {assign var='img_color_exists' value=file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
                                                                                <li{if $group.default == $id_attribute} class="selected"{/if}>
                                                                                        <a href="{$link->getProductLink($product)|escape:'html':'UTF-8'}" id="color_{$id_attribute|intval}" name="{$colors.$id_attribute.name|escape:'html':'UTF-8'}" class="color_pick{if ($group.default == $id_attribute)} selected{/if}"{if !$img_color_exists && isset($colors.$id_attribute.value) && $colors.$id_attribute.value} style="background:{$colors.$id_attribute.value|escape:'html':'UTF-8'};"{/if} title="{$colors.$id_attribute.name|escape:'html':'UTF-8'}">
                                                                                                {if $img_color_exists}
                                                                                                        <img src="{$img_col_dir}{$id_attribute|intval}.jpg" alt="{$colors.$id_attribute.name|escape:'html':'UTF-8'}" title="{$colors.$id_attribute.name|escape:'html':'UTF-8'}" width="20" height="20" />
                                                                                                {/if}
                                                                                        </a>
                                                                                </li>
                                                                                {if ($group.default == $id_attribute)}
                                                                                        {$default_colorpicker = $id_attribute}
                                                                                {/if}
                                                                        {/foreach}
                                                                </ul>
                                                                <input type="hidden" class="color_pick_hidden" name="{$groupName|escape:'html':'UTF-8'}" value="{$default_colorpicker|intval}" />
                                                        {elseif ($group.group_type == 'radio')}
                                                                <ul>
                                                                        {foreach from=$group.attributes key=id_attribute item=group_attribute}
                                                                                <li>
                                                                                        <input type="radio" class="attribute_radio" name="{$groupName|escape:'html':'UTF-8'}" value="{$id_attribute}" {if ($group.default == $id_attribute)} checked="checked"{/if} />
                                                                                        <span>{$group_attribute|escape:'html':'UTF-8'}</span>
                                                                                </li>
                                                                        {/foreach}
                                                                </ul>
                                                        {/if}
                                                </div> <!-- end attribute_list -->
                                        </fieldset>
                                {/if}
                        {/foreach}
                </div> <!-- end attributes -->
        {/if}
</div> <!-- end product_attributes -->

<sapn class="text_p_contractuelle">{l s='Photo non contractuelle' mod='tunnelvente'}</sapn>

{if $myLittelEcosapin}
<script type="text/javascript">
    $(function($){
        
        $('.thirdCol .text_p_contractuelle').show();
        if($('.plus_infos').length){
            $('.plus_infos .pot .name').text($('select.attribute_select:eq('+$('.next_pot').data('numbre_attribute')+')').find('option')
                    .filter('[value="'+$('select.attribute_select:eq('+$('.next_pot').data('numbre_attribute')+')').val()+'"]').text());

            $('.plus_infos .deco .name').text($('select.attribute_select:eq('+$('.next_deco').data('numbre_attribute')+')').find('option')
                    .filter('[value="'+$('select.attribute_select:eq('+$('.next_deco').data('numbre_attribute')+')').val()+'"]').text());
        }
    });
</script>
{/if}

{strip}
{if isset($smarty.get.ad) && $smarty.get.ad}
	{addJsDefL name=ad}{$base_dir|cat:$smarty.get.ad|escape:'html':'UTF-8'}{/addJsDefL}
{/if}
{if isset($smarty.get.adtoken) && $smarty.get.adtoken}
	{addJsDefL name=adtoken}{$smarty.get.adtoken|escape:'html':'UTF-8'}{/addJsDefL}
{/if}
{addJsDef allowBuyWhenOutOfStock=$allow_oosp|boolval}
{addJsDef availableNowValue=$product->available_now|escape:'quotes':'UTF-8'}
{addJsDef availableLaterValue=$product->available_later|escape:'quotes':'UTF-8'}
{addJsDef attribute_anchor_separator=$attribute_anchor_separator|escape:'quotes':'UTF-8'}
{addJsDef attributesCombinations=$attributesCombinations}
{addJsDef currencySign=$currencySign|html_entity_decode:2:"UTF-8"}
{addJsDef currencyRate=$currencyRate|floatval}
{addJsDef currencyFormat=$currencyFormat|intval}
{addJsDef currencyBlank=$currencyBlank|intval}
{addJsDef currentDate=$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}
{if isset($combinations) && $combinations}
	{addJsDef combinations=$combinations}
	{addJsDef combinationsFromController=$combinations}
	{addJsDef displayDiscountPrice=$display_discount_price}
	{addJsDefL name='upToTxt'}{l s='Up to' js=1}{/addJsDefL}
{/if}
{if isset($combinationImages) && $combinationImages}
	{addJsDef combinationImages=$combinationImages}
{/if}
{addJsDef customizationFields=$customizationFields}
{addJsDef default_eco_tax=$product->ecotax|floatval}
{addJsDef displayPrice=$priceDisplay|intval}
{addJsDef ecotaxTax_rate=$ecotaxTax_rate|floatval}
{addJsDef group_reduction=$group_reduction}
{if isset($cover.id_image_only)}
	{addJsDef idDefaultImage=$cover.id_image_only|intval}
{else}
	{addJsDef idDefaultImage=0}
{/if}
{addJsDef img_ps_dir=$img_ps_dir}
{addJsDef img_prod_dir=$img_prod_dir}
{addJsDef id_product=$product->id|intval}
{addJsDef jqZoomEnabled=$jqZoomEnabled|boolval}
{addJsDef maxQuantityToAllowDisplayOfLastQuantityMessage=$last_qties|intval}
{addJsDef minimalQuantity=$product->minimal_quantity|intval}
{addJsDef noTaxForThisProduct=$no_tax|boolval}
{addJsDef customerGroupWithoutTax=$customer_group_without_tax|boolval}
{addJsDef oosHookJsCodeFunctions=Array()}
{addJsDef productHasAttributes=isset($groups)|boolval}
{addJsDef productPriceTaxExcluded=($product->getPriceWithoutReduct(true)|default:'null' - $product->ecotax)|floatval}
{addJsDef productBasePriceTaxExcluded=($product->base_price - $product->ecotax)|floatval}
{addJsDef productBasePriceTaxExcl=($product->base_price|floatval)}
{addJsDef productReference=$product->reference|escape:'html':'UTF-8'}
{addJsDef productAvailableForOrder=$product->available_for_order|boolval}
{addJsDef productPriceWithoutReduction=$productPriceWithoutReduction|floatval}
{addJsDef productPrice=$productPrice|floatval}
{addJsDef productUnitPriceRatio=$product->unit_price_ratio|floatval}
{addJsDef productShowPrice=(!$PS_CATALOG_MODE && $product->show_price)|boolval}
{addJsDef PS_CATALOG_MODE=$PS_CATALOG_MODE}
{if $product->specificPrice && $product->specificPrice|@count}
	{addJsDef product_specific_price=$product->specificPrice}
{else}
	{addJsDef product_specific_price=array()}
{/if}
{if $display_qties == 1 && $product->quantity}
	{addJsDef quantityAvailable=$product->quantity}
{else}
	{addJsDef quantityAvailable=0}
{/if}
{addJsDef quantitiesDisplayAllowed=$display_qties|boolval}
{if $product->specificPrice && $product->specificPrice.reduction && $product->specificPrice.reduction_type == 'percentage'}
	{addJsDef reduction_percent=$product->specificPrice.reduction*100|floatval}
{else}
	{addJsDef reduction_percent=0}
{/if}
{if $product->specificPrice && $product->specificPrice.reduction && $product->specificPrice.reduction_type == 'amount'}
	{addJsDef reduction_price=$product->specificPrice.reduction|floatval}
{else}
	{addJsDef reduction_price=0}
{/if}
{if $product->specificPrice && $product->specificPrice.price}
	{addJsDef specific_price=$product->specificPrice.price|floatval}
{else}
	{addJsDef specific_price=0}
{/if}
{addJsDef specific_currency=($product->specificPrice && $product->specificPrice.id_currency)|boolval} {* TODO: remove if always false *}
{addJsDef stock_management=$PS_STOCK_MANAGEMENT|intval}
{addJsDef taxRate=$tax_rate|floatval}
{addJsDefL name=doesntExist}{l s='This combination does not exist for this product. Please select another combination.' js=1}{/addJsDefL}
{addJsDefL name=doesntExistNoMore}{l s='This product is no longer in stock' js=1}{/addJsDefL}
{addJsDefL name=doesntExistNoMoreBut}{l s='with those attributes but is available with others.' js=1}{/addJsDefL}
{addJsDefL name=fieldRequired}{l s='Please fill in all the required fields before saving your customization.' js=1}{/addJsDefL}
{addJsDefL name=uploading_in_progress}{l s='Uploading in progress, please be patient.' js=1}{/addJsDefL}
{addJsDefL name='product_fileDefaultHtml'}{l s='No file selected' js=1}{/addJsDefL}
{addJsDefL name='product_fileButtonHtml'}{l s='Choose File' js=1}{/addJsDefL}
{/strip}

