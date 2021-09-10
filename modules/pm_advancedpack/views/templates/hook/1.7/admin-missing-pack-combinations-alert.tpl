{l s='You have one or more packs with invalid product default combinations. You must choose another default combination for each product in conflict and then save the corresponding pack(s) to fix the issue.' mod='pm_advancedpack'}
<ul style="list-style:none;margin:0;padding:0">
	<li style="display: inline-block;">{l s='Pack(s) to fix:' mod='pm_advancedpack'}</li>
{foreach from=$idPackListToFix item='packToFix'}
	<li style="display: inline-block;">
		<a href="{$packToFix.editPackLink|escape:'html':'UTF-8'}">#{$packToFix.idPack|intval}</a>
	</li>
{/foreach}
</ul>