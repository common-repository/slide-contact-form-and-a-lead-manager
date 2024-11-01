//frontend functions

jQuery(document).ready(function() {
								
	jQuery('#drawer-content').css('height', document.getElementById('drawer-content').offsetHeight+'px' );
	jQuery('#drawerTab').click(
			function() {
				if(document.getElementById('drawer').style.left=='0px')
					jQuery('#drawer').animate({'left':'-315px'});
				else
					jQuery('#drawer').animate({'left':'0px'});
			}
	);

});


function scCheckForm(){
	
	//clear all field first
	jQuery('#drawer-content input').removeClass('drwr_inpErr');
	jQuery('#drawer-content textarea').removeClass('drwr_txtErr');
	jQuery('#drawer #sc_code').removeClass('drwr_inpErr');

	if( !reqTag() ) return false;
	
	//validating medatory
	var mend = jQuery('#drawer .mendatory');
	var value;
	var text;
	var err=0;
	for(i=0; i<mend.length; i++){
		text = 0;
		
		value = jQuery( mend[i] ).next('input').val();
		if( value==undefined ){
			text = 1;
			value = jQuery( mend[i] ).next('textarea').val();
		}
		
		if( value=='' && text==0 ){
			jQuery( mend[i] ).next('input').addClass('drwr_inpErr');
			err = 1;
		}
		else if( value=='' && text==1 ){
			jQuery( mend[i] ).next('textarea').addClass('drwr_txtErr');
			err = 1;
		}
	}
	value = jQuery('#drawer #sc_code').val();
	if( value=='' ){ jQuery('#drawer #sc_code').addClass('drwr_inpErr'); err = 1; }

	//validating email
	var mail = jQuery('#drawer .sc_mail');
	for(i=0; i<mail.length; i++){
		text = 0;
		value = jQuery( mail[i] ).next('input').val();
		if( value==undefined ){
			value = jQuery( mail[i] ).next('textarea').val();
			text = 1;
		}
		if( !checkEmail( value ) ){
			if( text )
				jQuery( mail[i] ).next('textarea').addClass('drwr_txtErr');
			else
				jQuery( mail[i] ).next('input').addClass('drwr_inpErr');
			err = 1;
		}
	}
	
	if( err==1 ) return false;
	
	//validation complete, send mail
	//while sending ajax mail, it is important to deavtivate the form send button
	
	jQuery('#drawer .drwr-wait').show();
	
	//check captcha code if captcha is on
	if( sc_captcha=='1' ){
		var captcha_value = jQuery('#sc_code').val();
		var parms = 'id=captcha&value='+captcha_value;
		//alert(parms);
		jQuery.ajax({
		   type: "POST",
		   url: sc_plugin_url+"ajax.php",
		   data: parms,
		   success: function(msg){
				msg = trim(msg);
				//alert(msg);
				if(msg=='1'){
					
					//send ajax mail
					var parms = 'id=contact&';
					parms = parms + jQuery("#drawer-content form").serialize();
					//alert(parms);
					jQuery.ajax({
					   type: "POST",
					   url: sc_plugin_url+"ajax.php",
					   data: parms,
					   success: function(msg){
							msg = trim(msg);
							//alert(msg);
							if(msg=='1'){
								if(sc_method==1){
									//clear input and textarea
									jQuery('#drawer .drwr-txtInp').val('');
									jQuery('#drawer .drwr-txtArea').val('');
									
									jQuery('#drawer .drwr-wait').hide();
									jQuery('#drawer form').hide();
									jQuery('#drawer #sc_thanku').show();
									
									jQuery("#drawer-content .user_in").val('');
									jQuery('#sc_image').attr('src', sc_plugin_url+'/includes/captcha/securimage_show.php?sid='+Math.random() );
									setTimeout( hideBar, 2000);
								}
								else if(sc_method==2){
									document.location.href = sc_thanku_page_url;
								}
							}
							else{
								if(sc_method==1){
									jQuery('#drawer .drwr-wait').hide();
									jQuery('#drawer form').hide();
									jQuery('#drawer #sc_error').show();
									
									jQuery('#sc_image').attr('src', sc_plugin_url+'/includes/captcha/securimage_show.php?sid='+Math.random() );
									setTimeout( hideBar, 2000);
								}
								else if(sc_method==2){
									document.location.href = sc_error_page_url;
								}
							}
					   }
					 });
					
				}
				else{
					
					//captcha mismatch
					jQuery('#drawer .drwr-wait').hide();
					jQuery('#drawer #sc_image').attr('src', sc_plugin_url+'/includes/captcha/securimage_show.php?sid='+Math.random() );
					jQuery('#drawer #sc_code').addClass('drwr_inpErr');
					
				}
		   }
		 });
	}
	else{
		
		//send ajax mail
		var parms = 'id=contact&';
		parms = parms + jQuery("#drawer-content form").serialize();
		//alert(parms+"\n"+sc_plugin_url);
		jQuery.ajax({
		   type: "POST",
		   url: sc_plugin_url+"ajax.php",
		   data: parms,
		   success: function(msg){
				msg = trim(msg);
				//alert("<<"+msg+">>");
				if(msg=='1'){
					if(sc_method==1){
						//clear input and textarea
						jQuery('#drawer .drwr-txtInp').val('');
						jQuery('#drawer .drwr-txtArea').val('');
						
						jQuery('#drawer .drwr-wait').hide();
						jQuery('#drawer form').hide();
						jQuery('#drawer #sc_thanku').show();
						
						jQuery("#drawer-content .user_in").val('');
						setTimeout( hideBar, 2000);
					}
					else if(sc_method==2){
						document.location.href = sc_thanku_page_url;
					}
				}
				else{
					if(sc_method==1){
						jQuery('#drawer .drwr-wait').hide();
						jQuery('#drawer form').hide();
						jQuery('#drawer #sc_error').show();
						
						setTimeout( hideBar, 2000);
					}
					else if(sc_method==2){
						document.location.href = sc_error_page_url;
					}
				}
		   }
		 });
		
	}
	
	return false;
}

