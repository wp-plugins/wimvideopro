<?php
/*
Plugin Name: Wim Tv Pro
Plugin URI: http://wimtvpro.tv
Description: Publish your wimtv's video
Version: 2.0.4
Author: WIMLABS
Author URI: http://www.wimlabs.com
License: GPLv2 or later
*/

/*  Copyright 2012  wimlabs  (email : riccardo@cedeo.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Create a term metadata table where $type = metadata type

include ("hooks.php");
include ("functions.php");
include ("pages.php");


/* What to do when the plugin is activated? */
register_activation_hook(__FILE__,'wimtvpro_install');
/* What to do when the plugin is deactivated? */
register_deactivation_hook( __FILE__, 'wimtvpro_remove');

function wimtvpro_install() {
	/* Create a new database field */
  	global $wpdb;
	wimtvpro_create_metadata_table($table_name);
  
  // Create page MyWimTv Streaming
  $my_streaming_page = array(
    'post_title'    => 'My WimTv Streaming',
    'post_content'  => '',
    'post_status'   => 'publish',
    'post_author'   => 1,
    'post_type'   => 'page',
    'post_name' => 'my_streaming_wimtv',
  );

  // Insert the post into the database
  wp_insert_post($my_streaming_page);
  
  //$embeddedLive = wimtvpro_elencoLive("video", "0") . "<br/>UPCOMING EVENT<br/>" . wimtvpro_elencoLive("list", "0");
  
  $embeddedLive =  plugins_url('pages/embeddedLive.php', __FILE__);
  // Create page Event Live 
  $my_wimlive_page = array(
    'post_title'    => 'Live',
    'post_content'  => '<script>jQuery(document).ready(function(){
    jQuery.ajax({
			context: this,
			url:  "'. $embeddedLive . '", 		      
			type: "GET",
			dataType: "html",
			async: false,
			success: function(response) {
				jQuery(".entry-content").append(response);
			},
		});
    });</script>',
    'post_status'   => 'publish',
    'post_author'   => 1,
    'post_type'   => 'page',
    'post_name' => 'wimlive_wimtv',
  );

  // Insert the post into the database
  wp_insert_post($my_wimlive_page);
  
  
}
function wimtvpro_setting() {
  register_setting('configwimtvpro-group', 'wp_userwimtv');
  register_setting('configwimtvpro-group', 'wp_passwimtv');
  register_setting('configwimtvpro-group', 'wp_nameSkin');
  register_setting('configwimtvpro-group', 'wp_uploadSkin');  
  register_setting('configwimtvpro-group', 'wp_heightPreview');
  register_setting('configwimtvpro-group', 'wp_widthPreview');
  register_setting('configwimtvpro-group', 'wp_basePathWimtv');
  register_setting('configwimtvpro-group', 'wp_urlVideosWimtv');
  register_setting('configwimtvpro-group', 'wp_urlVideosDetailWimtv');
  register_setting('configwimtvpro-group', 'wp_urlThumbsWimtv');				
  register_setting('configwimtvpro-group', 'wp_urlEmbeddedPlayerWimtv');
  register_setting('configwimtvpro-group', 'wp_urlPostPublicWimtv');
  register_setting('configwimtvpro-group', 'wp_urlPostPublicAcquiWimtv');			
  register_setting('configwimtvpro-group', 'wp_urlSTWimtv');
  register_setting('configwimtvpro-group', 'wp_urlShowTimeWimtv');	
  register_setting('configwimtvpro-group', 'wp_urlShowTimeDetailWimtv');	
  register_setting('configwimtvpro-group', 'wp_urlUserProfileWimtv');
  register_setting('configwimtvpro-group', 'wp_replaceContentWimtv');
  register_setting('configwimtvpro-group', 'wp_replaceUserWimtv');
  register_setting('configwimtvpro-group', 'wp_replaceacquiredIdentifier');			
  register_setting('configwimtvpro-group', 'wp_replaceshowtimeIdentifier');
  register_setting('configwimtvpro-group', 'wp_sandbox');
  
  register_setting('profilewimtvpro-group', 'wp_name');
  register_setting('profilewimtvpro-group', 'wp_logo');
  register_setting('profilewimtvpro-group', 'wp_date');
  register_setting('profilewimtvpro-group', 'wp_email');
  register_setting('profilewimtvpro-group', 'wp_social');

  add_option( 'wp_userwimtv','username');
  add_option( 'wp_passwimtv','password');
  add_option( 'wp_nameSkin','');
  add_option( 'wp_uploadSkin','');
  add_option( 'wp_heightPreview','280');
  add_option( 'wp_widthPreview','500');
  
  add_option( 'wp_name','si');
  add_option( 'wp_logo','si');
  add_option( 'wp_date','');
  add_option( 'wp_email','');
  add_option( 'wp_social','si');
  add_option( 'wp_sandbox','No'); 
} 
add_action( 'admin_init', 'wimtvpro_setting');

