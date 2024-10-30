<?php
/**
* Plugin Name: 				CPO Shortcodes
* Description: 				Lets you use over 30 different shortcodes to create incredible, rich-media pages. You can easily insert them using a shortcode generator added to the WordPress visual editor toolbar.
* Version: 					1.5.0
* Author: 					WPChill
* Author URI: 				https://wpchill.com
* Requires: 				4.6 or higher
* License: 					GPLv3 or later
* License URI:       		http://www.gnu.org/licenses/gpl-3.0.html
* Requires PHP: 			5.6
*
* Copyright 2014-2017		Manuel Vicedo 		mvicedo@cpo.es, manuelvicedo@gmail.com			
* Copyright 2017-2020 		MachoThemes 		office@machothemes.com
* Copyright 2020 			WPChill 			heyyy@wpchill.com
*
* Original Plugin URI: 		https://cpothemes.com/plugins/cpo-shortcodes
* Original Author URI: 		https://cpothemes.com/
* Original Author: 			https://profiles.wordpress.org/manuelvicedo/
*
* Note: 
* Manuel Vicedo transferred ownership rights on: : 03/23/2017 12:57:14 PM  when ownership was handed over to MachoThemes
* The MachoThemes ownership period started on: 03/23/2017 12:57:15 PM
* SVN commit proof of ownership transferral: https://plugins.trac.wordpress.org/changeset/1620175/cpo-shortcodes
*
* MachoThemes transferred ownership to WPChill on: 5th of November, 2020.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License, version 3, as
* published by the Free Software Foundation.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Plugin setup
if(!function_exists('ctsc_setup')){
	add_action('plugins_loaded', 'ctsc_setup');
	function ctsc_setup(){
		//Load text domain
		$textdomain = 'ctsc';
		$locale = apply_filters('plugin_locale', get_locale(), $textdomain);
		if(!load_textdomain($textdomain, trailingslashit(WP_LANG_DIR).$textdomain.'/'.$textdomain.'-'.$locale.'.mo')){
			load_plugin_textdomain($textdomain, FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
		}
	}
}


//Add Public scripts
add_action('wp_enqueue_scripts', 'ctsc_scripts_front');
function ctsc_scripts_front( ){
    $scripts_path = plugins_url('scripts/' , __FILE__);
	
	//Register custom scripts for later enqueuing
	wp_register_script('ctsc-core', $scripts_path.'core.js', array('jquery'), false, true);
	wp_register_script('ctsc-waypoints', $scripts_path.'jquery-waypoints.js', array('jquery'));
	wp_register_script('ctsc-toggles', $scripts_path.'shortcodes-toggles.js', array('jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-tabs'));
	wp_register_script('cpotheme-cycle', $scripts_path.'jquery-cycle2.js', array('jquery'), false, true);
}

//Add public stylesheets
add_action('wp_enqueue_scripts', 'ctsc_add_styles');
function ctsc_add_styles(){
	$stylesheets_path = plugins_url('css/' , __FILE__);
	wp_register_style('ctsc-shortcodes', $stylesheets_path.'style.css');
	wp_register_style('ctsc-fontawesome', $stylesheets_path.'fontawesome.css');
}

//Add admin stylesheets
add_action('admin_print_styles', 'ctsc_add_styles_admin');
function ctsc_add_styles_admin(){
	$stylesheets_path = plugins_url('css/' , __FILE__);
	wp_enqueue_style('ctsc-admin', $stylesheets_path.'admin.css');
	wp_enqueue_style('ctsc-fontawesome', $stylesheets_path.'fontawesome.css');
}


//Add localized vars
add_action('admin_enqueue_scripts', 'ctsc_shortcode_tinymce_vars');
function ctsc_shortcode_tinymce_vars($plugin_array) {  
	$core_path = plugins_url('images/' , __FILE__);
	wp_localize_script('jquery-ui-core', 'ctsc_vars', array('prefix' => ctsc_shortcode_prefix()));
}
	

//Add TinyMCE button script
add_filter('mce_external_plugins', 'ctsc_shortcode_tinymce');  
function ctsc_shortcode_tinymce($plugin_array) {  
	$core_path = plugins_url('scripts/' , __FILE__);	
	$plugin_array['ctsc_shortcodes'] = $core_path.'shortcodes-tinymce.js';
	return $plugin_array; 
}


//Add TinyMCE button
add_filter('mce_buttons', 'ctsc_shortcode_tinymce_buttons'); 
function ctsc_shortcode_tinymce_buttons($button_list){
   array_push($button_list, "ctsc_shortcodes_button");
   return $button_list; 
} 	


//Add Settings link in plugin list page
add_filter('plugin_action_links', 'ctsc_action_links', 10, 2);
function ctsc_action_links($links, $file){
	if($file == 'cpo-shortcodes/cpo-shortcodes.php'){
		$new_links = '<a href="'.admin_url('options-general.php?page=ctsc_settings').'">'.__('Settings', 'ctsc').'</a>';
		array_unshift($links, $new_links);
	}
	return $links;
}


//Add upgrade notices for important changes
add_action('in_plugin_update_message-cpo-shortcodes/cpo-shortcodes.php', 'ctsc_upgrade_notice', 10, 2);
function ctsc_upgrade_notice($current, $new){
	if(isset($new->upgrade_notice) && strlen(trim($new->upgrade_notice)) > 0){
		echo '<p style="background-color:#d54e21; padding:10px; color:#f9f9f9; margin-top:10px">';
		echo esc_html($new->upgrade_notice);
		echo '</p>';
	}
}


//Allow shortcodes in text widgets
add_filter('widget_text', 'do_shortcode');

//Add all Shortcode components
$core_path = plugin_dir_path(__FILE__);

//General
require_once($core_path.'includes/general.php');
require_once($core_path.'includes/settings.php');
require_once($core_path.'includes/metadata.php');
//Shortcodes
require_once($core_path.'shortcodes/shortcode-accordion.php');
require_once($core_path.'shortcodes/shortcode-animation.php');
require_once($core_path.'shortcodes/shortcode-button.php');
require_once($core_path.'shortcodes/shortcode-clear.php');
require_once($core_path.'shortcodes/shortcode-column.php');
require_once($core_path.'shortcodes/shortcode-counter.php');
require_once($core_path.'shortcodes/shortcode-definition.php');
require_once($core_path.'shortcodes/shortcode-dropcap.php');
require_once($core_path.'shortcodes/shortcode-feature.php');
require_once($core_path.'shortcodes/shortcode-focus.php');
require_once($core_path.'shortcodes/shortcode-leading.php');
require_once($core_path.'shortcodes/shortcode-list.php');
require_once($core_path.'shortcodes/shortcode-login.php');
require_once($core_path.'shortcodes/shortcode-map.php');
require_once($core_path.'shortcodes/shortcode-message.php');
require_once($core_path.'shortcodes/shortcode-optin.php');
require_once($core_path.'shortcodes/shortcode-posts.php');
require_once($core_path.'shortcodes/shortcode-pricing.php');
require_once($core_path.'shortcodes/shortcode-progress.php');
require_once($core_path.'shortcodes/shortcode-register.php');
require_once($core_path.'shortcodes/shortcode-separator.php');
require_once($core_path.'shortcodes/shortcode-section.php');
require_once($core_path.'shortcodes/shortcode-slideshow.php');
require_once($core_path.'shortcodes/shortcode-spacer.php');
require_once($core_path.'shortcodes/shortcode-tabs.php');
require_once($core_path.'shortcodes/shortcode-team.php');
require_once($core_path.'shortcodes/shortcode-testimonial.php');

register_activation_hook(__FILE__, 'ctsc_settings_defaults');

//Change directory for overriding VC templates
if(function_exists('vc_set_template_dir')){
	vc_set_template_dir($core_path.'templates/');
}