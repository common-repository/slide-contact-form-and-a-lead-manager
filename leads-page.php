<?php

/*
	Database manipulation functions
	Edit FAQs
*/
function sc_edit_lead(){
	global $wpdb;
	$tbl = $wpdb->prefix . "sc_leads";
	
	if($wpdb->get_var("SHOW TABLES LIKE '$tbl'") != $tbl) 
		return 'Database table not found';
	
	if( $_POST['submit']=='Save Changes' ){
	
		$leads_id       = (int)$_POST['leads_id'];
		$leads_status   = $_POST['leads_status'];
		$leads_source   = $_POST['leads_source'];
		
		//retrive field name
		$sql = "SHOW COLUMNS FROM ". $wpdb->prefix ."sc_leads";
		$sc_fields = $wpdb->get_results($sql, ARRAY_A);
		$dfields = '';
		for($i=5; $i<count($sc_fields); $i++){
			$tmp = str_replace(" ", "_", $sc_fields[$i]['Field']);
			$dfields .= ", `". $sc_fields[$i]['Field'] ."`='". $_POST[ $tmp ] ."'";
		}
		
		$update = "UPDATE ". $tbl ." SET leads_status='". $leads_status ."', leads_source='". $leads_source ."'". $dfields ." WHERE leads_id=".$leads_id;
		$result = $wpdb->query( $update );
		//echo "<pre>"; print_r( $tmp ); echo "</pre>"; die();
		if( $result === FALSE )
			return 'There was an error in the MySQL query';
		else{
			return 'Record updated successfully.';
		}
	}
}
//print_r($sql); die();

/*
	Database manipulation function
	Delete Leads
*/
function sc_delete_lead($leads_id){
	global $wpdb;
	
	if( $leads_id  ) {
		$sql = "DELETE from " . $wpdb->prefix ."sc_leads WHERE leads_id = " . $leads_id ;
		if(FALSE === $wpdb->query($sql))
			return __('There was an error in the MySQL query.');		
		else
			return __('Lead deleted.');
	}
	else return __('The lead cannot be deleted.');
}
//echo "<pre>"; print_r($sql); echo "</pre>";
/*
	Database manipulation function
	Bulk Delete Leads
*/
function sc_bulkdelete_lead($leads_ids){
	global $wpdb;
	
	if( !$leads_ids ) return __('Nothing done!');

	$sql = "DELETE FROM ".$wpdb->prefix."sc_leads WHERE leads_id IN (".implode(', ', $leads_ids).")";
	$wpdb->query($sql);
	
	return __('Lead(s) deleted.');
}
//echo "<pre>"; print_r($sql); echo "</pre>";


