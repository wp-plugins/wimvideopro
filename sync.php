<?php
/**
  * @file
  * Syncronize the video with wim.tv.
  *
  */
  if (!isset($upload))
  	include("../../../wp-blog-header.php");
  else
    include("../wp-blog-header.php");
  global $user,$wpdb;
  $url_video = get_option("wp_basePathWimtv") . get_option("wp_urlVideosDetailWimtv");
  $credential = get_option("wp_userwimtv") . ":" . get_option("wp_passwimtv");
  $table_name = $wpdb->prefix . 'wimtvpro_video';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,  $url_video);
  curl_setopt($ch, CURLOPT_VERBOSE, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  curl_setopt($ch, CURLOPT_USERPWD, $credential);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

  $response = curl_exec($ch);
  $array_json_videos = json_decode($response);

  curl_close($ch);

  if ($array_json_videos==NULL) {
    _e("Non si riesce a stabilire una connessione con Wimtv. Contattare l'amministratore.");
  } 
  else {
  //$num = (array)simplexml_load_string($response);
    $i=0;
    foreach ($array_json_videos -> items as $a) {
      foreach ($a as $key => $value) {
        $array_all_videos[$i][$key] = $value;
      }
      $i++;
    }
    $num = count($array_json_videos);
    if ($num > 0 ) {
      $elenco_video_wimtv = array();
      $elenco_video_wp = array();
      $array_videos_new_wp = $wpdb->get_results("SELECT contentidentifier FROM " . $table_name . " WHERE uid = '" . get_option("wp_userwimtv") . "'");
      foreach ($array_videos_new_wp as $record) {
        array_push($elenco_video_wp, $record -> contentidentifier);
      }
      /* Information detail videos into Showtime */
      $json_st   = wimtvpro_detail_showtime(FALSE, 0);
      $arrayjson_st   = json_decode($json_st);
      $values_st = $arrayjson_st->items;
      foreach ($values_st as $key => $value) {
        $array_st[$value -> {"contentId"}]["showtimeIdentifier"] = $value-> {"showtimeIdentifier"};
      }
      if ($array_all_videos) {
	      foreach ($array_all_videos as $video) {
	        $url_video = $video["actionUrl"];
	        $status = $video["status"];
	        //$acquired_identifier = $video["acquired_identifier "];
	        $title= $video["title"];
	        $urlVideo= $video["streamingUrl"]->streamer . "$$" . $video["streamingUrl"]->file . "$$" . $video["streamingUrl"]->auth_token;
	        $duration= $video["duration"];
	        $content_item =  $video["contentId"];
	        $url_thumbs = '<img src="' . $video["thumbnailUrl"] . '"  title="' . $title . '" class="wimtv-thumbnail" />';
	        $categories  = "";
	        $valuesc_cat_st = "";
	        foreach ($video["categories"] as $key => $value) {
	          $valuesc_cat_st .= $value->categoryName;
	          $categories .= $valuesc_cat_st;
	          foreach ($value -> subCategories as $key => $value) {
	            $categories .= " / " . $value -> categoryName;
	          }
	          $categories .= "<br/>";
	        }
	        array_push($elenco_video_wimtv, $content_item);
	        if (trim($content_item)!="") {
	          //controllo se il video esiste
	          $trovato = FALSE;
	          //controllo se il video eiste in DRUPAL ma non pi&#65533; in WIMTV
	          foreach ($array_videos_new_wp as $record) {
	            $content_itemAll = $record -> contentidentifier;
	            if ($content_itemAll == $content_item) {
	              $trovato = TRUE;
	            }
	          }
	          $pos_wimtv="";
	          $showtime_identifier ="";
	          if (isset($array_st[$content_item])) {
	            $pos_wimtv="showtime";
	            $showtime_identifier = $array_st[$content_item]["showtimeIdentifier"];
	          } 
	          else {
	            $pos_wimtv="";
	          }
	          
	          if (!$trovato) {
	            $wpdb->insert( $table_name, 
	            	array (
	            	'uid' => get_option("wp_userwimtv"),
	            	'contentidentifier' => $content_item,
	            	'mytimestamp' => time(),
	            	'position' => '0',
	            	'state' => $pos_wimtv,
	            	'viewVideoModule' => '3',
	            	'status' => $status,
	            	'acquiredIdentifier' => $acquired_identifier,
	            	'urlThumbs' => mysql_escape_string($url_thumbs),
	            	'category' =>  $categories,
	            	'urlPlay' =>  mysql_escape_string($urlVideo),
	            	'title' =>  mysql_escape_string($title),
	            	'duration' => $duration,
	            	'showtimeidentifier' => $showtime_identifier
	            	)
	           	);
	            
	          } 
	          else {
	          	$query = "UPDATE " . $table_name . 
	            " SET state = '" . $pos_wimtv . "'," . 
	            " status = '" . $status . "'," . 
	            " title = '" . mysql_escape_string($title) . "'," .             
	            " urlThumbs = '" . mysql_escape_string($url_thumbs) . "'," .
	            " urlPlay = '" . mysql_escape_string($urlVideo) . "'," .
	            " duration = '" . $duration . "'," .
	            " showtimeidentifier = '" . $showtime_identifier . "'," .
	            " category = '" . $categories . "'" .
	            " WHERE contentidentifier = '"  . $content_item . "' ";
	            $wpdb->query($query);
	          }
	      }
		}
	} else {
		
	_e("You aren't videos");
		
	}

    //var_dump(array_diff($elenco_video_wp ,$elenco_video_wimtv ));
    $delete_into_wp = array_diff($elenco_video_wp, $elenco_video_wimtv);
    foreach ($delete_into_wp as  $value) {
      $wpdb->query( 
		  "DELETE FROM " . $table_name . " WHERE contentidentifier ='"  . $value . "'"
      );
    }
    if (isset($_GET['sync'])) {
      echo wimtvpro_getThumbs($_GET['showtime'], TRUE);
    }
    
    //UPDATE PAGE MY STREAMING
	update_page_mystreaming();    
  }
  else {
    echo t("Non ci sono elementi");
  }
}

if (!isset($upload))
  die();