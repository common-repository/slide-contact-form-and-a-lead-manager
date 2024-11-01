<?php

if( !class_exists('upload') )
	include_once(dirname(__FILE__).'/includes/class.upload.php');

/*
	Function Name : sc_settings_page
	Objective     : Change and save the settings for the plugin
*/
function sc_settings_page(){
	global $wpdb;
	$upload_dir = wp_upload_dir();
	$up_path = $upload_dir['basedir']."/";
	
	if( $_POST['submit']=="Update Settings" ){
		
		$msg = '';
		
		//catch the value of the form
		$sc_email        = strip_tags( $_POST['sc_email'] );
		$sc_captcha      = (int)$_POST['sc_captcha'];
		$sc_mess_method  = strip_tags( $_POST['sc_mess_method'] );
		$sc_thanku       = strip_tags( $_REQUEST['sc_thanku'] );
		$sc_error        = strip_tags( $_REQUEST['sc_error'] );
		$sc_thanku_page  = (int)$_REQUEST['sc_thanku_page'];
		$sc_error_page   = (int)$_REQUEST['sc_error_page'];
		$auto_res_switch = (int)$_POST['auto_res_switch'];
		$auto_res_sub    = strip_tags( $_POST['auto_res_sub'] );
		$auto_res_mess   = strip_tags( $_POST['auto_res_mess'] );
		$sc_file_path    = strip_tags( $_REQUEST['sc_file_path'] );
		
		$lead_id     = strip_tags( $_POST['lead_id'] );
		$lead_lang   = trim( strip_tags( $_REQUEST['lead_lang'] ) );
		$lead_format = strip_tags( $_REQUEST['lead_format'] );
		$lead_color  = strip_tags( $_REQUEST['lead_color'] );
		$lead_label  = strip_tags( $_REQUEST['lead_label'] );
		$lead_value  = (float)strip_tags( $_POST['lead_value'] );
		
		$sc_bar_color       = strip_tags( $_POST['sc_bar_color'] );
		$sc_bar_radius      = (int)$_POST['sc_bar_radius'];
			if( $sc_bar_radius>35 ) $sc_bar_radius = 35;
		$sc_label_fsize     = (int)$_POST['sc_label_fsize'];
			if( $sc_label_fsize<8 ) $sc_label_fsize = 8;
			elseif( $sc_label_fsize>18 ) $sc_label_fsize = 18;
		$sc_label_fcolor    = strip_tags( $_POST['sc_label_fcolor'] );
		$sc_label_fface     = $_REQUEST['sc_label_fface'];
		$sc_input_bcolor    = strip_tags( $_POST['sc_input_bcolor'] );
		$sc_input_bradius   = (int)$_POST['sc_input_bradius'];
			if( $sc_input_bradius>35 ) $sc_input_bradius = 35;
		$sc_input_bgcolor   = strip_tags( $_POST['sc_input_bgcolor'] );
		$sc_input_fsize     = (int)$_POST['sc_input_fsize'];
			if( $sc_input_fsize<8 ) $sc_input_fsize = 8;
			elseif( $sc_input_fsize>18 ) $sc_input_fsize = 18;
		$sc_input_fcolor    = strip_tags( $_POST['sc_input_fcolor'] );
		$sc_input_fface     = $_REQUEST['sc_input_fface'];
		$sc_form_bcolor     = strip_tags( $_POST['sc_form_bcolor'] );
		$sc_req_fcolor      = strip_tags( $_POST['sc_req_fcolor'] );
		$sc_form_bimage_del = $_POST['sc_form_bimage_del'];
		$sc_form_submit_del = $_POST['sc_form_submit_del'];
		//echo "<pre>"; var_dump($sc_form_bimage_del); echo "</pre>";
		
		//form background image upload
		
		//retrive previous name
		$img_del = 1;
		$sc_form_bimage = get_option('sc_form_styles');
		$sc_form_bimage = $sc_form_bimage['sc_form_bimage'];
		$tmp_sc_form_bimage = $_FILES['sc_form_bimage']['name'];
		if( $tmp_sc_form_bimage!='' ){
			$img_del = 0;
			$handle = new Upload($_FILES['sc_form_bimage']);
			
			if ($handle->uploaded) {
			
				$handle->allowed = array('image/*');
				
				//resize images
				$bg_image = getimagesize($_FILES['sc_form_bimage']['tmp_name']);
				if( $bg_image[0]>313 ){
					//if only width is greater than the desired size
					$handle->image_resize            = true;
					$handle->image_ratio_y           = true;
					$handle->image_x                 = 313;
				}
				$handle->Process($up_path);
				
				if ($handle->processed) {
					//upload successful.
					//delete previous file [thumbnail too]
					$sc_form_bimage_old = $sc_form_bimage;
					if( $sc_form_bimage_old ) @unlink($up_path.$sc_form_bimage_old);
					if( $sc_form_bimage_old ) @unlink($up_path."th_".$sc_form_bimage_old);
					
					$sc_form_bimage = $handle->file_dst_name;
					
					//create thumbnail image
					$th_image = getimagesize($_FILES['sc_form_bimage']['tmp_name']);
					if( $th_image[0]>100 && $th_image[1]>100 ){
						//if both height and width is greater than the desired size
						$handle->image_resize            = true;
						$handle->image_ratio             = true;
						$handle->image_y                 = 100;
						$handle->image_x                 = 100;
					}
					elseif( $th_image[0]>100 ){
						//if only width is greater than the desired size
						$handle->image_resize            = true;
						$handle->image_ratio_y           = true;
						$handle->image_x                 = 100;
					}
					elseif( $th_image[1]>100 ){
						//if only height is greater than the desired size
						$handle->image_resize            = true;
						$handle->image_ratio_x           = true;
						$handle->image_y                 = 100;
					}
					$handle->Process($up_path);
					if ($handle->processed) {
						$th_image_name = $handle->file_dst_name;
						@copy($up_path.$handle->file_dst_name,$up_path."th_".$sc_form_bimage);
						@unlink($up_path.$th_image_name);
					}
				}
				else{
					$err = $handle->error;
				}
				$handle->Clean();
			}
			else{
				$err = $handle->error;
			}
		}
		if( $err )
			$msg .= $err." [Form Background Image]";
			
			
		//retrive previous sumbit button image name
		$img_del2 = 1;
		$sc_form_submit = get_option('sc_form_styles');
		$sc_form_submit = $sc_form_submit['sc_form_submit'];
		$tmp_sc_form_submit = $_FILES['sc_form_submit']['name'];
		if( $tmp_sc_form_submit!='' ){
			$img_del2 = 0;
			$handle = new Upload($_FILES['sc_form_submit']);
			
			if ($handle->uploaded) {
			
				$handle->allowed = array('image/*');
				
				//resize images
				$handle->image_resize   = true;
				$handle->image_x        = 133;
				$handle->image_y        = 34;
				
				$handle->Process($up_path);
				
				if ($handle->processed) {
					//upload successful.
					//delete previous file
					$sc_form_submit_old = $sc_form_submit;
					if( $sc_form_submit_old ) @unlink($up_path.$sc_form_submit_old);
					
					$sc_form_submit = $handle->file_dst_name;
				}
				else{
					$err2 = $handle->error;
				}
				$handle->Clean();
			}
			else{
				$err2 = $handle->error;
			}
		}
		if( $err2 ){
			if( $msg=='' ) $msg = $err2." [Form Submit Button]";
			else $msg .= "<br />".$err2." [Form Submit Button]";
		}
				
		//perform bgimage delete operation if no new image uploaded and image delete is on
		if( $img_del==1 && $sc_form_bimage_del=='1' ){
			if( $sc_form_bimage!='' ){
				@unlink($up_path.$sc_form_bimage);
				@unlink($up_path."th_".$sc_form_bimage);
			}
			$sc_form_bimage = '';
		}
		//perform submit image delete operation if no new image uploaded and image delete is on
		if( $img_del2==1 && $sc_form_submit_del=='1' ){
			if( $sc_form_submit!='' ){
				@unlink($up_path.$sc_form_submit);
			}
			$sc_form_submit = '';
		}
		//echo "<pre>"; var_dump($sc_form_bimage); echo "</pre>";
		
		
		//reorgonize (make array) the user inputted mails
		$temp = explode(",", $sc_email);
		//removing white spaces and check for valid email address, remove invalid email address.
		$loop = count($temp);
		for($i=0; $i<$loop; $i++){
			$email = trim($temp[$i]);
			if( !sc_isemail($email) || $email=='' ){
				unset($temp[$i]);
				continue;
			}
			$temp[$i] = $email;
		}
		$sc_email = $temp;
		//echo "<pre>"; print_r($sc_email); echo "</pre>";
		
		
		//arrenging the data in array for updating
		
		//update sc_settings
		$sc_settings['sc_email']         = $sc_email;
		$sc_settings['sc_captcha']       = $sc_captcha;
		$sc_settings['sc_mess_method']   = $sc_mess_method;
		$sc_settings['sc_thanku']        = $sc_thanku;
		$sc_settings['sc_error']         = $sc_error;
		$sc_settings['sc_thanku_page']   = $sc_thanku_page;
		$sc_settings['sc_error_page']    = $sc_error_page;
		$sc_settings['auto_res_switch']  = $auto_res_switch;
		$sc_settings['auto_res_sub']     = $auto_res_sub;
		$sc_settings['sc_file_path']     = $sc_file_path;
		
		update_option('sc_settings', $sc_settings);
		
		//update sc_auto_res_mess
		update_option('sc_auto_res_mess', $auto_res_mess);
		
		//update sc_auto_res_mess
		$lead_vars = array();
		$lead_vars['id']       = $lead_id;
		$lead_vars['language'] = $lead_lang;
		$lead_vars['format']   = $lead_format;
		$lead_vars['color']    = $lead_color;
		$lead_vars['label']    = $lead_label;
		$lead_vars['value']    = $lead_value;
		update_option('sc_google_lead_vars', $lead_vars);
		
		//update sc_form_styles
		$sc_form_styles = array();
		$sc_form_styles['sc_bar_color']     = $sc_bar_color;
		$sc_form_styles['sc_bar_radius']    = $sc_bar_radius;
		$sc_form_styles['sc_label_fsize']   = $sc_label_fsize;
		$sc_form_styles['sc_label_fcolor']  = $sc_label_fcolor;
		$sc_form_styles['sc_label_fface']   = $sc_label_fface;
		$sc_form_styles['sc_input_bcolor']  = $sc_input_bcolor;
		$sc_form_styles['sc_input_bradius'] = $sc_input_bradius;
		$sc_form_styles['sc_input_bgcolor'] = $sc_input_bgcolor;
		$sc_form_styles['sc_input_fsize']   = $sc_input_fsize;
		$sc_form_styles['sc_input_fcolor']  = $sc_input_fcolor;
		$sc_form_styles['sc_input_fface']   = $sc_input_fface;
		$sc_form_styles['sc_form_bimage']   = $sc_form_bimage;
		$sc_form_styles['sc_form_submit']   = $sc_form_submit;
		$sc_form_styles['sc_form_bcolor']   = $sc_form_bcolor;
		$sc_form_styles['sc_req_fcolor']    = $sc_req_fcolor;
		update_option('sc_form_styles', $sc_form_styles);
		
		if( $msg=='' )
			$msg .= "Thank You. Your settings updated successfully.";
	}
	
	//retrive options
	$settings        = get_option('sc_settings');
	$sc_email        = $settings['sc_email'];
	$sc_captcha      = $settings['sc_captcha'];
	$sc_mess_method  = $settings['sc_mess_method'];
	$sc_thanku       = $settings['sc_thanku'];
	$sc_error        = $settings['sc_error'];
	$sc_thanku_page  = $settings['sc_thanku_page'];
	$sc_error_page   = $settings['sc_error_page'];
	$auto_res_switch = $settings['auto_res_switch'];
	$auto_res_sub    = $settings['auto_res_sub'];
	$sc_file_path    = $settings['sc_file_path'];
	
	//make $sc_email array to string
	$sc_email = implode(", ",$sc_email);
	
	$auto_res_mess  = get_option('sc_auto_res_mess');
	
	$lead_vars      = get_option('sc_google_lead_vars');
	$lead_id        = $lead_vars['id'];
	$lead_lang      = $lead_vars['language'];
	$lead_format    = $lead_vars['format'];
	$lead_color     = $lead_vars['color'];
	$lead_label     = $lead_vars['label'];
	$lead_value     = $lead_vars['value'];
	
	$sc_form_styles     = get_option('sc_form_styles');
	$sc_bar_color       = $sc_form_styles['sc_bar_color'];
	$sc_bar_radius      = $sc_form_styles['sc_bar_radius'];
	$sc_label_fsize     = $sc_form_styles['sc_label_fsize'];
	$sc_label_fcolor    = $sc_form_styles['sc_label_fcolor'];
	$sc_label_fface     = $sc_form_styles['sc_label_fface'];
	$sc_input_bcolor    = $sc_form_styles['sc_input_bcolor'];
	$sc_input_bradius   = $sc_form_styles['sc_input_bradius'];
	$sc_input_bgcolor   = $sc_form_styles['sc_input_bgcolor'];
	$sc_input_fsize     = $sc_form_styles['sc_input_fsize'];
	$sc_input_fcolor    = $sc_form_styles['sc_input_fcolor'];
	$sc_input_fface     = $sc_form_styles['sc_input_fface'];
	$sc_form_bimage     = $sc_form_styles['sc_form_bimage'];
	$sc_form_submit     = $sc_form_styles['sc_form_submit'];
	$sc_form_bcolor     = $sc_form_styles['sc_form_bcolor'];
	$sc_req_fcolor      = $sc_form_styles['sc_req_fcolor'];
	
	//retrive all the pages
	$sql = "SELECT * FROM ". $wpdb->prefix ."posts WHERE post_type='page' ORDER BY post_title";
	$pages = $wpdb->get_results($sql, ARRAY_A);
	
	//echo "<pre>"; print_r($spages); echo "</pre>"; die();
	
?>	
	<div class="wrap">
		<h2>Leads Settings</h2>
		
		<iframe src="http://www.image-psd-to-wordpress.com/plugin_info.php" frameborder="0" height="150" width="100%" scrolling="auto"></iframe>
		
		
		<?php if($msg): ?><div id="message" class="updated fade"><p><strong><?php echo $msg; ?></strong></p></div><?php endif; ?>
		
		<div style="padding-bottom:20px">
		<a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=slide-contact-form-and-a-lead-manager/slider-contact.php" class="button">Leads Form</a>
		<a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=sc-leads-page" class="button">Leads List</a>
		<a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=sc-settings-page" class="button">Settings</a>
		</div>
		
		<form name="sc_settings_form" id="sc_settings_form" action="" method="post" enctype="multipart/form-data">
		
			<!--General Options starts-->
			<fieldset style="margin-bottom:20px; padding-bottom:10px; border:1px solid #dfdfdf">
			<legend style="margin-left:10px;">General Options</legend>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="sc_email">Mail Sent to: </label></th>
					<td class="form-field">
						<textarea name="sc_email" id="sc_email" rows="5" cols="50"><?php echo $sc_email; ?></textarea><br />
						<span class="description">Leave this filed blank if you want to use current admin email address. Your current admin email address is <code><?php echo get_option('admin_email'); ?></code>.<br />
						You can enter multiple email addresses, each separated by <strong>comma(,)</strong>.</span>
					</td>
					
				</tr>
				<tr valign="top">
					<th scope="row"><label for="sc_captcha1">Active Captcha?: </label></th>
					<td>
						<input name="sc_captcha" id="sc_captcha1" type="radio" value="1" <?php if($sc_captcha==1) echo ' checked="checked"'; ?> /> <label for="sc_captcha1">Yes&nbsp;&nbsp;&nbsp;</label>
						<input name="sc_captcha" id="sc_captcha2" type="radio" value="0" <?php if($sc_captcha==0) echo ' checked="checked"'; ?> /> <label for="sc_captcha2">No</label>
						<!--<br /><span class="description"></span>-->
					</td>
				</tr>
			</table>
			</fieldset>
			<!--General Options ends-->


<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {

	jQuery('#sc_bar_color, #sc_label_fcolor, #sc_input_bcolor, #sc_input_bgcolor, #sc_input_fcolor, #sc_form_bcolor, #sc_req_fcolor').ColorPicker({
		onShow: function(colpkr) {
			jQuery(colpkr).fadeIn(500);
			return false;
		},
		onHide: function(colpkr) {
			jQuery(colpkr).fadeOut(500);
			return false;
		},
		onSubmit: function(hsb, hex, rgb, el) {
			var spanid = "#"+ el.id +"_span";
			jQuery(el).val("#"+hex);
			jQuery(el).ColorPickerHide();
			jQuery(spanid).css("background", "#"+hex);
		},
		onBeforeShow: function () {
			jQuery(this).ColorPickerSetColor(this.value);
		}
	})

});
</script>

			<!--Customize Form starts-->
			<fieldset style="margin-bottom:20px; padding-bottom:10px; border:1px solid #dfdfdf">
			<legend style="margin-left:10px;">Customize Form Styles</legend>
			<table class="form-table">
				<!--bar starts-->
				<tr valign="top">
					<th scope="row"><label for="sc_bar_color">Sliding Bar Color: </label></th>
					<td class="form-field">
						<input name="sc_bar_color" id="sc_bar_color" type="text" value="<?php echo $sc_bar_color; ?>" readonly="readonly" style="width:100px; text-align:center; float:left" />
						<span class="sc_color_div" id="sc_bar_color_span" <?php if($sc_bar_color!=''): ?>style="background:<?php echo $sc_bar_color; ?>"<?php endif; ?>><!----></span>
						&nbsp;<span class="description"><small>Set background and border color of the sliding bar.</small></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="sc_bar_radius">Sliding Bar Border Radius: </label></th>
					<td class="form-field">
						<input name="sc_bar_radius" id="sc_bar_radius" type="text" value="<?php echo $sc_bar_radius; ?>" maxlength="2" style="width:50px; text-align:right;" />
						&nbsp;<span class="description"><small>Set the border radius of the sliding bar. [Enter an integer number. Maximum 35px allowed.]</small></span>
					</td>
				</tr>
				<!--bar ends-->
				
				<!--label starts-->
				<tr valign="top">
					<th scope="row"><label for="sc_label_fsize">Label Font Size: </label></th>
					<td class="form-field">
						<input name="sc_label_fsize" id="sc_label_fsize" type="text" value="<?php echo $sc_label_fsize; ?>" maxlength="2" style="width:50px; text-align:right;" />
						&nbsp;<span class="description"><small>Set the font size of the labels. Range 8px to 18px. [Enter an integer number.]</small></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="sc_label_fcolor">Label Font Color: </label></th>
					<td class="form-field">
						<input name="sc_label_fcolor" id="sc_label_fcolor" type="text" value="<?php echo $sc_label_fcolor; ?>" readonly="readonly" style="width:100px; text-align:center; float:left" />
						<span class="sc_color_div" id="sc_label_fcolor_span" <?php if($sc_label_fcolor!=''): ?>style="background:<?php echo $sc_label_fcolor; ?>"<?php endif; ?>><!----></span>
						&nbsp;<span class="description"><small>Set the font color of the labels.</small></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="sc_label_fface">Label Font Family: </label></th>
					<td class="form-field">
						<select name="sc_label_fface" id="sc_label_fface">
							<option value="0">Select a font family</option>
							<option<?php if($sc_label_fface=="Arial, Helvetica, sans-serif") echo ' selected="selected"'; ?> value="Arial, Helvetica, sans-serif">Arial, Helvetica, sans-serif</option>
							<option<?php if($sc_label_fface=="'Times New Roman', Times, serif") echo ' selected="selected"'; ?> value="'Times New Roman', Times, serif">Times New Roman, Times, serif</option>
							<option<?php if($sc_label_fface=="'Courier New', Courier, monospace") echo ' selected="selected"'; ?> value="'Courier New', Courier, monospace">Courier New, Courier, monospace</option>
							<option<?php if($sc_label_fface=="Georgia, 'Times New Roman', Times, serif") echo ' selected="selected"'; ?> value="Georgia, 'Times New Roman', Times, serif">Georgia, Times New Roman, Times, serif</option>
							<option<?php if($sc_label_fface=="Verdana, Arial, Helvetica, sans-serif") echo ' selected="selected"'; ?> value="Verdana, Arial, Helvetica, sans-serif">Verdana, Arial, Helvetica, sans-serif</option>
							<option<?php if($sc_label_fface=="Geneva, Arial, Helvetica, sans-serif") echo ' selected="selected"'; ?> value="Geneva, Arial, Helvetica, sans-serif">Geneva, Arial, Helvetica, sans-serif</option>
							<option<?php if($sc_label_fface=="'Trebuchet MS', Arial, Helvetica, sans-serif") echo ' selected="selected"'; ?> value="'Trebuchet MS', Arial, Helvetica, sans-serif">Trebuchet MS, Arial, Helvetica, sans-serif</option>
						</select>
						&nbsp;<span class="description"><small>Set the font family of the labels.</small></span>
					</td>
				</tr>
				<!--label ends-->
				
				<!--input starts-->
				<tr valign="top">
					<th scope="row"><label for="sc_input_bcolor">Input Border Color: </label></th>
					<td class="form-field">
						<input name="sc_input_bcolor" id="sc_input_bcolor" type="text" value="<?php echo $sc_input_bcolor; ?>" readonly="readonly" style="width:100px; text-align:center; float:left;" />
						<span class="sc_color_div" id="sc_input_bcolor_span" <?php if($sc_input_bcolor!=''): ?>style="background:<?php echo $sc_input_bcolor; ?>"<?php endif; ?>><!----></span>
						&nbsp;<span class="description"><small>Set the border color input fields.</small></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="sc_input_bradius">Input Border Radius: </label></th>
					<td class="form-field">
						<input name="sc_input_bradius" id="sc_input_bradius" type="text" value="<?php echo $sc_input_bradius; ?>" maxlength="2" style="width:50px; text-align:right;" />
						&nbsp;<span class="description"><small>Set the border radius of input fields. [Enter an integer number. Maximum 35px allowed.]</small></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="sc_input_bgcolor">Input Background Color: </label></th>
					<td class="form-field">
						<input name="sc_input_bgcolor" id="sc_input_bgcolor" type="text" value="<?php echo $sc_input_bgcolor; ?>" readonly="readonly" style="width:100px; text-align:center; float:left" />
						<span class="sc_color_div" id="sc_input_bgcolor_span" <?php if($sc_input_bgcolor!=''): ?>style="background:<?php echo $sc_input_bgcolor; ?>"<?php endif; ?>><!----></span>
						&nbsp;<span class="description"><small>Set the background color of input fields.</small></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="sc_input_fsize">Input Font Size: </label></th>
					<td class="form-field">
						<input name="sc_input_fsize" id="sc_input_fsize" type="text" value="<?php echo $sc_input_fsize; ?>" maxlength="2" style="width:50px; text-align:right;" />
						&nbsp;<span class="description"><small>Set the font size of input fields. Range 8px to 18px. [Enter an integer number.]</small></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="sc_input_fcolor">Input Font Color: </label></th>
					<td class="form-field">
						<input name="sc_input_fcolor" id="sc_input_fcolor" type="text" value="<?php echo $sc_input_fcolor; ?>" readonly="readonly" style="width:100px; text-align:center; float:left" />
						<span class="sc_color_div" id="sc_input_fcolor_span" <?php if($sc_input_fcolor!=''): ?>style="background:<?php echo $sc_input_fcolor; ?>"<?php endif; ?>><!----></span>
						&nbsp;<span class="description"><small>Set the font color of input fields.</small></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="sc_input_fface">Input Font Family: </label></th>
					<td class="form-field">
						<select name="sc_input_fface" id="sc_input_fface">
							<option value="0">Select a font family</option>
							<option<?php if($sc_input_fface=="Arial, Helvetica, sans-serif") echo ' selected="selected"'; ?> value="Arial, Helvetica, sans-serif">Arial, Helvetica, sans-serif</option>
							<option<?php if($sc_input_fface=="'Times New Roman', Times, serif") echo ' selected="selected"'; ?> value="'Times New Roman', Times, serif">Times New Roman, Times, serif</option>
							<option<?php if($sc_input_fface=="'Courier New', Courier, monospace") echo ' selected="selected"'; ?> value="'Courier New', Courier, monospace">Courier New, Courier, monospace</option>
							<option<?php if($sc_input_fface=="Georgia, 'Times New Roman', Times, serif") echo ' selected="selected"'; ?> value="Georgia, 'Times New Roman', Times, serif">Georgia, Times New Roman, Times, serif</option>
							<option<?php if($sc_input_fface=="Verdana, Arial, Helvetica, sans-serif") echo ' selected="selected"'; ?> value="Verdana, Arial, Helvetica, sans-serif">Verdana, Arial, Helvetica, sans-serif</option>
							<option<?php if($sc_input_fface=="Geneva, Arial, Helvetica, sans-serif") echo ' selected="selected"'; ?> value="Geneva, Arial, Helvetica, sans-serif">Geneva, Arial, Helvetica, sans-serif</option>
							<option<?php if($sc_input_fface=="'Trebuchet MS', Arial, Helvetica, sans-serif") echo ' selected="selected"'; ?> value="'Trebuchet MS', Arial, Helvetica, sans-serif">Trebuchet MS, Arial, Helvetica, sans-serif</option>
						</select>
						&nbsp;<span class="description"><small>Set the font family of input fields.</small></span>
					</td>
				</tr>
				<!--input ends-->
				
				<!--other starts-->
				<tr valign="top">
					<th scope="row"><label for="sc_form_bcolor">Form Background Color: </label></th>
					<td class="form-field">
						<input name="sc_form_bcolor" id="sc_form_bcolor" type="text" value="<?php echo $sc_form_bcolor; ?>" readonly="readonly" style="width:100px; text-align:center; float:left;" />
						<span class="sc_color_div" id="sc_form_bcolor_span" <?php if($sc_form_bcolor!=''): ?>style="background:<?php echo $sc_form_bcolor; ?>"<?php endif; ?>><!----></span>
						&nbsp;<span class="description"><small>Set the background color of the form.</small></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="sc_form_bimage">Form Background Image: </label></th>
					<td class="form-field">
					
						<input name="sc_form_bimage" id="sc_form_bimage" type="file" value="<?php echo $sc_form_bimage; ?>" style="width:200px;" />
						&nbsp;<span class="description"><small>Upload an image to set as background image of the form.</small></span>

						<?php if($sc_form_bimage!=''): ?>
						<div class="sc_admin_image_preview">
							<img src="<?php echo get_bloginfo('url'); ?>/wp-content/uploads/<?php echo "th_".$sc_form_bimage; ?>" alt="" align="absbottom" />
							<input name="sc_form_bimage_del" id="sc_form_bimage_del" type="checkbox" value="1" style="width:20px;" />
							<label for="sc_form_bimage_del">Delete Image</label>
						</div>
						<?php endif; ?>
						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="sc_form_bimage">Form Submit Button: </label></th>
					<td class="form-field">
					
						<input name="sc_form_submit" id="sc_form_submit" type="file" value="<?php echo $sc_form_submit; ?>" style="width:200px;" />
						&nbsp;<span class="description"><small>Upload an image of 133px &times; 34px to replace the default submit button image.</small></span>
						
						<?php if($sc_form_submit!=''): ?>
						<div class="sc_admin_image_preview">
							<img src="<?php echo get_bloginfo('url'); ?>/wp-content/uploads/<?php echo $sc_form_submit; ?>" alt="" align="absbottom" />
							<input name="sc_form_submit_del" id="sc_form_submit_del" type="checkbox" value="1" style="width:20px;" />
							<label for="sc_form_submit_del">Delete Image</label>
						</div>
						<?php endif; ?>
						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="sc_req_fcolor">Required Text Color: </label></th>
					<td class="form-field">
						<input name="sc_req_fcolor" id="sc_req_fcolor" type="text" value="<?php echo $sc_req_fcolor; ?>" readonly="readonly" style="width:100px; text-align:center; float:left;" />
						<span class="sc_color_div" id="sc_req_fcolor_span" <?php if($sc_req_fcolor!=''): ?>style="background:<?php echo $sc_req_fcolor; ?>"<?php endif; ?>><!----></span>
						&nbsp;<span class="description"><small>Set the color of required text.</small></span>
					</td>
				</tr>
				<!--other ends-->
				
			</table>
			</fieldset>
			<!--Customize Form ends-->
			
			<!--Thank You Message Options starts-->
			<fieldset style="margin-bottom:20px; padding-bottom:10px; border:1px solid #dfdfdf">
			<legend style="margin-left:10px;">Thank You Message Options</legend>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label>Thank you method: </label></th>
					<td>
						<input type="radio" name="sc_mess_method" id="sc_mess_method1" value="1"<?php if($sc_mess_method==1) echo ' checked="checked"'; ?> onchange="showhide_method('1');" /> <label for="sc_mess_method1" style="margin-right:20px;">Simple</label>
						<input type="radio" name="sc_mess_method" id="sc_mess_method2" value="2"<?php if($sc_mess_method==2) echo ' checked="checked"'; ?> onchange="showhide_method('2');" /> <label for="sc_mess_method2">Advanced</label>
						<br /><span class="description">Simple method will just show the Thank you message/Error message, <br />Advanced method will redirect the user to the specified page.</span>
					</td>
				</tr>
				
				<tr valign="top">
					<th class="method1" scope="row" <?php if($sc_mess_method!=1) echo ' style="display:none"'; ?>><label for="sc_thanku">Thank you message: </label></th>
					<td class="method1 form-field" <?php if($sc_mess_method!=1) echo ' style="display:none"'; ?>>
						<textarea name="sc_thanku" id="sc_thanku" rows="5" cols="50"><?php echo $sc_thanku; ?></textarea><br />
						<!--<span class="description"></span>-->
					</td>
				</tr>
				
				<tr valign="top">
					<th class="method1" scope="row" <?php if($sc_mess_method!=1) echo ' style="display:none"'; ?>><label for="sc_error">Error message: </label></th>
					<td class="method1 form-field" <?php if($sc_mess_method!=1) echo ' style="display:none"'; ?>>
						<textarea name="sc_error" id="sc_error" rows="5" cols="50"><?php echo $sc_error; ?></textarea><br />
						<!--<span class="description"></span>-->
					</td>
				</tr>
				
				<tr valign="top">
					<th class="method2" scope="row" <?php if($sc_mess_method!=2) echo ' style="display:none"'; ?>><label for="sc_thanku_page">Thank you page: </label></th>
					<td class="method2 form-field" <?php if($sc_mess_method!=2) echo ' style="display:none"'; ?>>
						<select name="sc_thanku_page" id="sc_thanku_page">
							<option value="0">Select a page</option>
							<?php
							for($i=0; $i<count($pages); $i++){
								if( $sc_thanku_page==$pages[$i]['ID'] ) $sel = ' selected="selected"';
								else $sel = '';
								echo '<option value="'. $pages[$i]['ID'] .'"'. $sel .'>'. $pages[$i]['post_title'] .'</option>';
							}
							?>
						</select>
						<!--<br /><span class="description"></span>-->
					</td>
				</tr>
				<tr valign="top">
					<th class="method2" scope="row" <?php if($sc_mess_method!=2) echo ' style="display:none"'; ?>><label for="sc_error_page">Error page: </label></th>
					<td class="method2 form-field" <?php if($sc_mess_method!=2) echo ' style="display:none"'; ?>>
						<select name="sc_error_page" id="sc_error_page">
							<option value="0">Select a page</option>
							<?php
							for($i=0; $i<count($pages); $i++){
								if( $sc_error_page==$pages[$i]['ID'] ) $sel = ' selected="selected"';
								else $sel = '';
								echo '<option value="'. $pages[$i]['ID'] .'"'. $sel .'>'. $pages[$i]['post_title'] .'</option>';
							}
							?>
						</select>
						<!--<br /><span class="description"></span>-->
					</td>
				</tr>
			</table>
			</fieldset>
			<!--Thank You Message Options ends-->
			
			<!--Auto Responder Options starts-->
			<fieldset style="margin-bottom:20px; padding-bottom:10px; border:1px solid #dfdfdf">
			<legend style="margin-left:10px;">Auto Responder Options</legend>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label>Auto Responder: </label></th>
					<td>
						<input name="auto_res_switch" id="auto_res_switch1" value="1" type="radio"<?php if($auto_res_switch=='1') echo ' checked="checked"'; ?> /> <label for="auto_res_switch1" style="margin-right:20px;">ON</label>
						<input name="auto_res_switch" id="auto_res_switch2" value="0" type="radio"<?php if($auto_res_switch=='0') echo ' checked="checked"'; ?> /> <label for="auto_res_switch2">OFF</label>
						<!--<span class="description"></span>-->
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="auto_res_sub">Auto Responder Subject: </label></th>
					<td class="form-field">
						<input name="auto_res_sub" id="auto_res_sub" value="<?php echo $auto_res_sub; ?>" class="regular-text" type="text" /><br />
						<!--<span class="description"></span>-->
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="auto_res_mess">Auto Responder Message: </label></th>
					<td class="form-field">
						<textarea name="auto_res_mess" id="auto_res_mess" rows="5" cols="50"><?php echo $auto_res_mess; ?></textarea><br />
						<a href="javascript:void(0)" title="Will reset the Auto Responder Message" onclick="$('#auto_res_mess').val('We have received your inquiry and we will respond back to you shortly. For your record here is a copy of the message you submitted.')">Restore Message</a>
						<!--<span class="description"></span>-->
					</td>
				</tr>
			</table>
			</fieldset>
			<!--Auto Responder Options ends-->
				
			<!--Lead JS Options starts-->	
			<fieldset style="margin-bottom:20px; padding-bottom:10px; border:1px solid #dfdfdf">
			<legend style="margin-left:10px;">Lead JS Options</legend>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="lead_id">Google Lead ID: </label></th>
					<td class="form-field">
						<input name="lead_id" id="lead_id" value="<?php echo $lead_id; ?>" class="regular-text" type="text" />
						<!--<span class="description"></span>-->
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="lead_lang">Google Lead Language: </label></th>
					<td class="form-field">
						<input name="lead_lang" id="lead_lang" value="<?php echo $lead_lang; ?>" class="regular-text" type="text" style="width:100px" />
						<!--<span class="description"></span>-->
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="lead_format">Google Lead Format: </label></th>
					<td class="form-field">
						<input name="lead_format" id="lead_format" value="<?php echo $lead_format; ?>" class="regular-text" type="text" style="width:100px" />
						<!--<span class="description"></span>-->
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="lead_color">Google Lead Color: </label></th>
					<td class="form-field">
						<input name="lead_color" id="lead_color" value="<?php echo $lead_color; ?>" class="regular-text" type="text" style="width:100px" />
						<!--<span class="description"></span>-->
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="lead_label">Google Lead Label: </label></th>
					<td class="form-field">
						<input name="lead_label" id="lead_label" value="<?php echo $lead_label; ?>" class="regular-text" type="text" />
						<!--<span class="description"></span>-->
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="lead_value">Google Lead Value: </label></th>
					<td class="form-field">
						<input name="lead_value" id="lead_value" value="<?php echo $lead_value; ?>" class="regular-text" type="text" style="width:100px" />
						<!--<span class="description"></span>-->
					</td>
				</tr>
			</table>
			</fieldset>
			<!--Lead JS Options ends-->
			
			<!--Lead JS Options starts-->	
			<!--<fieldset style="margin-bottom:20px; padding-bottom:10px; border:1px solid #dfdfdf">
			<legend style="margin-left:10px;">Include File</legend>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="sc_file_path">Lead File Path: </label></th>
					<td class="form-field">
						<input name="sc_file_path" id="sc_file_path" value="<?php echo $sc_file_path; ?>" class="regular-text" type="text" />
						<br /><span class="description">Enter the directory path to Lead File. Example: <code>/var/www/html/zend.developer4lease.com/public/renderedHtml/lead</code></span>
					</td>
				</tr>
			</table>
			</fieldset>-->
			<!--Lead JS Options ends-->
			
			<p class="submit">
				<input type="submit" name="submit" value="Update Settings" class="button-primary" />
			</p>
			
		</form>
	</div>
	
	<?php sc_credit(); ?>
<?php
}

?>