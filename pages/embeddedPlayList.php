<?php
  	global $user,$wpdb;
  	include("../../../../wp-blog-header.php");
	

  	$idPlayList=$_GET['id'];
	$page=$_GET['page'];
	
    $table_name = $wpdb->prefix . 'wimtvpro_playlist';
  	$playlist = $wpdb->get_results("SELECT listVideo,name FROM {$table_name} WHERE id='" . $idPlayList . "'");
	foreach ($playlist as $record) {
		$listVideo = $record->listVideo;
		$title = $record->name;
	}
	
	$uploads_info = wp_upload_dir();
    $directory = $uploads_info["baseurl"] .  "/skinWim";
    $nameFile = "/playlist_" .  $idPlayList . ".xml";
        
     if (!isset($page))	{
    		$height = get_option("wp_heightPreview") +70;
			$width = get_option("wp_widthPreview") +280;
			$widthP = get_option("wp_widthPreview") +250; 	

			echo "<div style='text-align:center;height:" . $height . "px;width:" . $width . "px;'><h3>" . $title . "</h3>";
			$playlistSize = "250px";
			$dimensions = "width: '" . $widthP . "', height: '" . get_option("wp_heightPreview") . "',";
			echo "<div id='container' style='margin:0;padding:0 10px;'></div>";
	} else {
		$playlistSize = "30%";
		$dimensions = "width: '100%',";
		echo "<div id='container' style='width: 10px; height: 10px; background-color: #c7cc63;'></div>";
	
	}
	
	//echo "Change order: ";
	$doc = simplexml_load_file($directory . $nameFile);
	$sxe = new SimpleXMLElement($doc->asXML());
	$channel = $sxe->channel;
	$playlist = "";
	foreach ($channel->item as $items) {
	  $playlist .= "{'file':'" . $items->file . "','image':'" . $items->image . "','title':'" . $items->title . "'},";
	  //echo $items->title . " ";
	}
	$playlist = substr($playlist , 0, -1);

	
		//For jwplayer 6 - skin is file xml
		/*
		echo "
		<script type='text/javascript'>

		    jwplayer('container').setup({";
		    if (get_option('wp_nameSkin')!="") echo "skin: '" . $directory . "/" . get_option('wp_nameSkin') . ".zip',";
 		echo "
 			width: '" . $widthP . "',
		    height: '" . get_option("wp_heightPreview") . "',

		        'flashplayer': url_pathPlugin + 'script/jwplayer/player.swf',
		        'playlist': [" .  $playlist . "],

			listbar: {
		        position: 'right',
		        size: 250
		    },
		    });
		   
		</script>";
		*/
		
		//For jwplayer 5
		$dirJwPlayer = plugin_dir_url(dirname(__FILE__)) . "script/jwplayer/player.swf"; 
		echo "<script type='text/javascript'>jwplayer('container').setup({";
       if (get_option('wp_nameSkin')!="") echo "skin: '" . $directory . "/" . get_option('wp_nameSkin') . ".zip',";
 		echo $dimensions . "'flashplayer':'" .  $dirJwPlayer . "','playlist': [" .  $playlist . "],'playlist.position': 'right',	'playlist.size': '" . $playlistSize  . "'});</script>&nbsp;";

	if (!isset($page))	{	
		echo "</div>";
	}


?>