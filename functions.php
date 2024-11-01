<?php

/*
	code added to the head section of the page
*/
function sc_admin_head(){
	?>
<!--leads plugin starts-->

<link rel="stylesheet" href="<?php echo get_option('siteurl'); ?>/wp-content/plugins/slide-contact-form-and-a-lead-manager/css/admin.css" type="text/css" media="screen" />
<link rel="stylesheet" id="thickbox-css"  href="<?php echo get_option('siteurl'); ?>/wp-includes/js/thickbox/thickbox.css?ver=20090514" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo get_option('siteurl'); ?>/wp-content/plugins/slide-contact-form-and-a-lead-manager/js/colorpicker/css/colorpicker.css" type="text/css" media="screen" />

<script type="text/javascript" language="javascript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/slide-contact-form-and-a-lead-manager/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo get_option('siteurl'); ?>/wp-includes/js/thickbox/thickbox.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/slide-contact-form-and-a-lead-manager/js/colorpicker/colorpicker.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/slide-contact-form-and-a-lead-manager/js/sc_admin.js"></script>
<script type="text/javascript" language="javascript">
	var sc_blog_url   = "<?php bloginfo('url'); ?>";
	var sc_plugin_url = "<?php bloginfo('url'); ?>/wp-content/plugins/slide-contact-form-and-a-lead-manager/";
</script>
<!--leads plugin ennds-->
	<?php
}
add_action('admin_head', 'sc_admin_head');


