<?php

//Page for view My Media
function wimtvpro_mymedia (){
    echo ' <script type="text/javascript"> jQuery(document).ready(function(){
    jQuery("a.viewThumb").click( function(){
    var url = jQuery(this).attr("id");
    jQuery(this).colorbox({href:url});
    });}); </script>';

   	echo " <div class='wrap'><h2>My Media</h2>";
   	$title = "<div class='action'><span class='icon_sync0' title='Syncronize'>Syncronize</span></div>";
	$getThumbs = $title . "<ul class='items' id='FALSE'>" . wimtvpro_getThumbs(FALSE) . "</ul>"; 
	echo $getThumbs;
	echo "</div>";

}
   
//Page for view My Video streaming  
function wimtvpro_mystreaming(){
   
  echo ' <script type="text/javascript">
  jQuery(document).ready(function(){ 

  /*SORTABLE*/      						
  jQuery( ".items" ).sortable({
  placeholder: "ui-state-highlight",
  handle : ".icon_moveThumbs",		
  });

  /*SAVE SORTABLE*/	  								
  jQuery("#save").click(function(){
  var ordina =	jQuery(".items").sortable("toArray") ;

  jQuery.ajax({
  context: this,
  url:  url_pathPlugin + "scripts.php",
  type: "GET",
  dataType: "html",
  data: "namefunction=ReSortable&ordina=" + ordina , 

  beforeSend: function(){ 
  jQuery(".icon").hide(); 
  jQuery(".loader").show(); 
  },
    
  success: function(data) {

  jQuery(".icon").show(); 
  jQuery(".loader").hide();

  },

  error: function(request,error) {
  alert(request.responseText); 
  }	



  });	
  });	

  });

    jQuery(document).ready(function(){
    jQuery("a.viewThumb").click( function(){
    var url = jQuery(this).attr("id");
    jQuery(this).colorbox({href:url});
    });}); </script>';

   	echo "<div class='wrap'><h2>My Streaming</h2>";
   	$title = "<div id='poststuff'><div class='action'>
   	<span class='icon_sync0' title='Syncronize'>Syncronize</span>";
   	$user = wp_get_current_user();
	$idUser = $user->ID;
	$userRole = $user->roles[0];
   	if ($user->roles[0] == "administrator"){
   	  $title .= "<span class='icon_save' id='save'>Save</span>";
	}
	$getThumbs =   $title . "</div>
		<div id='post-body' class='metabox-holder columns-2'>
			<div id='post-body-content'> 
				<ul class='items' id='TRUE'>" . wimtvpro_getThumbs(TRUE) . "</ul>
			</div>
		</div>"; 
	echo $getThumbs;
	echo "</div></div>";
	
	
	echo '<div class="postbox-container">
        <div class="metabox-holder">
            <div class="meta-box-sortables">
                <div class="postbox" id="first">
                    <h3><span>PlayList</span></h3>
                    <div class="inside">';
    
	//Count playlist saved in DB
	/*
	global $wpdb; 
    $table_name = $wpdb->prefix . 'wimtvpro_playlist';
	$array_playlist = $wpdb->get_results("SELECT * FROM {$table_name} WHERE uid='" . get_option("wp_userwimtv") . "' AND position<>0 " . $sql_where . " ORDER BY name ASC");
  	$count=count($array_playlist);
  	if ($count>0) {
    	foreach ($array_playlist as $record_new) {
      		echo '<div class="playlist" id="playlist_' . $count . '" rel="' . $record_new["id"] . '"><span class="title">' . $record_new["name"] .  '<span class="icon_modTitlePlay"></span></div>';
    	}
  	}

   
    $count +=1;                
    echo '<div class="playlist selected" id="playlist_' . $count . '" rel=""><input type="text" value="Playlist ' . $count .  '" /><span class="icon_selectPlay"></span><span class="icon_createPlay"></span></div>';
*/
	echo "<p>Cooming soon</p>";
   
	 echo '</div>
    </div>
                
            </div>
        </div>
    </div>';
	
	echo "</div>";

}


