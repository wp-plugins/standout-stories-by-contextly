<?php
/* 
Plugin Name: Standout Stories by Contextly
Plugin URI: http://contextly.com
Version: 1.00
Author: <a href="http://www.contextly.com/">Ryan Singel</a>
Description: Google announced in September 2011 a new system for news publishers to label their best stories and to give credit to other publications that get a "scoop" - what Google calls standout. Doing this gives the story a boost in Google News. This plugin allows you to do both.
*/                         
/* 
License:
  This file is part of Standout Stories by Contextly.

    Standout Stories by Contextly is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    (at your option) any later version.

    Standout Stories by Contextly is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/


if (!class_exists("ContextlyStandout")) {
   class ContextlyStandout {
      var $csoKey = "_Contextly_SoKey";
      var $csoLnk = "_Contextly_SoLnk";
      var $csoJson= "_Contextly_SoJson";
      var $csoNow = "";
      
      function ContextlyStandout ()
      {
         $this->csoNow = getdate();
         add_action('add_meta_boxes', array(&$this, 'csoMetaBox'));
         add_action('edit_post', array(&$this, 'csoPostEdit'));
			add_action('wp_head', array(&$this, 'csoLinkHead'));
			add_filter("mce_external_plugins", array(&$this, 'csoTinyMCEPlugin'));
			add_filter('mce_buttons', array(&$this, 'csoTinyMCEButton'));
      }
		
		function csoTinyMCEPlugin($plugin_array) {
			$plugin_array['contextlystandout'] = plugins_url('js/contextly-standout.js' , __FILE__ );
			return $plugin_array;
		}

		function csoTinyMCEButton($buttons) {
			//return $buttons[] = "contextlystandout";
         if (is_array($buttons) && count($buttons) > 0) {
            foreach ($buttons as $btn_idx => $button) {
               if ($button == "link") {
                  array_splice($buttons, $btn_idx, 0, "contextlystandout");
               }
            }
         } else {
            array_push($buttons, "separator", "contextlystandout");
         }
			return $buttons;
		}

      function csoMetaBox ()
      {
         add_meta_box( 
            'ContextlyStandout_sectionid',
            __( 'Standout Stories by Contextly', 'ContextlyStandout_textdomain' ),
            array( &$this, 'csoMetaBoxView' ),
            'post',
            'side',
            'high'
         );
         add_meta_box(
            'ContextlyStandout_sectionid',
            __( 'Standout Stories by Contextly', 'ContextlyStandout_textdomain' ), 
            array( &$this, 'csoMetaBoxView' ),
            'page',
            'side',
            'high'
         );
      }

      function csoMetaBoxView($post) 
      {
         //global $wpdb;
         // Use nonce for verification
         wp_nonce_field( plugin_basename( __FILE__ ), 'ContextlyStandout_noncename' );
         $checkedval = get_post_meta($post->ID, $this->csoKey, true);
         $linkval = get_post_meta($post->ID, $this->csoLnk, true);
         $linkjson = get_post_meta($post->ID, $this->csoJson, true);
         $checked = ($checkedval) ? ' checked="checked"' : null;
         $weekvar = $this->weekNow ();
         echo '<p><input type="checkbox" name="cso_checkbox" id="cso_checkbox"'. $checked .' value="1" /> 
            Tell <a href="http://googlenewsblog.blogspot.com/2011/09/recognizing-publishers-standout-content.html" target="_blank">
            Google News</a> This Story is Great.</p>';
         echo '<input type="hidden" name="cso_url" id="cso_url" value="'.$linkval.'" />';
         echo '<input type="hidden" name="cso_json" id="cso_json" value="'.$linkjson.'" />';
         $csoBalanse = $this->csoBalanse();
         echo '<p>You have <em>'.$csoBalanse.'</em> Standout Stories remaining this week.</p>';
         if ($csoBalanse <= 0) {
            $csoStamp = getdate($weekvar[1]-$this->csoNow[0]+60);
            echo '<p>You have <em>'.round(($csoStamp['mday']*24)+$csoStamp['hours']+($csoStamp['minutes']/60)+($csoStamp['seconds']/3600), 3).'</em> hours until you can label a Standout story again.</p>';
         }
      }

      function csoBalanse ()
      {
         global $wpdb;
         $weekvar = $this->weekNow ();
         $csoLeft = $wpdb->get_row("SELECT COUNT(post_id) as total FROM $wpdb->postmeta WHERE meta_key = '$this->csoKey' AND meta_value >= $weekvar[0] AND meta_value <= $weekvar[1]");
         return (7 - (int)$csoLeft->total);
      }

      function csoPostEdit ($post_id) 
      {
         // verify if this is an auto save routine. 
         // If it is, our form has not been submitted, so we don't want to do anything
         if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
         return;

         // verify this came from the our screen and with proper authorization,
         // because save_post can be triggered at other times
         if ( !wp_verify_nonce( $_POST['ContextlyStandout_noncename'], plugin_basename( __FILE__ ) ) )
         return;

         // Check permissions
         if ( 'page' == $_POST['post_type'] ) {
            if ( !current_user_can( 'edit_page', $post_id ) )
               return;
         } else {
            if ( !current_user_can( 'edit_post', $post_id ) )
               return;
         }
         
         // OK, we're authenticated: we need to find and save the data
         if (isset($_POST['cso_checkbox']) and $_POST['cso_checkbox'] == "1") {
            $currentval = get_post_meta($post_id, $this->csoKey, true);
            $csoBalanse = $this->csoBalanse();
            if ($currentval) {
               update_post_meta($post_id, $this->csoKey, $currentval);
            } else {
               if ($csoBalanse > 0) {
                  $currentval = $this->csoNow[0];
                  update_post_meta($post_id, $this->csoKey, $currentval);
               }
            }
         } else {
           delete_post_meta($post_id, $this->csoKey); 
         }
         if (isset($_POST['cso_url']) and $_POST['cso_url'] != "http://" and isset($_POST['cso_json'])) {
            $linkval = htmlentities($_POST['cso_url'], ENT_QUOTES);
            $linkjson= htmlentities($_POST['cso_json'], ENT_QUOTES);
            update_post_meta($post_id, $this->csoLnk, $linkval);
            update_post_meta($post_id, $this->csoJson, $linkjson);
         } else {
            delete_post_meta($post_id, $this->csoLnk);
            delete_post_meta($post_id, $this->csoJson);
         }				
      }

      function csoLinkHead ()
      {
         global $post;
         if (!is_single())
				return;
         require_once( ABSPATH . WPINC . '/class-json.php' );
         $json = new Services_JSON();	
			$post_id = (is_object($post)) ? $post->ID : $post;
			$checkedval = get_post_meta($post->ID, $this->csoKey, true);
         $linkval = get_post_meta($post->ID, $this->csoLnk, true);
         $linkjson= html_entity_decode(get_post_meta($post->ID, $this->csoJson, true));
			if ($checkedval) {
				echo "<link rel='standout' href='" . get_permalink($post_id) . "' />\n";
			}
         if ($linkval && $linkjson) {
            $objJson = $json->decode($linkjson);
            foreach($objJson->val as $val) {
               echo "<link rel='standout' href='" . $val . "' />\n";
            }
         }
      }

      function weekNow ()
      {
         $today = $this->csoNow;//getdate();
         $sunday = mktime(0, 0, 0, $today['mon'], $today['mday']-$today['wday'], $today['year']);
         $sunday2 = mktime(0, 0, 0, $today['mon'], $today['mday']-$today['wday']+7, $today['year']);
         return array($sunday, $sunday2);        
      }
   }
}
if (class_exists("ContextlyStandout")) {
	$cso_plugin = new ContextlyStandout();
}


?>
