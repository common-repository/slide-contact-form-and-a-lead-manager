<?php
/*
	Plugin Name: Wordpress Leads Plugin
	Plugin URI: http://www.image-psd-to-wordpress.com/
	Description: Integrate a Sliding Contact Us bar on the left side of the Site
	Version: 2.0 (beta)
*/

/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/*-------------------------------------------------------------------------------------------------------------------------------------------
					D E C L E A R I N G / I N C L U D I N G     C O N T A N T S
-------------------------------------------------------------------------------------------------------------------------------------------*/

// "sc" is the initial prefix for this plugin
// "sc" refers to slider contact, [lately its name changed to leads-plugin, but prefix was not changed]
// use this prefix to each function name, db table name, or any identifier

$sc_aul = 8;    //admin user level    // Refer http://codex.wordpress.org/Roles_and_Capabilities
$sc_url = get_option('siteurl').'/wp-content/plugins/slide-contact-form-and-a-lead-manager';

//including files
include_once(dirname(__FILE__).'/functions.php');
include_once(dirname(__FILE__).'/admin-page.php');
include_once(dirname(__FILE__).'/leads-page.php');
include_once(dirname(__FILE__).'/settings-page.php');

function sc_contact_div(){

global $sc_url;
$fields = get_option('sc_form');
$settings = get_option('sc_settings');
//echo "<pre>"; print_r($fields); echo "</pre>";

?>

<div id="drawer" style="left:-315px">
    <div id="drawer-content">
    	<div id="drawerTab"><b>Contact Us</b></div>
        <form method="post" action="" onsubmit="return scCheckForm()">
            
			<?php if( $fields!='' ): for($i=0; $i<count($fields); $i++): ?>
			
			<?php
				if( $fields[$i]['req']==1 ){ $mend = 'mendatory'; $mend_text = '<i class="drwr-mendatory">*</i> '; }
				else{ $mend = ''; $mend_text=''; }
				if( $fields[$i]['mail']==1 ) $mail = 'sc_mail';
				else $mail = '';
				
				$lbl = '<label class="'. $mend .' '. $mail .'" for="field_'. $i .'">'. $mend_text . $fields[$i]['label'] .'</label>';
				$hid = '<input name="field_name[]" value="'. $fields[$i]['label'] .'" type="hidden" />';
				if( $fields[$i]['type']=='textbox' )
					$in = '<input class="drwr-txtInp" name="field_val[]" id="field_'. $i .'" type="text" />';
				else
					$in = '<textarea class="drwr-txtArea" rows="5" cols="5" name="field_val[]" id="field_'. $i .'"></textarea>';
					
				echo $hid;
				echo $lbl;
				echo $in;
			?>
			
			<?php endfor; endif; ?>
			
			
			<?php //add captcha code starts 
			if( $settings['sc_captcha']==1 ): ?>
			<div id="verify">
            	<label><b>Security Code:</b> <em><i class="drwr-mendatory">*</i> <label for="sc_code">Verify Code:</label></em></label>
				<img src="<?php echo $sc_url; ?>/includes/captcha/securimage_show.php?sid=<?php echo md5(uniqid(time())); ?>" alt="Security Code" id="sc_image" />
				<a href="#" onclick="document.getElementById('sc_image').src = '<?php echo $sc_url; ?>/includes/captcha/securimage_show.php?sid=' + Math.random(); return false"><img src="<?php echo $sc_url; ?>/includes/captcha/images/refresh.png" alt="Reload Image" title="Reload Image" style="margin:0; cursor:pointer" /></a>
            	<input name="sc_code" id="sc_code" class="drwr-txtInp" width="100px" type="text" />
            </div>
			<?php endif; //add captcha code ends ?>
            
			<div id="submitDiv">
                <p>*required fields</p>
                <input value="" class="drwr-butnSubmit" type="submit" />
            </div>
        </form>
        
		<div class="drwr-wait"><span><!----></span><i><!----></i></div>
		<div id="sc_thanku" class="drwr-msg"><?php echo $settings['sc_thanku']; ?></div>
		<div id="sc_error"  class="drwr-msg"><?php echo $settings['sc_error']; ?></div>
		
		<!-- This plugin can be used as long as you provide a link back to our site. 
		if you like to use the plugin without the bellow link, then please contact 
		us at plugins@developer4lease.com -->
		<?php
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
				
			echo '<div class="drwr-ftr">Plugin by <a href="http://www.image-psd-to-wordpress.com/" target="_blank">'. $text .'</a> Solutions.</div>';
		?>
    </div>
</div>




<?php
}
add_action('wp_footer', 'sc_contact_div');

