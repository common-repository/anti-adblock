<?php
/*
Plugin Name: Anti-AdBlock
Plugin URI: http://omninoggin.com/projects/wordpress-plugins/anti-adblock-wordpress-plugin/
Description: This plugin displays a notification message to visitors with AdBlock on, humbly asking them to turn it off.
Version: 0.2.1
Author: Thaya Kareeson
Author URI: http://omninoggin.com
*/

/*
Copyright 2009 Thaya Kareeson (email : thaya.kareeson@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class AntiAdBlock {

  var $name = 'anti_adblock'; 
  var $homepage = 'http://omninoggin.com/projects/wordpress-plugins/anti-adblock-wordpress-plugin/';
  var $displayed = false;

  function get_default_options() {
    return array(
      'can_close'       => true,
      'hide_content'    => false,
      'image'           => $this->get_plugin_url() . '/images/disable_adblock.png',
      'message'         => __('<h3>Doh! It appears that you are using AdBlock software.</h3><p>We noticed that you\'ve been here <strong>###visit_count###</strong> times.  We are glad that you find our content useful, but we are also sad that you are blocking advertisements on this site.</p><p>Without advertisement revenue we will not be able to continue to provide quality content.  If you enjoy our content, please consider supporting us by any of the following ways:<ul><li>Disable your AdBlock software for this site (see image on the right).</li><li>Make a small (or large) donation.</li><li>Link to us or write about us on your blog.</li></ul>Thank you in advance for your support and understanding!</p><br/>', $this->name),
      'message_css'     => '
background-color:#fff;
border:1px solid #ccc;
color:#333;
height:auto;
left:25%;
padding:20px;
position:fixed;
top:100px;
width:50%;
z-index:10;',
      'message_bar'     => 'It appears that you are using advertisement blocking software.  <a id="show-adblock-message" href="#">See why this is important.</a>',
      'message_bar_css' => '
background:#ffffe1;
border:1px solid #aca899;
font-size:12px;
font-weight:bold;
left:0px;
line-height:24px;
position:fixed;
text-align:center;
top:0px;
width:100%;
z-index:100;
',
      'max_nag'         => 1,
      'show_link'       => true,
      'use_message_bar' => false,
      'visits_till_nag' => 10
    );
  }

  function activate() {
    $this->upgrade_options();
    $this->set_nonce('nonce_message');
    $this->set_nonce('nonce_message_bar');
    $this->set_nonce('nonce_banner');
  }

  function set_nonce($seed) {
    $aab_options = get_option($this->name);
    $aab_options[$seed] = 'a' . substr(md5(wp_create_nonce($name . $seed)), rand(0, 15), rand(5, 16));
    update_option($this->name, $aab_options);
  }

  function display() {
    if ( !is_feed() && !$this->displayed ) {
      $aab_options = get_option($this->name);
      // strip end-of-line characters
      $message_style = str_replace("\n", '', $aab_options['message_css']);
      $message_style = str_replace("\r", '', $message_style);
      $message_bar_style = str_replace("\n", '', $aab_options['message_bar_css']);
      $message_bar_style = str_replace("\r", '', $message_bar_style);
      printf('<div id="%s" style="%sdisplay:none;"></div>', $aab_options['nonce_message'], $message_style);
      printf('<div id="%s" style="%sdisplay:none;"><div style="float:right;margin:0 10px 0 0"><a id="close-adblock-bar" href="#" rel="nofollow">X</a></div></div>', $aab_options['nonce_message_bar'], $message_bar_style);
      $this->load_scripts();
      $this->displayed = true;
    }
  }

  function display_bait() {
    if ( !is_feed() && $this->displayed ) {
      $aab_options = get_option($this->name);
      printf('<img id="%s" src="%s/images/ad/a%s.gif" width="1" height="1" alt="">', $aab_options['nonce_banner'], site_url(), $aab_options['nonce_message']);
    }
  }

  function redirect_to_image() {
    $aab_options = get_option($this->name);
    $req_uri = $_SERVER['REQUEST_URI'];
    if ( strrpos($req_uri, $aab_options['nonce_message']) ) {
      Header( "HTTP/1.1 301 Moved Permanently" );
      Header( sprintf("Location: %s/images/ad/banner.gif", $this->get_plugin_url()) );
    }
  }

  function queue_jquery() {
    wp_enqueue_script('jquery');
  }

  function load_scripts() {
    $aab_options = get_option($this->name);

    $expire_days = 365;
    
    if ( $aab_options['image'] && $aab_options['image'] != '' )
      $image_html = sprintf('<img src=\'%s\' alt=\'Adblock\' class=\'adblock_message_image\'/>', $aab_options['image']);
    else
      $image_html = '';

    if ( $aab_options['can_close'] ) {
      $close_html = sprintf('<br/><p align=\'center\'><a id=\'close-adblock-message\' href=\'#\' rel=\'nofollow\'>%s</a><br/>%s%s%s<p>',
        __('Click here to close', $this->name),
        __('You will no longer receive this message after ', $this->name),
        $aab_options['max_nag'],
        __(' visit(s) (cookies required).', $this->name)
      );
    }
    else
      $close_html = '';

    if ( $aab_options['use_message_bar'] ) {
      $use_message_bar_html = "true";
    }
    else {
      $use_message_bar_html = "false";
    }

    if ( $aab_options['show_link'] )
      $link_html = sprintf('<p class=\'aab_powered_by\'><a href=\'%s\' alt=\'%s\'>%s</a></p>',
        $this->homepage,
        __('Powered by Anti-AdBlock', $this->name),
        __('Powered by Anti-AdBlock', $this->name)
      );
    else
      $link_html = '';

    // generate JS
    printf('
      <script type="text/javascript">
        /* <![CDATA[ */
        function aab_get_cookie(c_name) {
          if (document.cookie.length>0) {
            c_start=document.cookie.indexOf(c_name + "=");
            if (c_start!=-1) {
              c_start=c_start + c_name.length+1;
              c_end=document.cookie.indexOf(";",c_start);
              if (c_end==-1) c_end=document.cookie.length;
              return unescape(document.cookie.substring(c_start,c_end));
            }
          }
          return "";
        }
        
        function aab_set_cookie(c_name,value,expiredays) {
          var exdate = new Date();
          exdate.setDate(exdate.getDate()+expiredays);
          document.cookie=c_name+ "=" +escape(value)+";path="+"/"+
          ((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
        }

        // run when document is ready
        jQuery(document).ready(function($){
          aab_c_view_count = aab_get_cookie("aab_view_count");
          if(aab_c_view_count == ""){
            // create cookie to track view count
            aab_set_cookie("aab_view_count", 1, %s);
          }
          else {
            // increment view count
            aab_set_cookie("aab_view_count", ++aab_c_view_count, %s);
          }

          aab_c_view_count = parseInt(aab_c_view_count);

          // if view count > visitors-till-nag and <= (vistors-till-nag + max-nag)
          if(aab_c_view_count > %s && aab_c_view_count <= (%s + %s)){
            // check if adblock is on, second part is for Anti-AdBlock
            if($("#%s").css("display") == "none" || $("#%s").css("-moz-binding").search("chrome://") != -1){
              $("#%s").html("%s%s%s%s".replace(/###visit_count###/, aab_c_view_count)).hide();
              if(%s){
                $("#%s").append("%s").hide().fadeIn();
                // bind show action to show message link
                $("#show-adblock-message").click(function(){
                  $("#%s").fadeIn();
                  $("#%s").fadeOut();
                });
                // bind close action to adblock bar
                $("#close-adblock-bar").click(function(){
                  $("#%s").fadeOut();
                });
              }
              else{
                $("#%s").fadeIn();
              }
              // bind close action to adblock message
              $("#close-adblock-message").click(function(){
                $("#%s").fadeOut();
              });
            }
          }

        });
        /* ]]> */
      </script>',
      $expire_days,
      $expire_days,
      ($aab_options['visits_till_nag']) ? $aab_options['visits_till_nag'] : 0,
      ($aab_options['visits_till_nag']) ? $aab_options['visits_till_nag'] : 0,
      ($aab_options['max_nag']) ? $aab_options['max_nag'] : 0,
      $aab_options['nonce_banner'],
      $aab_options['nonce_banner'],
      $aab_options['nonce_message'],
      $image_html,
      str_replace('"', '\"', $aab_options['message']),
      $close_html,
      $link_html,
      $use_message_bar_html,
      $aab_options['nonce_message_bar'],
      str_replace('"', '\\"', $aab_options['message_bar']),
      $aab_options['nonce_message'],
      $aab_options['nonce_message_bar'],
      $aab_options['nonce_message_bar'],
      $aab_options['nonce_message'],
      $aab_options['nonce_message']
    );
  }

  function load_styles() {
    $style= $this->get_plugin_url().'/css/style.css';
    wp_register_style($this->name.'_style', $style);
    wp_enqueue_style($this->name.'_style');
  }

  function update_options() {
    // new options
    $aab_new_options = stripslashes_deep($_POST['aab_options_update']);

    // current options
    $aab_current_options = get_option($this->name);

    // convert "on" to true and "off" to false for checkbox fields
    // and set defaults for fields that are left blank
    if ( isset($aab_new_options['can_close']) )
      $aab_new_options['can_close'] = true;
    else
      $aab_new_options['can_close'] = false;

    if ( isset($aab_new_options['hide_content']) )
      $aab_new_options['hide_content'] = true;
    else
      $aab_new_options['hide_content'] = false;

    if ( isset($aab_new_options['show_link']) && $aab_new_options['show_link'] == "on")
      $aab_new_options['show_link'] = true;
    else
      $aab_new_options['show_link'] = false;

    if ( isset($aab_new_options['use_message_bar']) && isset($aab_new_options['use_message_bar']) == "on")
      $aab_new_options['use_message_bar'] = true;
    else
      $aab_new_options['use_message_bar'] = false;

    // set defaults if some fields that are left blank or invalid
    if ( !is_numeric($aab_new_options['max_nag']) )
      $aab_new_options['max_nag'] = $aab_current_options['max_nag'];
    elseif ( !$aab_new_options['max_nag'] || (int)$aab_new_options['max_nag'] < 0 )
      $aab_new_options['max_nag'] = 0;
    else {
      $aab_new_options['max_nag'] = (int)$aab_new_options['max_nag'];
    }

    if ( !is_numeric($aab_new_options['visits_till_nag']) )
      $aab_new_options['visits_till_nag'] = $aab_current_options['visits_till_nag'];
    elseif ( !$aab_new_options['visits_till_nag'] || (int)$aab_new_options['visits_till_nag'] < 0 )
      $aab_new_options['visits_till_nag'] = 0;
    else {
      $aab_new_options['visits_till_nag'] = (int)$aab_new_options['visits_till_nag'];
    }

    // Update options
    foreach($aab_new_options as $key => $value) {
      $aab_current_options[$key] = $value;
    }
    update_option($this->name, $aab_current_options);
  }

  function upgrade_options() {
    $aab_options = get_option($this->name);
    if ( !$aab_options ) {
      add_option($this->name, $this->get_default_options());
    }
    else {
      $default_options = $this->get_default_options();
      foreach($default_options as $option_name => $option_value) {
        if(!isset($aab_options[$option_name])) {
          $aab_options[$option_name] = $option_value;
        }
      }
      update_option($this->name, $aab_options);
    }
  }

  function reset_options() {
    if ( !get_option($this->name) ) {
      add_option($this->name, $this->get_default_options());
    }
    else {
      update_option($this->name, $this->get_default_options());
    }
  }

  function get_name() {
    return $this->name;
  }

  function get_plugin_dir() {
    // Pre-2.6 compatibility
    if ( !defined('WP_CONTENT_DIR') )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
    return WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__));
  }

  function get_plugin_url() {
    // Pre-2.6 compatibility
    if ( !defined('WP_CONTENT_URL') )
      define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
    return WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__));
  }

  function get_current_page_url() {
    $isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
    $port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
    $port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
    $url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port.$_SERVER["REQUEST_URI"];
    return $url;
  }

  function admin_menu() {
    add_options_page('Anti-AdBlock', 'Anti-AdBlock', 'manage_options', 'anti-adblock', array($this, 'admin_page'));
  }

  function admin_page() {
    // load text domain for translations
    load_plugin_textdomain($this->name);

    if ( isset($_POST['aab_options_update_submit']) ) {
      // if user wants to update options
      check_admin_referer($this->name);
      $this->update_options();
      printf('<div class="updated fade"><p>%s</p></div>', __('Anti-AdBlock options has been updated.', $this->name));
    }
    elseif ( isset($_POST['aab_options_upgrade_submit']) ) {
      // if user wants to upgrade options ( for new options on version upgrades )
      check_admin_referer($this->name);
      $this->upgrade_options();
      printf('<div class="updated fade"><p>%s</p></div>', __('Anti-AdBlock options has been upgraded.', $this->name));
    }
    elseif ( isset($_POST['aab_options_reset_submit']) ) {
      // if user wants to reset all options
      check_admin_referer($this->name);
      $this->reset_options();
      printf('<div class="updated fade"><p>%s</p></div>', __('Anti-AdBlock options has been reset.', $this->name));
    }

    // generate nonce
    $this->set_nonce('nonce_message');
    $this->set_nonce('nonce_message_bar');
    $this->set_nonce('nonce_banner');

    $aab_options = get_option($this->name);
    printf('
      <div class="wrap">
        <h2>Anti-AdBlock Options</h2>
        <div>
          <a href="%s">%s</a>&nbsp;|&nbsp;
          <a href="%s">%s</a>
        </div>',
      preg_replace('/&aab-page=[^&]*/', '', $_SERVER['REQUEST_URI']),
      __('General Configurations', $this->name),
      $this->homepage,
      __('Documentation', $this->name)
    );
    if ( isset($_GET['aab-page']) ) {
      if ( $_GET['aab-page'] || !$_GET['aab-page'] ) {
        require_once('options-generic.php');
      }
    }
    else {
      require_once('options-generic.php');
    }
    printf('
      </div><!--wrap-->'
    );
  } // function admin_page()
} // class aab

$anti_adblock = new AntiAdBlock();

if ( function_exists('add_action') ) {
  add_action('admin_menu', array($anti_adblock, 'admin_menu'));
  add_action('init', array($anti_adblock, 'load_styles'));
  add_action('wp_print_scripts', array($anti_adblock, 'queue_jquery'));
  add_action('wp_head', array($anti_adblock, 'redirect_to_image'));
  add_action('wp_footer', array($anti_adblock, 'display'));
  add_action('wp_footer', array($anti_adblock, 'display_bait'));
}

if ( function_exists('register_activation_hook') ) {
  register_activation_hook(__FILE__, array($anti_adblock, 'activate'));
}
?>
