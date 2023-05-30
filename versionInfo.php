<?php
$getVersion = $db->prepare("SELECT versionInfo FROM company WHERE id='1'");
$getVersion->execute();
$getrow = $getVersion->fetch();
$versionInfo = nl2br($getrow['versionInfo']);

echo "<article id='mainTableBox' style='padding:10px;'>$versionInfo</article>";