
<h2>{lcw s='Refund Transaction' mod='twintcw'}</h2>

<p>{lcw s='You are along the way to refund the order %s.' mod='twintcw' sprintf=$orderId} 
{lcw s='Do you want to send this order also the?' mod='twintcw'}</p>

<p>{lcw s='Amount to refund:' mod='twintcw'} {$refundAmount} {$transaction->getCurrencyCode()}</p>

{if !$transaction->isRefundClosable()}
	<p><strong>{lcw s='This is the last refund possible on this transaction. This payment method does not support any further refunds.' mod='twintcw'}</strong></p>
{/if}

<form action="{$targetUrl}" method="POST">
<p>
	{$hiddenFields}	
	<a class="button" href="{$backUrl}">{lcw s='Cancel' mod='twintcw'}</a>
	<input type="submit" class="button" name="submitTwintCwRefundNormal" value="{lcw s='No' mod='twintcw'}" />
	<input type="submit" class="button" name="submitTwintCwRefundAuto" value="{lcw s='Yes' mod='twintcw'}" />
</p>
</form>