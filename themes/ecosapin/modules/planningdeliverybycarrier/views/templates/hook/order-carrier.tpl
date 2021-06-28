<script type="text/javascript" src="{$base_dir|escape:'htmlall'}modules/planningdeliverybycarrier/js/datepickerSlot.js"></script>
{$datepickerJs}
<div id="choose_delivery_date" class="" style="display:none" data-idcarrierpost="{$id_carrier_post}">
	<p class="carrier_title norm col-md-12">{l s='Choose your delivery date' mod='planningdeliverybycarrier'}</p>

	{if isset($pderrors) && $pderrors}
		<br />
		<div class="error" style="color: red">
			<p>{if $pderrors|@count > 1}{l s='There are %d errors' sprintf=$pderrors|@count mod='planningdeliverybycarrier'}{else}{l s='There is %d error' sprintf=$pderrors|@count  mod='planningdeliverybycarrier'}{/if}</p>
			<ol>
			{foreach from=$pderrors key=k item=error}
				<li>{$error}</li>
			{/foreach}
			</ol>
		</div>
	{/if}
	<div class="">
	
	<div id="day_slots" class="delivery_option col-md-4">
		<label id="lab_date_delivery" for="date_delivery">
			{l s='Date de livraison' mod='planningdeliverybycarrier'} :
			<input style="margin-left:10px" type="text" name="date_delivery" id="date_delivery"
				   value="{if isset($date_delivery)}{$date_delivery|escape:'htmlall'}{/if}" readonly />

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
        {*tst*}
        {if isset($dateRetourRequired) && $dateRetourRequired }
	<div id="day_slots_retour" class="delivery_option col-md-4">
		<label id="lab_date_retour" for="date_retour">
			{l s='Date de retour' mod='planningdeliverybycarrier'} :
			<input style="margin-left:10px" type="text" name="date_retour" id="date_retour" value="{if isset($date_retour)}{$date_retour|escape:'htmlall'}{/if}" readonly />

			{if isset($date_retour) && $dateRetourRequired }
				<span id="deliverydate_confirmed">{l s='Date de retour confirmée' mod='planningdeliverybycarrier'}</span>
			{/if}

		</label>		
	</div>
        {/if}
       {if $opc}
       <div class="col-md-12">
		<input type="button" id="submitDateDelivery" name="submitDateDelivery" value="{l s='Save' mod='planningdeliverybycarrier'}" class="btn btn-default link-home" /></div>
	{/if}
        </div>
	<div class="loading"></div>
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
				$('#date_retour').val('');
				includeDatePicker(path);
			}
		});
        if (opc) {
            $('#submitDateDelivery').click(function () {
                if ($('#message').val() == '') {
                    $("#message-error").show();
                } else {
                    $("#message-error").hide();
                    var $me = $("#choose_delivery_date"),classe= 'isactive';
                    if(!$me.hasClass(classe))
                        $me.addClass(classe);
                    var id_carrier = getIdCarrierChecked();
                    params = "submitDateDelivery=1&id_cart=" + id_cart + "&id_carrier=" + id_carrier + "&format=" + format;
                    params += "&date_delivery=" + $('#date_delivery').val() + "&id_planning_delivery_slot=" + $('#id_planning_delivery_slot').val();
                    params += "&date_retour=" + $('#date_retour').val();
//                    console.log(params);
                    if ($('#date_delivery').val() != "") {
                        $.ajax({
                            type: "GET",
                            cache: false,
                            url: path + "ajax/planningdeliverybycarrier.ajax.php?" + params,
                            success: function (data) {
                                //test
                                updateCarrierSelectionAndGift();
                            }
                        });
                    }
                    return false;
                }
            });
        }
        includeDatePicker(path);
	});
	</script>
{/literal}

