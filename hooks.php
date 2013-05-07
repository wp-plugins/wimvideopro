<?php

define('BASE_URL', get_bloginfo('url'));

function wimtvpro_configure(){
  $submenu = "<ul class='subsubsub'>";
  $submenu .= "<li><a href='admin.php?page=WimTvPro' class='config'>Configuration</a> |";
  $submenu .= "<li><a href='admin.php?page=WimTvPro&pack=1' class='packet'>Pricing</a> |";
  $submenu .= "<li><a href='admin.php?page=WimTvPro&update=1' class='payment'>Payment</a> |";
  $submenu .= "<li><a href='admin.php?page=WimTvPro&update=2' class='live'>WimLive Configuration</a> |";
  $submenu .= "<li><a href='admin.php?page=WimTvPro&update=3' class='user'>Update Personal Info</a> |";
  $submenu .= "<li><a href='admin.php?page=WimTvPro&update=4' class='other'>Features</a> ";
  $submenu .= "</ul>";
echo "<div class='clear'></div>";
  if (!isset($_GET["pack"]))	{	
    if (!isset($_GET["update"])){
	    $directory = $uploads_info["basedir"] .  "/skinWim";
	    $styleReg = "display:none";
	        
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
	          
	          if ($_POST['sandbox']=="No") {
	          	update_option( 'wp_basePathWimtv','https://www.wim.tv/wimtv-webapp/rest/');
	          } else {
	          	update_option( 'wp_basePathWimtv','http://peer.wim.tv:8080/wimtv-webapp/rest/');
	          }
	          
	          if (($_POST['sandbox']!=get_option('wp_sandbox')) && (($_POST['userWimtv']=="username") && ($_POST['passWimtv']=="password"))){
	            update_option('wp_registration', 'FALSE'); 
	            update_option('wp_userwimtv', 'username');
	            update_option('wp_passwimtv', 'password');
	
	          } else {
	          
		          update_option('wp_userwimtv', $_POST['userWimtv']);
		          update_option('wp_passwimtv', $_POST['passWimtv']);
		      }    
		          
	          update_option('wp_heightPreview', $_POST['heightPreview']);
	          update_option('wp_widthPreview', $_POST['widthPreview']);
	
	          
	          update_option('wp_sandbox', $_POST['sandbox']);
	          update_option( 'wp_urlVideosWimtv','videos');
	          update_option( 'wp_urlVideosDetailWimtv','videos?details=true&incomplete=true');
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
	          update_option( 'wp_publicPage', $_POST['publicPage']);
	          update_page_mystreaming();
	
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
		
		
	$uploads_info = wp_upload_dir();	
	?>
	  <div class="wrap">
	         <h2>WimTvPro Configuration</h2>
				
				<?php echo str_replace("config","current",$submenu) ; ?>
				
	            <div>
					
					 <?php
					 	wimtvpro_alert_reg();
					 ?>
									
					<form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">
					
						<table class="form-table">
					
			              	<tr>
			              		<th><label for="edit-userwimtv">Username WimTV <span class="form-required" title="">*</span></label></th>
								<td><input type="text" id="edit-userwimtv" name="userWimtv" value="<?php echo get_option("wp_userwimtv");?>" size="100" maxlength="200"/></td>
							</tr>
							
							<tr>
								<th><label for="edit-passwimtv">Password WimTV <span class="form-required" title="">*</span></label></th>
								<td><input value="<?php echo get_option("wp_passwimtv");?>" type="password" id="edit-passwimtv" name="passWimtv" size="100" maxlength="200" class="form-text required" /></td>
							</tr>
						</table>
						
						<h4><?php _e("Upload and/or choose your skin player into <a target='new' href='http://www.longtailvideo.com/addons/skins'>page Jwplayer</a> for your videos" ); ?></h4>
	
						<table class="form-table">	
							<tr>
								<th><label for="edit-nameskin">Name Skin</label></th>
								<td><select id="edit-nameskin" name="nameSkin" class="form-select"><?php echo $createSelect; ?></select></td>
							</tr>
							<tr>
								<th><label for="edit-uploadskin"> or upload new skin player </label></th>
								<td><input type="file" id="edit-uploadskin" name="files[uploadSkin]" size="100" class="form-file" />
									<div class="description"><?php _e("Only zip. Save into a public URL " . $directory . "<br/>
									For running the skin selected, copy the file <a href='http://plugins.longtailvideo.com/crossdomain.xml' target='_new'>crossdomain.xml</a> to the root directory (e.g. http://www.mysite.it). You can do it all from your FTP program (e.g. FileZila, Classic FTP, etc).
									So open up your FTP client program. First, identify your root directory. This is the folder titled or beginning with www -- and this is where you ultimately need to move that pesky crossdomain.xml. Now all you have to do is find it."); ?>
									</div>
								</td>
							</tr>
						</table>
		
						<h4><?php _e("Dimensions of player for your videos" ); ?></h4>
		
						<table class="form-table">	
							<tr>
								<th><label for="edit-heightpreview">Height (default: 280) </label></th>
								<td><input type="text" id="edit-heightpreview" name="heightPreview" value="<?php echo get_option("wp_heightPreview");?>" size="100" maxlength="200" class="form-text" /></td>
							</tr>
							<tr>
								<th><label for="edit-widthpreview">Width (default: 500) </label></th>
								<td><input type="text" id="edit-widthpreview" name="widthPreview" value="<?php echo get_option("wp_widthPreview");?>" size="100" maxlength="200" class="form-text" /></td>
							</tr>
							
						</table>
						
						<h4><?php _e("Other information" ); ?></h4>
						
						
						
						<input type="hidden" value="No" name="sandbox"> 
			<table class="form-table"> <!-- SANDBOX -->
				<!--tr>	 
					<th><label for="edit-sandbox">Please select "no" to use the plugin on the WimTV server. Select "yes" to try the service only on test server</label></th>
					<td>
						<select id="edit-sandbox" name="sandbox" class="form-select">
						<option value="No" <?php if (get_option("wp_sandbox")=="No") echo "selected='selected'" ?>>No</option>
						<option value="Yes" <?php if (get_option("wp_sandbox")=="Yes") echo "selected='selected'" ?>>Yes, for Developer or Test</option>
						</select>
					</td>
				</tr>-->
			
							<tr>
								<th><label for="edit-publicPage">Would you added a public MyStreaming Page?</label></th>
								<td>
									<select id="edit-publicPage" name="publicPage" class="form-select">
										<option value="No" <?php if (get_option("wp_publicPage")=="No") echo "selected='selected'" ?>>No</option>
										<option value="Yes" <?php if (get_option("wp_publicPage")=="Yes") echo "selected='selected'" ?>>Yes (add a page My WimTv Streaming)</option>
									</select>
								</td>
							</tr>
						
						</table>
						
						<input type="hidden" name="wimtvpro_update" value="Y" />
						<?php submit_button(); ?>
					</form> 
				</div>	
			
		</div>
	
	<?php
	}
	
	else {
	
		echo "<div class='wrap'>";
	
		// https://www.wim.tv/wimtv-webapp/rest/users/{username}/profile
		$urlUpdate = get_option("wp_basePathWimtv") . "profile";
		$credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
		
		if ($_POST['wimtvpro_update'] == 'Y'){
			//UPDATE INFORMATION
			
			foreach ($_POST as $key=>$value){		
				if ($value=="")  unset($_POST[$key]);
				//$key = str_replace("Uri","URI",$key);
				$dati[$key] = $value;
			}
  
			unset($dati['wimtvpro_update']);
			unset($dati['submit']);
			
			$jsonValue = json_encode($dati);	
			//var_dump  ($jsonValue);
			$ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $urlUpdate);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json","Accept: application/json"));
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	        curl_setopt($ch, CURLOPT_USERPWD, $credential);
	        curl_setopt($ch, CURLOPT_POST, TRUE);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonValue); 
	        $response = curl_exec($ch);
	        
	        $arrayjsonst = json_decode($response);
	        curl_close($ch);
			if ($arrayjsonst->result=="SUCCESS") {
			
				 echo '<div class="updated"><p><strong>';
	              _e("Update successfully.");
	              echo  '</strong></p></div>';
	
			
			} else {
			
				foreach ($arrayjsonst->messages as $message){
	            		$testoErrore .=  $message->field . " : " .  $message->message . "<br/>";         	
	            }
	            $error++;
	
				echo '<div class="error"><p><strong>' . $testoErrore . '</strong></p></div>';
	
			}
			
			foreach ($dati as $key=>$value){		
				$key = str_replace("URI","Uri",$key);
			}

			

		} 
		
		//Read
			$urlUpdate = get_option("wp_basePathWimtv") . "profile";
			$credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");

			$ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $urlUpdate);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	        curl_setopt($ch, CURLOPT_USERPWD, $credential);
	        $response = curl_exec($ch);
			$dati = json_decode($response, true);
			//var_dump ($dati);
			curl_close($ch);
			
		
		
		switch ($_GET['update']){
		
			case "1": //Payment
			
			  echo '<h2>Payment</h2>';
			  echo str_replace("payment","current",$submenu);

			  echo '<div class="clear"></div>
			  <p>If you wish to make financial transactions on Wim.tv (buy video, sell video content or watch on pay per view), you must complete the following fields. You can choose to store your information now or do it later by clicking the settings button from your personal page.</p>';
			  echo '
			  
			  <form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">
				 <h4>' . __("PayPal") . '</h4>
						<table class="form-table">
					
			              	<tr>
			              		<th><label for="paypalEmail">' . __("Paypal Email") . '</label></th>
								<td><input type="text" id="edit-paypalEmail" name="paypalEmail" value="' . $dati['paypalEmail'] . '" size="100" maxlength="100"/></td>
							</tr>
							
							
						
						</table>

				 
				 <h4>' . __("Tax Info") . '</h4>
						<table class="form-table">
							<tr>
			              		<th><label for="vatCode">' . __("Tax Code") . '</label></th>
								<td><input type="text" id="edit-taxCode" name="taxCode" value="' . $dati['taxCode'] . '" size="80" maxlength="20"/></td>
								<th><label for="vatCode">or ' . __("Vat Code") . '</label></th>
								<td><input type="text" id="edit-vatCode" name="vatCode" value="' . $dati['vatCode'] . '" size="80" maxlength="20"/></td>
							</tr>

						
						</table>
						
				<h4>' . __("Billing Address") . '</h4>
						<table class="form-table">
					
			              								
							<tr>
			              		<th><label for="billingAddress[street]">' . __("Street") . '</label></th>
								<td><input type="text" id="edit-billingAddressStreet" name="billingAddress[street]" value="' . $dati['billingAddress']['street'] . '" size="100" maxlength="100"/></td>
							</tr>

							<tr>
			              		<th><label for="billingAddress[city]">' . __("City") . '</label></th>
								<td><input type="text" id="edit-billingAddressCity" name="billingAddress[city]" value="' . $dati['billingAddress']['city'] . '" size="100" maxlength="100"/></td>
							</tr>

							<tr>
			              		<th><label for="billingAddress[state]">' . __("State") . '</label></th>
								<td><input type="text" id="edit-billingAddressCity" name="billingAddress[state]" value="' . $dati['billingAddress']['state'] . '" size="100" maxlength="100"/></td>
							</tr>
							
							<tr>
			              		<th><label for="billingAddress[zipCode]">' . __("Zip Code") . '</label></th>
								<td><input type="text" id="edit-billingAddressCity" name="billingAddress[zipCode]" value="' . $dati['billingAddress']['zipCode'] . '" size="100" maxlength="100"/></td>
							</tr>

						
						</table>';
						
						echo '<input type="hidden" name="wimtvpro_update" value="Y" />';
						submit_button(__("Update"));

						
			  echo '</form>';
 	
			  /*
			  "paypalEmail": "-- indirizzo email account Pay Pal --",
			  "companyName": "-- nome azienda --",
			  "affiliateConfirm": "-- hai i diritti legali per operare come affiliato dell&#65533;azienza --",
			  "vatCode": "-- P. iva --",
			  "taxCode": "-- CF --",
			  "billingAddress": {
			  	"street": "-- via  --",
			  	"city": "-- citt&#65533; --",
			  	"state": "-- provincia --",
			  	"zipCode": "-- cap --"
			  	}
			  */
			break;
			
			case "2": //Live
			
			  echo '<h2>WimLive Configuration</h2>';
			  echo str_replace("live","current",$submenu);


				if (!isset($dati['liveStreamPwd'])) $dati['liveStreamPwd']= get_option("wp_passwimtv");
				
				
			  echo '<div class="clear"></div>
			  <p>In this section you can enable the more functional live streaming settings for your needs. Choose between "Live streaming" to stream your own events, or use the features reserved for event Resellers and event Organizers to sell and organize live events.</p>';
			  echo '
			  
			  <script>
			  
			  	jQuery(document).ready(function() {
			    
			    	jQuery("#edit-liveStreamEnabled,#edit-eventResellerEnabled,#edit-eventOrganizerEnabled").click(
			    	
			    	function() {
			    		var name = jQuery(this).attr("name");
			    		if (jQuery(this).attr("checked")=="checked") {
			    			jQuery("." + name).remove();
			    		}
			    		else {
			    		
			    			jQuery("<input>").attr({
							    type: "hidden",
							    value: "false",
							    name: name ,
							    class: name ,
							}).appendTo(".hidden_value");
	
			    		}
			    	})
			    
			    });
			  
			  </script>
			  
			  <form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">
				 <table class="form-table">
					
			              								
							<tr>
			              		<th><label for="liveStreamEnabled">' . __("Live streaming") . '</label></th>
								<td>
								  <input type="checkbox" id="edit-liveStreamEnabled" name="liveStreamEnabled" value="true"
								  ';
								  if (strtoupper($dati['liveStreamEnabled'])=="TRUE") echo ' checked="checked"';
								 echo  ' 
								  />
								  <div class="description"> Enables the feature that allows you to broadcast your live streaming events with WimTV. </div>
								</td>
							</tr>
							
							<tr> 
			              		<th><label for="liveStreamPwd">' . __("Password") . '</label></th>
								<td>
								  <input type="password" id="edit-liveStreamPwd" name="liveStreamPwd" value="' . $dati['liveStreamPwd'] .  '"/>
								  <div class="description"> If you do not change the password is the same of credential wimtv </div>
								</td>
							</tr>

							
							<tr>
			              		<th><label for="eventResellerEnabled">' . __("Live stream events resale") . '</label></th>
								<td>
								  <input type="checkbox" id="edit-eventResellerEnabled" name="eventResellerEnabled" value="true"
								  ';
								  if (strtoupper($dati['eventResellerEnabled'])=="TRUE") echo ' checked="checked"';
								 echo '
								  />
								  <div class="description"> Enables you to resell the streaming of live events organized bu other Web TVs. </div>
								</td>
							</tr>
							
							<tr>
			              		<th><label for="eventOrganizerEnabled">' . __("Live stream events organizing") . '</label></th>
								<td>
								  <input type="checkbox" id="edit-eventOrganizerEnabled" name="eventOrganizerEnabled" value="true"
								  ';
								  if (strtoupper($dati['eventOrganizerEnabled'])=="TRUE") echo ' checked="checked"';
								 echo '
								  />
								  <div class="description">  Enables the feature that allows you to organize live streaming events.  </div>
								</td>
							</tr>


						
							
						
						</table>';
						echo '<div class="hidden_value"></div>';
						echo '<input type="hidden" name="wimtvpro_update" value="Y" />';
						submit_button(__("Update")); 

						
			  echo '</form>';

			
				//"liveStreamPwd": "-- pwd per il live di wim.tv --",
  				//"liveStreamEnabled": "-- abilita live true|false --"
  				//eventResellerEnabled": "-- abilita event reselling true|false --",
  				//"eventOrganizerEnabled": "-- abilita event organizing true|false --",
			
			break;
			
			case "3": //Update personal information
			 	echo ' 
		        <script type="text/javascript">
		  		jQuery(document).ready(function(){
		  		  jQuery( ".pickadate" ).datepicker({
		            dateFormat: "dd/mm/y",     });
		  		  				  
		  		});
		  		</script>
		     	';

			
			  echo '<h2>Personal Info</h2>';
			  echo str_replace("user","current",$submenu);
			  
			  echo '<div class="clear"></div>
			  <form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">
			  <h4>Personal Information</h4>
				<table class="form-table">			
					<tr>
						<th><label for="edit-name">' . __("Name") . '<span class="form-required" title="">*</span></label></th>
						<td><input type="text" id="edit-name" name="name" value="' . $dati['name'] . '" size="40" maxlength="200"/></td>
					    <th><label for="edit-Surname">' . __("Surname") . '<span class="form-required" title="">*</span></label></th>				
						<td><input type="text" id="edit-Surname" name="surname" value="' . $dati['surname'] . '" size="40" maxlength="200"/></td>
					</tr>
					<tr>
						<th><label for="edit-Email">Email<span class="form-required" title="">*</span></label></th>
						<td><input type="text" id="edit-email" name="email" value="' . $dati['email'] . '" size="80" maxlength="200"/></td>
					</tr>
					
					<tr>
						<th><label for="sex">Gender<span class="form-required" title="">*</span></label></th>
						<td>
							<select id="edit-sex" name="sex" class="form-select">
								<option value="M"';
								if ( $dati['sex']=="M") echo 'selected="selected"';
								echo '>M</option>
								<option value="F"';
								if ( $dati['sex']=="F") echo 'selected="selected"';
								echo '>F</option>
							</select>
	
						</td>
						
						<th><label for="dateOfBirth">Date Of Birth</label></th>
						<td>
							<input  type="text" class="pickadate" id="edit-giorno" name="dateOfBirth" value="' . $dati['dateOfBirth'] . '" size="10" maxlength="10">		     				
							<div class="description">Date mm/dd/yy</div>
						</td>

						
					</tr>
	
					
				</table>';

			  echo '
			  
			  			  
			  
				 
				 <h4>Your social networks</h4>
				 
				 <table class="form-table">
					
			              								
						<tr>
							<th><label for="facebookUri">Facebook Url</label></th>
							<td>
								<input  type="text"  id="edit-facebookURI" name="facebookUri" value="' . $dati['facebookURI'] . '" size="100" maxlength="100">	
							</td>
						</tr>
						
						<tr>
						
						<tr>
						
						<th><label for="twitterUri">Twitter Url</label></th>
							<td>
								<input  type="text"  id="edit-twitterURI" name="twitterUri" value="' . $dati['twitterURI'] . '" size="100" maxlength="100">	
							</td>

						
						</tr>

						
						<th><label for="linkedInUri">LinkedIn Url</label></th>
							<td>
								<input  type="text"  id="edit-LinkedInUri" name="linkedInUri" value="' . $dati['linkedInURI'] . '" size="100" maxlength="100">	
							</td>

						
						</tr>
						
						
	

						
							
						
				  </table>';
				echo '<div class="hidden_value"></div>';
				echo '<input type="hidden" name="wimtvpro_update" value="Y" />';
				submit_button(__("Update")); 

						
			  echo '</form>';

			
			break;
			
			
			case "4": //Features
			 	echo ' 
		        <script type="text/javascript">
		  		jQuery(document).ready(function(){
		  		  jQuery( "#edit-hidePublicShowtimeVideos" ).change( function(){

		          	if  (jQuery(this).val()=="true") {
				      	jQuery("#viewPage").fadeIn();
				      }else{		      
				      	jQuery("#viewPage").fadeOut();
				      
				      }
 
		          });
		  		  				  
		  		});
		  		</script>
		     	';

			  echo str_replace("other","current",$submenu);
			  
			  echo '<div class="clear"></div>
			  <form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">
		
				<table class="form-table">			
					<tr>
						<th><label for="edit-name">' . __("Index and show public videos into WimTv's site") . '</label></th>
						<td>
							<select id="edit-hidePublicShowtimeVideos" name="hidePublicShowtimeVideos" class="form-select">
								<option value="true"';
								if ( $dati['hidePublicShowtimeVideos']=="true") echo 'selected="selected"';
								echo '>' . __("Yes") . '</option>
								<option value="false"';
								if ( $dati['hidePublicShowtimeVideos']=="false") echo 'selected="selected"';
								echo '>No</option>
							</select>
	
						</td>
						
												
					</tr>
	
					
				</table>';

			  echo '
			  
				 
				 <table id="viewPage"';
				 
				 if ( $dati['hidePublicShowtimeVideos']=="false") echo ' style="display:none; "';
				 
				 echo ' class="form-table">
					
					<tr><td colspan="2"><h4>Page into WimTv</h4></td></tr>			              
								
						<tr>
							<th><label for="pageName">Page Name</label></th>
							<td>
								<input  type="text"  id="edit-pageName" name="pageName" value="' . $dati['pageName'] . '" size="100" maxlength="100">	
							</td>
						</tr>
						

						
						<tr>
						
						<th><label for="pageDescription">Page Description</label></th>
							<td>
								<textarea  type="text" style="width:260px; height:90px;" id="edit-pageDescription" name="pageDescription">' . $dati['pageDescription'] . '</textarea>	
							</td>

						
						</tr>
	
						
				  </table>';
				echo '<div class="hidden_value"></div>';
				echo '<input type="hidden" name="wimtvpro_update" value="Y" />';
				submit_button(__("Update")); 

						
			  echo '</form>';

			
				//"liveStreamPwd": "-- pwd per il live di wim.tv --",
  				//"liveStreamEnabled": "-- abilita live true|false --"
  				//eventResellerEnabled": "-- abilita event reselling true|false --",
  				//"eventOrganizerEnabled": "-- abilita event organizing true|false --",

			
			break;

			
		
		}
	
		echo "</div>";
	
	}	
  }
  
  else {
  	
		
echo "<div class='wrap'>";
		echo "<h2>Pricing";
		if (isset($_GET['return']))  echo "<a href='?page=WimVideoPro_Report' class='add-new-h2'>" . __("Back") . "</a>";
		echo "</h2>";
		
		$credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
		$uploads_info = wp_upload_dir();
		$directoryCookie = $uploads_info["basedir"] .  "/cookieWim";
		if (!is_dir($directoryCookie)) {
		  $directory_create = mkdir($uploads_info["basedir"] . "/cookieWim");
		}
	
		if (isset($_GET['upgrade'])){
		    		    
		    $fileCookie = "cookies_" . get_option("wp_userWimtv") . "_" . $_GET['upgrade'] . ".txt";
		    
            if (!is_file($directoryCookie. "/" . $fileCookie)) {
            	$f = fopen($directoryCookie. "/" . $fileCookie,"w");
				fwrite($f,"");
				fclose($f);
		    }
			//Update Packet
			$data = array("name" => $_GET['upgrade']);                                                                    
			$data_string = json_encode($data);
			
			// chiama
			$ch = curl_init();
			$my_page = admin_url() . "?page=WimTvPro&pack=1&success=" . $_GET['upgrade']; 
		    if (isset($_GET['return'])) $my_page .= "&return=true";

			curl_setopt($ch, CURLOPT_URL,  get_option("wp_basePathWimtv") . "userpacket/payment/pay?externalRedirect=true&success=" . urlencode ($my_page));
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $credential);
			
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			    'Content-Type: application/json', 'Accept-Language: en-US,en;q=0.5',                                                                               
			    'Content-Length: ' . strlen($data_string))                                                                       
			);  
	
	 		// salva cookie di sessione
			curl_setopt($ch, CURLOPT_COOKIEJAR, $directoryCookie . "/" . $fileCookie);	
			$result = curl_exec($ch);
			curl_close($ch);
			$arrayjsonst = json_decode($result);
			
			if ($arrayjsonst->result=="REDIRECT") {
			  echo "
			  	 <script>
					  jQuery(document).ready(function() {
						jQuery.colorbox({
						    onLoad: function() {
				        		jQuery('#cboxClose').remove();
				        	},
				        	html:'<h2>" . $arrayjsonst->message . "</h2><h2><a href=\"" . $arrayjsonst->successUrl . "\">Yes</a> | <a onClick=\"jQuery(this).colorbox.close();\" href=\"#\">" . __("No") . "</a></h2>'
				        })
				     });   		
                 </script> 
                  ";
                 
			}  else {
			
				//var_dump($arrayjsonst);
			
			}

		
		}
		
		
		if (isset($_GET['success'])) {	

			//controlla stato pagamento
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, get_option("wp_basePathWimtv") . "userpacket/payment/check");
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: en-US,en;q=0.5'));
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $credential);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			
			$fileCookie = "cookies_" . get_option("wp_userWimtv") . "_" . $_GET['success'] . ".txt";
			
			// Recupera cookie sessione
			curl_setopt($ch, CURLOPT_COOKIEFILE,  $directoryCookie . "/" . $fileCookie);
			
			$result = curl_exec($ch);
			curl_close($ch);
			$arrayjsonst = json_decode($result);
			
			
			
			
		
		}
		
		if (!isset($_GET['return'])){
			
			echo str_replace("packet","current",$submenu) ; 
		}
		$url_packet_user = get_option("wp_basePathWimtv") . "userpacket/" . get_option("wp_userWimtv");

		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url_packet_user);
	   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: en-US,en;q=0.5'));
	    curl_setopt($ch, CURLOPT_VERBOSE, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	
	    $response = curl_exec($ch);
	    $packet_user_json = json_decode($response);
	    //var_dump ($response);
    	$id_packet_user = $packet_user_json->id;
    	$createDate_packet_user = $packet_user_json->createDate;
		$updateDate_packet_user = $packet_user_json->updateDate;
		
		$createDate = date('d/m/Y', $createDate_packet_user/1000);
		$updateDate = date('d/m/Y', $updateDate_packet_user/1000);
		$dateRange = getDateRange($createDate , $updateDate );
		
		$count_date = $packet_user_json->daysLeft;
		//$count_date = count($dateRange)-1;
 		
	    curl_close($ch);


		$url_packet = get_option("wp_basePathWimtv") . "commercialpacket"; 

	  	$header = array("Accept-Language: en-US,en;q=0.5");


		$ch2 = curl_init();
	    curl_setopt($ch2, CURLOPT_URL, $url_packet);
	    curl_setopt($ch2, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch2, CURLOPT_VERBOSE, 0);
	    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch2, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

	    $response2 = curl_exec($ch2);
	    
	    //$info = curl_getinfo($ch2);


	    $packet_json = json_decode($response2);
	    

	    
	    curl_close($ch2);
	    //var_dump ($response2);

		echo "<table class='wp-list-table widefat fixed pages'>";
	    echo "<thead><tr><th></th>";
	    foreach ($packet_json -> items as $a) {
	    	
	    	echo "<th><b>" . $a->name . "</b></th>";	
	    
	    }

	    echo "</thead>";
	    echo "<tbody>";
		echo "<tr class='alternate'>";
			echo "<td>" . __("Band") . "</td>";
			foreach ($packet_json -> items as $a) {
	    	echo "<td>" . $a->band . " GB</td>";		    
	    	}

		echo "</tr>";
		
		echo "<tr>";
			echo "<td>" . __("Storage") . "</td>";
			foreach ($packet_json -> items as $a) {
	    	echo "<td>" . $a->storage . " GB</td>";		    
	    	}

		echo "</tr>";
		
		echo "<tr class='alternate'>";
			echo "<td>" . __("Support") . "</td>";
			foreach ($packet_json -> items as $a) {
	    	echo "<td>" . $a->support . "</td>";		    
	    	}

		echo "</tr>";

	
		echo "<tr>";
			echo "<td>" . __("Price") . "</td>";
			foreach ($packet_json -> items as $a) {
	    	echo "<td>" . number_format($a->price,2) . " &euro; / month</td>";		    
	    	}

		echo "</tr>";
		
		echo "<tr class='alternate'>";
			echo "<td></td>";
			foreach ($packet_json -> items as $a) {
	    	//echo "<td>" . $a->dayDuration . " - " . $a->id . "</td>";
	    	echo "<td>";
	    	if ($id_packet_user==$a->id) {
	    		
	    		echo "<img  src='" . plugins_url('images/check.png', __FILE__) . "' title='Checked'><br/>";
	    		echo $count_date . " " . __("day left");
	    	}
	    	else {
	    		echo "<a href='?page=WimTvPro&pack=1";
	    		if (isset($_GET['return'])) echo "&return=true";
		    	echo "&upgrade=" . $a->name;
		    	echo "'><img class='icon_upgrade' src='" . plugins_url('images/uncheck.png', __FILE__) . "' title='Upgrade'>";
		    	echo "</a>";
			}
			echo "</td>"; 		    
	    }

		echo "</tr>";


	
		echo "</tbody>";
		echo "</table>";
		
		echo "<h3>You have a free trial of 30 days to try the WimTVPro plugin.</h3> 
<h3>After 30 days you can subscribe a plan that suit your needs.</h3>
<h3>All plans come with all features, only changes the amount of bandwidth and storage available.</h3>
<h3>Enyoy your WimTVPro video plugin!</h3>
";
		
		
		echo "</div>";
	}
	
}

 