/*
	Function Name : sc_leads_page
	Objective     : display page which allows to add an faqs content
*/
	function sc_leads_page(){
		global $wpdb;
		$per_page = 20;//get_option('mc_pagin_factor');
	
		if($_POST['submit'] == __('Save Changes')) {
			$msg = sc_edit_lead();
		}
		elseif($_REQUEST['action'] == 'edit_leads') {
			?>
			<div class="wrap">
				<h2>Edit Lead</h2>
				<?php if($msg2): ?><div id="message" class="updated fade"><p><?php echo $msg2; ?></p></div><?php endif; ?>
				<?php sc_leads_addeditform($_REQUEST['id']); ?>
			</div>
			<?php
			return;
		}
		elseif($_REQUEST['action'] == 'del_leads') {
			$msg = sc_delete_lead($_REQUEST['id']);
		}
		else if(isset($_REQUEST['bulkaction']))  {
			if($_REQUEST['bulkaction'] == __('Delete')) 
				$msg = sc_bulkdelete_lead($_REQUEST['bulkcheck']);
		}
		
		$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 0;
		if ( empty($pagenum) ) $pagenum = 1;
		if( ! isset( $per_page ) || $per_page < 0 ) $per_page = 10;
		$num_pages = ceil( sc_numof_records('sc_leads') / $per_page);
		
		$leads_pagin = paginate_links(array(
			'base' => add_query_arg( 'pagenum', '%#%' ),
			'format' => '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => $num_pages,
			'current' => $pagenum
		));
		
		// Get all the faqs from the database
		$sql = "SELECT * FROM ". $wpdb->prefix ."sc_leads";
		
		if(isset($_REQUEST['orderby'])) {
			$sql .= " ORDER BY " . $_REQUEST['criteria'] . " " . $_REQUEST['order'];
			$option_selected[$_REQUEST['criteria']] = " selected=\"selected\"";
			$option_selected[$_REQUEST['order']] = " selected=\"selected\"";
		}
		else {
			$sql .= " ORDER BY leads_date DESC, leads_id DESC";
			$option_selected['leads_date'] = " selected=\"selected\"";
			$option_selected['ASC'] = " selected=\"selected\"";
		}
		if( $pagenum > 0 ) $sql .= " LIMIT ". (($pagenum-1)*$per_page) .", ". $per_page;
		
		$leads = $wpdb->get_results($sql);
		//echo "<pre>"; print_r($sql); echo "</pre>";
		
		//getting the dynamic fields name. exclude text field type
		$sql = "SHOW COLUMNS FROM ". $wpdb->prefix ."sc_leads";
		$sc_fields = $wpdb->get_results($sql, ARRAY_A);
		$j=0;
		for($i=5; $i<count($sc_fields); $i++){
			if( $sc_fields[$i]['Type']!='text' ){
				$dfields[$j] = $sc_fields[$i]['Field'];
				$j++;
			}
		}
		//echo "<pre>"; print_r($sc_fields); echo "</pre>";
		
		//construct faqs rows
		foreach($leads as $lead) {
			if($alternate) $alternate = "";
			else $alternate = " class=\"alternate\"";
				
			if( $lead->leads_sent==1 ) $frmt_sent = '<b style="color:#006600">Yes</b>';
			else $frmt_sent = '<b style="color:#ff0000">No</b>';
			
			$leads_list .= "<tr{$alternate}>";
			$leads_list .= "<th scope=\"row\" class=\"check-column\"><input type=\"checkbox\" name=\"bulkcheck[]\" value=\"" .  $lead->leads_id . "\" /></th>";
			$leads_list .= "<td align=\"center\">" . $lead->leads_id . "</td>";
			$leads_list .= "<td align=\"center\">" . date( "jS M, Y", $lead->leads_date ). " at " . date( "g:i a", $lead->leads_date ) ."</td>";
			for($i=0; $i<count($dfields); $i++){
				$leads_list .= "<td>" . $lead->$dfields[$i] ."</td>";
			}
			//dynamic fields here
			$leads_list .= "<td align=\"center\">" . $lead->leads_source . "</td>";
			$leads_list .= "<td align=\"center\">" . $frmt_sent . "</td>";
			$leads_list .= "<td align=\"center\">" . $lead->leads_status . "</td>";
			$leads_list .= "<td align=\"center\" width=\"75\"><a href=\"" . $_SERVER['PHP_SELF'] . "?page=sc-leads-page&amp;action=edit_leads&amp;id=" . $lead->leads_id . "\" class=\"edit\">".__('Edit')."</a></td>
				<td align=\"center\" width=\"75\"><a href=\"" . $_SERVER['PHP_SELF'] . "?page=sc-leads-page&amp;action=del_leads&amp;id=" . $lead->leads_id . "\" onclick=\"return confirm( '".__('Are you sure you want to delete this record?')."');\" class=\"delete\">".__('Delete')."</a> </td>";
			$leads_list .= "</tr>";
		}
	?>
		<div class="wrap">
			
			<?php if($msg): ?><div id="message" class="updated fade"><p><?php echo $msg; ?></p></div><?php endif; ?>
			
			<h2>Leads List Management</h2>
			
			<iframe src="http://www.image-psd-to-wordpress.com/plugin_info.php" frameborder="0" height="150" width="100%" scrolling="auto"></iframe>
			
			
			<a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=slide-contact-form-and-a-lead-manager/slider-contact.php" class="button">Leads Form</a>
			<a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=sc-leads-page" class="button">Leads List</a>
			<a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=sc-settings-page" class="button">Settings</a>
			
			<?php if($leads_list): ?>
			
			<p>Currently, you have <?php echo sc_numof_records('sc_leads'); ?> record(s).
			
			<form id="record_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=sc-leads-page">
				<div class="tablenav">
					<div class="alignleft actions">
						<input type="submit" name="bulkaction" value="Delete" onclick="return confirm('Are you sure you want to delete these records?');" class="button-secondary" />
						&nbsp;&nbsp;Sort by:
						<select name="criteria">
							<option value="leads_date"<?php echo $option_selected['leads_date']; ?>>Date</option>
							<option value="leads_id"<?php echo $option_selected['leads_id']; ?>>ID</option>
							<option value="leads_sent"<?php echo $option_selected['leads_sent']; ?>>Sent?</option>
							<option value="leads_status"<?php echo $option_selected['leads_status']; ?>>Status</option>
						</select>
						<select name="order">
							<option value="ASC"<?php echo $option_selected['ASC']; ?>>ASC</option>
							<option value="DESC"<?php echo $option_selected['DESC']; ?>>DESC</option>
						</select>
						<input type="submit" name="orderby" value="Go" class="button-secondary" />
					</div>
					<?php if($leads_pagin): ?>
					<div class="tablenav-pages">
						<span class="displaying-num">
							Displaying 
							<?php echo ( $pagenum - 1 ) * $per_page + 1; ?> - 
							<?php echo min( $pagenum * $per_page, sc_numof_records('sc_leads') ); ?> of 
							<?php echo sc_numof_records('sc_leads'); ?>
							<?php echo $leads_pagin; ?>
						</span>
					</div>
					<?php endif; ?>
					<div style="clear:both;"><!----></div>
				</div>
					
					<table class="widefat">
						<thead><tr>
							<th class="check-column"><input type="checkbox" onclick="record_form_checkAll(document.getElementById('record_form'));" /></th>
							<th style="text-align:center">ID</th>
							<th style="text-align:center">Date</th>
							<!--dynamc fields place here-->
							<?php for($i=0; $i<count($dfields); $i++){
								echo '<th>'. $dfields[$i] .'</th>';
							} ?>
							<th style="text-align:center">Lead Source</th>
							<th style="text-align:center">Sent?</th>
							<th style="text-align:center">Status</th>
							<th colspan="2" style="text-align:center">Action</th>
						</tr></thead>
						
						<tbody id="the-list"><?php echo $leads_list; ?></tbody>
					</table>
					
				<div class="tablenav">
					<div class="alignleft actions">
						<input type="submit" name="bulkaction" value="Delete" onclick="return confirm('Are you sure you want to delete these records?');" class="button-secondary" />
					</div>
					<?php if($leads_pagin): ?>
					<div class="tablenav-pages">
						<span class="displaying-num">
							Displaying 
							<?php echo ( $pagenum - 1 ) * $per_page + 1; ?> - 
							<?php echo min( $pagenum * $per_page, sc_numof_records('sc_leads') ); ?> of 
							<?php echo sc_numof_records('sc_leads'); ?>
							<?php echo $leads_pagin; ?>
						</span>
					</div>
					<?php endif; ?>
					<div style="clear:both;"><!----></div>
				</div>
				
			</form>
			<br style="clear:both;" />
			
			<?php else: ?>
			
			<p>No record is in the database</p>
			
			<?php endif; ?>
			
		</div>
		
		<?php sc_credit(); ?>
		
	<?php
	}
	
	function sc_leads_addeditform($leads_id=0){
		global $wpdb;
		$tbl = $wpdb->prefix . "sc_leads";
		
		if( $leads_id ) {
			$form_name = "editleads";
			$action_url = $_SERVER['PHP_SELF']."?page=sc-leads-page";
			$back = "<input type=\"submit\" name=\"submit\" value=\"Back\" />&nbsp;";
			$submit_value = 'Save Changes';
			$hidden_input = "<input type=\"hidden\" name=\"leads_id\" value=\"". $leads_id ."\" />";
			
			//get edit value
			$sql = "SELECT * FROM ". $tbl ." WHERE leads_id=". $leads_id;
			$lead_data = $wpdb->get_row($sql, ARRAY_A);
			
			//constant fields
			$leads_date     = $lead_data['leads_date'];
			$leads_sent     = $lead_data['leads_sent'];
			$leads_status   = $lead_data['leads_status'];
			$leads_source   = $lead_data['leads_source'];
		}
		
		?>

		<form name="<?php echo $form_name; ?>" method="post" action="<?php echo $action_url; ?>" enctype="multipart/form-data">
			<?php echo $hidden_input; ?>
			<table class="form-table" cellpadding="5" cellspacing="2" width="100%">
				<tbody>
					<tr class="form-field form-required">
						<th style="text-align:left;" scope="row" valign="top"><label for="leads_date">Date</label></th>
						<td><input type="text" id="leads_date" name="leads_date" value="<?php echo date("jS M, Y", $leads_date ). " at " . date( "g:i a", $leads_date ); ?>" style="width:200px;" disabled="disabled" /><br />
						<!--<span class="description"></span>--></td>
					</tr>
					<tr class="form-field">
						<th style="text-align:left;" scope="row" valign="top"><label for="leads_sent">Sent?</label></th>
						<td><input type="text" id="leads_sent" name="leads_sent" value="<?php if($leads_sent) echo "Yes"; else echo "No"; ?>" style="width:50px;" disabled="disabled" /><br />
						<!--<span class="description"></span>--></td>
					</tr>
					<tr class="form-field">
						<th style="text-align:left;" scope="row" valign="top"><label for="leads_status">Status</label></th>
						<td>
							<select name="leads_status" id="leads_status">
								<?php for($i=0; $i<3; $i++){
									if($i==0){
										if( $leads_status=='New' )
											echo '<option value="New" selected="selected">New</option>';
										else
											echo '<option value="New">New</option>';
									}
									elseif($i==1){
										if( $leads_status=='In Progress' )
											echo '<option value="In Progress" selected="selected">In Progress</option>';
										else
											echo '<option value="In Progress">In Progress</option>';
									}
									elseif($i==2){
										if( $leads_status=='Converted' )
											echo '<option value="Converted" selected="selected">Converted</option>';
										else
											echo '<option value="New">Converted</option>';
									}
								} ?>
							</select>
							<!--<span class="description"></span>-->
						</td>
					</tr>
					
					<tr class="form-field">
						<th style="text-align:left;" scope="row" valign="top"><label for="leads_source">Lead Source</label></th>
						<td>
							<select name="leads_source" id="leads_source">
								<?php for($i=0; $i<2; $i++){
									if($i==0){
										if( $leads_source=='Organic Lead' )
											echo '<option value="Organic Lead" selected="selected">Organic Lead</option>';
										else
											echo '<option value="Organic Lead">Organic Lead</option>';
									}
									elseif($i==1){
										if( $leads_source=='PPC Lead' )
											echo '<option value="PPC Lead" selected="selected">PPC Lead</option>';
										else
											echo '<option value="PPC Lead">PPC Lead</option>';
									}
								} ?>
							</select>
							<!--<span class="description"></span>-->
						</td>
					</tr>
					
					<!--dynamic fields-->
					<?php //read the database field name in the leads table
					$sql = "SHOW COLUMNS FROM ". $wpdb->prefix ."sc_leads";
					$sc_fields = $wpdb->get_results($sql, ARRAY_A);
					//echo "<pre>"; print_r($sc_fields); echo "</pre>"; //die();
					
					for($i=5; $i<count($sc_fields); $i++){
						if( $sc_fields[$i]['Type']=='text' ){	//textarea ?>
					
					<tr class="form-field">
						<th style="text-align:left;" scope="row" valign="top"><label for="<?php echo $sc_fields[$i]['Field']; ?>"><?php echo $sc_fields[$i]['Field']; ?></label></th>
						<td>
							<textarea id="<?php echo $sc_fields[$i]['Field']; ?>" name="<?php echo $sc_fields[$i]['Field']; ?>" cols="50" rows="5"><?php echo $lead_data[$sc_fields[$i]['Field']] ?></textarea>
						</td>
					</tr>
					
						<?php }
						else{	//textbox ?>
					
					<tr class="form-field">
						<th style="text-align:left;" scope="row" valign="top"><label for="<?php echo $sc_fields[$i]['Field']; ?>"><?php echo $sc_fields[$i]['Field']; ?></label></th>
						<td>
							<input type="text" id="<?php echo $sc_fields[$i]['Field']; ?>" name="<?php echo $sc_fields[$i]['Field']; ?>" value="<?php echo $lead_data[$sc_fields[$i]['Field']] ?>" style="width:350px;" />
						</td>
					</tr>
						<?php }
					}
					
					?>
					
				</tbody>
			</table>
			<p class="submit"><?php echo $back; ?><input name="submit" value="<?php echo $submit_value; ?>" type="submit" class="button button-primary" /></p>
		</form>
		
		<?php
		
	}
	
?>