//Page for view for UPLOAD new Video  
function wimtvpro_upload(){

    if ($_POST['wimtvpro_upload']=="Y") {
    
    	$uploads_info = wp_upload_dir();
        $directory = $uploads_info["basedir"];
    	$error = 0;
        //Upload Skin
        $urlfile = @$_FILES['videoFile']['tmp_name'];
        $titlefile = $_POST['titlefile'];
        $descriptionfile = $_POST['descriptionfile'];
        $video_category = $_POST['videoCategory'];

        // Required
        if (strlen(trim($titlefile))==0) {  
           echo '<div class="error"><p><strong>';
           _e("You must write a title.");
           echo '</strong></p></div>';
           $error ++;
        }
      
        if ((strlen(trim($urlfile))>0) && ($error==0)) {
            global $user,$wpdb;  
			
			
			
			$unique_temp_filename = $directory .  "/" . time() . '.' . preg_replace('/.*?\//', '',"tmp");
        	$unique_temp_filename = str_replace("\\" , "/" , $unique_temp_filename);
        	if (@move_uploaded_file( $urlfile , $unique_temp_filename)) {
        		//echo "copiato";
        	}else{
        		echo "non copiato";
        	}
        	

            $credential = get_option("wp_userwimtv") . ":" . get_option("wp_passwimtv");
            $table_name = $wpdb->prefix . 'wimtvpro_video';

        	//UPLOAD VIDEO INTO WIMTV
        	set_time_limit(0);
        	//connect at API for upload video to wimtv
            $ch = curl_init();
            $url_upload = get_option("wp_basePathWimtv") . 'videos';
            curl_setopt($ch, CURLOPT_URL, $url_upload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $credential);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
            //add category/ies (if exist)
            $category_tmp = array();
            $subcategory_tmp = array();
            $post= array("file" => "@" . $unique_temp_filename,"title" => $titlefile,"description" => $descriptionfile);
            if (isset($video_category)) {
              $id=0;
              foreach ($video_category as $cat) {
                $subcat = explode("|", $cat);
                $post['category[' . $id . ']'] = $subcat[0];
                $post['subcategory[' . $id . ']'] = $subcat[1];
                $id++;
              }
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
            $response = curl_exec($ch);
            curl_close($ch);
            $arrayjsonst = json_decode($response);
            if (isset($arrayjsonst->contentIdentifier)) {
              echo '<div class="updated"><p><strong>';
              _e("Upload successfully.");
              echo  '</strong></p></div>';
              $wpdb->insert( $table_name, 
            	array (
            	  'uid' => get_option("wp_userwimtv"),
            	  'contentidentifier' => $arrayjsonst->contentIdentifier,
            	  'mytimestamp' => time(),
            	  'position' => '0',
            	  'state' => '',
            	  'viewVideoModule' => '3',
            	  'status' => 'OWNED',
            	  'acquiredIdentifier' => '',
            	  'urlThumbs' => '',
            	  'category' =>  '',
            	  'title' => $titlefile ,
            	  'duration' => '',
            	  'showtimeidentifier' => ''
            	 )
           	  );
           } 
           else{
             $error ++;
             echo '<div class="error"><p><strong>';
             _e("Upload error.");
             echo  $response  . '</strong></p></div>';

           }
    
        } else {
           $error ++;
           echo '<div class="error"><p><strong>';
           _e("You must upload a file.");
           echo '</strong></p></div>';
        }
    }

	echo "<div class='wrap'><h2>Upload Video</h2>";
	$category="";
	$url_categories = get_option("wp_basePathWimtv") . "videoCategories";

	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url_categories);

    curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
     
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    $category_json = json_decode($response);
    $category = array();

    foreach ($category_json as $cat) {
      foreach ($cat as $sub) {
        $category .= '<optgroup label="' . $sub->name . '">';
        foreach ($sub->subCategories as $subname) {
          $category .= '<option value="' . $sub->name . '|' . $subname->name . '">' . $subname->name . '</option>';
        }
        $category .= '</optgroup>';
      }
    }
    curl_close($ch);
?>

    <form enctype="multipart/form-data" action="#" method="post" id="wimtvpro-upload" accept-charset="UTF-8"><div><div class="form-item form-type-textfield form-item-titlefile">
      
      <p><label for="edit-titlefile">Title Video <span class="form-required" title="Questo campo &#65533; obbligatorio.">*</span></label>
      <input type="text" id="edit-titlefile" name="titlefile" value="" size="100" maxlength="200" class="form-text required" /></p>
      <p><label for="edit-descriptionfile">Description Video </label><br/>
      <textarea id="edit-descriptionfile" name="descriptionfile" cols="150" rows="5"></textarea></p>
      
      <p><label for="edit-videofile">Upload video <span class="form-required" title="Questo campo &#65533; obbligatorio.">*</span></label>
      <input onchange="wimtvpro_TestFileType()" type="file" id="edit-videofile" name="videoFile" size="60" class="form-file required" />
	  Pick a video file to upload.</p>

      <p><label for="edit-videocategory">Category-Subcategory </label><br/>
      <select onchange="viewCategories(this);" multiple="multiple" name="videoCategory[]" id="edit-videocategory" size="15" class="form-select"><?php echo $category; ?></select>
      <br/>(Multiselect with CTRL)</p>

      <p class='description' id='addCategories'></p>
      <input type="hidden" name="wimtvpro_upload" value="Y" />
      <?php submit_button(); ?>
    </form>

<?php
  echo "</div>";
}


