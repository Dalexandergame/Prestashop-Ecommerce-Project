function getSlotForm()
{
	if (document.forms)
		return (document.forms['slot_form']);
	else
		return (document.slot_form);
}

function editSlot(id)
{
	var form = getSlotForm();
	form.elements['id_planning_delivery_slot'].value = id;
	form.elements['slot_name'].value = document.getElementById('slot_name_' + id).value;
	form.elements['slot_slot1'].value = document.getElementById('slot_slot1_' + id).value;
	form.elements['slot_slot2'].value = document.getElementById('slot_slot2_' + id).value;
	form.elements['slot_customers_max'].value = document.getElementById('slot_customers_max_' + id).value;
	form.elements['slot_action'].value = 'edit';
	form.submit();
}

function deleteSlot(id)
{
	var form = getSlotForm();
	form.elements['id_planning_delivery_slot'].value = id;
	form.elements['slot_action'].value = 'delete';
	form.submit();
}

function getDaySlotForm()
{
	if (document.forms)
		return (document.forms['day_slot_form']);
	else
		return (document.day_slot_form);
}

function getPlanningDaySlot(path, id_day, format, id_lang, onAdminPlanningDelivery, id_carrier)
{
	$.get(path, {id_day: id_day, format: format, id_lang: id_lang, onAdminPlanningDelivery: onAdminPlanningDelivery, id_carrier: id_carrier, ajax: 1, no_cache: $.now() },
	function(data){
		document.getElementById('day_slots').innerHTML = data;
	});
}

function getExceptionForm()
{
    if (document.forms)
        return (document.forms['exception_form']);
    else
        return (document.exception_form);
}

function getRetourExceptionForm()
{
    if (document.forms)
        return (document.forms['retour_exception_form']);
    else
        return (document.exception_form);
}

function deleteException(id)
{
    var form = getExceptionForm();
    form.elements['id_planning_delivery_carrier_exception'].value = id;
    form.elements['exception_action'].value = 'delete';
    form.submit();
}

function getExceptionRetourForm()
{
	if (document.forms)
		return (document.forms['retour_exception_form']);
	else
		return (document.retour_exception_form);
}

function deleteRetourException(id)
{
	var form = getExceptionRetourForm();
	form.elements['id_planning_retour_carrier_exception'].value = id;
	form.elements['retour_exception_action'].value = 'delete';
	form.submit();
}
