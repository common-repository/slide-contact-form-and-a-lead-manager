
//admin functions
function setReq( id, obj ){
	if( jQuery(obj).attr('checked') ) jQuery('#'+id).val('1');
	else jQuery('#'+id).val('0');
}

function setEmail( id, obj ){
	if( jQuery(obj).attr('checked') ) jQuery('#'+id).val('1');
	else jQuery('#'+id).val('0');
}

function record_form_checkAll(form) {
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox" && !(form.elements[i].hasAttribute('onclick'))) {
			if(form.elements[i].checked == true)
				form.elements[i].checked = false;
			else
				form.elements[i].checked = true;
		}
	}
}

function showhide_method(method){
	if( method=='1' ){
		jQuery('.method1').show();
		jQuery('.method2').hide();
	}
	else if( method=='2' ){
		jQuery('.method1').hide();
		jQuery('.method2').show();
	}
}
