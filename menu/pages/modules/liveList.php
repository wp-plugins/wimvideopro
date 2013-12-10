<?php
include("../../../../../../wp-load.php");
//include_once("../api/api.php");

$userpeer = get_option("wp_userWimtv");
$timezone = isset($_POST['timezone_']) ? $_POST['timezone_'] : "";
$type = $_POST['type'];
$id =  $_POST['id'];
$onlyActive = $_POST['onlyActive'];
header('Content-type: text/html');
  $json = apiGetLiveEvents($timezone, $onlyActive);
  $arrayjson_live = json_decode($json);
  $count = -1;
  $output = "";
  if ($arrayjson_live ){
   foreach ($arrayjson_live->{"hosts"} as $key => $value) {
    $count ++;  
    $name = $value -> name;
    if (isset($value -> url))
      $url =  $value -> url;
    else
      $url = "";
    $day =  $value -> eventDate;
    $payment_mode =  $value -> paymentMode;
    if ($payment_mode=="FREEOFCHARGE") $payment_mode="Free";
    else {
      $payment_mode=  $value->pricePerView . " &euro;";
    }
    if ( $value -> durationUnit=="Minute") {
   		$tempo = $value->duration;
		$ore = floor($tempo / 60);
		$minuti = $tempo % 60;
		$durata = $ore . " h ";
		if ($minuti<10)
		  $durata .= "0";
		$durata .= $minuti . " min";
	}
	else
		 $durata =  $value->duration . " " . $value -> durationUnit;	
    
    $identifier = $value -> identifier;

    $embedded_iframe = apiGetLiveIframe($identifier, $timezone);
    $details_live = apiGetLive($identifier, $timezone);
    //d($livedate);
    $livedate = json_decode($details_live);

    $data = $livedate->eventDateMillisec;
    $timezoneOffset = intval($livedate->timezoneOffset)/1000;
	$timestamp = floor($data);
    $timestamp = $timestamp/1000;

    $start = new DateTime("@$timestamp");
    $timezoneName = timezone_name_from_abbr("", $timezoneOffset, false);
    $real_timezone = new DateTimeZone($timezoneName);
    $start->setTimezone($real_timezone);
	$oraMin = $start->format('H') . ":" . $start->format('i');
	$timeToStart= $livedate->timeToStart;
	$timeLeft = $livedate->timeLeft;
    //$urlPeer = "http://peer.wim.tv:8080/wimtv-webapp/rest";
    //$embedded_code = htmlentities(curl_exec($ch_embedded));
    //$embedded_iframe = '<iframe id="com-wimlabs-player" name="com-wimlabs-player" src="' . $urlPeer . '/liveStreamEmbed/' . $identifier . '/player?width=692&height=440" style="min-width: 692px; min-height: 440px;"></iframe>';
    
    $embedded_code = '<textarea readonly="readonly" onclick="this.focus(); this.select();">' . $embedded_iframe . '</textarea>'; 
    if ($type=="table") {
      //Check Live is now
		
      $liveIsNow = false;
      if (intval($timeToStart) < 0 && intval($timeLeft) > 0){
         $liveIsNow = true;
      }
     
      $output .="<tr>
      <td>" . $name . "</td>";
	  
	  if ($identifier==get_option("wp_liveNow"))  $file= "live_rec.gif";
	  else $file= "webcam.png";
	  
      if ($liveIsNow)  {
          $output .= "<td><a  target='page_newTab' href='" .  get_option('wp_wimtvPluginPath')
                  . "embedded/live_webproducer.php?id=" . $identifier . "' class='clickWebProducer' id='"
                  . $identifier . "'><img  onClick='clickImg(this)' src='"
                  . get_option('wp_wimtvPluginPath') . "images/" . $file . "' /></a></td>";
      } else {
          $output .="<td></td>";
      }
      
      $output .=  "<td>" . $payment_mode . "</td>
      <td>" . $url . "</td>
      <td>"  . $start->format('d/m/Y H:i') . "<br/>" . $durata . "</td>
      <td>" . $embedded_code . "</td>
      <td> ";
	  /*$output .="<a href='?page=WimLive&namefunction=modifyLive&id=" . $identifier . "&timezone=" . $timezoneOffset . "' alt='" . __("Modify")
          . "'   title='" . __("Modify","wimtvpro") . "'><img src='" . get_option('wp_wimtvPluginPath') . "images/mod.png"
          . "'  alt='" . __("Modify","wimtvpro") . "'></a>";*/
	$output .=  "<a href='?page=WimLive&namefunction=deleteLive&id=" . $identifier . "' title='" . __("Remove"). "'><img src='" . get_option('wp_wimtvPluginPath') ."images/remove.png" . "' alt='" . __("Remove") . "'></a>";
		 
		$output .="</td>

      </tr>";
    }
    elseif ($type=="list") {
      if ($count==0) $output .= "";
      elseif ($count>0) $output .="<li><b>" . $name . "</b> " . $payment_mode . " - " . $start->format('d/m/Y H:i') . " - " . $durata . "</li>";
      else $output .="<li><b>" . $name . "</b> " . $payment_mode . " - " . $start->format('d/m/Y H:i')   . " - " . $durata . "</li>";
    }
    else {
      if ($count==0) {
        $name = "<b>" . $name . "</b>";
        $day =  __("Begins to ","wimtvpro") . $day;
        $output = $name . "<br/>";
        $output .= $data . " " . $oraMin  . "<br/>" . $durata . "<br/>";
        $output .= $embedded_iframe;
      }
    }
   }
  }
  if ($count<0)
    $output = __("There are no live events","wimtvpro");

echo $output;

?>
