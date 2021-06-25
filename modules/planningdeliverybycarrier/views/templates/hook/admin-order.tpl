<br />

<!-- Shipping block -->
{$includeDatepicker}
<script type="text/javascript">
{literal}
// <![CDATA[
	$('document').ready(function(){
		$('#submitDateDelivery').click(function(){
			params = $('#form_update_planningdelivery').serialize();
			$.ajax({type:"GET", cache:false, url:"../modules/planningdeliverybycarrier/ajax/update_date_delivery.ajax.php?" + params, success:
				function(data){
					$('#refresh_tab_planningdelivery').html(data);
				}
			});
			return false;
		});
		$('#ui-datepicker-div').css('clip', 'auto');
		$('#ui-datepicker-div').removeClass('ui-helper-hidden-accessible');
	})

{/literal}
//]]>
</script>
<style>
.ui-helper-hidden-accessible { position: absolute !important; clip: rect(1px 1px 1px 1px);}
</style>
<div class="panel">
	<fieldset>
		<legend><img src="https://ecosapin.pulse.digital/ch/img/admin/delivery.gif" /> {l s='Delivery Day' mod='planningdeliverybycarrier'}</legend>
		<div id="refresh_tab_planningdelivery">
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
						<td>{$delivery_slot}</td>
					</tr>
				</tbody>
			</table>
		</div>

		<form action="" id="form_update_planningdelivery" method="post" style="margin-top:10px;">
			<p id="lab_date_delivery">
				{l s='New delivery date' mod='planningdeliverybycarrier'} :
				<input type="text" name="date_delivery" id="date_delivery" style="width:200px"/>
				<div id="day_slots"></div><input type="submit" id="submitDateDelivery" name="submitDateDelivery" value="{l s='Change' mod='planningdeliverybycarrier'}" class="button btn btn-primary" />
			</p>
			<input type="hidden" name="id_order" value="{$order->id|escape:'intval'}" />
		</form>
	</fieldset>
</div>