<?php

function wimtvpro_registration(){


	if ($_POST['wimtvpro_registration'] == 'Y'){
	
		if ($_POST['sandbox']=="No") {
          	update_option( 'wp_basePathWimtv','https://www.wim.tv/wimtv-webapp/rest/');
        } else {
          	update_option( 'wp_basePathWimtv','http://peer.wim.tv:8080/wimtv-webapp/rest/');
        }

    
   		$error = 0;
		
		if ($_POST['reg_RepeatPassword']!=$_POST['reg_Password']){
			$error ++;
			$testoErrore .= "Password isn't same<br/>";
			$_POST['reg_RepeatPassword'] = "";
			$_POST['reg_Password'] = "";
		}		
		if (($error==0) && (isset($_POST['reg_acceptEula'])) && ($_POST['reg_name']!="") && ($_POST['reg_Surname']!="") && ($_POST['reg_Email']!="") && ($_POST['reg_Username']!="") && ($_POST['reg_Password']!="") && ($_POST['reg_RepeatPassword']!="")  && ($_POST['reg_sex']!="")) {
		
			$ch = curl_init();
            $url_reg = get_option("wp_basePathWimtv") . 'register';
            curl_setopt($ch, CURLOPT_URL, $url_reg);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json","Accept: application/json","Accept-Language: en-US,en;q=0.5"));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_POST, TRUE);
             $post = '{"acceptEula":"' . $_POST['reg_acceptEula'] . '","name":"' . $_POST['reg_name'] . '","surname":"' . $_POST['reg_Surname'] . '","email":"' . $_POST['reg_Email'] . '"';
			$post .= ',"username":"' . $_POST['reg_Username'] . '","password":"' . $_POST['reg_Password'] . '"';
			$post .= ',"role":"webtv","sex":"' . $_POST['reg_sex'] . '","dateOfBirth":"01/01/1900"}';

			curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
            $response = curl_exec($ch);
            curl_close($ch);

            $arrayjsonst = json_decode($response);
      
            if ($arrayjsonst->result=="SUCCESS") {
         
              
               echo '
              <script type="text/javascript">
				<!--
				window.location = "admin.php?page=WimTvPro&pack=1";
				//-->
				</script>';
              echo '<div class="updated"><p><strong>';
              _e("Registration successfully.");
              echo  '</strong></p></div>';
              update_option('wp_userwimtv', $_POST['reg_Username']);
          	  update_option('wp_passwimtv', $_POST['reg_Password']);
          	  
          	  update_option('wp_registration', 'TRUE');
          	  
          	  
          	  

            } else {
            
            	foreach ($arrayjsonst->messages as $message){
            		$testoErrore .=  $message->field . " : " .  $message->message . "<br/>";         	
            	}
            	$error++;
            
            }


		
		} else {
		
			$error++;
			 $testoErrore .= "You are not compiled all field required";
		
		}
		
		
		if ($error>0) {
			$styleReg = "display:block";
			echo '<div class="error"><p><strong>' . $testoErrore . '</strong></p></div>';
		
		}
		
    
    }
	echo " <div class='wrap'><h2>Registration WimTv</h2>";
	echo "</div>";

?>
	<form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">
		
		<h4>Personal Information</h4>
			<table class="form-table">			
				<tr>
					<th><label for="edit-name">Name<span class="form-required" title="">*</span></label></th>
					<td><input type="text" id="edit-name" name="reg_name" value="<?php echo $_POST['reg_name'];?>" size="40" maxlength="200"/></td>
				</tr>
				<tr>
					<th><label for="edit-Surname">Surname<span class="form-required" title="">*</span></label></th>				
					<td><input type="text" id="edit-Surname" name="reg_Surname" value="<?php echo $_POST['reg_Surname'];?>" size="40" maxlength="200"/></td>
				</tr>
				<tr>
					<th><label for="edit-Email">Email<span class="form-required" title="">*</span></label></th>
					<td><input type="text" id="edit-Email" name="reg_Email" value="<?php echo $_POST['reg_Email'];?>" size="80" maxlength="200"/></td>
				</tr>
				
				<tr>
					<th><label for="edit-sex">Gender<span class="form-required" title="">*</span></label></th>
					<td>
						<select id="edit-sex" name="reg_sex" class="form-select">
							<option value="M" <?php if ( $_POST['reg_sex']=="M") echo "selected='selected'" ?>>M</option>
							<option value="F" <?php if ( $_POST['reg_sex']=="F") echo "selected='selected'" ?>>F</option>
						</select>

					</td>
				</tr>

				
			</table>	

		<h4>Login Credentials</h4>
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
				</tr-->
				
				<tr>
					<th><label for="edit-name">Username<span class="form-required" title="">*</span></label></th>
					<td><input type="text" id="edit-Username" name="reg_Username" value="<?php echo $_POST['reg_Username'];?>" size="30" maxlength="200"/></td>
				</tr>
				
				<tr>
					<th><label for="edit-Password">Password<span class="form-required" title="">*</span></label></th>
					<td><input type="password" id="edit-Password" name="reg_Password" value="<?php echo $_POST['reg_Password'];?>" size="30" maxlength="200"/></td>
				</tr>
				
				<tr>
					<th><label for="edit-repPassword">Repeat Password<span class="form-required" title="">*</span></label></th>
					<td><input type="password" id="edit-repPassword" name="reg_RepeatPassword" value="<?php echo $_POST['reg_RepeatPassword'];?>" size="30" maxlength="200"/></td>
				</tr>
				
				<tr>
					<?php
					if ($_POST['sandbox']=="No") {
			          	$baseWimtv = "https://www.wim.tv/";
			          } else {
			          	$baseWimtv = "http://peer.wim.tv:8080/";
			          }

					
					?>
					<th><label for="edit-acceptEula">Terms of Use<span class="form-required" title="">*</span></label></th>
					<td>
						<div class="description"><input type="checkbox" id="edit-acceptEula" name="reg_acceptEula" value="true" <?php if (isset($_POST['reg_acceptEula'])) echo "checked='checked'"; ?>/> I have read and agree to the wim.tv&reg; 
						<a class="termsLink" href="<?php echo $baseWimtv; ?>wimtv-webapp/term.do">Terms of Service</a> 
						and
						<a class="termsLink" href="<?php echo $baseWimtv; ?>wimtv-webapp/privacy.do">Privacy Policies</a></div>	
					</td>
				
				</tr>
				
			</table>
			
			<input type="hidden" name="wimtvpro_registration" value="Y" />
			<?php submit_button(__("Register")); ?>
		
			
	</form>
	
	
<?php

}

?>