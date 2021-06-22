<script type="text/javascript" src="{$base_dir|escape:'htmlall'}modules/planningdeliverybycarrier/js/datepickerSlot.js"></script>
{$datepickerJs}

<div id="choose_delivery_date" style="display:none">
	<h3 style="margin-bottom:5px">{l s='Choose your delivery date' mod='planningdeliverybycarrier'}</h3>

	{if isset($pderrors) && $pderrors}
		<br />
		<div class="error">
			<p>{if $pderrors|@count > 1}{l s='There are %d errors' sprintf=$pderrors|@count mod='planningdeliverybycarrier'}{else}{l s='There is %d error' sprintf=$pderrors|@count  mod='planningdeliverybycarrier'}{/if}</p>
			<ol>
			{foreach from=$pderrors key=k item=error}
				<li>{$error}</li>
			{/foreach}
			</ol>
		</div>
	{/if}

	{if $opc}
		<input type="button" id="submitDateDelivery" name="submitDateDelivery" value="{l s='Save' mod='planningdeliverybycarrier'}" class="button" />
	{/if}
	<div id="day_slots" class="delivery_option">
		<label id="lab_date_delivery" for="date_delivery">
			{l s='Date de livraison' mod='planningdeliverybycarrier'} :
			<input style="width:210px; border:2px solid #CCC; margin-left:10px" type="text" name="date_delivery" id="date_delivery" value="{if isset($date_delivery)}{$date_delivery|escape:'htmlall'}{/if}" readonly />

			{if isset($date_delivery) && ($slotRequired == 0 || ($slotRequired == 1 && isset($delivery_slot)))}
				<span id="deliverydate_confirmed">{l s='Delivery date confirmed' mod='planningdeliverybycarrier'}</span>
			{/if}

		</label>
		{if isset($slotsAvalaibles) && count($slotsAvalaibles)}
		<label id="lab_delivery_slot" for="id_planning_delivery_slot">
			{l s='Time Slot' mod='planningdeliverybycarrier'} :
			<select  name="id_planning_delivery_slot" id="id_planning_delivery_slot" >
				<option> - </option>
				{foreach from=$slotsAvalaibles item=row key=key}
					<option value="{$key|escape:'htmlall'}" {if isset($delivery_slot) && $delivery_slot == $key}selected="selected"{/if}>{$row|escape:'htmlall'}</option>
				{/foreach}
			</select>
		</label>
		{/if}
	</div>
</div>

{literal}
<script type="text/javascript">

	var path = "{/literal}{$path}{literal}";
	var id_cart = "{/literal}{$id_cart}{literal}";
	var format = "{/literal}{$format}{literal}";
	var opc = "{/literal}{$opc}{literal}";
	var ps_version = {/literal}{$ps_version}{literal};
	var selector = getSelector();

	$(document).ready(function(){

		if ($('#carrierTable').length) $('#carrierTable').after($('#choose_delivery_date'));

		$(selector).click(function(){
			resetDateDelivery(path, id_cart);
			if (!opc && ps_version > 151000){
				$('#deliverydate_confirmed').remove();
				$('#lab_delivery_slot').remove();
				$('#date_delivery').val('');
				includeDatePicker(path);
			}
		});
		if (opc){
			$('#submitDateDelivery').click(function(){
				var id_carrier = getIdCarrierChecked();
				params = "submitDateDelivery=1&id_cart=" + id_cart + "&id_carrier=" + id_carrier + "&format=" + format;
				params += "&date_delivery=" + $('#date_delivery').val() + "&id_planning_delivery_slot=" + $('#id_planning_delivery_slot').val();
				if ($('#date_delivery').val() != ""){
					$.ajax({type:"GET", cache:false, url:path + "ajax/planningdeliverybycarrier.ajax.php?" + params, success:
						function(data){
							updateCarrierSelectionAndGift();
						}
					});
				}
				return false;
			});
		}
		includeDatePicker(path);
	});
	</script>
{/literal}