function wimtvpro_live(){
  $noneElenco = FALSE;
  $userpeer = get_option("wp_userWimtv");
  $url_live =  get_option("wp_basePathWimtv") . "liveStream/" . $userpeer . "/" . $userpeer . "/hosts";
  $credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");

  

  switch ($_GET['namefunction']) {
     case "addLive":
     
       $noneElenco = TRUE;
       //aggiungere script per pickdata e pickhour
       if (isset($_POST["wimtvpro_live"])) {
          wimtvpro_savelive("insert");
       }
       $name = "";
       $payperview = "0";
       $url = "";
       $giorno = "";
       $ora = "00:00";
       $tempo = "00:00";
     
     break;
     case "modifyLive":
     
       $noneElenco = TRUE;

	   if (isset($_POST["wimtvpro_live"])) {
          wimtvpro_savelive("modify");
       }

       
       //Recove dates live
       $url_live .= "/" . $_GET['id'] . "/embed";
       $ch_embedded = curl_init();
       curl_setopt($ch_embedded, CURLOPT_URL, $url_live);
       curl_setopt($ch_embedded, CURLOPT_VERBOSE, 0);
       curl_setopt($ch_embedded, CURLOPT_RETURNTRANSFER, TRUE);
       curl_setopt($ch_embedded, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
       curl_setopt($ch_embedded, CURLOPT_USERPWD, $credential);
       curl_setopt($ch_embedded, CURLOPT_SSL_VERIFYPEER, FALSE);
       $dati = curl_exec($ch_embedded);
       $arraydati = json_decode($dati);
       $name = $arraydati->name;
       if ($arraydati->paymentMode=="FREEOFCHARGE") 
        $payperview = "0";
       else
        $payperview =  $arraydati->pricePerView;
       $url = $arraydati->url;
       $giorno = $arraydati->eventDate;
       $ora = $arraydati->eventHour . ":" . $arraydati->eventMinute;
       $tempo = $arraydati->duration;
       $ore = floor($tempo / 60);
       $minuti = $tempo % 60;
       $durata = $ore . "h" . $minuti;
    break;
     
    case "deleteLive":
      $url_live .= "/" . $_GET['id'];
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url_live);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, $credential);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      $response = curl_exec($ch);
      //echo $response;
      curl_close($ch);   
     break;	
	
     default:
      break;
  }
  
  global $wpdb;
  $post_id  = $wpdb->get_var("SELECT max(ID) FROM $wpdb->posts WHERE post_name = 'wimlive_wimtv'");
  $my_streaming_wimtv= array();
  $my_streaming_wimtv['ID'] = $post_id;
  $my_streaming_wimtv['post_content'] = wimtvpro_elencoLive("video", "0") . "<br/>UPCOMING EVENT<br/>" . wimtvpro_elencoLive("list", "0");
  wp_update_post($my_streaming_wimtv);

  
  if ($noneElenco==FALSE) {
    global $post_type_object;
    $screen = get_current_screen();
    echo " <div class='wrap'><h2>Wim Live";
   	echo " <a href='" . $_SERVER['REQUEST_URI'] . "&namefunction=addLive' class='add-new-h2'>" . __( 'Add' ) . " " . __( 'Live' ) . "</a> ";
    echo "</h2>";
    echo "<table class='wp-list-table widefat fixed pages'>";
    echo "<thead><tr><th>Name</th><th>Pay-Per-View</th><th>URL</th><th>Streaming</th><th>Embed Code</th><th></th></tr></thead>";
    echo "<tbody>";

    echo wimtvpro_elencoLive("table", "all");
    echo "</thead></table>";
    echo "</div>";

    
  } else {
     //aggiungere script per richiamare la CREATE URL
      echo ' 
        <script type="text/javascript">
  		jQuery(document).ready(function(){
  		  jQuery(document).ready(function(){jQuery( ".pickatime" ).timepicker({  defaultTime:"00:00"  });});
  		  jQuery(document).ready(function(){jQuery( ".pickaduration" ).timepicker({   defaultTime:"00h05",showPeriodLabels: false,timeSeparator: "h", });});});
  		  jQuery(document).ready(function(){jQuery( ".pickadate" ).datepicker({
            dateFormat: "dd/mm/yy",
            autoSize: true,
            minDate: 0,
          });});
  		</script>
     ';
     echo "<div class='wrap'><h2>Wim Live";
   	 echo "<a href='" . $_SERVER['REQUEST_URI'] . "&namefunction=listLive' class='add-new-h2'>" . __( 'Return' ) . " " . __( 'Live' ) . "</a> ";
	 echo "</h2>";
	 ?>
	 <form action="#" method="post" id="wimtvpro-wimlive-form" accept-charset="UTF-8">
	 
	 <p><label for="edit-name">Title <span>*</span></label>
     <input type="text" id="edit-name" name="name" value="<?php echo $name;?>" size="100" maxlength="200"></p>
     <div class="description">Name of the event</div>
     
     <p><label for="edit-payperview">Set the access event *</label>
     <input type="text" id="edit-payperview" name="payperview" value="<?php echo $payperview;?>" size="10" maxlength="5" class="form-text required"></p>
     <div class="description">0 as free of charge or you can decide the price of access for each viewer (in &euro;).</div>

     <p><label for="edit-url">Url *</label>
     <input type="text" id="edit-url" name="Url" value="<?php echo $url;?>" size="100" maxlength="800" class="form-text required">
     </p>
     <div class="description">URL through which the streaming can be done.  
       <b class="createUrl">CREATE YOUR URL</b>
       <b id="'<?php echo get_option("wp_userWimtv");?>'" class="removeUrl">REMOVE YOUR URL</b>
       <br><div class="passwordUrlLive">Password Live is missing, insert a password for live streaming: <input type="password" id="passwordLive"> <b class="createPass">Salva</b>
       </div>
     </div>

     <p><label for="edit-giorno">Data *</label>
     <input  type="text" class="pickadate" id="edit-giorno" name="Giorno" value="<?php echo $giorno;?>" size="10" maxlength="10"></p>
     <div class="description">Date of the event mm/dd/yy</div>

     <p><label for="edit-ora">Start time *</label>
     <input class="pickatime" type="text" id="edit-ora" name="Ora" value="<?php echo $ora;?>" size="10" maxlength="10"></p>
     <div class="description">We recommend applying a tolerance on the advance to facilitate payment transactions to the spectators.</div>

     <p><label for="edit-duration">Duration *</label>
     <input class="pickaduration" type="text" id="edit-duration" name="Duration" value="<?php echo $durata;?>" size="10" maxlength="10">
     <div class="description">Event duration.</div>
     <input type="hidden" name="wimtvpro_live" value="Y" />
     <?php submit_button(); ?>

  </form>
	 
  <?php
  

  
  }
  
}


?>