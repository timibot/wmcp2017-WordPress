<?php
// Direct calls to this file are Forbidden when core files are not present
if ( ! function_exists ('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

// HUD Alerts in WP Dashboard
// Reset|Recheck Dismiss Notices is in core-forms.php
function bps_HUD_WP_Dashboard() {
	
	if ( current_user_can('manage_options') ) { 
		bps_check_php_version_error();
		bps_check_safemode();
		bps_check_permalinks_error();
		bps_check_iis_supports_permalinks();
		bps_hud_check_bpsbackup();
		bpsPro_bonus_custom_code_dismiss_notices();
		bps_hud_PhpiniHandlerCheck();
		bps_hud_check_sucuri();
		bps_hud_check_wordpress_firewall2();
		bpsPro_hud_woocommerce_enable_lsm_jtc();
		bps_hud_BPSQSE_old_code_check();
		bpsPro_BBM_htaccess_check();
		bpsPro_hud_speed_boost_cache_code();
		bps_hud_check_autoupdate();
		//bps_hud_check_public_username();
	}
}
add_action('admin_notices', 'bps_HUD_WP_Dashboard');

// Heads Up Display - Check PHP version - top error message new activations/installations
function bps_check_php_version_error() {
	
	if ( version_compare( PHP_VERSION, '5.0.0', '>=' ) ) {
		return;
	}
	
	if ( version_compare( PHP_VERSION, '5.0.0', '<' ) ) {
		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS requires at least PHP5 to function correctly. Your PHP version is: ', 'bulletproof-security').PHP_VERSION.'</font><br><a href="https://www.ait-pro.com/aitpro-blog/1166/bulletproof-security-plugin-support/bulletproof-security-plugin-guide-bps-version-45#bulletproof-security-issues-problems" target="_blank">'.__('BPS Guide - PHP5 Solution', 'bulletproof-security').'</a><br>'.__('The BPS Guide will open in a new browser window. You will not be directed away from your WordPress Dashboard.', 'bulletproof-security').'</div>';
		echo $text;
	}
}

// Heads Up Display w/ Dismiss - Check if PHP Safe Mode is On - 1 is On - 0 is Off
function bps_check_safemode() {
	
	if ( ini_get('safe_mode') == 1 ) {
		
		global $current_user;
		$user_id = $current_user->ID;
		
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}		
		
		if ( ! get_user_meta($user_id, 'bps_ignore_safemode_notice') ) { 
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS has detected that Safe Mode is set to On in your php.ini file.', 'bulletproof-security').'</font><br>'.__('If you see errors that BPS was unable to automatically create the backup folders this is probably the reason why.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_safemode_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bps_safemode_nag_ignore');

function bps_safemode_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bps_safemode_nag_ignore']) && '0' == $_GET['bps_safemode_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_safemode_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - Check if Permalinks are enabled - top error message new activations/installations
function bps_check_permalinks_error() {

	if ( current_user_can('manage_options') && get_option('permalink_structure') == '' ) {

		global $current_user;
		$user_id = $current_user->ID;
		
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}	
	
		if ( ! get_user_meta($user_id, 'bps_ignore_Permalinks_notice') ) { 
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: Custom Permalinks are NOT being used.', 'bulletproof-security').'</font><br>'.__('It is recommended that you use Custom Permalinks: ', 'bulletproof-security').'<a href="https://www.ait-pro.com/aitpro-blog/2304/wordpress-tips-tricks-fixes/permalinks-wordpress-custom-permalinks-wordpress-best-wordpress-permalinks-structure/" target="_blank" title="Link opens in a new Browser window">'.__('How to setup Custom Permalinks', 'bulletproof-security').'</a><br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_Permalinks_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;		
		}
	}
}

add_action('admin_init', 'bps_Permalinks_nag_ignore');

function bps_Permalinks_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bps_Permalinks_nag_ignore']) && '0' == $_GET['bps_Permalinks_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_Permalinks_notice', 'true', true);
	}
}

// Heads Up Display w/Dismiss - Check if Windows IIS server and if IIS7 supports permalink rewriting
function bps_check_iis_supports_permalinks() {
global $wp_rewrite, $is_IIS, $is_iis7, $current_user;
$user_id = $current_user->ID;	

	if ( current_user_can('manage_options') && $is_IIS && ! iis7_supports_permalinks() ) {
	if ( ! get_user_meta($user_id, 'bps_ignore_iis_notice')) {

	if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
		$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
	} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
		$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
	} else {
		$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
	}

		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS has detected that your Server is a Windows IIS Server that does not support htaccess rewriting.', 'bulletproof-security').'</font><br>'.__('Do NOT activate BulletProof Modes unless you know what you are doing.', 'bulletproof-security').'<br>'.__('Your Server Type is: ', 'bulletproof-security').esc_html( $_SERVER['SERVER_SOFTWARE'] ).'<br><a href="http://codex.wordpress.org/Using_Permalinks" target="_blank" title="This link will open in a new browser window.">'.__('WordPress Codex - Using Permalinks - see IIS section', 'bulletproof-security').'</a><br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_iis_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';		
		echo $text;
	}
	}
}