function wimtvpro_remove() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'wimtvpro_video';
  $wpdb->query("DROP TABLE  {$table_name}");
  
  $table_name2 = $wpdb->prefix . 'wimtvpro_playlist';
  $wpdb->query("DROP TABLE {$table_name2}");

  
  delete_option('wp_userwimtv');
  delete_option('wp_passwimtv');
  delete_option('wp_nameSkin');
  delete_option('wp_uploadSkin');
  delete_option('wp_heightPreview');
  delete_option('wp_widthPreview');
  delete_option('wp_basePathWimtv');
  delete_option( 'wp_urlVideosWimtv');
  delete_option( 'wp_urlVideosDetailWimtv');
  delete_option( 'wp_urlThumbsWimtv');
  delete_option( 'wp_urlEmbeddedPlayerWimtv');
  delete_option( 'wp_urlPostPublicWimtv');
  delete_option( 'wp_urlPostPublicAcquiWimtv');
  delete_option( 'wp_urlSTWimtv');
  delete_option( 'wp_urlShowTimeWimtv');
  delete_option( 'wp_urlShowTimeDetailWimtv');
  delete_option( 'wp_urlUserProfileWimtv'); 
  delete_option( 'wp_replaceContentWimtv'); 
  delete_option( 'wp_replaceUserWimtv'); 
  delete_option( 'wp_replaceacquiredIdentifier');
  delete_option( 'wp_replaceshowtimeIdentifier'); 
  delete_option( 'wp_name');
  delete_option( 'wp_logo');
  delete_option( 'wp_date');
  delete_option( 'wp_email');
  delete_option( 'wp_social');
  
  $wpdb->query("DELETE FROM " .  $wpdb->posts . " WHERE post_name LIKE '%my_streaming_wimtv%' OR post_name LIKE '%wimlive_wimtv%'");



  
}


// Add table for wimvideo pro
function wimtvpro_create_metadata_table($table_name) {
  global $wpdb;
  
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  
  $table_name = $wpdb->prefix . 'wimtvpro_video';
  if (!empty ($wpdb->charset))
      $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
  if (!empty ($wpdb->collate))
      $charset_collate .= " COLLATE {$wpdb->collate}";
             
  $sql = "CREATE TABLE {$table_name} (
            uid varchar(100) NOT NULL COMMENT 'User identifier',
            contentidentifier varchar(100) NOT NULL COMMENT 'Contentidentifier Video',
            state varchar(100) NOT NULL COMMENT 'Showtime or no',
            status varchar(100) NOT NULL COMMENT 'OWNED-ACQUIRED-PERFORMING',
            acquiredIdentifier varchar(100) NOT NULL,
            mytimestamp int(11) NOT NULL COMMENT 'My timestamp',
            position int(11) NOT NULL COMMENT 'Position video user',
            viewVideoModule varchar(100) NOT NULL COMMENT 'View video into page or block',
            urlThumbs text NOT NULL COMMENT 'Url thumbs video',
            urlPlay text NOT NULL COMMENT 'Url player video',
            category text NOT NULL COMMENT 'Category and subcategory video[Json]',
            title varchar(100) NOT NULL COMMENT 'Title videos',
            duration varchar(10) NOT NULL COMMENT 'Duration videos',
            showtimeIdentifier varchar(100) NOT NULL COMMENT 'showtimeIdentifier videos',
            PRIMARY KEY (contentidentifier),
            UNIQUE KEY mycolumn1 (contentidentifier)
  ) {$charset_collate};";
  

  dbDelta($sql);
  
  
  $table_name2 = $wpdb->prefix . 'wimtvpro_playlist';
             
  $sql2 = "CREATE TABLE {$table_name2} (
            id INT NOT NULL AUTO_INCREMENT COMMENT 'Id',
            name varchar(100) NOT NULL COMMENT 'Name of playlist',
            uid varchar(100) COMMENT 'User identifier',
            listVideo varchar(1000) COMMENT 'List video contentidentifier',
            PRIMARY KEY (id),
            UNIQUE KEY mycolumn2 (id)
            
  ) {$charset_collate};";

  
  dbDelta($sql2);
  
    
}
// End table for wimvideo pro


