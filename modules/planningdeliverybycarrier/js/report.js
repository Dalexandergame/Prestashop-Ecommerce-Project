
jQuery(document).ready(function($){	

	if ($("form#calendar_form .datepicker").length > 0){
		$("form#calendar_form .datepicker").datepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd'
		});
	}

	$('#submitDatePicker').click(function(){
	
		if($('#pd_datepickerFrom').val() == "" || $('#pd_datepickerTo').val() == ""){
			alert('Invalid dates');
		}
		else if($('#id_product_field').val() == "" && $('#report').val() == 3){
			alert('Invalid Product ID');
		}
		else{
			var datas = $('#calendar_form').serialize();		
			var url_refresh = $('#urlRefresh').val();		
			$.ajax({type:"GET",cache:false,url: url_refresh + '&' + datas,success: 
				function(data){
					$('#dashboard').empty();
					$('#dashboard').html(data);
				}
			});
		}		
		return false;
	});	
	
	$('#report').change(function(){				
		report = $(this).val();
		if(3 == report){ $('#p_product').css('display','inline'); }
		else{ $('#p_product').css('display','none'); }	
	});
	
});