add_action('admin_init', 'bps_iis_nag_ignore');

function bps_iis_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_iis_nag_ignore'] ) && '0' == $_GET['bps_iis_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_iis_notice', 'true', true);
	}
}

// Heads Up Display - check if /bps-backup and /bps-backup/master-backups folders exist
function bps_hud_check_bpsbackup() {

	$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );	

	if ( ! is_dir( WP_CONTENT_DIR . '/bps-backup' ) ) {
		$text = '<div style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:0px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS was unable to automatically create the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup folder.', 'bulletproof-security').'</font><br>'.__('You will need to create the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup folder manually via FTP. The folder permissions for the bps-backup folder need to be set to 755 in order to successfully perform permanent online backups.', 'bulletproof-security').'<br>'.__('To remove this message permanently click ', 'bulletproof-security').'<a href="https://www.ait-pro.com/aitpro-blog/2566/bulletproof-security-plugin-support/bulletproof-security-error-messages" target="_blank">'.__('here.', 'bulletproof-security').'</a></div>';
		echo $text;
	}
	
	if ( ! is_dir( WP_CONTENT_DIR . '/bps-backup/master-backups' ) ) {
		$text = '<div style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:0px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS was unable to automatically create the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup/master-backups folder.', 'bulletproof-security').'</font><br>'.__('You will need to create the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup/master-backups folder manually via FTP. The folder permissions for the master-backups folder need to be set to 755 in order to successfully perform permanent online backups.', 'bulletproof-security').'<br>'.__('To remove this message permanently click ', 'bulletproof-security').'<a href="https://www.ait-pro.com/aitpro-blog/2566/bulletproof-security-plugin-support/bulletproof-security-error-messages" target="_blank">'.__('here.', 'bulletproof-security').'</a></div>';
		echo $text;
	}
}