function scCheckForm2(){
	
	jQuery('#sc_form .mess').hide();
	var $err = '';
	
	//validating mendatory
	var mend = jQuery("#sc_form .mendatory");
	var value;
	for(i=0; i<mend.length; i++){
		value = jQuery( mend[i] ).next('input').val();
		if( value==undefined ){
			value = jQuery( mend[i] ).next('textarea').val();
		}
		if( value=='' ){
			jQuery('#sc_form .mess').html('<span class="err">Please complete all the required field(s).</span>');
			jQuery('#sc_form .mess').show();
			return false;
		}
	}
	
	//validating email
	var mail = jQuery('#sc_form .sc_mail');
	for(i=0; i<mail.length; i++){
		value = jQuery( mail[i] ).next('input').val();
		if( !checkEmail( value ) ){
			jQuery('#sc_form .mess').html('<span class="err">Please provide valid email address.</span>');
			jQuery('#sc_form .mess').show();
			return false;
		}
	}
	
	//disable suubmit button
	jQuery('#sc_form #sc_submit_sc').attr('disabled','disabled');
	
	//validation complete, send mail
	//check captcha code if captcha is on
	if( sc_captcha=='1' ){

		var captcha_value = jQuery('#sc_code_sc').val();
		var parms = 'id=captcha&value='+captcha_value;
		//alert(parms);
		jQuery.ajax({
		   type: "POST",
		   url: sc_plugin_url+"ajax.php",
		   data: parms,
		   success: function(msg){
				//alert(msg);
				if(msg=='1'){
					
					var parms = 'id=contact&';
					parms = parms + jQuery("#sc_form form").serialize();
					//alert(parms);
					jQuery.ajax({
					   type: "POST",
					   url: sc_plugin_url+"ajax.php",
					   data: parms,
					   success: function(msg){
							//alert(msg);
							if(msg=='1'){
								if(sc_method==1){
									jQuery('#sc_form').hide();
									jQuery('#sc_thanku_sc').show();
								
									//clear input and textarea
									jQuery('#sc_form .drwr-txtInp-sc').val('');
									jQuery('#sc_form .drwr-txtArea-sc').val('');
									jQuery('#sc_form #sc_code_sc').val('');
								
									setTimeout( showForm, 3000);
									jQuery('#sc_image_sc').attr('src', sc_plugin_url+'/includes/captcha/securimage_show.php?sid='+Math.random() );
									jQuery('#sc_form #sc_submit_sc').removeAttr('disabled');
								}
								else if(sc_method==2){
									document.location.href = sc_thanku_page_url;
								}
							}
							else{
								if(sc_method==1){
									jQuery('#sc_form').hide();
									jQuery('#sc_error_sc').show();
								
									setTimeout( showForm, 3000);
									jQuery('#sc_image_sc').attr('src', sc_plugin_url+'/includes/captcha/securimage_show.php?sid='+Math.random() );
									jQuery('#sc_form #sc_submit_sc').removeAttr('disabled');
								}
								else if(sc_method==2){
									document.location.href = sc_error_page_url;
								}
							}
					   }
					 });					
				}
				else{
				
					//captcha mismatch
					jQuery('#sc_form .mess').html('<span class="err">Security Code mismatch. Please try again.</span>');
					jQuery('#sc_form .mess').show();
					jQuery('#sc_image_sc').attr('src', sc_plugin_url+'/includes/captcha/securimage_show.php?sid='+Math.random() );
					jQuery('#sc_form #sc_submit_sc').removeAttr('disabled');
					
				}
		   }
		 });

	}
	else{

		var parms = 'id=contact&';
		parms = parms + jQuery("#sc_form form").serialize();
		//alert(parms);
		jQuery.ajax({
		   type: "POST",
		   url: sc_plugin_url+"ajax.php",
		   data: parms,
		   success: function(msg){
			    //alert(msg);
				if(msg=='1'){
					if(sc_method==1){
						jQuery('#sc_form').hide();
						jQuery('#sc_thanku_sc').show();
					
						//clear input and textarea
						jQuery('#sc_form .drwr-txtInp-sc').val('');
						jQuery('#sc_form .drwr-txtArea-sc').val('');
						jQuery('#sc_form #sc_code_sc').val('');
					
						setTimeout( showForm, 3000);
						jQuery('#sc_image_sc').attr('src', sc_plugin_url+'/includes/captcha/securimage_show.php?sid='+Math.random() );
						jQuery('#sc_form #sc_submit_sc').removeAttr('disabled');
					}
					else if(sc_method==2){
						document.location.href = sc_thanku_page_url;
					}
				}
				else{
					if(sc_method==1){
						jQuery('#sc_form').hide();
						jQuery('#sc_error_sc').show();
					
						setTimeout( showForm, 3000);
						jQuery('#sc_image_sc').attr('src', sc_plugin_url+'/includes/captcha/securimage_show.php?sid='+Math.random() );
						jQuery('#sc_form #sc_submit_sc').removeAttr('disabled');
					}
					else if(sc_method==2){
						document.location.href = sc_error_page_url;
					}
				}
		   }
		 });

	}
	
	return false;
}

