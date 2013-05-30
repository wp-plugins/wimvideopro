

<?php
  include("../../../../wp-blog-header.php");

	$id =  $_GET['id'];

	$userpeer = get_option("wp_userWimtv");
	$url_live_embedded = get_option("wp_basePathWimtv") . "liveStream/" . $userpeer . "/" . $userpeer . "/hosts/" . $id;

   	$credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
  	$ch_embedded= curl_init();

    curl_setopt($ch_embedded, CURLOPT_URL, $url_live_embedded);
    curl_setopt($ch_embedded, CURLOPT_VERBOSE, 0);

    curl_setopt($ch_embedded, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch_embedded, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch_embedded, CURLOPT_USERPWD, $credential);
    curl_setopt($ch_embedded, CURLOPT_SSL_VERIFYPEER, FALSE);
    $embedded= curl_exec($ch_embedded);
	$arrayjson_live = json_decode($embedded);
	$url =  $arrayjson_live->url;
	$title = $arrayjson_live->name;

?>


<div id="producer"></div>



<script type="text/javascript">
jQuery(document).ready(function(){ 

	
	var xiSwfUrlStr = url_pathPlugin  + "script/swfObject/playerProductInstall.swf";
	console.log(xiSwfUrlStr );
	var flashvars = {};
    var params = {};
    params.quality = "high";
    params.bgcolor = "#ffffff";
    params.allowscriptaccess = "sameDomain";
    params.allowfullscreen = "true";
    var attributes = {};
    attributes.align = "left";

	swfobject.embedSWF(url_pathPlugin  + "script/swfObject/producer.swf", "producer", "640", "480", "11.4.0",xiSwfUrlStr, flashvars, params, attributes );
	setTimeout(function () {
		producer = jQuery('#producer')[0];
	    console.log(producer);
	    
	    producer.setCredentials('<?php echo get_option("wp_userWimtv"); ?>', '<?php echo get_option("wp_passWimtv"); ?>');
	    producer.setUrl(decodeURIComponent('<?php echo $url;?>'));
	    producer.setStreamName('<?php echo $title;?>');
	    producer.setStreamWidth(640);
	    producer.setStreamHeight(480);
	    producer.connect();
	}, 1000);
    
});
</script>