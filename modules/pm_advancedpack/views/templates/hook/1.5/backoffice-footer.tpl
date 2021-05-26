<script>
$(document).ready(function() {
{foreach from=$cartPackProducts item=cartPackContent key=cartPackSmallAttribute}
	$('#orderProducts td:contains("{$cartPackSmallAttribute}")').each(function (idx, elem) {
		var changed = $(elem).html().replace("{$cartPackSmallAttribute}", {$cartPackContent.cart});
		$(elem).html(changed);
	});
{/foreach}
});
</script>