function media_wimtvpro_process() {
  media_upload_header();
  
  $videos .= "<h3 class='media-title'>My Streaming</h3><ul class='itemsInsert'>" . wimtvpro_getThumbs(TRUE, FALSE, TRUE) . "</ul><div class='empty'></div>";
  
  global $wpdb; 
  $table_name = $wpdb->prefix . 'wimtvpro_playlist';
  $array_playlist = $wpdb->get_results("SELECT * FROM {$table_name} WHERE uid='" . get_option("wp_userwimtv") . "'  ORDER BY name ASC");
  $numberPlaylist=count($array_playlist);
  if ($numberPlaylist>0) {
    $videos .= "<h3 class='media-title'>My PlayList</h3><ul class='itemsInsert'>";
    foreach ($array_playlist as $record_new) {
	    $listVideo = $record_new->listVideo;
	    $arrayVideo = explode(",", $listVideo);
	    if ($listVideo=="") $countVideo = 0;
	    else $countVideo = count($arrayVideo);
	    
	    $uploads_info = wp_upload_dir();
    	$directory = $uploads_info["baseurl"] .  "/skinWim";
    	$nameFile = "/playlist_" .  $record_new->id . ".xml";

  		$videos .='<li><b>' . $record_new->name .  ' (<span class="counter">' . $countVideo . '</span>)</b>';
  		$doc = simplexml_load_file($directory . $nameFile);
		$sxe = new SimpleXMLElement($doc->asXML());
		$channel = $sxe->channel;
		$playlist = "<br/>Videos:<br/>";
		foreach ($channel->item as $items) {
		  $playlist .= " - " . $items->title . " - <br/>";
		}

  		$videos .= $playlist;
  		
  		$videos .= '<input type="hidden" value="' . $_GET['post_id'] . '" name="post_id">';
        $send = get_submit_button( __( 'Insert into Post' ), 'buttonInsertPlayList', $record_new->id, false );

  		
  		$videos .= $send . '</li>';

  			}
}

  $videos .= "</ul><div class='empty'></div>";
  echo $videos;

}
function wimtvpro_media_menu_handle() {
    return wp_iframe( 'media_wimtvpro_process');
}
add_action('media_upload_wimtvpro', 'wimtvpro_media_menu_handle');


?>
