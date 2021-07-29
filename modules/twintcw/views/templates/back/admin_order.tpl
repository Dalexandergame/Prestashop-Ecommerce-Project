<div class="panel">
	<div class="panel-heading">
		<img src="{$base_url}modules/twintcw/logo.gif" alt="" />{lcw s='Transactions' mod='twintcw'}
	</div>
	
	{if $errorMessage}
		<div class="twintcw-error-message">
			{$errorMessage}
		</div>
	{/if}
	
	<div>
		<table class="table twintcw-transaction-table" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>#</th>
					<th>{lcw s='Date' mod='twintcw'}</th>
					<th>{lcw s='Payment Method' mod='twintcw'}</th>
					<th>{lcw s='Is Authorized' mod='twintcw'}</th>
					<th>{lcw s='Amount' mod='twintcw'}</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$transactions item=transaction}
					{if $activeTransactionId == $transaction->getTransactionId()}
						{assign var="activeClass" value="active"}
					{else}
						{assign var="activeClass" value=""}
					{/if}
					<tr id="twintcw-_main_row_{$transaction->getTransactionId()}" class="twintcw-main-row {$activeClass}">
						<td>{$transaction->getTransactionId()}</td>
						<td>{$transaction->getCreatedOn()->format($date_format)}</td>
						<td>
							{if $transaction->getTransactionObject()}
								{$transaction->getTransactionObject()->getPaymentMethod()->getPaymentMethodDisplayName()}
							{else}
								--
							{/if}
						</td>
						<td>
							{if $transaction->getTransactionObject() && $transaction->getTransactionObject()->isAuthorized()}
								{lcw s='yes' mod='twintcw'}
							{else}
								{lcw s='no' mod='twintcw'}
							{/if}
						</td>
						<td>
							{if $transaction->getTransactionObject()}
								{$transaction->getTransactionObject()->getAuthorizationAmount()}
							{else}
								--
							{/if}
						</td>
						<td>
							<img class="twintcw-more-details-button" src="{$base_url}modules/twintcw/images/details.png" />
							<img class="twintcw-less-details-button" src="{$base_url}modules/twintcw/images/close.png" />
						</td>
					</tr>
					<tr  class="twintcw-details-row {$activeClass}" id="twintcw_details_row_{$transaction->getTransactionId()}">
						<td colspan="6">
							
							{if $transaction->getTransactionObject()}
								<div class="box buttons">
									
									{if $transaction->getTransactionObject()->isCapturePossible()}
										<a href="{$link->getAdminLink('AdminTwintCwTransaction')|escape:'htmlall':'UTF-8'}&transactionId={$transaction->getTransactionId()}&action=t_capture" class="button btn btn-success">{lcw s='Capture' mod='twintcw'}</a>
									{/if}
									
									
									{if $transaction->getTransactionObject()->isCancelPossible()}
										<a href="{$link->getAdminLink('AdminTwintCwTransaction')|escape:'htmlall':'UTF-8'}&transactionId={$transaction->getTransactionId()}&action=cancel" class="button btn btn-danger">{lcw s='Cancel' mod='twintcw'}</a>
									{/if}
									
									
									{if $transaction->getTransactionObject()->isRefundPossible()}
										<a href="{$link->getAdminLink('AdminTwintCwTransaction')|escape:'htmlall':'UTF-8'}&transactionId={$transaction->getTransactionId()}&action=t_refund" class="button btn btn-danger">{lcw s='Refund' mod='twintcw'}</a>
									{/if}
									
								</div>
								
								<div class="box information-box">
									<h4>{lcw s='Transaction Details' mod='twintcw'}</h4>
									
									<table class="transaction-information-table">
									
									{assign var="counter" value="0"}
									{assign var="total" value="{count($transaction->getTransactionObject()->getTransactionLabels())}"}
									{assign var="bucketSize" value="{ceil($total / 3)}"}
									
									{foreach from=$transaction->getTransactionObject()->getTransactionLabels() item=label}
									
										{assign var="counter" value="{$counter + 1}"}
										<tr>
											<th><div class="label-title">{$label.label}</div>{if isset($label.description) && $label.description} <div class="description hidden"><div class="content">{$label.description}</div></div>{/if}</th>
											<td>{$label.value|escape:'htmlall'}</td>
										</tr>
										
										{if $counter % $bucketSize == 0}
											</table>
											<table class="transaction-information-table">
										{/if}
									{/foreach}
										
									</table>
								</div>
								
								{if $transaction->getTransactionObject()->getPaymentInformation()}
									<div class="box information-box">
										<h4>{lcw s='Payment Information' mod='twintcw'}</h4>
										{$transaction->getTransactionObject()->getPaymentInformation()}
									</div>
								{/if}
							
								{if count($transaction->getTransactionObject()->getHistoryItems())}
									<div class="previous-actions-box box">
										<h4>{lcw s='Previous Actions' mod='twintcw'}</h4>
										<table class="table" cellpadding="0" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th>{lcw s='Date' mod='twintcw'}</th>
													<th>{lcw s='Action' mod='twintcw'}</th>
													<th>{lcw s='Message' mod='twintcw'}</th>
												</tr>
											</thead>
											<tbody>
												{foreach from=$transaction->getTransactionObject()->getHistoryItems() item=historyItem}
													<tr>
														<td>{$historyItem->getCreationDate()->format($date_format)}</td>
														<td>{$historyItem->getActionPerformed()}</td>
														<td>{$historyItem->getMessage()}</td>
													</tr>
												{/foreach}
											</tbody>
										</table>
									</div>
								{/if}
							
								
								
								{if count($transaction->getTransactionObject()->getCaptures()) }
									<div class="capture-history-box box">
										<h4>{lcw s='Transaction Captures' mod='twintcw'}</h4>
										<table class="table" cellpadding="0" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th>{lcw s='Date' mod='twintcw'}</th>
													<th>{lcw s='Amount' mod='twintcw'}</th>
												</tr>
											</thead>
											<tbody>
												{foreach from=$transaction->getTransactionObject()->getCaptures() item=capture}
													<tr>
														<td>{$capture->getCaptureDate()->format($date_format)}</td>
														<td>{$capture->getAmount()}</td>
													</tr>
												{/foreach}
											</tbody>
										</table>
									</div>
								{/if}
								
								
								{if count($transaction->getTransactionObject()->getRefunds()) }
									<div class="refund-history-box box">
										<h4>{lcw s='Transaction Refunds' mod='twintcw'}</h4>
										<table class="table" cellpadding="0" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th>{lcw s='Date' mod='twintcw'}</th>
													<th>{lcw s='Amount' mod='twintcw'}</th>
												</tr>
											</thead>
											<tbody>
												{foreach from=$transaction->getTransactionObject()->getRefunds() item=refund}
													<tr>
														<td>{$refund->getRefundedDate()->format($date_format)}</td>
														<td>{$refund->getAmount()}</td>
													</tr>
												{/foreach}
											</tbody>
										</table>
									</div>
								{/if}
							{else}
								{lcw s='No more details to show.' mod='twintcw' }
							{/if}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>

</div>