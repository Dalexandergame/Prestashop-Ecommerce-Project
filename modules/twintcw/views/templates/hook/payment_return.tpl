{if isset($paymentMethodMessage) && !empty($paymentMethodMessage)}
	<p class="payment-method-message">{$paymentMethodMessage}</p>
{/if}

{if isset($paymentInformation) && !empty($paymentInformation)}
	<div class="twintcw-invoice-payment-information twintcw-payment-return-table" id="twintcw-invoice-payment-information">
		<h4>{$paymentInformationTitle}</h4>
		{$paymentInformation nofilter}
	</div>
{/if}