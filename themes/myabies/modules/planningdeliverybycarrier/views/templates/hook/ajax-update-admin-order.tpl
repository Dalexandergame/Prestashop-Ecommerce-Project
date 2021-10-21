{if isset($pderrors) && $pderrors}
	<br />
	<div class="error">
		<p>{if $pderrors|@count > 1}{l s='There are %d errors' sprintf=$pderrors|@count mod='planningdeliverybycarrier'}{else}{l s='There is %d error' sprintf=$pderrors|@count mod='planningdeliverybycarrier'}{/if}</p>
		<ol>
		{foreach from=$pderrors key=k item=error}
			<li>{$error|escape:'htmlall'}</li>
		{/foreach}
		</ol>
	</div>
{/if}

<table id="tab_planningdelivery" class="table" width="100%" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th style="width:30%">{l s='Delivery Day' mod='planningdeliverybycarrier'}</th>
			<th style="width:20%">{l s='Time Slot' mod='planningdeliverybycarrier'}</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>{dateFormat date=$date_delivery}</td>
			<td>{$delivery_slot|escape:'htmlall'}</td>
		</tr>
	</tbody>
</table>
<table id="tab_planningretour" class="table" width="100%" cellspacing="0" cellpadding="0">
        <thead>
                <tr>
                        <th style="width:30%">{l s='Date de retour' mod='planningdeliverybycarrier'}</th>
{*						<th style="width:20%">{l s='Time Slot' mod='planningdeliverybycarrier'}</th>*}
                </tr>
        </thead>
        <tbody>
                <tr>
                        <td>{dateFormat date=$date_retour}</td>
{*						<td>{$delivery_slot}</td>*}
                </tr>
        </tbody>
</table>