/*
	code added to the head section front side of the site
*/
function sc_user_head(){
	$upload_dir = wp_upload_dir();
	$up_path = $upload_dir['baseurl']."/";
	
	$lead_vars     = get_option('sc_google_lead_vars');
	$lead_id       = $lead_vars['id'];
	$lead_lang     = $lead_vars['language'];
	$lead_format   = $lead_vars['format'];
	$lead_color    = $lead_vars['color'];
	$lead_label    = $lead_vars['label'];
	$lead_value    = $lead_vars['value'];

	?>
<!--leads plugin starts-->
<link rel="stylesheet" href="<?php echo get_option('siteurl'); ?>/wp-content/plugins/slide-contact-form-and-a-lead-manager/css/style.css" type="text/css" media="screen" />
<script type="text/javascript" language="javascript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/slide-contact-form-and-a-lead-manager/js/sc.js"></script>
<script type="text/javascript" language="javascript">
	var sc_blog_url   = "<?php bloginfo('url'); ?>";
	var sc_plugin_url = "<?php bloginfo('url'); ?>/wp-content/plugins/slide-contact-form-and-a-lead-manager/";
	
	<?php if($lead_id):     echo "\n"; ?> var google_conversion_id       = <?php echo $lead_id; ?>; <?php endif; ?>
	<?php if($lead_lang):   echo "\n"; ?> var google_conversion_language = "<?php echo $lead_lang; ?>"; <?php endif; ?>
	<?php if($lead_format): echo "\n"; ?> var google_conversion_format   = "<?php echo $lead_format; ?>"; <?php endif; ?>
	<?php if($lead_color):  echo "\n"; ?> var google_conversion_color    = "<?php echo $lead_color; ?>"; <?php endif; ?>
	<?php if($lead_label):  echo "\n"; ?> var google_conversion_label    = "<?php echo $lead_label; ?>"; <?php endif; ?>
	<?php if($lead_value):  echo "\n"; ?>
	if (15.0) {
		var google_conversion_value = <?php echo $lead_value; ?>;
	}
	<?php endif; ?>
	
	<?php
		$settings = get_option('sc_settings');
		$method   = (int)$settings['sc_mess_method'];
		$thanku   = (int)$settings['sc_thanku_page'];
		$error    = (int)$settings['sc_error_page'];
		$captcha  = (int)$settings['sc_captcha'];
		
		//$thanku_uri = get_page_uri($thanku);
		$thanku_url = get_permalink($thanku);
		$error_url  = get_permalink($error);
		
		echo 'var sc_method = '.$method.";\n";
		echo 'var sc_captcha = '. $captcha .";\n";
		if($method==2){
			if($thanku==0)
				echo 'var sc_thanku_page_url = "";'."\n";
			else
				echo 'var sc_thanku_page_url = "'. $thanku_url ."\";\n";
		}
		if($method==2){
			if($error==0)
				echo 'var sc_error_page_url = "";'."\n";
			else
				echo 'var sc_error_page_url = "'. $error_url ."\";\n";
		}
	?>
</script>
<style type="text/css"><?php

$options = get_option('sc_form_styles');
$sc_bar_color     = $options['sc_bar_color'];
$sc_bar_radius    = $options['sc_bar_radius'];
$sc_label_fsize   = $options['sc_label_fsize'];
$sc_label_fcolor  = $options['sc_label_fcolor'];
$sc_label_fface   = $options['sc_label_fface'];
$sc_input_bcolor  = $options['sc_input_bcolor'];
$sc_input_bradius = $options['sc_input_bradius'];
$sc_input_bgcolor = $options['sc_input_bgcolor'];
$sc_input_fsize   = $options['sc_input_fsize'];
$sc_input_fcolor  = $options['sc_input_fcolor'];
$sc_input_fface   = $options['sc_input_fface'];
$sc_form_bimage   = $options['sc_form_bimage'];
$sc_form_submit   = $options['sc_form_submit'];
$sc_form_bcolor   = $options['sc_form_bcolor'];
$sc_req_fcolor    = $options['sc_req_fcolor'];
$sc_bar_color     = $options['sc_bar_color'];

/*border styles*/
if( $sc_bar_color!='' && strlen($sc_bar_color)==7 ){
	echo "\n\t".'#drawer{ border-color:'. $sc_bar_color .'; background-color:'. $sc_bar_color .'; }';
}
if( $sc_bar_radius!='' && is_int($sc_bar_radius) ){
	echo "\n\t".'#drawer{ -moz-border-radius-topright :'. $sc_bar_radius .'px; -moz-border-radius-bottomright :'. $sc_bar_radius .'px; -webkit-border-top-right-radius:'. $sc_bar_radius .'px; -webkit-border-bottom-right-radius:'. $sc_bar_radius .'px; }';
}

/*label styles*/
if( $sc_label_fsize!='' && is_int($sc_label_fsize) ){
	echo "\n\t".'#drawer-content form label{ font-size:'. $sc_label_fsize .'px; }';
}
if( $sc_label_fcolor!='' && strlen($sc_label_fcolor)==7 ){
	echo "\n\t".'#drawer-content form label{ color:'. $sc_label_fcolor .'; } #drawer-content .drwr-ftr{ color:'. $sc_label_fcolor .';} #drawer-content .drwr-ftr a{ color:'. $sc_label_fcolor .';}' ;
}
if( $sc_label_fface!='' ){
	echo "\n\t"."#drawer-content form label{ font-family:". $sc_label_fface ."; }";
}

/*input styles*/
if( $sc_input_bcolor!='' && strlen($sc_input_bcolor)==7 ){
	echo "\n\t".'#drawer-content form input.drwr-txtInp, #drawer-content form textarea.drwr-txtArea{ border:1px solid '. $sc_input_bcolor .'; }';
}
if( $sc_input_bradius!='' && is_int($sc_input_bradius) ){
	echo "\n\t".'#drawer-content form input.drwr-txtInp, #drawer-content form textarea.drwr-txtArea{ -moz-border-radius:'. $sc_input_bradius .'px; -webkit-border-radius:'. $sc_input_bradius .'px; }';
}
if( $sc_input_bgcolor!='' && strlen($sc_input_bgcolor)==7 ){
	echo "\n\t".'#drawer-content form input.drwr-txtInp, #drawer-content form textarea.drwr-txtArea{ background-color:'. $sc_input_bgcolor .'; }';
}
if( $sc_input_fsize!='' && is_int($sc_input_fsize) ){
	echo "\n\t".'#drawer-content form input.drwr-txtInp, #drawer-content form textarea.drwr-txtArea{ font-size:'. $sc_input_fsize .'px; }';
}
if( $sc_input_fcolor!='' && strlen($sc_input_fcolor)==7 ){
	echo "\n\t".'#drawer-content form input.drwr-txtInp, #drawer-content form textarea.drwr-txtArea{ color:'. $sc_input_fcolor .'; }';
}
if( $sc_input_fface!='' ){
	echo "\n\t"."#drawer-content form input.drwr-txtInp, #drawer-content form textarea.drwr-txtArea{ font-family:". $sc_input_fface ." }";
}

/*form styles*/
if( $sc_form_bimage!='' ){
	echo "\n\t"."#drawer-content{ background-image:url(". $up_path.$sc_form_bimage .") }";
}
if( $sc_form_submit!='' ){
	echo "\n\t"."#drawer-content form input.drwr-butnSubmit{ background:url(". $up_path.$sc_form_submit .") no-repeat; }";
}
if( $sc_form_bcolor!='' && strlen($sc_form_bcolor)==7 ){
	echo "\n\t"."#drawer-content{ background-color:". $sc_form_bcolor ."; }";
}
if( $sc_req_fcolor!='' && strlen($sc_req_fcolor)==7 ){
	echo "\n\t"."#submitDiv p{ color:". $sc_req_fcolor ."; } #drawer-content form label i.drwr-mendatory{ color:". $sc_req_fcolor ."; }\n";
}
//echo "<pre>"; print_r($options); echo "</pre>";


?></style>
<?php
	if( is_page($thanku) && $method==2 ){
		echo '<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js"></script>'."\n";
		echo '<style type="text/css"> iframe{ display:none;} </style>'."\n";
	}
?>
<!--leads plugin ends-->
	<?php
}
add_action('wp_head', 'sc_user_head');

