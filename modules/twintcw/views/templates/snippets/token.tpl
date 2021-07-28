<!DOCTYPE html>
<html>
<head>
<title>{$pageTitle}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport"
	content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="Cache-Control"
	content="no-cache, no-store, must-revalidate" />
<link href="{$styleUrl}" rel="stylesheet" type="text/css" media="all" />
</head>
<body>
	<div class="section nav">
		<div class="wrap">
			<div class="menu-item one-third column text-left">
				<a class="back-link" href="{$cancelUrl}" target="_self">&nbsp;&nbsp;{$backToOrderText}</a>
			</div>
			<div class="menu-item one-third column text-center text-uppercase">
				<a href="http://www.twint.ch" target="_blank"><div
						class="twint-logo"></div>
					<div class="icon-info"></div></a>
			</div>
		</div>
	</div>
	<div class="section nav-mobile hidden">
		<div class="wrap">
			<div class="menu-item one-third column text-left">
				<a class="logo" href="http://www.twint.ch" target="_blank"><div
						class="twint-logo"></div></a>
			</div>
			<div class="menu-item one-third column text-center"></div>
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
				<a class="button info-button" href="http://www.twint.ch"
					target="_blank">{$informationText}</a>
			</div>
		</div>
		<div class="row text-center text-uppercase">
			<div>
				<p class="value">
					{$amount}
				</p>
			</div>
		</div>
	</div>
	<div class="section page">
		<div class="row grey-bg text-center">
			<div class="wrap">
				<div class="credentials">
					<div
						class="credentials-box credential-qrcode text-center text-uppercase float-lt">
						<div class="credentials-item">
							<img class="qr-code-graphic" alt="qrcode" width="220px"
								height="220px" src="{$qrCode}">
						</div>
					</div>
					<div
						class="credentials-box credential-token text-center text-uppercase float-rt">
						<div class="credentials-item">
							{foreach from=$numericCode item=digit}
							<p class="credential-number">
								{$digit}
							</p>
							{/foreach}
						</div>
					</div>

				</div>
			</div>
		</div>
		<div class="row text-center">
			<div class="wrap">
				<div class="copy-cache">
					<button type="button" class="btn">
						{$copyText}
					</button>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="description-box">
				<div class="description-item">
					<div class="description-image">
						<div class="description-icon icon-app"></div>
					</div>
					<div class="description-text">
						{$step1}
					</div>
				</div>
				<div class="description-item">
					<div class="description-image">
						<div class="description-icon icon-pay"></div>
					</div>
					<div class="description-text">
						{$step2}
					</div>
				</div>
				<div class="description-item">
					<div class="description-image">
						<div class="description-icon-cam icon-cam"></div>
					</div>
					<div class="description-text">
						{$step3}
					</div>
				</div>
			</div>
		</div>
	</div>

	{$script}
</body>
</html>