/*-------------------------------------------------------------------------------------------------------------------------------------------
											S H O R T C O D E     F U N C T I O N
-------------------------------------------------------------------------------------------------------------------------------------------*/
function sc_shortcode_form() {
	global $sc_url;
	$fields   = get_option('sc_form');
	$settings = get_option('sc_settings');

	$form = '';
	$form .= '<div id="sc_form">';
	$form .= '<div class="mess"></div>';
	$form .= '<form method="post" action="" onsubmit="return scCheckForm2()">';
	
	if( $fields!='' ): for($i=0; $i<count($fields); $i++):
		
		if( $fields[$i]['req']==1 ){ $mend = 'mendatory '; $ast = '* '; }
		else { $mend = ''; $ast = ''; }
		
		if( $fields[$i]['mail']==1 ) $mail = 'sc_mail';
		else $mail = '';
		
		$lbl = '<label class="'. $mend. $mail .'" for="field_'. $i .'_sc">'. $ast . $fields[$i]['label'] .'</label>';
		$hid = '<input name="field_name[]" value="'. $fields[$i]['label'] .'" type="hidden" style="display:none;" />';
		
		if( $fields[$i]['type']=='textbox' )
			$in = '<input class="drwr-txtInp-sc" name="field_val[]" id="field_'. $i .'_sc" type="text" />';
		else
			$in = '<textarea class="drwr-txtArea-sc" rows="5" cols="5" name="field_val[]" id="field_'. $i .'_sc"></textarea>';
		
		$form .= "\n\n<p>".$hid;
		$form .= "\n".$lbl;
		$form .= "\n".$in."</p>";
	
	endfor; endif;
	
	
	//add captcha code starts 
	if( $settings['sc_captcha']==1 ){
		
		$form .= '<p><label>Security Code</label>';
		$form .= '<img src="'. $sc_url .'/includes/captcha/securimage_show.php?sid='. md5(uniqid(time())) .'" alt="Security Code" id="sc_image_sc" style="float:left" />';
		
		$form .= '<a href="#" onclick="document.getElementById(\'sc_image_sc\').src = \''. $sc_url .'/includes/captcha/securimage_show.php?sid=\' + Math.random(); return false"><img src="'. $sc_url .'/includes/captcha/images/refresh.png" alt="Reload Image" title="Reload Image" style="float:left;padding-left:10px;" /></a></p>';
		
		$form .= '<p><label for="sc_code_sc" class="mendatory">* Verify Code</label>';
		$form .= '<input name="sc_code" id="sc_code_sc" type="text" style="text-align:center;" /></p>';
	
	}
	//add captcha code ends
	
	
		$form .= '<p><label>*required fields</label><input value="Submit" type="submit" id="sc_submit_sc" /></p>';
	$form .= '</form>';
	$form .= '</div>';

	$form .= '<div id="sc_thanku_sc" style="display:none"><div class="mess">'. $settings['sc_thanku'] .'</div></div>';
	$form .= '<div id="sc_error_sc"  style="display:none"><div class="mess">'. $settings['sc_error'] .'</div></div>';

	return $form;
}
add_shortcode('leadsform', 'sc_shortcode_form');

/*-------------------------------------------------------------------------------------------------------------------------------------------
											A D M I N     M E N U     F U N C T I O N
-------------------------------------------------------------------------------------------------------------------------------------------*/

add_action('admin_menu', 'sc_admin_menu');
function sc_admin_menu() {
	global $sc_aul, $menu;
	
	//general purpose tab
	add_menu_page('Leads Form', 'Leads Form', $sc_aul, __FILE__, 'sc_admin_page',   '');
	add_submenu_page(__FILE__,   'Leads List Management',     'Leads List',   $sc_aul,  'sc-leads-page',                     'sc_leads_page');
	add_submenu_page(__FILE__,   'Leads Settings',     'Settings',   $sc_aul,           'sc-settings-page',                  'sc_settings_page');
}