function sc_loadJQuery(){
	wp_enqueue_script('jquery');
}
add_action('init', 'sc_loadJQuery');


/*----------------------------------------------------------------------------------------------------
								R E S U A B L E     F U N C T I O N S
----------------------------------------------------------------------------------------------------*/

function sc_numof_records($tbl_name=''){
	global $wpdb;
	
	if( $tbl_name=='' ) return false;
	
	$sql = "SELECT * FROM ". $wpdb->prefix . $tbl_name;
	$resultset = $wpdb->get_results($sql);
	return count( $resultset );
}
//print_r($sql); die();

function sc_fetch_row($tbl_name='', $id=0, $fn=''){
	global $wpdb;
	
	if( $tbl_name=='' || $id < 1 || fn=='' ) return false;
	$sql = "SELECT * FROM ". $wpdb->prefix . $tbl_name . " WHERE " . $fn . "=" . $id;
	
	return $wpdb->get_results($sql, ARRAY_A);
}
//print_r($sql); die();

function sc_fetch_value($tbl_name='', $id=0, $fn='', $cfn=''){
	global $wpdb;
	
	if( $tbl_name=='' || $id < 1 || fn=='' || $cfn=='' ) return false;
	$sql = "SELECT ". $cfn ." FROM ". $wpdb->prefix . $tbl_name . " WHERE " . $fn . "=" . $id;
	$ret = $wpdb->get_row($sql, ARRAY_A);
		
	return $ret[$cfn];
}

function sc_isemail($email){
	return preg_match('|^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$|i', $email);
}

function sc_credit(){
	
	$domain = $_SERVER['HTTP_HOST'];
	$domain_array = explode('www.',$domain);

	if(count($domain_array)>1)$domain = $domain_array[1];

	$toal_count = strlen($domain);
	if($toal_count>9){
		$domain_count = (($toal_count-($toal_count%10))/10)+($toal_count%10);     
		$domain_count = (($domain_count-($domain_count%10))/10)+($domain_count%10);   
	}
	else{
	    $domain_count = $toal_count;
	}
	if( $domain_count>=5 ) $text = 'psd to wordpress';
	else $text = 'image to wordpress';
		
	//echo "<pre>"; print_r( $domain_int ); echo "</pre>"; //die();
	echo '<div class="foo_credit">Plugin by <a href="http://www.image-psd-to-wordpress.com/" target="_blank">'. $text .'</a> Solutions.</div>';
}

function sc_dalg($str){
	if( strlen($str)<10 )
		return strlen($str);
	else{
		$tmp = strlen($str);
		while( $tmp>9 ){
			$sum = 0;
			$tmp2 = strval($tmp);
			for( $i=0; $i<strlen($tmp2); $i++ ){
				$sum = $sum + $tmp2{$i};
			}
			$tmp = $sum;
		}
		return $tmp;
	}
}


?>