//menu admin
function wimtvpro_menu(){
    $user = wp_get_current_user();
    //For Admin
    if ($user->roles[0] == "administrator"){
      add_menu_page('Configuration', 'WimTvPro', 'administrator', 'WimVideo', 'wimtvpro_configure', plugins_url('images/iconMenu.png', __FILE__), 6);      
      add_submenu_page('WimVideo', 'My Media', 'My Media', 'administrator', 'WimVideoPro_MyMedia', 'wimtvpro_mymedia');
      add_submenu_page('WimVideo', 'My Streaming', 'My Streaming', 'administrator', 'WimVideoPro_MyStreaming', 'wimtvpro_mystreaming');
      add_submenu_page('WimVideo', 'Upload Video', 'Upload Video', 'administrator', 'WimVideoPro_UploadVideo', 'wimtvpro_upload');
      add_submenu_page('WimVideo', 'Wim Live', 'Wim Live', 'administrator', 'WimVideoPro_WimLive', 'wimtvpro_live');
      add_submenu_page('WimVideo', 'Report', 'Report', 'administrator', 'WimVideoPro_Report', 'wimtvpro_Report');
    }
    
    if ($user->roles[0]=="author") {
      add_menu_page('My Streaming', 'Streaming Wimtv', 'author', 'WimVideo', 'wimtvpro_mystreaming', plugins_url('images/iconMenu.png', __FILE__), 6);
    }
    if ($user->roles[0]=="editor") {
      add_menu_page('My Streaming', 'Streaming Wimtv', 'author', 'WimVideo', 'wimtvpro_mystreaming', plugins_url('images/iconMenu.png', __FILE__), 6);
    }
    
    
    
}
add_action('admin_menu', 'wimtvpro_menu');
// END menu admin


// Attach video into post
function wimtvpro_media_menu($tabs) {
  $newtab = array('wimtvpro' => __('My Wimtv video', 'wimtvpro_insert'));
  return array_merge($tabs, $newtab);
  
  //VEDERE http://axcoto.com/blog/article/307
  
}
add_filter('media_upload_tabs', 'wimtvpro_media_menu');
// End attach video into post


//Jquery and Css
add_action('init', 'wimtvpro_install_jquery');
function wimtvpro_install_jquery() {
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-sortable');
  wp_enqueue_script('jquery-ui-datepicker');
  wp_enqueue_script('jwplayer', plugins_url('script/jwplayer/jwplayer.js', __FILE__));
  wp_enqueue_script('timepicker', plugins_url('script/timepicker/jquery.ui.timepicker.js', __FILE__));
  wp_enqueue_script('colorbox', plugins_url('script/colorbox/js/jquery.colorbox.js', __FILE__));
  wp_register_style( 'colorboxCss', plugins_url('script/colorbox/css/colorbox.css', __FILE__) );
  
  wp_enqueue_script('colorbox', plugins_url('script/colorbox/js/jquery.colorbox.js', __FILE__));
  wp_register_style( 'colorboxCss',plugins_url('script/colorbox/css/colorbox.css', __FILE__) );

  wp_enqueue_style('colorboxCss');
  wp_enqueue_script('jquery-ui-core');
  if (!is_admin()) {
    wp_register_style( 'wimtvproCss', plugins_url('css/wimtvpro_public.css', __FILE__) );
    wp_enqueue_style('wimtvproCss');
    
  } 
  else {
    
    wp_register_style( 'wimtvproCss', plugins_url('css/wimtvpro.css', __FILE__) );
    wp_enqueue_style('wimtvproCss');
    wp_register_style('wimtvproCssCore',plugins_url('script/css/redmond/jquery-ui-1.8.21.custom.css', __FILE__));
    wp_enqueue_style('wimtvproCssCore');
 }
 wp_enqueue_script('wimtvproScript',plugins_url('script/wimtvpro.js', __FILE__));

}


function my_custom_js() {
    echo '<script type="text/javascript">var url_pathPlugin ="' . plugin_dir_url(__FILE__) . '";</script>';
}
// Add hook for admin <head></head>
add_action('admin_head', 'my_custom_js');
//End Jquery and Css


//Widget
class myStreaming extends WP_Widget {
    function myStreaming() {
        parent::__construct( false, 'Wimtv: My Streaming' );
    }
    function widget( $args, $instance ) {
        extract($args);
        echo $before_widget;
        $title = apply_filters( 'My WimTV Videos', $instance['title'] );
        echo $before_widget;
        if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;

        echo "<ul class='itemsPublic'>" . wimtvpro_getThumbs(TRUE, FALSE, FALSE, "block") . "</ul>";
         
        echo $after_widget;
    }
    function update( $new_instance, $old_instance ) {
    	$instance['title'] = strip_tags( $new_instance['title'] );

        return $new_instance;
    }
    function form( $instance ) {
       _e("Title");
        $title = apply_filters( 'My WimTV Videos', $instance['title'] );
        ?>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
          
        <?php
    }
}
class myPersonalDate extends WP_Widget {
    function myPersonalDate () {
        parent::__construct( false, 'Wimtv: My Profile' );
    }
        
