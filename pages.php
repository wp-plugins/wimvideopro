<?php

//Page for view My Media
function wimtvpro_mymedia (){
    echo ' <script type="text/javascript"> jQuery(document).ready(function(){
    jQuery("a.viewThumb").click( function(){
    var url = jQuery(this).attr("id");
    jQuery(this).colorbox({href:url});
    });
    
    jQuery("a.wimtv-thumbnail").click( function(){
    if( jQuery(this).parent().children(".headerBox").children(".icon").children("a.viewThumb").length  ) {
		var url = jQuery(this).parent().children(".headerBox").children(".icon").children("a.viewThumb").attr("id");
		jQuery(this).colorbox({href:url});
	}
    });
    
    }); 
    
    
    </script>';

   	echo " <div class='wrap'><h2>My Media</h2>";
   	echo "<p>Here are stored all video uploaded. If you want to publish on your site one of these videos, move it in My Streaming</p>";
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
       });
       jQuery("a.wimtv-thumbnail").click( function(){
      if( jQuery(this).parent().children(".headerBox").children(".icon").children("a.viewThumb").length  ) {
		var url = jQuery(this).parent().children(".headerBox").children(".icon").children("a.viewThumb").attr("id");
		jQuery(this).colorbox({href:url});
	  }

     }); 
    
    </script>';

   	echo "<div class='wrap'><h2>My Streaming</h2>";
   	echo "<p>Here you can manage the videos you want to publish on the web pages, both in posts and widgets</p>";
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
                    <p>Create a playlist of videos to be<br/>inserted within your website</p>
                    <div class="inside">';
    
	//Count playlist saved in DB
	
	global $wpdb; 
    $table_name = $wpdb->prefix . 'wimtvpro_playlist';
	$array_playlist = $wpdb->get_results("SELECT * FROM {$table_name} WHERE uid='" . get_option("wp_userwimtv") . "'  ORDER BY name ASC");
  	$numberPlaylist=count($array_playlist);
  	$count = 1;
  	if ($numberPlaylist>0) {
    	foreach ($array_playlist as $record_new) {
    	
    	    $listVideo = $record_new->listVideo;
    	    $arrayVideo = explode(",", $listVideo);
    	    if ($listVideo=="") $countVideo = 0;
    	    else $countVideo = count($arrayVideo);
      		echo '<div class="playlist" id="playlist_' . $count . '" rel="' . $record_new->id . '"><span class="icon_selectPlay"></span><input class="title" type="text" value="' . $record_new->name .  '"/>(<span class="counter">' . $countVideo . '</span>)<span class="icon_deletePlay"></span><span class="icon_modTitlePlay"></span>';
      		echo '<span class="icon_viewPlay"></span>';
      		echo '</div>';
    		$count +=1;
    	}
  	}
              
    echo '<div class="playlist new" id="playlist_' . $count . '" rel=""><span class="icon_selectPlay" style="visibility:hidden"></span><input type="text" value="Playlist ' . $count .  '" /><span class="icon_createPlay"></span></div>';

	
   
	 echo '</div>
    </div>
                
            </div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div><div class="clear"></div>';
	
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
            //$url_upload = "http://192.168.31.200:8082/wimtv-webapp/rest/videos";
            //$credential = "albi:12345678";
            curl_setopt($ch, CURLOPT_URL, $url_upload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $credential);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            //add category/ies (if exist)
            $category_tmp = array();
            $subcategory_tmp = array();
            
            $post= array("file" => "@" . $unique_temp_filename,"title" => $titlefile,"description" => $descriptionfile);
             /*$post= array(
                'file' => array(CURLFORM_FILENAME => $_FILES['videoFile']['name'], CURLFORM_FILE
=> $unique_temp_filename),
           
            	"title" => $titlefile,
            	"description" => $descriptionfile
            );*/
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
              unlink($unique_temp_filename);
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
            	  'urlPlay' => '',
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
      <?php submit_button("Upload"); 
      echo "Do not leave this page until the file upload is not terminated";
      ?>
      
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
  /*
  global $wpdb;
  $post_id  = $wpdb->get_var("SELECT max(ID) FROM $wpdb->posts WHERE post_name = 'wimlive_wimtv'");
  $my_streaming_wimtv= array();
  $my_streaming_wimtv['ID'] = $post_id;
  $my_streaming_wimtv['post_content'] = wimtvpro_elencoLive("video", "0") . "<br/>UPCOMING EVENT<br/>" . wimtvpro_elencoLive("list", "0");
  wp_update_post($my_streaming_wimtv);
*/
  
  if ($noneElenco==FALSE) {
    global $post_type_object;
    $screen = get_current_screen();
    echo " <div class='wrap'><h2>Wim Live";
   	echo " <a href='" . $_SERVER['REQUEST_URI'] . "&namefunction=addLive' class='add-new-h2'>" . __( 'Add' ) . " " . __( 'Live' ) . "</a> ";
    echo "</h2>";
    echo "<p>Here you can create live streaming events to be published on the pages of the site.<br/>To use this service you must have installed on your pc a video encoding software (e.g.  Adobe Flash Media Live Encoder, Wirecast etc.)</p>";
  	echo "<p><b>REMEMBER: for use this functionality you need to enable \"Live Transmission\" on your personal page</b></p>";

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
	 echo "<p>Here you can create live streaming events to be published on the pages of the site.<br/>To use this service you must have installed on your pc a video encoding software (e.g.  Adobe Flash Media Live Encoder, Wirecast etc.)</p>";
  	  echo "<p>REMEMBER: for use this functionality you need to enable \"Live Transmission\" on your personal page on wim.tv</p>";

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

	 <p> <label for="edit-url">Event Public or Private *</label><br/>
     	Public <input type="radio" name="Public" value="true" checked="checked"/> |
     	Private <input type="radio" name="Public" value="false"/>
     </p>
     	
     	 <p> <label for="edit-record">Record event</label><br/>
     	I want to record <input type="radio" name="Record" value="true" checked="checked"/> |
     	I don't want to record <input type="radio" name="Record" value="false"/>
     </p>



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


function wimtvpro_report (){
  global $user,$wpdb;

   
   $table_name = $wpdb->prefix . 'wimtvpro_video';
	
	if (get_option("wp_sandbox")=="No")
		$baseReport = "http://www.wim.tv:3131/api/";
	else
		$baseReport = "http://peer.wim.tv:3131/api/";
	$megabyte = 1024*1024;
	
	
	
	if ((isset($_POST['from'])) && (isset($_POST['to'])) && (trim($_POST['from'])!="") && (trim($_POST['to'])!="")) {
		$from = $_POST['from'];
		$to = $_POST['to'];
		//convert to  (YYYY-MM-DD)
		$current_month=FALSE;
		list($day_from, $month_from, $year_from) = explode('/',$from);
		//$from_tm = mktime(0, 0, 0, $month, $day, $year);
		list($day_to, $month_to, $year_to) = explode('/',$to);
		//$to_tm = mktime(0, 0, 0,  $month, $day, $year);
		
		$from_tm = strtotime( $year_from . "-" . $month_from . "-" . $day_from . " 00:00:00.00")*1000;
		$to_tm = strtotime( $year_to . "-" . $month_to . "-" . $day_to . " 00:00:00.00")*1000;
		
		$from_dmy =$month_from . "/" . $day_from . "/" . $year_from;
		$to_dmy= $month_to . "/" . $day_to . "/" . $year_to;

	} else {
		$current_month=TRUE;
		
		$d = new DateTime(date('m/d/y'));
		
    	$d->modify('first day of this month');
		$from_dmy = $d->format('m/d/y');
		
		$d->modify('last day of this month');
		$to_dmy = $d->format('m/d/y');

	}

    if ($current_month==TRUE){
    
    	$url_view  = $baseReport . "users/" . get_option("wp_userWimtv") . "/views";
    	$title_views = "Views (current month)";
    	
    	$url_stream = $baseReport . "users/" . get_option("wp_userWimtv") . "/streams"; 	
    	$title_streams = "Streams (current month)";
    	$url_view_single = $baseReport . "views/@";

    	
    	$url_info_user = $baseReport . "users/" . get_option("wp_userWimtv"); 
    	$title_user = "Current Month  <a href='#' id='customReport'>Change Date</a> ";
    	$style_date = "display:none;";
    	$url_packet = $baseReport . "users/" . get_option("wp_userWimtv") . "/commercialPacket/usage";
    	
    } else {
    
    	$url_view = $baseReport . "users/" . get_option("wp_userWimtv") . "/views_by_time?from=" . $from_tm . "&to=" . $to_tm;
    	$title_views = "Views (from " . $from . " to " . $to . ")";
    	
    	$url_stream = $baseReport . "users/" . get_option("wp_userWimtv") . "/streams?from=" . $from_tm . "&to=" . $to_tm ;	
    	$title_streams = "Streams (from " . $from . " to " . $to . ")";
    	$url_view_single = $baseReport . "views/@?from=" . $from_tm . "&to=" . $to_tm ;
    	
    	$url_info_user = $baseReport . "users/" . get_option("wp_userWimtv") . "?from=" . $from_tm . "&to=" . $to_tm . "&format=json";
    	
		$title_user = "<a href='?page=WimVideoPro_Report'>Current Month</a> Change Date";
		
		

    }
    
   	echo "<div class='wrap'><h1>Report user Wimtv " . get_option("wp_userWimtv") . "</h1>";
	
	    
    echo ' 
        <script type="text/javascript">
  		jQuery(document).ready(function(){
  		  jQuery( ".pickadate" ).datepicker({
            dateFormat: "dd/mm/yy",     maxDate: 0,      });
  		  jQuery("#customReport").click(function(){
			jQuery("#fr_custom_date").fadeToggle();
			jQuery("#changeTitle").html("<a href=\'?page=WimVideoPro_Report\'>Current Month</a> Change Date");
		  });
		  
		  jQuery(".tabs span").click(function(){
		    var idSpan = jQuery(this).attr("id");
		    jQuery(".view").fadeOut();
		  	jQuery("#view_" + idSpan).fadeIn();
		  	jQuery(".tabs span").attr("class","");
		  	jQuery(this).attr("class","active");
		  });
		  
  		});
  		</script>
     ';

	
	echo "<h3 id='changeTitle'>" . $title_user . "</h3>";
	
	echo '<div class="registration" id="fr_custom_date" style="' . $style_date . '">
	
		<form method="post">
			<fieldset>From <input  type="text" class="pickadate" id="edit-from" name="from" value="' . $from . '" size="10" maxlength="10"> 
			To <input  type="text" class="pickadate" id="edit-to" name="to" value="' . $to . '" size="10" maxlength="10">
			<input type="submit" value=">" class="button button-primary" /></fieldset>
		</form>
	
	</div>';

	
   	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url_info_user);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    $response = curl_exec($ch);
   	curl_close($ch);
	
	$traffic_json = json_decode($response);
	$traffic = $traffic_json->traffic;
	$storage = $traffic_json->storage;
	
	
	if (isset($url_packet)) {
	
		$ch2 = curl_init();
	    curl_setopt($ch2, CURLOPT_URL, $url_packet);
	    curl_setopt($ch2, CURLOPT_VERBOSE, 0);
	    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch2, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    $response2 = curl_exec($ch2);
	   	curl_close($ch2);
		
		$commercialPacket_json = json_decode($response2);
		$currentPacket = $commercialPacket_json->current_packet;
		if (($currentPacket->id)>0) $namePacket =  $currentPacket->name;
		else $namePacket =  $currentPacket->error;
		echo "<p>Commercial Packet: <b>" . $namePacket . "</b></p> ";

		$traffic_of = " of " . $currentPacket->band_human;
		$storage_of = " of " . $currentPacket->storage_human;
		
		$traffic_bar = "<div class='progress'><div class='bar' style='width:" . $commercialPacket_json->traffic->percent . "%'>" . $commercialPacket_json->traffic->percent_human . "%</div></div>";
		$storage_bar = "<div class='progress'><div class='bar' style='width:" . $commercialPacket_json->storage->percent . "%'>" . $commercialPacket_json->storage->percent_human . "%</div></div>";
		
		$byteToMb = "<b>" . $commercialPacket_json->traffic->current_human . '</b>' . $traffic_of . $traffic_bar;
		$byteToMbS = "<b>" . $commercialPacket_json->storage->current_human . '</b>' . $storage_of . $storage_bar;
	
	} else {
	
		$byteToMb = "<b>" . round($traffic/ $megabyte, 2) . ' MB</b>';
		$byteToMbS = "<b>" . round($storage/ $megabyte, 2) . ' MB</b>';

	
	}
	
	//$commercialPacket = $traffic_json->commercialPacket;
	if ($traffic=="") {
		echo "<p>You account don't generate traffic in this period.</p>";
	} else {
		echo "<p>Traffic: " . $byteToMb . "</p>";
		echo "<p>Storage space: " . $byteToMbS . "</p>";

		
		
		
	   	$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url_stream);
	    curl_setopt($ch, CURLOPT_VERBOSE, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    $response = curl_exec($ch);
	   	curl_close($ch);
	    $arrayStream = json_decode($response);
	
	    echo '
	    <div class="summary"><div class="tabs">
	    	<span id="stream" class="active">View Streams</span><span id="graph">View graph</span>
	    </div>
	    <div id="view_stream" class="view"><table class="wp-list-table widefat fixed posts" style="text-align:center;">
	     <h3>' . $title_streams . '</h3>
	      <tr>
	        <th class="manage-column column-title">Video</th>
	    	<th class="manage-column column-title">Views</th>
	    	<th class="manage-column column-title">Activate view</th>
	    	<th class="manage-column column-title">Max viewers</th>
	      </tr>
	    ';
	    
	    $dateNumber = array();
	    $dateTraffic = array();
		foreach ($arrayStream as $value){
			$arrayPlay = $wpdb->get_results("SELECT * FROM {$table_name} WHERE contentidentifier='" . $value->contentId . "'");
			$thumbs = $arrayPlay[0]->urlThumbs;
			if ((isset($value->title))) $video = $thumbs . "<br/><b>" . $value->title . "</b><br/>" . $value->type ;
			else $video = $thumbs . "<br/>" . $value->id;
			
			$html_view_exp = "<b>Total " . $value->views . " Views</b><br/>";
			$view_exp = $value->views_expanded;
			if (count($view_exp)>0) {
				$html_view_exp .= "<table class='wp-list-table'>
				<tr>
			        <th class='manage-column column-title' style='font-size:10px;'>End Time</th>
			    	<th class='manage-column column-title' style='font-size:10px;'>Duration</th>
			    	<th class='manage-column column-title' style='font-size:10px;'>Traffic</th>
			    </tr>
				";
				foreach ($view_exp as $value_exp){
					$value_exp->traffic =  round($value_exp->traffic / $megabyte, 2) . " MB";
					$date_human =  date('d/m/Y', ($value_exp->end_time/1000));
					$html_view_exp .= "<tr>";
					$html_view_exp .= "<td style='font-size:10px;'>" . $date_human . "</td>";
					$html_view_exp .= "<td style='font-size:10px;'>" . $value_exp->duration . "s</td>";
					$html_view_exp .= "<td style='font-size:10px;'>" . $value_exp->traffic  . "</td>";
					$html_view_exp .= "</tr>";
					
					if (isset($dateNumber[$date_human])) $dateNumber[$date_human] = $dateNumber[$date_human] + 1;
					else $dateNumber[$date_human] = 1;
					
					if (isset($dateTraffic[$date_human])) array_push($dateTraffic[$date_human], $value_exp->traffic);
					else $dateTraffic[$date_human] = array($value_exp->traffic);

					
				}
				$html_view_exp .= "</table>";
			} else
			{
			  $html_view_exp .= "";
			}
			echo "
			 <tr class='alternate'>
			  <td class='image'>" .  $video . "</td>
			  <td>" .  $html_view_exp . "</td>
			  <td>" . $value->viewers . "</td>
			  <td>" .  $value->max_viewers . "</td>
			 </tr>";
		
		}
		echo "</table><div class='clear'></div></div>";
		
		
		echo "<div id='view_graph' class='view'>";
		$dateRange = getDateRange($from_dmy, $to_dmy);
		$count_date = count($dateRange);
		$count_single= 0;
		$traffic_single = 0;
		echo "<div class='cols'>";
		
		$number_view_max = max($dateNumber);
		$single_percent = (100/$number_view_max);
		
		$single_taffic_media = array();
		foreach ($dateTraffic as $dateFormat => $traffic_number){
			$single_taffic_media[$dateFormat] = round(array_sum($dateTraffic[$dateFormat]) / count($dateTraffic[$dateFormat]),2);
		}
		$traffic_view_max = max($single_taffic_media);
		$single_traffic_percent = (100/$traffic_view_max);

		echo "<div class='col'><div class='date'>Date</div><div class='title'>Total view</div><div class='title'>Average Traffic</div></div>";
		for ($i=0;$i<$count_date;$i++){
		    if (isset($dateNumber[$dateRange[$i]])) $count_single = $single_percent * $dateNumber[$dateRange[$i]];
		    if (isset($single_taffic_media[$dateRange[$i]])) $traffic_single = $single_traffic_percent * $single_taffic_media[$dateRange[$i]];		    
		    
		 	echo "<div class='col' >
					<div class='date'>" . $dateRange[$i] . "</div>
					<div class='countview'><div class='bar' style='width:" . $count_single . "%'>";
			if ($dateNumber[$dateRange[$i]]>1) echo $dateNumber[$dateRange[$i]] . " viewers";
			if ($dateNumber[$dateRange[$i]]==1) echo $dateNumber[$dateRange[$i]] . " viewer";
			echo "</div></div>
					<div class='countview'><div class='barTraffic' style='width:" . $traffic_single . "%'>";
			if ($single_taffic_media[$dateRange[$i]]>0) echo $single_taffic_media[$dateRange[$i]] . " MB";
			echo "</div></div>
					</div>";
			$count_single = 0;
			$traffic_single = 0;
		}
		
		echo "</div>";
		//print_r($dateRange);	
		echo "<div class='clear'></div></div><div class='clear'></div></div>";
	/*
	   	$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url_view);
	    curl_setopt($ch, CURLOPT_VERBOSE, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    $response = curl_exec($ch);
	
	   	curl_close($ch);
	    $arrayView = json_decode($response);
	    
	    if (count($arrayView)>1) {	
		
		/*echo "<h3>" . $title_views . "</h3>";
	   
		    echo '<table class="wp-list-table widefat fixed posts">
		      <tr>
		        <th class="manage-column column-title">End Time</th>
		    	<th class="manage-column column-title">Duration</th>
		    	<th class="manage-column column-title">Traffic</th>
		    	<th class="manage-column column-title">Stream name</th>
		      </tr>
		    ';*/
		    /*			foreach ($arrayView as $value){
				
				//Pari alternate
				$url_view_single2 = str_replace("@",$value,$url_view_single);
				
				$ch = curl_init();
			    curl_setopt($ch, CURLOPT_URL, $url_view_single2);
			    curl_setopt($ch, CURLOPT_VERBOSE, 0);
			    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			    $responseSingle = curl_exec($ch);
			    $arrayresponseSingle = json_decode($responseSingle);
			    
			   	curl_close($ch);
				
				echo $arrayresponseSingle->end_time;
				
				$traffic = $arrayresponseSingle->traffic;
				$traffic =  round($traffic / $megabyte, 2) . " MB";
				$title = $arrayresponseSingle->title;
				if ($title==false)
					$title = $arrayresponseSingle->video_url;
				/*echo "
				 <tr class='alternate'>
				  <td>" . $arrayresponseSingle->human_end_time . "</td>
				  <td>" .  $arrayresponseSingle->duration . "s</td>
				  <td>" . $traffic . "</td>
				  <td>" .  $title . "</td>
				 </tr>";*/
			/*
			}
			echo "</table>";
		}*/
   		
   	}
   	echo "</div>";

}




?>