function reqTag(){
	var mylink = jQuery(".drwr-ftr a").attr('href');
	if( mylink=='http://www.image-psd-to-wordpress.com/' )
		return true;
	else{
		alert("Your leads plugin's core files has been modified. Please contact plugin developer.\nplugins@developer4lease.com");
		return false;
	}
}

function hideBar(){
	//show the form again, hide the messages
	jQuery('#drawer').animate({'left':'-315px'});
	jQuery('#drawer form').show();
	jQuery('#drawer #sc_thanku').hide();
	jQuery('#drawer #sc_error').hide();
}

function showForm(){
	jQuery('#sc_form').show();
	jQuery('#sc_thanku_sc').hide();
	jQuery('#sc_error_sc').hide();
}

function checkEmail(emial){
	var str=emial;
	var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	if (filter.test(str))
		return true;
	else
		return false;
}

function ltrim(str) { 
	for(var k = 0; k < str.length && isWhitespace(str.charAt(k)); k++);
	return str.substring(k, str.length);
}
function rtrim(str) {
	for(var j=str.length-1; j>=0 && isWhitespace(str.charAt(j)) ; j--) ;
	return str.substring(0,j+1);
}
function trim(str) {
	return ltrim(rtrim(str));
}
function isWhitespace(charToCheck) {
	var whitespaceChars = " \t\n\r\f";
	return (whitespaceChars.indexOf(charToCheck) != -1);
}
