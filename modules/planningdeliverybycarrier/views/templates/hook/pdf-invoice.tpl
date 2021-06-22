
<table style="width: 100%" >
	<tr style="line-height:5px;">
		<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold;width:60%"></td>
		<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold;width:20%;">{l s='Delivery Date' mod='planningdeliverybycarrier'}</td>
		<td style="text-align: center; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold;width:20%;">{l s='Delivery Slot' mod='planningdeliverybycarrier'}</td>
		<td style="text-align: right; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold;width:20%;">{l s='Date Retour' mod='planningdeliverybycarrier'}</td>
	</tr>
	<tr style="line-height:6px;background-color:{cycle values='#FFF, #DDD'};">
	 <td style="width:60%"></td>
	 <td style="width:20%;">{dateFormat date=$date_delivery}</td>
		<td style="width:20%;text-align: center;">{$delivery_slot|escape:'htmlall'}</td>
		<td style="width:20%;text-align: right;">{dateFormat date=$date_retour}</td>
	</tr>
</table>