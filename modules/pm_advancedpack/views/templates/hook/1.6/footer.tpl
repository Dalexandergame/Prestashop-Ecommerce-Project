<script>
function ap5_setPackContentData() {
	{foreach from=$cartPackProducts item=cartPackContent key=cartPackSmallAttribute}
		$('#cart_block dl dd *:contains("{$cartPackSmallAttribute}"), .cart_block dl dt *:contains("{$cartPackSmallAttribute}")').each(function (idx, elem) {
			if (!$(elem).children().size()) {
				var changed = $(elem).html().replace("{$cartPackSmallAttribute}", {$cartPackContent.block_cart});
				$(elem).html(changed);
			}
		});
		$('#cart_summary .cart_description *:contains("{$cartPackSmallAttribute}"), #order-detail-content *:contains("{$cartPackSmallAttribute}")').each(function (idx, elem) {
			if (!$(elem).children().size()) {
				var changed = $(elem).html().replace("{$cartPackSmallAttribute}", {$cartPackContent.cart});
				$(elem).html(changed);
			}
		});
		$('#order-detail-content .product_attributes:contains("{$cartPackSmallAttribute}")').each(function (idx, elem) {
			var changed = $(elem).html().replace("{$cartPackSmallAttribute}", {$cartPackContent.cart});
			$(elem).html(changed);
		});
	{/foreach}
}
$(document).ready(function() {
	ap5_setPackContentData();
	$(document).ajaxSuccess(function() {
		ap5_setPackContentData();
	});
});
</script>