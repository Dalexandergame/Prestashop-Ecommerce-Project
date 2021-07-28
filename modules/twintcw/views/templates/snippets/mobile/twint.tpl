<!DOCTYPE html>
<html>
	<head>
		<title>{$pageTitle}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
		<link rel='stylesheet' type='text/css' href='{$styleUrl}' />
	</head>
<body>
	<div class="section nav">
		<div class="wrap">
			<div class="menu-item one-third column text-left">
				<a class="back-link" href="{$cancelUrl}" target="_self">&nbsp;&nbsp;{$backToOrderText}</a>
			</div>
			<div class="menu-item one-third column text-center text-uppercase">
				<a href="http://www.twint.ch" target="_blank"><div class="twint-logo"></div> <div class="icon-info"></div></a>
			</div>
		</div>
	</div>
	<div class="section nav-mobile hidden">
		<div class="wrap">
			<div class="menu-item one-third column text-left">
				<a class="logo" href="http://www.twint.ch" target="_blank"><div class="twint-logo"></div></a>
			</div>
			<div class="menu-item one-third column text-center">
			</div>
		</div>
	</div>
	<div class="section header">
		<div class="wrap">
			<div class="header-item one-merge column text-center text-uppercase">
				<span class="value">{$amount}</span>
			</div>
		</div>
	</div>
	<div class="section header-mobile hidden">
		<div class="row text-center">
			<div class="text-uppercase">
				<a class="button back-button" href="#" target="_self">{$backText}</a>
				<a class="button info-button" href="http://www.twint.ch" target="_blank">{$informationText}</a>
			</div>
		</div>
		<div class="row text-center text-uppercase">
			<div>
				<p class="value">{$amount}</p>
			</div>
		</div>
	</div>
	<div class="section page">
		<div class="row text-center" id="app-chooser-container" style="display:none;">
			<div class="app-chooser-title">
				{$chooseText}
			</div>
			<div class="app-logos-container">
				<a class="bank-logo bank-ubs" href="twint-issuer2://{$iOSLinkSuffix}"></a>
				<a class="bank-logo bank-raiffeisen" href="twint-issuer6://{$iOSLinkSuffix}"></a>
				<a class="bank-logo bank-pf" href="twint-issuer7://{$iOSLinkSuffix}"></a>
				<a class="bank-logo bank-zkb" href="twint-issuer3://{$iOSLinkSuffix}"></a>
				<a class="bank-logo bank-cs" href="twint-issuer4://{$iOSLinkSuffix}"></a>
				<a class="bank-logo bank-bcv" href="twint-issuer5://{$iOSLinkSuffix}"></a>
			</div>
			<div class="copy-cache">
				<select class="app-chooser" id="app-chooser">
					<option value="">{$otherBanksText}</option>
					{foreach from=$issuers item=issuer}
						<option value="{$issuer.issuerUrlScheme}">{$issuer.displayName}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="row text-center">
			<div class="wrap">
				<div class="copy-cache">
					<button type="button" class="btn" id="pay-with-twint">
                        {$switchText}
					</button>
				</div>
			</div>
		</div>
		<div class="row text-center">
			<div class="wrap or-wrapper">
				<h2><span class="or">{$orText}<span></h2>
			</div>
			<div class="alternative-text copy-cache">{$enterText}</div>
		</div>
		<div class="row white-bg text-center">
			<div class="wrap">
				<div class="credentials">
					<div class="credentials-box credential-token text-center text-uppercase float-rt">
						<div class="credentials-item">
							{foreach from=$numericCode item=digit}
								<p class="credential-number">{$digit}</p>
						    {/foreach}
						</div>
					</div>
				</div>
			</div>
			<input type="text" value ="{$numericCodeRaw}" id="tokenHolder" style="border: 0; outline: 0; color: white; font-size: 16px;">
		</div>
		<div class="row text-center" style="margin-top: 20px;">
			<div class="wrap">
				<div class="copy-cache">
					<button type="button" class="secondary btn" id="copy-token">
						{$copyText}
					</button>
				</div>
			</div>
		</div>
	</div>
	
	
	<script src='{$appSwitchUrl}' ></script>
	<script>twintRedirect.init("{$numericCodeRaw}");</script>
	{$script}
</body>
</html>