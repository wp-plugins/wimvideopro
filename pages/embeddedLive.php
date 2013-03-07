<?php
  include("../../../../wp-blog-header.php");

echo wimtvpro_elencoLive("video", "0") . "<br/>";
$upcoming = wimtvpro_elencoLive("list", "0");
if ($upcoming!="Aren't Event Live")
	echo "UPCOMING EVENT<br/>" . wimtvpro_elencoLive("list", "0");

?>