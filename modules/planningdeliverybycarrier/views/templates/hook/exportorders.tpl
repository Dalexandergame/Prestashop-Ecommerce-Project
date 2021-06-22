<h2>{l s='Export Deliveries' mod='planningdeliverybycarrier'}</h2>

<fieldset>
	<form method="post" action="{$action|escape:'htmlall'}">
		{l s='Export Dates' mod='planningdeliverybycarrier'} {l s='From' mod='planningdeliverybycarrier'} :
		<input type="text" name="datepickerFrom" id="datepickerFrom" />
		{l s='to' mod='planningdeliverybycarrier'} : <input type="text" name="datepickerTo" id="datepickerTo"/>
		{l s='Status' mod='planningdeliverybycarrier'} : <select name="orderState">{$orderStates}</select>
		{l s='Carrier' mod='planningdeliverybycarrier'} : <select name="carrier">{$carriers}</select>
		{l s='delimiter' mod='planningdeliverybycarrier'} : <select name="delimiter" width="10"><option value="1">;</option><option value="2">, </option></select>
		{l s='enclosure' mod='planningdeliverybycarrier'} : <select name="enclosure" width="10"><option value="1">"</option><option value="2">'</option></select>
		<input type="submit" class="button" name="renderCSV" value="{l s='Generate CSV' mod='planningdeliverybycarrier'}" />
	</form>
</fieldset>