    function widget( $args, $instance ) {
        extract($args);
		$title = apply_filters( 'My WimTV Profile', $instance['title'] );
        echo $before_widget;
        if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
        // This example is adapted from node.module.
        $urlprofile = get_option("wp_basePathWimtv") . str_replace( get_option("wp_replaceUserWimtv"), get_option("wp_userWimtv"), get_option("wp_urlUserProfileWimtv"));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlprofile);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);
        $arrayjsuser = json_decode($response);
        $profileuser= "";
        $namepage = "";
        if (get_option("wp_logo")=="si")  
          $profileuser .= "<img src='" . $arrayjsuser ->imageLogoPath . "'>";
        if (get_option("wp_name")=="si"){
          if (isset($arrayjsuser->pageName)) $namepage .= "<p><b>" . $arrayjsuser->pageName . "</b><br/>" . $arrayjsuser->pageDescription . "</p>";
          else $namepage .= "<p><b>" . $arrayjsuser->username . "</b></p>";
        }
        $profileuser .= $namepage;
        if (get_option("wp_date")=="si")
          $profileuser .= "<p><b>" . __("My Detail") . "</b><br/>" . $arrayjsuser->name . " " . $arrayjsuser->surname . "<br/>" . $arrayjsuser->dateOfBirth . "<br/>" . $arrayjsuser->sex . "<br/>" . "</p>"; 
        if (get_option("wp_email")=="si")
          $profileuser .= "<p><b>" . __("Contact") . "</b><br/>" . $arrayjsuser->email . "<br/>";
        if (get_option("wp_social")=="si") {
          if (isset($arrayjsuser->linkedinURI))
            $profileuser .= "<a target='_new' href='" . $arrayjsuser->linkedinURI . "'><img src='" . plugins_url('images/linkedin.png', __FILE__) . "'></a>";
          if (isset($arrayjsuser->twitterURI))
            $profileuser .= "<a target='_new' href='" . $arrayjsuser->twitterURI . "'><img src='" . plugins_url('images/twitter.png', __FILE__) . "'></a>";
          if (isset($arrayjsuser->facebookURI))
            $profileuser .= "<a target='_new' href='" . $arrayjsuser->facebookURI . "'><img src='" . plugins_url('images/facebook.png', __FILE__) . "'></a>";
          $profileuser .= "</p>";
        }
        echo $profileuser;
        echo $after_widget;
    }
    function update( $new_instance, $old_instance ) {
      var_dump($old_instance);
      $instance['title'] = strip_tags( $new_instance['title'] );
      update_option('wp_logo', $_POST['ImageLogoProfile']);
 	  update_option('wp_name', $_POST['pageNameProfile']);
 	  update_option('wp_date', $_POST['personalDateProfile']);
 	  update_option('wp_email', $_POST['EmailProfile']);
 	  update_option('wp_social', $_POST['SocialProfile']);
      return $new_instance;
    }
    function form( $instance ) {
        
    	_e("Title");
    	$title = apply_filters( 'My WimTV Profile', $instance['title'] );
        ?>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
          <p>Would you like view...<br/>
           <input type="checkbox" id="edit-imagelogoprofile" name="ImageLogoProfile" value="si" <?php if (get_option("wp_logo")=="si") echo 'checked="checked"'; ?> class="form-checkbox" />  <label class="option" for="edit-imagelogoprofile">Image Logo</label><br/>
           <input type="checkbox" id="edit-pagenameprofile" name="pageNameProfile" value="si" <?php if (get_option("wp_name")=="si") echo 'checked="checked"'; ?> class="form-checkbox" />  <label class="option" for="edit-pagenameprofile">Page name</label><br/>
           <input type="checkbox" id="edit-personaldateprofile" name="personalDateProfile" value="si"  <?php if (get_option("wp_date")=="si") echo 'checked="checked"'; ?> class="form-checkbox" />  <label class="option" for="edit-personaldateprofile">Personal Date</label><br/>
           <input type="checkbox" id="edit-emailprofile" name="EmailProfile" value="si"  <?php if (get_option("wp_email")=="si") echo 'checked="checked"'; ?> class="form-checkbox" /> <label class="option" for="edit-emailprofile">Email</label><br/>
           <input type="checkbox" id="edit-socialprofile" name="SocialProfile" value="si"  <?php if (get_option("wp_social")=="si") echo 'checked="checked"'; ?> checked="checked" class="form-checkbox" />  <label class="option" for="edit-socialprofile">Link Social</label>
           <br/>... of Wimtv Profile</p>
                   
        <?php
    }
}
function my_register_widgets() {
    register_widget( "myPersonalDate" );
}

function my_register_widgets2() {
    register_widget( "myStreaming" );
}

add_action( 'widgets_init', 'my_register_widgets' );
add_action( 'widgets_init', 'my_register_widgets2' );
//End Widget

