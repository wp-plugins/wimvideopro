<?php
function wimtvpro_configure(){

    $uploads_info = wp_upload_dir();
    $directory = $uploads_info["basedir"] .  "/skinWim";

    if($_POST['wimtvpro_update'] == 'Y') {  
        //Form data sent 

        $error = 0;
        //Upload Skin
        $file = $_FILES['files']['name']["uploadSkin"];
        $tmpfile =  $_FILES['files']['tmp_name']["uploadSkin"];
        $arrayFile = explode(".", $file);
        if (!empty($file)) {            
			if ($arrayFile[1] != "zip") {
			  echo '<div class="error"><p><strong>';
              _e("This file isn't format correct for jwplayer's skin");
              echo '</strong></p></div>';
              $error ++;
			} else {
			  if (filesize($tmpfile) > 10485760) {
			    echo '<div class="error"><p><strong>';
                _e("Uploaded file is " .  round(filesize($tmpfile) / 1048576, 2) . "Kb. It must be less than 10Mb.");
                echo '</strong></p></div>';
                $error ++;
			  } else {
			  	if ( false === @move_uploaded_file( $tmpfile, $directory . "/" . $file) ) {
			  	  echo '<div class="error"><p><strong>';
                  _e("Internal error.");
                  echo '</strong></p></div>';
                  $error ++;
	            }
	            update_option('wp_nameSkin', $arrayFile[0]);
			  }
			}
        } else {
          update_option('wp_nameSkin', $_POST['nameSkin']);
        }
        
        // Required
        if (strlen(trim($_POST['userWimtv']))==0) {        
        	echo '<div class="error"><p><strong>';
            _e("The username is required.");
            echo '</strong></p></div>';
            $error ++;
        }
        // Required
        if (strlen(trim($_POST['passWimtv']))==0) {
        	echo '<div class="error"><p><strong>';
            _e("The password is required.");
            echo '</strong></p></div>';
            $error ++;     
        }

        
        if ($error==0) {
  	  	  update_option('wp_userwimtv', $_POST['userWimtv']);
          update_option('wp_passwimtv', $_POST['passWimtv']);
          update_option('wp_heightPreview', $_POST['heightPreview']);
          update_option('wp_widthPreview', $_POST['widthPreview']);  
          //update_option( 'wp_basePathWimtv','http://peer.wim.tv:8080/wimtv-webapp/rest/');
          update_option( 'wp_basePathWimtv','https://www.wim.tv/wimtv-webapp/rest/');
          update_option( 'wp_urlVideosWimtv','videos');
          update_option( 'wp_urlVideosDetailWimtv','videos?details=true');
          update_option( 'wp_urlThumbsWimtv','videos/{contentIdentifier}/thumbnail');
          update_option( 'wp_urlEmbeddedPlayerWimtv','videos/{contentIdentifier}/embeddedPlayers?get=1');
          update_option( 'wp_urlPostPublicWimtv','videos/{contentIdentifier}/showtime');
          update_option( 'wp_urlPostPublicAcquiWimtv','videos/{contentIdentifier}/acquired/{acquiredIdentifier}/showtime');
          update_option( 'wp_urlSTWimtv','videos/{contentIdentifier}/showtime/{showtimeIdentifier}');
          update_option( 'wp_urlShowTimeWimtv','users/{username}/showtime');
          update_option( 'wp_urlShowTimeDetailWimtv','users/{username}/showtime?details=true');
          update_option( 'wp_urlUserProfileWimtv','users/{username}/profile'); 
          update_option( 'wp_replaceContentWimtv','{contentIdentifier}'); 
          update_option( 'wp_replaceUserWimtv','{username}'); 
          update_option( 'wp_replaceacquiredIdentifier','{acquiredIdentifier}');
          update_option( 'wp_replaceshowtimeIdentifier','{showtimeIdentifier}'); 

          echo '<div class="updated"><p><strong>';
          _e('Options saved.' );
          echo '</strong></p></div>'; 
        }
  	}
   
   // If directory skinWim don't exist, create the directory (if change Public file system path into admin/config/media/file-system after installation of this module or is the first time)
   if (!is_dir($directory)) {
      $directory_create = mkdir($uploads_info["basedir"] . "/skinWim");
   }
   if (is_dir($directory)) {
     if ($directory_handle = opendir($directory)) {
     //Read directory for skin JWPLAYER
   	 $elencoSkin[""] = "-- Base Skin --";
     while (($file = readdir($directory_handle)) !== FALSE) {
       if ((!is_dir($file)) && ($file!=".") && ($file!="..")) {
         $explodeFile = explode("." , $file);
         if ($explodeFile[1]=="zip")
           $elencoSkin[$explodeFile[0]] = $explodeFile[0];
         }
       }
       closedir($directory_handle);
     }
   }
   //Create option select form Skin
   $createSelect = "";
   foreach ($elencoSkin as $key => $value){
     $createSelect .= "<option value='" . $key . "'";
     if ($value==get_option("wp_nameSkin"))  $createSelect .= " selected='selected' ";
     $createSelect .= ">" . $value . "</option>";
   }

?>
  <div class="wrap">
         <h2>WimTv Configuration</h2>
        <form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">
            <div>
               <h4><?php _e("If you haven't yet done, <a href='http://www.wim.tv/wimtv-webapp/userRegistration.do?execution=e1s1' target='_new'>sign up</a> at wim.tv" ); ?></h4>
              
              	<p><label for="edit-userwimtv">Username Wimtv <span class="form-required" title="">*</span>
				<input type="text" id="edit-userwimtv" name="userWimtv" value="<?php echo get_option("wp_userwimtv");?>" size="100" maxlength="200"/></p>
				
				<p><label for="edit-passwimtv">Password Wimtv <span class="form-required" title="">*</span></label>
				<input value="<?php echo get_option("wp_passwimtv");?>" type="password" id="edit-passwimtv" name="passWimtv" size="100" maxlength="200" class="form-text required" /></p>
			
				
				<h4><?php _e("Upload and/or choose your skin player into <a target='new' href='http://www.longtailvideo.com/addons/skins'>page Jwplayer</a> for your videos" ); ?></h4>
				<p><label for="edit-nameskin">Name Skin</label>
				<select id="edit-nameskin" name="nameSkin" class="form-select"><?php echo $createSelect; ?></select></p>
				
				<p><label for="edit-uploadskin">Upload new skin player </label>
				<input type="file" id="edit-uploadskin" name="files[uploadSkin]" size="100" class="form-file" /></p>

				<div class="description"><?php _e("Only zip. Save into a public URL " . $directory . "<br/>
					For running the skin selected, copy the file <a href='http://plugins.longtailvideo.com/crossdomain.xml' target='_new'>crossdomain.xml</a> to the root directory (e.g. http://www.mysite.it). You can do it all from your FTP program (e.g. FileZila, Classic FTP, etc).
					So open up your FTP client program. First, identify your root directory. This is the folder titled or beginning with www -- and this is where you ultimately need to move that pesky crossdomain.xml. Now all you have to do is find it."); ?>
				</div>
				
				<h4><?php _e("Dimensions of player for your videos" ); ?></h4>
				<p><label for="edit-heightpreview">Height (default: 280) </label>
				<input type="text" id="edit-heightpreview" name="heightPreview" value="<?php echo get_option("wp_heightPreview");?>" size="100" maxlength="200" class="form-text" /></p>
				<p><label for="edit-widthpreview">Width (default: 500) </label>
				<input type="text" id="edit-widthpreview" name="widthPreview" value="<?php echo get_option("wp_widthPreview");?>" size="100" maxlength="200" class="form-text" /></p>
				
				<input type="hidden" name="wimtvpro_update" value="Y" />
				<?php submit_button(); ?>
			</div>	
		</form> 
	</div>
<?php
}

 

function media_wimtvpro_process() {
  media_upload_header();
  
  $videos .= "<h3 class='media-title'>My Streaming</h3><ul class='itemsInsert'>" . wimtvpro_getThumbs(TRUE, FALSE, TRUE) . "</ul><div class='empty'></div>";
  $videos .= "<h3 class='media-title'>My PlayList</h3><ul class='itemsInsert'></ul><div class='empty'></div>";
  echo $videos;

}
function wimtvpro_media_menu_handle() {
    return wp_iframe( 'media_wimtvpro_process');
}
add_action('media_upload_wimtvpro', 'wimtvpro_media_menu_handle');


?>
