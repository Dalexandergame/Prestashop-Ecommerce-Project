function strrev (string)
{
	var grapheme_extend = /(.)([\uDC00-\uDFFF\u0300-\u036F\u0483-\u0489\u0591-\u05BD\u05BF\u05C1\u05C2\u05C4\u05C5\u05C7\u0610-\u061A\u064B-\u065E\u0670\u06D6-\u06DC\u06DE-\u06E4\u06E7\u06E8\u06EA-\u06ED\u0711\u0730-\u074A\u07A6-\u07B0\u07EB-\u07F3\u0901-\u0903\u093C\u093E-\u094D\u0951-\u0954\u0962\u0963\u0981-\u0983\u09BC\u09BE-\u09C4\u09C7\u09C8\u09CB-\u09CD\u09D7\u09E2\u09E3\u0A01-\u0A03\u0A3C\u0A3E-\u0A42\u0A47\u0A48\u0A4B-\u0A4D\u0A51\u0A70\u0A71\u0A75\u0A81-\u0A83\u0ABC\u0ABE-\u0AC5\u0AC7-\u0AC9\u0ACB-\u0ACD\u0AE2\u0AE3\u0B01-\u0B03\u0B3C\u0B3E-\u0B44\u0B47\u0B48\u0B4B-\u0B4D\u0B56\u0B57\u0B62\u0B63\u0B82\u0BBE-\u0BC2\u0BC6-\u0BC8\u0BCA-\u0BCD\u0BD7\u0C01-\u0C03\u0C3E-\u0C44\u0C46-\u0C48\u0C4A-\u0C4D\u0C55\u0C56\u0C62\u0C63\u0C82\u0C83\u0CBC\u0CBE-\u0CC4\u0CC6-\u0CC8\u0CCA-\u0CCD\u0CD5\u0CD6\u0CE2\u0CE3\u0D02\u0D03\u0D3E-\u0D44\u0D46-\u0D48\u0D4A-\u0D4D\u0D57\u0D62\u0D63\u0D82\u0D83\u0DCA\u0DCF-\u0DD4\u0DD6\u0DD8-\u0DDF\u0DF2\u0DF3\u0E31\u0E34-\u0E3A\u0E47-\u0E4E\u0EB1\u0EB4-\u0EB9\u0EBB\u0EBC\u0EC8-\u0ECD\u0F18\u0F19\u0F35\u0F37\u0F39\u0F3E\u0F3F\u0F71-\u0F84\u0F86\u0F87\u0F90-\u0F97\u0F99-\u0FBC\u0FC6\u102B-\u103E\u1056-\u1059\u105E-\u1060\u1062-\u1064\u1067-\u106D\u1071-\u1074\u1082-\u108D\u108F\u135F\u1712-\u1714\u1732-\u1734\u1752\u1753\u1772\u1773\u17B6-\u17D3\u17DD\u180B-\u180D\u18A9\u1920-\u192B\u1930-\u193B\u19B0-\u19C0\u19C8\u19C9\u1A17-\u1A1B\u1B00-\u1B04\u1B34-\u1B44\u1B6B-\u1B73\u1B80-\u1B82\u1BA1-\u1BAA\u1C24-\u1C37\u1DC0-\u1DE6\u1DFE\u1DFF\u20D0-\u20F0\u2DE0-\u2DFF\u302A-\u302F\u3099\u309A\uA66F-\uA672\uA67C\uA67D\uA802\uA806\uA80B\uA823-\uA827\uA880\uA881\uA8B4-\uA8C4\uA926-\uA92D\uA947-\uA953\uAA29-\uAA36\uAA43\uAA4C\uAA4D\uFB1E\uFE00-\uFE0F\uFE20-\uFE26]+)/g;
	string = string.replace(grapheme_extend, '$2$1');
	return string.split('').reverse().join('');
}

function str_repeat (input, multiplier)
{
	var y = '';
	while (true)
	{
		if (multiplier & 1) y += input;
			multiplier >>= 1;
		if (multiplier) input += input;
			else break;
	}
	return y;
}

function implode (glue, pieces)
{
	var i = '',
	retVal = '',
	tGlue = '';
	if (arguments.length === 1)
	{
		pieces = glue;
		glue = '';
	}
	if (typeof(pieces) === 'object')
	{
		if (Object.prototype.toString.call(pieces) === '[object Array]') return pieces.join(glue);
		for (i in pieces)
		{
			retVal += tGlue + pieces[i];
			tGlue = glue;
		}
		return retVal;
	}
	return pieces;
}

function desintifier(_int, delimiter)
{
	var delimiter_len = parseInt(_int[0]);
	_int = strrev(_int.substring(1));
	var elm = _int.split(str_repeat('0', delimiter_len + 1));
	return strrev(implode(delimiter, elm));
}

function getSelector()
{
	var selector = "input[class*=delivery_option_radio]:checked";
	var selector1 = "input[class*=delivery_option_radio]:checked";
	var selector2 = "input[name*=id_carrier]:checked";
	if($(selector1).length){ selector = selector1; }
	else if($(selector2).length){ selector = selector2; }
	return selector;
}

function getIdCarrierChecked()
{
	var selector = getSelector();
	//alert(selector);
	var key = $(selector).val();
	
	//console.log($("input[class=delivery_option_radio]:checked"));
	
	if(key.search(/,/) < 0){key = desintifier(key,','); }
	var ids_carriers_checked =  key.split(',');
	var id_carrier_checked = ids_carriers_checked[0];
	return id_carrier_checked;
}

function getIdCarrierPost()
{
    var id_carrier_post = $('#choose_delivery_date').data('idcarrierpost');
    return  id_carrier_post != null ? parseInt(id_carrier_post): -1;
}