/*-------------------------------------------------------------------------------------------------------------------------------------------
											I N S T A L L A T I O N     F U N C T I O N
-------------------------------------------------------------------------------------------------------------------------------------------*/
register_activation_hook( __FILE__, 'sc_install' );
function sc_install(){
	global $wpdb;
	$upload = wp_upload_dir();
	
	//initial db setup
	if(!defined('DB_CHARSET') || !($db_charset = DB_CHARSET))
		$db_charset = 'utf8';
	$db_charset = "CHARACTER SET ".$db_charset;
	if(defined('DB_COLLATE') && $db_collate = DB_COLLATE) 
		$db_collate = "COLLATE ".$db_collate;
	
	//leads table
	$table_name = $wpdb->prefix . "sc_leads";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
			leads_id        bigint NOT NULL auto_increment,
			leads_date      int NOT NULL,
			leads_sent      varchar(10) NOT NULL,
			leads_status    varchar(20) NOT NULL,
			leads_source    varchar(20) NOT NULL,
			Name            varchar(255) NOT NULL,
			Email           varchar(255) NOT NULL,
			Message         text NOT NULL,
			PRIMARY KEY  (leads_id)
		) {$db_charset} {$db_collate};";
		$results = $wpdb->query( $sql );
	}
	
	//initial options setup
	$default_form = array();
	
	//contructing array for "sc_form" option
	$default_form[0]['label'] = 'Name';
	$default_form[0]['type']  = 'textbox';
	$default_form[0]['req']   = '1';
	$default_form[0]['mail']  = '0';
	$default_form[0]['order'] = '1';
	
	$default_form[1]['label'] = 'Email';
	$default_form[1]['type']  = 'textbox';
	$default_form[1]['req']   = '1';
	$default_form[1]['mail']  = '1';
	$default_form[1]['order'] = '2';
	
	$default_form[2]['label'] = 'Message';
	$default_form[2]['type']  = 'textarea';
	$default_form[2]['req']   = '1';
	$default_form[2]['mail']  = '0';
	$default_form[2]['order'] = '3';
	
	add_option("sc_form",     $default_form, '', 'yes');
	
	//contructing array for "sc_settings" option
	$default_settings = array();
	$default_settings['sc_email']   = array();
	$default_settings['sc_captcha'] = '0';
	$default_settings['sc_mess_method'] = '1';
	$default_settings['sc_thanku'] = 'Thank you. Your message has been sent.';
	$default_settings['sc_error']  = 'There was an error while sending your message. Please try again later.';
	$default_settings['sc_thanku_page']  = '0';
	$default_settings['sc_error_page']   = '0';
	$default_settings['auto_res_switch'] = '1';
	$default_settings['auto_res_sub']    = '';
	$default_settings['sc_file_path']    = '';
	add_option("sc_settings", $default_settings,   '', 'yes');
	
	add_option("sc_auto_res_mess", "We have received your inquiry and we will respond back to you shortly. For your record here is the copy of the message you submitted.",   '', 'yes');
	
	//contructing array for "sc_form_styles" option
	$default_styles = array();
	$default_styles['sc_bar_color']     = '#123e66';
	$default_styles['sc_bar_radius']    = '15';
	$default_styles['sc_label_fsize']   = '14';
	$default_styles['sc_label_fcolor']  = '#333333';
	$default_styles['sc_label_fface']   = '';
	$default_styles['sc_input_bcolor']  = '#67a9de';
	$default_styles['sc_input_bradius'] = '15';
	$default_styles['sc_input_bgcolor'] = '#dcebf5';
	$default_styles['sc_input_fsize']   = '13';
	$default_styles['sc_input_fcolor']  = '#333333';
	$default_styles['sc_input_fface']   = '';
	$default_styles['sc_form_bimage']   = '';
	$default_styles['sc_form_submit']   = '';
	$default_styles['sc_form_bcolor']   = '#ffffff';
	$default_styles['sc_req_fcolor']    = '#ff0000';
	add_option("sc_form_styles", $default_styles,   '', 'yes');
	
	//contructing array for "sc_google_lead_vars" option
	$default_lead_vars = array();
	$default_lead_vars['id']       = '';
	$default_lead_vars['language'] = 'en_US';
	$default_lead_vars['format']   = '1';
	$default_lead_vars['color']    = 'ffffff';
	$default_lead_vars['label']    = '';
	$default_lead_vars['value']    = '15.0';
	add_option("sc_google_lead_vars",     $default_lead_vars, '', 'yes');
	
	//create uploads directory
	wp_mkdir_p( ABSPATH . 'wp-content/uploads/' );
	
}

/*-------------------------------------------------------------------------------------------------------------------------------------------
											U N I N S T A L L A T I O N     F U N C T I O N
-------------------------------------------------------------------------------------------------------------------------------------------*/
register_deactivation_hook( __FILE__, 'sc_uninstall' );
function sc_uninstall(){
	delete_option('sc_form');
	delete_option('sc_settings');
	delete_option('sc_form_styles');
	delete_option('sc_auto_res_mess');
	delete_option('sc_google_lead_vars');
}

?>