// Heads Up Display - Bonus Custom Code with Dismiss Notices
function bpsPro_bonus_custom_code_dismiss_notices() {
global $current_user;
$user_id = $current_user->ID;	
	
	if ( current_user_can('manage_options') ) { 
		$text = '';
	
	// Setup Wizard DB option is saved by running the Setup Wizard, on BPS Upgrades & manual BPS setup
	if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
		return;
	}

	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		return;
	}

	if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
		$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
	} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
		$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
	} else {
		$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
	}
		
	if ( get_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice') && ! get_user_meta($user_id, 'bps_post_request_attack_notice') ) {

		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Bonus Custom Code:', 'bulletproof-security').'</font><br>'.__('Click the links below to get Bonus Custom Code or click the Dismiss Notice links or click this ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_bonus_code_dismiss_all_nag_ignore=0&bps_post_request_attack_nag_ignore=0'.'" style="">'.__('Dismiss All Notices', 'bulletproof-security').'</a></span>'.__(' link. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br>';


		$text .= '<div id="BC5" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/post-request-protection-post-attack-protection-post-request-blocker/" title="Protects against POST Request Attacks" target="_blank">'.__('POST Request Attack Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_post_request_attack_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		echo $text;
		echo '</div>';
	}		
	
	if ( ! get_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice') ) {

	if ( ! get_user_meta($user_id, 'bps_brute_force_login_protection_notice') || ! get_user_meta($user_id, 'bps_speed_boost_cache_notice') || ! get_user_meta($user_id, 'bps_author_enumeration_notice') || ! get_user_meta($user_id, 'bps_xmlrpc_ddos_notice') || ! get_user_meta($user_id, 'bps_post_request_attack_notice') || ! get_user_meta($user_id, 'bps_sniff_driveby_notice') || ! get_user_meta($user_id, 'bps_iframe_clickjack_notice') ) { 		
		
		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Bonus Custom Code:', 'bulletproof-security').'</font><br>'.__('Click the links below to get Bonus Custom Code or click the Dismiss Notice links or click this ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_bonus_code_dismiss_all_nag_ignore=0&bps_post_request_attack_nag_ignore=0'.'" style="">'.__('Dismiss All Notices', 'bulletproof-security').'</a></span>'.__(' link. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br>';
		
	}

	if ( ! get_user_meta($user_id, 'bps_brute_force_login_protection_notice') ) { 	
		
		$text .= '<div id="BC1" style="">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/protect-login-page-from-brute-force-login-attacks/" title="Additional Protection for the Login Page from Brute Force Login Attacks" target="_blank">'.__('Brute Force Login Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_brute_force_login_protection_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}
		
	if ( ! get_user_meta($user_id, 'bps_speed_boost_cache_notice') ) { 	

		$text .= '<div id="BC2" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-caching-code-speed-boost-cache-code/" title="Speed up your website performance with Browser Cache code" target="_blank">'.__('Speed Boost Cache Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_speed_boost_cache_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}
		
	if ( ! get_user_meta($user_id, 'bps_author_enumeration_notice') ) { 

		$text .= '<div id="BC3" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/wordpress-author-enumeration-bot-probe-protection-author-id-user-id/" title="Protects against hacker and spammer bots finding Author names & User names on your website" target="_blank">'.__('Author Enumeration BOT Probe Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_author_enumeration_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}
		
	if ( ! get_user_meta($user_id, 'bps_xmlrpc_ddos_notice') ) { 		

		$text .= '<div id="BC4" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/wordpress-xml-rpc-ddos-protection-protect-xmlrpc-php-block-xmlrpc-php-forbid-xmlrpc-php/" title="Protects against the XML Quadratic Blowup Attack, DDoS Attacks as well as other various XML-RPC exploits" target="_blank">'.__('XML-RPC DDoS Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_xmlrpc_ddos_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}
	
	/*
	if ( ! get_user_meta($user_id, 'bps_referer_spam_notice') ) {

		$text .= '<div id="BC5" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/block-referer-spammers-semalt-kambasoft-ranksonic-buttons-for-website/" title="Protects against Referer Spamming and Phishing" target="_blank">'.__('Referer Spam|Phishing Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_referer_spam_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}
	*/
	
	if ( ! get_user_meta($user_id, 'bps_post_request_attack_notice') ) {

		$text .= '<div id="BC5" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/post-request-protection-post-attack-protection-post-request-blocker/" title="Protects against POST Request Attacks" target="_blank">'.__('POST Request Attack Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_post_request_attack_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}

	if ( ! get_user_meta($user_id, 'bps_sniff_driveby_notice') ) {		
		
		$text .= '<div id="BC6" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/mime-sniffing-data-sniffing-content-sniffing-drive-by-download-attack-protection/" title="Protects against Mime Sniffing, Data Sniffing, Content Sniffing and Drive-by Download Attacks" target="_blank">'.__('Mime Sniffing|Drive-by Download Attack Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_sniff_driveby_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
	}

	if ( ! get_user_meta($user_id, 'bps_iframe_clickjack_notice') ) {		
		
		$text .= '<div id="BC7" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/rssing-com-good-or-bad/" title="Protects against external websites displaying your website pages or Feeds in iFrames and Clickjacking Protection" target="_blank">'.__('External iFrame|Clickjacking Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_iframe_clickjack_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
	}

		echo $text;
		
		if ( ! get_user_meta($user_id, 'bps_brute_force_login_protection_notice') || ! get_user_meta($user_id, 'bps_speed_boost_cache_notice') || ! get_user_meta($user_id, 'bps_author_enumeration_notice') || ! get_user_meta($user_id, 'bps_xmlrpc_ddos_notice') || ! get_user_meta($user_id, 'bps_post_request_attack_notice') || ! get_user_meta($user_id, 'bps_sniff_driveby_notice') || ! get_user_meta($user_id, 'bps_iframe_clickjack_notice') ) { 	
		echo '</div>';
		}
		}
	}
}

add_action('admin_init', 'bpsPro_bonus_custom_code_nag_ignores');

function bpsPro_bonus_custom_code_nag_ignores() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bps_bonus_code_dismiss_all_nag_ignore']) && '0' == $_GET['bps_bonus_code_dismiss_all_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice', 'true', true);
	}

	if ( isset($_GET['bps_brute_force_login_protection_nag_ignore']) && '0' == $_GET['bps_brute_force_login_protection_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_brute_force_login_protection_notice', 'true', true);
	}

	if ( isset($_GET['bps_speed_boost_cache_nag_ignore']) && '0' == $_GET['bps_speed_boost_cache_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_speed_boost_cache_notice', 'true', true);
	}

	if ( isset($_GET['bps_author_enumeration_nag_ignore']) && '0' == $_GET['bps_author_enumeration_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_author_enumeration_notice', 'true', true);
	}

	if ( isset($_GET['bps_xmlrpc_ddos_nag_ignore']) && '0' == $_GET['bps_xmlrpc_ddos_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_xmlrpc_ddos_notice', 'true', true);
	}

	/*
	if ( isset($_GET['bps_referer_spam_nag_ignore']) && '0' == $_GET['bps_referer_spam_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_referer_spam_notice', 'true', true);
	}
	*/
	
	if ( isset($_GET['bps_post_request_attack_nag_ignore']) && '0' == $_GET['bps_post_request_attack_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_post_request_attack_notice', 'true', true);
	}

	if ( isset($_GET['bps_sniff_driveby_nag_ignore']) && '0' == $_GET['bps_sniff_driveby_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_sniff_driveby_notice', 'true', true);
	}

	if ( isset($_GET['bps_iframe_clickjack_nag_ignore']) && '0' == $_GET['bps_iframe_clickjack_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_iframe_clickjack_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - Check if php.ini handler code exists in root .htaccess file, but not in Custom Code
// .53.6: Additional conditional check added for Wordfence WAF Firewall mess.
function bps_hud_PhpiniHandlerCheck() {
global $current_user;
$user_id = $current_user->ID;
$file = ABSPATH . '.htaccess';	

	if ( esc_html($_SERVER['QUERY_STRING']) == 'page=bulletproof-security/admin/wizard/wizard.php' && ! get_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice') ) {
	
		if ( file_exists($file) ) {		

			$file_contents = @file_get_contents($file);
			$CustomCodeoptions = get_option('bulletproof_security_options_customcode');
			
			preg_match_all('/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $file_contents, $matches);
			preg_match_all('/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $CustomCodeoptions['bps_customcode_one'], $DBmatches);

			if ( $matches[0] && ! $DBmatches[0] ) {
			
			preg_match_all('/(([#\s]{1,}|)(AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application).*\s*){1,}/', $file_contents, $h_matches );

			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}			
			
			if ( stripos( $file_contents, "Wordfence WAF" ) ) {

				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: Wordfence PHP/php.ini handler htaccess code detected', 'bulletproof-security').'</font><br>'.__('Wordfence PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/wordfence-firewall-wp-contentwflogsconfig-php-file-quarantined/#wordfence-php-handler" target="_blank" title="Wordfence PHP Handler Fix">'.__('Click Here', 'bulletproof-security').'</a>'.__(' for the steps to fix this Wordfence problem before running the Setup Wizard.', 'bulletproof-security').'<br><font color="#fb0101">'.__('CAUTION: ', 'bulletproof-security').'</font>'.__('Using the Wordfence WAF Firewall may cause serious/critical problems for your website and BPS.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_PhpiniHandler_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
				echo $text;

			} else {
				
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: PHP/php.ini handler htaccess code check', 'bulletproof-security').'</font><br>'.__('PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code.', 'bulletproof-security').'<br>'.__('To automatically fix this click here: ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'">'.esc_attr__('Setup Wizard Pre-Installation Checks', 'bulletproof-security').'</a><br>'.__('The Setup Wizard Pre-Installation Checks feature will automatically fix this just by visiting the Setup Wizard page.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_PhpiniHandler_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
				echo $text;			
				echo '<pre style="margin:5px 0px 0px 5px;">';
				echo '# PHP/php.ini handler htaccess code<br>';				
				
				foreach ( $h_matches[0] as $Key => $Value ) {
					echo $Value;
				}
				echo '</pre>';
			}
			}
		}
	}

	if ( esc_html($_SERVER['QUERY_STRING']) != 'page=bulletproof-security/admin/wizard/wizard.php' && ! get_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice') ) {

		if ( file_exists($file) ) {		

			$file_contents = @file_get_contents($file);
			$CustomCodeoptions = get_option('bulletproof_security_options_customcode');
			
			preg_match_all('/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $file_contents, $matches);
			preg_match_all('/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $CustomCodeoptions['bps_customcode_one'], $DBmatches);
		
			if ( $matches[0] && ! $DBmatches[0] ) {
			
			preg_match_all('/(([#\s]{1,}|)(AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application).*\s*){1,}/', $file_contents, $h_matches );

			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}		
			
				if ( stripos( $file_contents, "Wordfence WAF" ) ) {
					
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: Wordfence PHP/php.ini handler htaccess code detected', 'bulletproof-security').'</font><br>'.__('Wordfence PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/wordfence-firewall-wp-contentwflogsconfig-php-file-quarantined/#wordfence-php-handler" target="_blank" title="Wordfence PHP Handler Fix">'.__('Click Here', 'bulletproof-security').'</a>'.__(' for the steps to fix this Wordfence problem.', 'bulletproof-security').'<br><font color="#fb0101">'.__('CAUTION: ', 'bulletproof-security').'</font>'.__('Using the Wordfence WAF Firewall may cause serious/critical problems for your website and BPS.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_PhpiniHandler_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
					echo $text;				
				
				} else {

					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: PHP/php.ini handler htaccess code check', 'bulletproof-security').'</font><br>'.__('PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code.', 'bulletproof-security').'<br>'.__('To automatically fix this click here: ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'">'.esc_attr__('Setup Wizard Pre-Installation Checks', 'bulletproof-security').'</a><br>'.__('The Setup Wizard Pre-Installation Checks feature will automatically fix this just by visiting the Setup Wizard page.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_PhpiniHandler_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
					echo $text;			
					echo '<pre style="margin:5px 0px 0px 5px;">';
					echo '# PHP/php.ini handler htaccess code<br>';				
				
					foreach ( $h_matches[0] as $Key => $Value ) {
						echo $Value;
					}
					echo '</pre>';
				}
			}
		}
	}
}

add_action('admin_init', 'bps_PhpiniHandler_nag_ignore');

function bps_PhpiniHandler_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_PhpiniHandler_nag_ignore'] ) && '0' == $_GET['bps_PhpiniHandler_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - Sucuri Restrict wp-content access Hardening Option wp-content .htaccess file problem - breaks BPS and lots of other stuff
// Unfortunately the limited whitelisting options provided by Sucuri in their settings don't provide any workable solutions for BPS.
function bps_hud_check_sucuri() {
$filename = WP_CONTENT_DIR . '/.htaccess';
$sucuri = 'sucuri-scanner/sucuri.php';
$sucuri_active = in_array( $sucuri, apply_filters('active_plugins', get_option('active_plugins') ) );

	if ( $sucuri_active == 1 && ! file_exists($filename) ) {
		return;	
	}
	
	if ( function_exists('sucuriscan_harden_wpcontent') ) {
	
		if ( $sucuri_active == 1 || is_plugin_active_for_network( $sucuri ) ) {

			if ( file_exists($filename) && preg_match( '/WP-content\sdirectory\sproperly\shardened/', sucuriscan_harden_wpcontent(), $matches ) ) { 

				global $current_user;
				$user_id = $current_user->ID;

				if ( ! get_user_meta($user_id, 'bps_ignore_sucuri_notice') ) {
			
				if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
					$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
				} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
					$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
				} else {
					$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
				}		
			
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('Sucuri Restrict wp-content access Hardening Option problem detected', 'bulletproof-security').'</font><br>'.__('Using the Sucuri Restrict wp-content access Hardening Option breaks BPS Security Logging, Plugin Firewall, Uploads Anti-Exploit Guard & probably other things in BPS and other plugins as well.', 'bulletproof-security').'<br>'.__('To fix this problem click this link: ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=sucuriscan_hardening#hardening' ).'">'.__('Sucuri Hardening Options', 'bulletproof-security').'</a>'.__(' and click the Sucuri Restrict wp-content access Revert hardening button.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_sucuri_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
				echo $text;
				}
			}
		}
	}
}

add_action('admin_init', 'bps_sucuri_nag_ignore');

function bps_sucuri_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_sucuri_nag_ignore'] ) && '0' == $_GET['bps_sucuri_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_sucuri_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - WordPress Firewall 2 plugin - breaks BPS and lots of other stuff
function bps_hud_check_wordpress_firewall2() {
$firewall2 = 'wordpress-firewall-2/wordpress-firewall-2.php';
$firewall2_active = in_array( $firewall2, apply_filters('active_plugins', get_option('active_plugins')));

	if ( $firewall2_active != 1 && ! is_plugin_active_for_network( $firewall2 ) ) {
		return;	
	}
	
	if ( $firewall2_active == 1 || is_plugin_active_for_network( $firewall2 ) ) {
	
		global $current_user;
		$user_id = $current_user->ID;			
		
		if ( ! get_user_meta($user_id, 'bps_ignore_wpfirewall2_notice') ) {
			
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}			
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('The WordPress Firewall 2 plugin is installed and activated', 'bulletproof-security').'</font><br>'.__('It is recommended that you delete the WordPress Firewall 2 plugin.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/wordpress-firewall-2-plugin-unable-to-save-custom-code/" target="_blank" title="Link opens in a new Browser window">'.__('Click Here', 'bulletproof-security').'</a>'.__(' for more information.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_wpfirewall2_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bps_wpfirewall2_nag_ignore');

function bps_wpfirewall2_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_wpfirewall2_nag_ignore'] ) && '0' == $_GET['bps_wpfirewall2_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_wpfirewall2_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - WooCommerce LSM enable options
// Notes: This Notice needs to be displayed to everyone who already currently have WooCommerce installed until they Dismiss this Notice.
// The reason for that is the BPS upgrade will automatically enable LSM for the WooCommerce custom login page.
// If they install WooCommerce at a later time then this Notice is displayed.
// Exception: This Notice should not be displayed for new BPS installations before or after the Setup Wizard has been run.
function bpsPro_hud_woocommerce_enable_lsm_jtc() {

	$lsm_options = get_option('bulletproof_security_options_login_security');
	$sw_woo_options = get_option('bulletproof_security_options_setup_wizard_woo');

	if ( ! $lsm_options['bps_enable_lsm_woocommerce'] ) {
		return;
	}

	if ( $sw_woo_options['bps_wizard_woo'] == '1' ) {
		return;
	}

	$woocommerce = 'woocommerce/woocommerce.php';
	$woocommerce_active = in_array( $woocommerce, apply_filters('active_plugins', get_option('active_plugins')));

	if ( $woocommerce_active == 1 || is_plugin_active_for_network( $woocommerce ) ) {

		global $current_user;
		$user_id = $current_user->ID;

		if ( ! get_user_meta($user_id, 'bps_ignore_woocommerce_lsm_jtc_notice') ) { 
			
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}			
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS WooCommerce Options Notice: Enable Login Security for WooCommerce', 'bulletproof-security').'</font><br>'.__('BPS Login Security & Monitoring (LSM) can be enabled/disabled for the WooCommerce custom login page by checking or unchecking the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php' ).'">'.__('Enable Login Security for WooCommerce', 'bulletproof-security').'</a>'.__(' checkbox option setting. The LSM WooCommerce option is automatically enabled during the BPS upgrade if you already had WooCommerce installed before upgrading BPS. If you just installed WooCommerce you can either run the Setup Wizard to enable the LSM WooCommerce option or you can enable this option manually by going to the BPS LSM plugin page if you want to enable LSM for WooCommerce.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_woo_jtc_lsm_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bps_woo_jtc_lsm_nag_ignore');

function bps_woo_jtc_lsm_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bps_woo_jtc_lsm_nag_ignore']) && '0' == $_GET['bps_woo_jtc_lsm_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_woocommerce_lsm_jtc_notice', 'true', true);
	}
}

// Check for older BPS Query String Exploits code saved to BPS Custom Code
function bps_hud_BPSQSE_old_code_check() {
$CustomCodeoptions = get_option('bulletproof_security_options_customcode');	

	if ( $CustomCodeoptions['bps_customcode_bpsqse'] == '' ) {
		return;
	}
	
	$subject = $CustomCodeoptions['bps_customcode_bpsqse'];	
	$pattern1 = '/RewriteCond\s%{QUERY_STRING}\s\(\\\.\/\|\\\.\.\/\|\\\.\.\.\/\)\+\(motd\|etc\|bin\)\s\[NC,OR\]/';
	$pattern2 = '/RewriteCond\s%\{THE_REQUEST\}\s(.*)\?(.*)\sHTTP\/\s\[NC,OR\]\s*RewriteCond\s%\{THE_REQUEST\}\s(.*)\*(.*)\sHTTP\/\s\[NC,OR\]/';
	$pattern3 = '/RewriteCond\s%\{THE_REQUEST\}\s.*\?\+\(%20\{1,\}.*\s*RewriteCond\s%\{THE_REQUEST\}\s.*\+\(.*\*\|%2a.*\s\[NC,OR\]/';

	if ( $CustomCodeoptions['bps_customcode_bpsqse'] != '' && preg_match($pattern1, $subject, $matches) || preg_match($pattern2, $subject, $matches) || preg_match($pattern3, $subject, $matches) ) {

		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Notice: BPS Query String Exploits Code Changes', 'bulletproof-security').'</font><br>'.__('Older BPS Query String Exploits code was found in BPS Custom Code. Several Query String Exploits rules were changed/added/modified in the root .htaccess file in BPS .49.6, .50.2 & .50.3.', 'bulletproof-security').'<br>'.__('Copy the new Query String Exploits section of code from your root .htaccess file and paste it into this BPS Custom Code text box: CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS and click the Save Root Custom Code button.', 'bulletproof-security').'<br>'.__('This Notice will go away once you have copied the new Query String Exploits code to BPS Custom Code and clicked the Save Root Custom Code button.', 'bulletproof-security').'</div>';
		echo $text;
	}
}

// Heads Up Display - Check if the /bps-backup/.htaccess file exists
function bpsPro_BBM_htaccess_check() {

	// New BPS installation - do not check or display error
	if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
		return;
	}

	$options = get_option('bulletproof_security_options_monitor');
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
	$filename = WP_CONTENT_DIR . '/bps-backup/.htaccess';
	$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );

	if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] != 'disabled' && @$_POST['Submit-BBM-Activate'] != true ) {
		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('BPS Alert! A BPS htaccess file was NOT found in the BPS Backup folder: ', 'bulletproof-security').'/'.$bps_wpcontent_dir.'/bps-backup/</font><br>'.__('Go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ).'">'.esc_attr__('Security Modes page', 'bulletproof-security').'</a>'.__(' and click the BPS Backup Folder BulletProof Mode Activate button.', 'bulletproof-security').'</div>';
		echo $text;
	}
}

## Checks for older BPS Speed Boost Cache code saved in BPS Custom Code
## 2.0: Checks for redundant Browser caching code & the BPS NOCHECK Marker in BPS Custom Code
function bpsPro_hud_speed_boost_cache_code() {
	
	$CC_options = get_option('bulletproof_security_options_customcode');
	$bps_customcode_one = htmlspecialchars_decode( $CC_options['bps_customcode_one'], ENT_QUOTES );	
	
	if ( $CC_options['bps_customcode_one'] == '' || strpos( $bps_customcode_one, "BPS NOCHECK" ) ) {
		return;
	}	
	
	if ( @$_POST['bps_customcode_submit'] == true ) {
		return;
	}

	global $current_user;
	$user_id = $current_user->ID;	
	
	$pattern1 = '/BEGIN\sWEBSITE\sSPEED\sBOOST/';
	$pattern2 = '/AddOutputFilterByType\sDEFLATE\stext\/plain\s*AddOutputFilterByType\sDEFLATE\stext\/html\s*AddOutputFilterByType\sDEFLATE\stext\/xml\s*AddOutputFilterByType\sDEFLATE\stext\/css\s*AddOutputFilterByType\sDEFLATE\sapplication\/xml\s*AddOutputFilterByType\sDEFLATE\sapplication\/xhtml\+xml\s*AddOutputFilterByType\sDEFLATE\sapplication\/rss\+xml\s*AddOutputFilterByType\sDEFLATE\sapplication\/javascript\s*AddOutputFilterByType\sDEFLATE\sapplication\/x-javascript\s*AddOutputFilterByType\sDEFLATE\sapplication\/x-httpd-php\s*AddOutputFilterByType\sDEFLATE\sapplication\/x-httpd-fastphp\s*AddOutputFilterByType\sDEFLATE\simage\/svg\+xml/';

	if ( ! get_user_meta($user_id, 'bpsPro_ignore_speed_boost_notice') ) { 
		
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}		

		if ( preg_match( $pattern1, htmlspecialchars_decode( $CC_options['bps_customcode_one'], ENT_QUOTES ), $matches1 ) && preg_match( $pattern2, htmlspecialchars_decode( $CC_options['bps_customcode_one'], ENT_QUOTES ), $matches2 ) ) {

			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('New Improved BPS Speed Boost Cache Code', 'bulletproof-security').'</font><br>'.__('Older BPS Speed Boost Cache Code was found saved in this BPS Custom Code text box: CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE', 'bulletproof-security').'.<br>'.__('Newer improved BPS Speed Boost Cache Code has been created, which should improve website load speed performance even more.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/htaccess-caching-code-speed-boost-cache-code/" target="_blank" title="BPS Speed Boost Cache Code">'.__('Get The New Improved BPS Speed Boost Cache Code', 'bulletproof-security').'</a>'.__('. To dismiss this Notice click the Dismiss Notice button below.', 'bulletproof-security').'<br>'.__('To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_hud_speed_boost_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}

		if ( strpos( $bps_customcode_one, "WEBSITE SPEED BOOST" ) ) {
			if ( strpos( $bps_customcode_one, "WPSuperCache" ) || strpos( $bps_customcode_one, "W3TC Browser Cache" ) || strpos( $bps_customcode_one, "Comet Cache" ) || strpos( $bps_customcode_one, "GzipWpFastestCache" ) || strpos( $bps_customcode_one, "LBCWpFastestCache" ) || strpos( $bps_customcode_one, "WP Rocket" ) ) {
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS Speed Boost Cache Custom Code Notice', 'bulletproof-security').'</font><br>'.__('BPS Speed Boost Cache Code was found in this BPS Custom Code text box: CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE', 'bulletproof-security').'<br>'.__('and another caching plugin\'s Marker text was also found in this BPS Custom Code text box.', 'bulletproof-security').'<br>'.__('Click this link: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/bps-speed-boost-cache-custom-code-notice/" target="_blank" title="BPS SBC Custom Code Forum Topic">'.__('BPS Speed Boost Cache Custom Code Notice Forum Topic', 'bulletproof-security').'</a>'.__(' for help information on what this Notice means and what to do next.', 'bulletproof-security').'</div>';
			echo $text;
			}
		}
	}
}

add_action('admin_init', 'bpsPro_hud_speed_boost_nag_ignore');

function bpsPro_hud_speed_boost_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_hud_speed_boost_nag_ignore']) && '0' == $_GET['bpsPro_hud_speed_boost_nag_ignore'] ) {
		add_user_meta($user_id, 'bpsPro_ignore_speed_boost_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - BPS Plugin AutoUpdate
// Notes: Only Display the AutoUpdate Dimiss Notice if the Bonus Custom Code Dismiss Notice is not being displayed (display after the BCC Dimiss Notice).
// There are 3 common scenarios: only the dismiss all notice link was clicked, some of the individual dismiss notices were clicked and 
// the dismiss all notice link was clicked and only all individual dimiss notice links were clicked, but not the dismiss all notice link.
// which leaves 2 possible conditions: either the dismiss all notice value == true or all other dismiss notice values == true.
// 1.2: New BPS MU Tools file created.
function bps_hud_check_autoupdate() {
	
	$MUTools_Options = get_option('bulletproof_security_options_MU_tools_free');
	
	if ( $MUTools_Options['bps_mu_tools_enable_disable_autoupdate'] == 'disable' ) {
	
		global $current_user;
		$user_id = $current_user->ID;

		$bcc_dismiss_all = get_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice');
		$bcc1 = get_user_meta($user_id, 'bps_brute_force_login_protection_notice');
		$bcc2 = get_user_meta($user_id, 'bps_speed_boost_cache_notice');
		$bcc3 = get_user_meta($user_id, 'bps_author_enumeration_notice');
		$bcc4 = get_user_meta($user_id, 'bps_xmlrpc_ddos_notice');
		$bcc5 = get_user_meta($user_id, 'bps_post_request_attack_notice');
		$bcc6 = get_user_meta($user_id, 'bps_sniff_driveby_notice');
		$bcc7 = get_user_meta($user_id, 'bps_iframe_clickjack_notice');

		if ( true == $bcc_dismiss_all || true == $bcc1 && true == $bcc2 && true == $bcc3 && true == $bcc4 && true == $bcc5 && true == $bcc6 && true == $bcc7 ) {

			if ( ! get_user_meta($user_id, 'bps_ignore_autoupdate_notice') ) {
			
			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}		
			
			if ( is_multisite() ) {
				$bps_mu_link = '<a href="'.network_admin_url( 'plugins.php?plugin_status=mustuse' ).'">'.esc_attr__('BPS Plugin AutoUpdates', 'bulletproof-security').'</a>';
			} else {
				$bps_mu_link = '<a href="'.admin_url( 'plugins.php?plugin_status=mustuse' ).'">'.esc_attr__('BPS Plugin AutoUpdates', 'bulletproof-security').'</a>';
			}

			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS Plugin Automatic Update Notice', 'bulletproof-security').'</font><br>'.__('Would you like to have BPS plugin updates installed automatically? Click this link: ', 'bulletproof-security').$bps_mu_link.__(' and click the BPS MU Tools Enable BPS Plugin AutoUpdates link.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_autoupdate_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
			}
		}
	}
}

add_action('admin_init', 'bps_autoupdate_nag_ignore');

function bps_autoupdate_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_autoupdate_nag_ignore'] ) && '0' == $_GET['bps_autoupdate_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_autoupdate_notice', 'true', true);
	}
}

?>