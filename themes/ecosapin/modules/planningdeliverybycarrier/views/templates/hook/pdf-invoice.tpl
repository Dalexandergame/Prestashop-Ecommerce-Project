
<table style="width: 100%" class="tproduct">
	<tr style="line-height:4px;color: #243f18;" class="bg-row-header">
		<td style="text-align: left;width:20%;">{l s='Delivery Date' mod='planningdeliverybycarrier'}</td>
{*		<td style="text-align: left;width:20%;">{l s='Delivery Slot' mod='planningdeliverybycarrier'}</td>*}
                <td style="border-left: 2px solid #FFF; text-align: left;width:20%;">{l s='Date retour' mod='planningdeliverybycarrier'}</td>
{*		<td style="text-align: left;width:20%;">{l s='Delivery Slot' mod='planningdeliverybycarrier'}</td>*}
	</tr>
	<tr style="line-height:6px;color: #243f18;background-color:{cycle values='#FFF, #C4DCB5'};">
            <td style="width:20%;">{dateFormat date=$date_delivery}</td>
{*            <td style="width:20%;text-align: left;">{$delivery_slot|escape:'htmlall'}</td>	*}
            <td style="width:20%;border-left: 2px solid #FFF;">{dateFormat date=$date_retour}</td>
{*            <td style="width:20%;text-align: left;">-</td>*}
	</tr>
</table>