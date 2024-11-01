<?php
	//session_start();
	
	include_once('../../../wp-load.php');
	require_once('includes/phpmailer/phpmailer.php');
	$msg="";
	$err="";
	
	$admin_email = get_option('admin_email');

	$id = $_POST['id'];
	//echo $id;
	if( $id=='contact' ){
		
		$field_name   = $_POST['field_name'];
		$field_val    = $_POST['field_val'];
		
		//sending mail
		$opt = get_option('sc_settings');
		$to = $opt['sc_email'];
		if( count($to)==0 ) $to = $admin_email;

		$str = '';
		$email = 'No Email';
		$db_fields = '';
		$db_values = '';
		$loop = count($field_val);
		for( $i=0; $i<$loop; $i++ ){

			$field_name[$i] = strip_tags( $field_name[$i], "<a>,<b>,<i>,<em>,<strong>,<br>,<p>" );
			$field_val[$i]  = strip_tags( $field_val[$i], "<a>,<b>,<i>,<em>,<strong>,<br>,<p>" );
		
			$str .= '<strong>'. $field_name[$i] .'</strong>: '. $field_val[$i] .'<br />';
			if( sc_isemail( $field_val[$i] ) )
				$email = $field_val[$i];
			
			if( $i!=$loop-1 ) $db_fields .= "`". $field_name[$i] ."`,";
			else $db_fields .= "`". $field_name[$i] ."`";
			if( $i!=$loop-1 ) $db_values .= "'". $field_val[$i] ."',";
			else $db_values .= "'". $field_val[$i] ."'";
			
		}

		
		//including file
		//if( file_exists( $opt['sc_file_path'].'/send_data.php' ) ){
		//	include( $opt['sc_file_path']."/send_data.php" );
		//}
		
		//determining lead source
		$leads_source = '';	
		if( isset($_SESSION['ppc']) && $_SESSION['ppc']==1 ) $leads_source = "PPC Lead";
		else $leads_source = "Organic Lead";
		
		//add record to the database
		$ctime = time();
		$insert = "INSERT INTO ". $wpdb->prefix ."sc_leads (leads_date ,leads_source, leads_status, ". $db_fields .") VALUES($ctime, '$leads_source', 'New', ". $db_values .")";
		$wpdb->query($insert);
		$ins_id = $wpdb->insert_id;

		//constructing body of the mail
		$body = "Hi, <br />New Lead From ". get_option('siteurl');
		$body .= "<h4>Lead Site details</h4>";
		$body .= "<b>Lead Page:</b> ". $_SERVER['HTTP_REFERER'] ."<br />";
		$body .= "<b>Lead Source:</b> ". $leads_source ."<br />";
		$body .= "<h4>Lead Contact Details</h4>";
		$body .= $str;
		$body .= "<br />Thanks.";
		
		$subject  = "New Lead from ". get_option('siteurl') ." (". $email .")";
		$fromname = get_option('blogname');
		if( $email != 'No Email' )
			$fromname .= " <". $email .">";
		
		$sent = 0;
		if( is_array($to) ){
			foreach( $to as $val ){
				//sending mail
				$mail = new PHPMailer();
				$mail->ClearAddresses();
				$mail->ClearBCCs();
				$mail->ClearCCs();
				$mail->ClearAttachments();
				$mail->AddAddress( $val );
				$mail->Subject = $subject;
				$mail->Body = $body;
				$mail->From = $email;
				$mail->FromName = $fromname;		
				$mail->IsHTML(true);
				$mail->Mailer="mail";
				
				if( $mail->send() ) $sent = 1;
			}
		}
		else{
			//sending mail
			$mail = new PHPMailer();
			$mail->ClearAddresses();
			$mail->ClearBCCs();
			$mail->ClearCCs();
			$mail->ClearAttachments();
			$mail->AddAddress( $to );
			$mail->Subject = $subject;
			$mail->Body = $body;
			$mail->From = $email;
			$mail->FromName = $fromname;		
			$mail->IsHTML(true);
			$mail->Mailer="mail";
			if( $mail->send() ) $sent = 1;
		}

		if( $sent ){
			//set leads_sent to 1
			$update = "UPDATE ". $wpdb->prefix ."sc_leads SET leads_sent='1' WHERE leads_id=". $ins_id;
			$wpdb->query($update);
			echo 1;
		}
		else{
			//set leads_sent to 0
			$update = "UPDATE ". $wpdb->prefix ."sc_leads SET leads_sent='0' WHERE leads_id=". $ins_id;
			$wpdb->query($update);
			echo 0;
		}
		
		
		//if auto responder is on send a mail to the user
		$auto_res_switch = (int)$opt['auto_res_switch'];
		if( $auto_res_switch && $email!='No Email' ){
			//send a copy to client
			$body = "Hello, <br />";
			$body .= nl2br( get_option('sc_auto_res_mess') );
			$body .= "<h4>Your Provided Information</h4>";
			$body .= $str ."<br /><br />";
			$body .= "Thanks.<br />". get_option('siteurl');
			
			$subject = trim( $opt['auto_res_sub'] );
			if( $subject=='' ) $subject = "Thank you for contacting...";
			$from      = $admin_email;
			$from_name = get_option('blogname');
			
			$mail = new PHPMailer();
			$mail->ClearAddresses();
			$mail->ClearBCCs();
			$mail->ClearCCs();
			$mail->ClearAttachments();
			$mail->AddAddress($email);
			$mail->Subject = $subject;
			$mail->Body = $body;
			$mail->From = $from;
			$mail->FromName = $from_name;		
			$mail->IsHTML(true);
			$mail->Mailer="mail";
			
			$mail->send();
		}
	}
	if( $id=='captcha' ){
		include("includes/captcha/securimage.php");
		$value = $_POST['value'];
		
		$img = new Securimage();
		$valid = $img->check( $value );
		
		if($valid == true) echo '1';
		else echo '0';
	}

//		$f = fopen("a.txt","w");
//		fwrite($f,$field_name[0]);
//		fclose($f);

?>