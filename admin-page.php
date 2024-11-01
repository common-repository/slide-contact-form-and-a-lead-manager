<?php
function sc_admin_page(){
	global $sc_url, $wpdb;
	
	if( $_POST['submit']=="Update Form" ){
	
		//getting form data
		$label  = array_unique( $_POST['label'] );
		$type   = $_POST['type'];
		$req    = $_POST['req'];
		$mail   = $_POST['mail'];
		$order  = $_POST['order'];
		
		$arr = array();
		for( $i=0; $i<count($type); $i++ ){
			//pass any array if it has default value set
			if( $label[$i]=='Label' ) continue;
			
			//prepare data
			$arr[$i]['label'] = stripslashes($label[$i]);
			$arr[$i]['type']  = $type[$i];
			$arr[$i]['req']   = (int)$req[$i];
			$arr[$i]['mail']  = (int)$mail[$i];
			$arr[$i]['order'] = (int)$order[$i];
		}
		
		//recreate the array for proper indexing
		$newarr = array();
		for( $i=0,$j=0; $i<count($type); $i++ ){
			if( $arr[$i]['label']!='' ){
				$newarr[$j] = $arr[$i];
				$j++;
			}
		}
		$arr = $newarr;
		
		//read the database field name in the leads table
		$sql = "SHOW COLUMNS FROM ". $wpdb->prefix ."sc_leads";
		$sc_fields = $wpdb->get_results($sql, ARRAY_A);
		
		//check if the label has to add
		for($i=0; $i<count($arr); $i++){
			$exists = 0;
			for($j=5; $j<count($sc_fields); $j++){
				if( $arr[$i]['label']==$sc_fields[$j]['Field'] ){
					$exists = 1;
					break;
				}
			}
			if( $exist==0 ){
				$arr[$i]['status'] = 'add';
			}
			else{
				$arr[$i]['status'] = '';
			}
		}
		//check if the label has to delete
		for($j=5; $j<count($sc_fields); $j++){
			$removed = 0;
			for($i=0; $i<count($arr); $i++){
				if( $arr[$i]['label']==$sc_fields[$j]['Field'] ){
					$removed = 1;
					break;
				}
			}
			if( $removed==0 ){
				$arr[$i]['label']  = $sc_fields[$j]['Field'];
				$arr[$i]['status'] = 'delete';
			}
			else{
				$arr[$i]['status'] = '';
			}
		}
		
		//perform add and delete table column operation
		for($i=0; $i<count($arr); $i++){
			if( $arr[$i]['status']=='add' ){
				if( $arr[$i]['type']=='textbox' ) $type = "VARCHAR( 255 )";
				else $type = "TEXT";
				$sql = "ALTER TABLE ". $wpdb->prefix ."sc_leads ADD `". $arr[$i]['label']."` ". $type ." NOT NULL";
				$wpdb->query($sql);
			}
			elseif( $arr[$i]['status']=='delete' ){
				$sql = "ALTER TABLE ". $wpdb->prefix ."sc_leads DROP `". $arr[$i]['label'] ."`";
				$wpdb->query($sql);
			}
			unset($arr[$i]['status']);
			//remove unwanted item from the array
			if( !isset( $arr[$i]['type'] ) )
				unset($arr[$i]);
		}
		//echo "<pre>"; print_r( $sql );  echo "</pre>";
		
		
		//re-read the updated database field name in the leads table
		$sql = "SHOW COLUMNS FROM ". $wpdb->prefix ."sc_leads";
		$sc_fields = $wpdb->get_results($sql, ARRAY_A);
		
		//update field type if changed
		$changed = '...';
		for($j=5; $j<count($sc_fields); $j++){
			for($i=0; $i<count($arr); $i++){
				if( $arr[$i]['label']==$sc_fields[$j]['Field'] ){
					
					if( $sc_fields[$j]['Type']=='varchar(255)' ) $tmp='textbox';
					elseif( $sc_fields[$j]['Type']=='text' ) $tmp='textarea';
										
					if( $tmp!=$arr[$i]['type'] ){	//update code
					
						if( $arr[$i]['type']=='textarea' )
							$update = "ALTER TABLE ". $wpdb->prefix ."sc_leads CHANGE `". $sc_fields[$j]['Field'] ."` `". $sc_fields[$j]['Field'] ."` TEXT NOT NULL";
						elseif( $arr[$i]['type']=='textbox' )
							$update = "ALTER TABLE ". $wpdb->prefix ."sc_leads CHANGE `". $sc_fields[$j]['Field'] ."` `". $sc_fields[$j]['Field'] ."` VARCHAR(255) NOT NULL";
						
						$wpdb->query($update);
					
					}
				}
			}
		}
		//echo "<pre>"; print_r( $changed );  echo "</pre>";
		//echo "<pre>"; print_r( $sc_fields );  echo "</pre>";
		//echo "<pre>"; print_r( $arr );  echo "</pre>"; die();
		
		//bubble sort array by order
		for($i=count($arr)-1; $i >= 0; $i--){
			for($j=1; $j <= $i; $j++){
				if($arr[$j]['order'] < $arr[$j-1]['order']){
					//swap
					$t = $arr[$j]['label'];
					$arr[$j]['label'] = $arr[$j-1]['label'];
					$arr[$j-1]['label'] = $t;
					
					$t = $arr[$j]['type'];
					$arr[$j]['type'] = $arr[$j-1]['type'];
					$arr[$j-1]['type'] = $t;
					
					$t = $arr[$j]['req'];
					$arr[$j]['req'] = $arr[$j-1]['req'];
					$arr[$j-1]['req'] = $t;
					
					$t = $arr[$j]['mail'];
					$arr[$j]['mail'] = $arr[$j-1]['mail'];
					$arr[$j-1]['mail'] = $t;
					
					$t = $arr[$j]['order'];
					$arr[$j]['order'] = $arr[$j-1]['order'];
					$arr[$j-1]['order'] = $t;
				}
			}
		}
		//echo "<pre>"; print_r( $arr );  echo "</pre>"; die();
		update_option('sc_form', $arr);
		
		$msg = "Thank You. Your changes saved successfully.";
	}
	
	$sc_form_data = get_option('sc_form');
	for( $i=0; $i<count($sc_form_data); $i++){
		$sc_form_data[$i]['label'] = htmlspecialchars($sc_form_data[$i]['label']);
	}
	//echo "<pre>"; var_dump( $sc_form_data );  echo "</pre>"; //die();

?>
	<div class="wrap">
		<h2>Create Contact Form</h2>
		
		<iframe src="http://www.image-psd-to-wordpress.com/plugin_info.php" frameborder="0" height="150" width="100%" scrolling="auto"></iframe>
		
		<?php if($msg): ?><div id="message" class="updated fade"><p><strong><?php echo $msg; ?></strong></p></div><?php endif; ?>
		
		
		<div class="warranty updated fade"><p>Construct the form by simply add and delete form fields. <br />You must name different for each form field Label.</p></div>

		<a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=slide-contact-form-and-a-lead-manager/slider-contact.php" class="button">Leads Form</a>
		<a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=sc-leads-page" class="button">Leads List</a>
		<a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=sc-settings-page" class="button">Settings</a>
			
<script type="text/javascript" language="javascript">
	function addField(){
		
		var total_field = parseInt( $("#total_field").val() );
		
		var label = $("#label_"+total_field).val();
		if( label!='Label' ){
		
			total_field = total_field + 1;
			var text = '';
			text = text + '<div style="margin-left:50px;" id="field_'+ total_field +'">';
				text = text + '<input name="label[]" id="label_'+ total_field +'" type="text" value="Label" style="width:200px;" onfocus="if(this.value==\'Label\') this.value=\'\'" onblur="if(this.value==\'\') this.value=\'Label\'" />';
				text = text + '<select name="type[]" id="type_'+ total_field +'">';
					text = text + '<option value="textbox">Text Box</option>';
					text = text + '<option value="textarea">Text Area</option>';
				text = text + '</select>';
				
				text = text + '<input name="req[]" id="req_'+ total_field +'" value="0" type="hidden" />';
				text = text + '<input id="dreq_'+ total_field +'" type="checkbox" onchange="setReq(\'req_'+ total_field +'\',this)" /> <label for="dreq_'+ total_field +'">Required</label>';
				text = text + '<input name="mail[]" id="mail_'+ total_field +'" value="0" type="hidden" />';
				text = text + '<input id="dmail_'+ total_field +'" type="checkbox" onchange="setReq(\'mail_'+ total_field +'\',this)" /> <label for="dmail_'+ total_field +'">Email</label>';

				text = text + '<input name="order[]" id="order_'+ total_field +'" type="text" value="Order" style="width:50px;" onfocus="if(this.value==\'Order\') this.value=\'\'" onblur="if(this.value==\'\') this.value=\'Order\'" />';
				text = text + '<img src="'+ sc_plugin_url +'/images/delete.png" align="top" style="cursor:pointer" onclick="delField(this)" />';
			text = text + '</div>';

			$("#sc_form_div").append(text);
			$("#total_field").val( total_field );
		}
	}
	
	function delField(obj){
		var divs = $('#sc_form_div div');
		if( divs.length > 1 ){
			var res = confirm("Do you really want to delete this field from the form?");
			if( res==true )
				$(obj).parent().remove();
		}
		else{
			alert("There must be atleast one field in the form.");
		}
	}
</script>

		<form name="sc_form" id="sc_form" action="" method="post">
		<div style="margin-top:30px;" id="sc_form_div">
			
			<?php if( $sc_form_data!='' ): ?>
			<input type="hidden" name="total_field" id="total_field" value="<?php echo count($sc_form_data); ?>" />
			<?php for($i=1; $i<=count($sc_form_data); $i++ ): ?>
			<div style="margin-left:50px;" id="field_<?php echo $i; ?>">
				<input name="label[]" id="label_<?php echo $i; ?>" type="text" value="<?php echo $sc_form_data[$i-1]['label']; ?>" style="width:200px;" onfocus="if(this.value=='Label') this.value=''" onblur="if(this.value=='') this.value='Label'" />
				<select name="type[]" id="type_<?php echo $i; ?>">
					<option value="textbox"<?php if( $sc_form_data[$i-1]['type']=='textbox' ) echo ' selected="selected"';?>>Text Box</option>
					<option value="textarea"<?php if( $sc_form_data[$i-1]['type']=='textarea' ) echo ' selected="selected"';?>>Text Area</option>
				</select>
				
				<input name="req[]" id="req_<?php echo $i; ?>" value="<?php echo $sc_form_data[$i-1]['req']; ?>" type="hidden" />
				<input id="dreq_<?php echo $i; ?>" type="checkbox" <?php if( $sc_form_data[$i-1]['req']==1 ) echo ' checked="checked"'; ?> onchange="setReq('req_<?php echo $i; ?>',this)" /> <label for="dreq_<?php echo $i; ?>">Required</label>
				
				<input name="mail[]" id="mail_<?php echo $i; ?>" value="<?php echo $sc_form_data[$i-1]['mail']; ?>" type="hidden" />
				<input id="dmail_<?php echo $i; ?>" type="checkbox" <?php if( $sc_form_data[$i-1]['mail']==1 ) echo ' checked="checked"'; ?> onchange="setReq('mail_<?php echo $i; ?>',this)" /> <label for="dmail_<?php echo $i; ?>">Email</label>
				
				<input name="order[]" id="order_<?php echo $i; ?>" type="text" value="<?php echo $sc_form_data[$i-1]['order']; ?>" style="width:50px;" onfocus="if(this.value=='Order') this.value=''" onblur="if(this.value=='') this.value='Order'" />
				<img src="<?php echo $sc_url; ?>/images/delete.png" align="top" style="cursor:pointer" onclick="delField(this)" />
			</div>
			<?php endfor; ?>
			<?php else: ?>
			
			<input type="hidden" name="total_field" id="total_field" value="1" />
			<div style="margin-left:50px;" id="field_1">
				<input name="label[]" id="label_1" type="text" value="Label" style="width:200px;" onfocus="if(this.value=='Label') this.value=''" onblur="if(this.value=='') this.value='Label'" />
				<select name="type[]" id="type_1">
					<option value="textbox">Text Box</option>
					<option value="textarea">Text Area</option>
				</select>
				
				<input name="req[]" id="req_1" value="0" type="hidden" />
				<input id="dreq_1" type="checkbox" onchange="setReq('req_1',this)" /> <label for="dreq_1">Required</label>
				
				<input name="mail[]" id="mail_1" value="0" type="hidden" />
				<input id="dmail_1" type="checkbox" onchange="setReq('mail_1',this)" /> <label for="dmail_1">Email</label>
				
				<input name="order[]" id="order_1" type="text" value="Order" style="width:50px;" onfocus="if(this.value=='Order') this.value=''" onblur="if(this.value=='') this.value='Order'" />
				<img src="<?php echo $sc_url; ?>/images/delete.png" align="top" style="cursor:pointer" onclick="delField(this)" />
			</div>
			
			<?php endif; ?>
			
		</div>
			<div style="margin-left:70px;margin-top:10px;">
				<input value="Update Form" name="submit" type="submit" class="button-primary" />
				<input value="Add another field" type="button" class="button" onclick="addField();" />
			</div>
		</form>
		
	</div>
	
	<?php sc_credit(); ?>
	
<?php
}
?>