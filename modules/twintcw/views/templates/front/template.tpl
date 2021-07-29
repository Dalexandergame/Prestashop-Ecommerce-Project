
{capture name=path}{lcw s='Payment' mod='twintcw'}{/capture}

<h2>{lcw s='Payment' mod='twintcw'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

$$$PAYMENT ZONE$$$