function addDaysToDate(old_date, delta_days)
{
	var dMyDate = old_date;
	dMyDate.setDate((dMyDate.getDate()+delta_days));
	return dMyDate;
}

function disableUnavailableDays(date)
{	
	var id_carrier_checked = getIdCarrierChecked();
	var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
	if(d < 10){d = "0"+d;}
	if(m+1 < 10){m = "0"+(m+1);}
	else{m = (m+1);}
	var day = date.getDay();
	if($.inArray(y + '-' + m + '-' + d, eval("unavalaibleDates" + id_carrier_checked)) != -1 
		|| $.inArray(day, eval("unavalaibleDays" + id_carrier_checked)) != -1 
		|| eval("firstDate" + id_carrier_checked) > date 
		|| eval("lastDate" + id_carrier_checked) < date) {
		return [false];
	}
	return [true];
}
function disableUnavailableDaysRetour(date)
{	
	var id_carrier_checked = getIdCarrierChecked();        
	var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
	if(d < 10){d = "0"+d;}
	if(m+1 < 10){m = "0"+(m+1);}
	else{m = (m+1);}
	var day = date.getDay();
	if($.inArray(y + '-' + m + '-' + d, eval("unavalaibleDatesRetour" + id_carrier_checked)) != -1 
		|| $.inArray(day, eval("unavalaibleDaysRetour" + id_carrier_checked)) != -1 
		|| eval("firstDateRetour" + id_carrier_checked) > date 
		|| eval("lastDateRetour" + id_carrier_checked) < date) {
		return [false];
	}
	return [true];
}

function enableDays(date){
    var id_carrier_checked = getIdCarrierChecked();
	var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
	if(d < 10){d = "0"+d;}
	if(m+1 < 10){m = "0"+(m+1);}
	else{m = (m+1);}
	var day = date.getDay();
        var currentDate = new Date();
        if(getIdCarrierChecked() == getIdCarrierPost()){
            if(currentDate.getDay() == 1 || currentDate.getDay() == 2 || currentDate.getDay() == 6 || currentDate.getDay() == 0){
                currentDate.setDate(currentDate.getDate() + 3); // add 3 days if carrier checked is Post and day is Monday / Tuesday / Saturday / Sunday
            }else{
                currentDate.setDate(currentDate.getDate() + 5); // add 5 day if currentDate is Post and day is Wednesday / Thursday / Friday
            }
        }else{
            currentDate.setDate(currentDate.getDate() + 1); // add 1 days if carrier checked is NOT Post
            if(currentDate.getDay() == 0){
                currentDate.setDate(currentDate.getDate() + 1); // add 1 day if currentDate == Sunday
            }else if(currentDate.getDay() == 6){
                currentDate.setDate(currentDate.getDate() + 2); // add 2 day if currentDate == Saturday
            }
        }        
        var isDateAvailable = date > currentDate;
	if($.inArray(y + '-' + m + '-' + d, eval("unavalaibleDates" + id_carrier_checked)) != -1
                && $.inArray(day, eval("unavalaibleDays" + id_carrier_checked)) == -1
                // && $.inArray(y + '-' + m + '-' + d, eval("datesRemove.carrier_" + id_carrier_checked)) == -1
                && isDateAvailable
                ) {
		return [true];
	}
	return [false];
}

function enableDaysRetour(date){
    var id_carrier_checked = getIdCarrierChecked();
	var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
	if(d < 10){d = "0"+d;}
	if(m+1 < 10){m = "0"+(m+1);}
	else{m = (m+1);}
	var day = date.getDay();
        var currentDate = new Date();
	if($.inArray(y + '-' + m + '-' + d, eval("unavalaibleDatesRetour" + id_carrier_checked)) != -1
                && $.inArray(day, eval("unavalaibleDaysRetour" + id_carrier_checked)) == -1 
                // && $.inArray(y + '-' + m + '-' + d, eval("datesRemoveRetour.carrier_" + id_carrier_checked)) == -1
                && date > currentDate
                ) {
		return [true];
	}
	return [false];
}

$(function(){
	if ($.browser.msie){
		$('input:radio').click(function(){
			this.blur();
			this.focus();
		});
	}
});

function includeDatePicker(path)
{
	$("#choose_delivery_date");
	var id_carrier_checked = getIdCarrierChecked();
	$.ajax({
		type : "GET",
		cache : false,
		url : path + 'ajax/planningdeliverybycarrier.ajax.php',
		data : "id_carrier=" + id_carrier_checked,
		success: function(data){
			eval(data);
		}
	});
}

function getPlanningDaySlot(path, dateText, format, id_lang, onAdminPlanningDelivery, id_carrier)
{
	$.get(path + 'ajax/planningdeliveryslotsbycarrier.ajax.php', { dateText: dateText, format: format, id_lang: id_lang, onAdminPlanningDelivery : onAdminPlanningDelivery, id_carrier: id_carrier, cache: new Date().getTime()},
	function(data){	
		if($('#lab_delivery_slot')){ $('#lab_delivery_slot').remove(); }
		if($('#no_slots')){	$('#no_slots').remove(); }
		$(data).insertAfter( $('#lab_date_delivery') );
		$('#lab_delivery_slot').fadeIn('slow');
	});
}

function getRetourDaySlot(path, dateText, format, id_lang, onAdminPlanningDelivery, id_carrier)
{
	// console.log("getRetourDaySlot");
}

function resetDateDelivery(path,id_cart)
{
//	$('#choose_delivery_date').hide();
	$.ajax({
		type	: "GET",
		cache	: false,
		url	: path + 'ajax/planningdeliverybycarrier.ajax.php',
		data	: "reset=1&id_cart=" + id_cart,
		success: function(data){
		}
	});
}
