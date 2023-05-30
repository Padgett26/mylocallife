<?php
$t = 0;
if ($showAdsSide == '1') {
	$sideA1 = $db->prepare ( "SELECT userId, adName, adExt, linkText, linkLocal FROM advertising WHERE slot = ? && activeUntil >= ? ORDER BY RAND() LIMIT 1" );
	$sideA1->execute ( array (
			'side1',
			$time
	) );
	$sideA1row = $sideA1->fetch ();
	if ($sideA1row) {
		$AuserId1 = $sideA1row ['userId'];
		$sideAd1Name = ($sideA1row ['adName'] != "") ? $sideA1row ['adName'] : "0";
		$sideAd1Ext = ($sideA1row ['adExt'] != "") ? $sideA1row ['adExt'] : "jpg";
		$linkAd1Text = $sideA1row ['linkText'];
		$linkAd1Local = $sideA1row ['linkLocal'];

		if (file_exists ( "userPics/" . $AuserId1 . "/" . $sideAd1Name . "." . $sideAd1Ext )) {
			$lt = ($linkAd1Local == '1') ? "href='index.php?page=BusinessDetail&business=$AuserId1'" : "href='$linkAd1Text' target='_blank'";
			echo "<div style='overflow:hidden; margin:0px; padding:0px; position:relative; width:200px; height:300px; margin-top:20px;'><a $lt><img src='userPics/" . $AuserId1 . "/" . $sideAd1Name . "." . $sideAd1Ext . "' alt='' style='max-width:200px; max-height:300px;' /></a></div>";
			$t ++;
		}
	}
}

if ($showCalendar == 1) {
	?>
    <div style="width:95%; padding:10px; border:5px double <?php

	echo $highlightColor;
	?>; text-align:center; cursor:pointer; box-shadow: 5px 5px 5px grey; margin-top:20px;" onclick="toggleview('upcomingevents')">Upcoming Events</div>
    <div id="upcomingevents" style="display:none; position:relative; top:0px; left:0px; width:100%; margin-left:5px;">
    <?php

	include "includes/calendar.php";
	?>
    </div>
    <?php
}

if ($showSubmitNews == 1) {
	?>
    <div style="width:95%; padding:10px; border:5px double <?php

	echo $highlightColor;
	?>; text-align:center; cursor:pointer; box-shadow: 5px 5px 5px grey; margin-top:20px;" onclick="toggleview('upcomingevents')">
    My Local Life welcomes news tips and story ideas from you.<br /><br />For immediate, breaking news, call us at 785-772-5151. You may e-mail news releases to admin@mylocal.life.<br /><br />To submit a story online, <a href="index.php?page=submitNews" style="font-weight:bold;">FOLLOW THIS LINK</a>
</div>
<?php
}

if ($showCandidates == 1) {
	?>
    <div style="width:95%; padding:10px; border:5px double <?php

	echo $highlightColor;
	?>; text-align:center; cursor:pointer; box-shadow: 5px 5px 5px grey; margin-top:20px;" onclick="toggleview('your2016candidates')">Your 2016 Candidates</div>
    <div id="your2016candidates" style="display:block;">
    <?php

	include "includes/candidates.php";
	?>
    </div>
    <?php
}
?>
<div style="width:95%; padding:10px; border:5px double <?php

echo $highlightColor;
?>; text-align:center; cursor:pointer; box-shadow: 5px 5px 5px grey; margin-top:20px;" onclick="toggleview('trending')">Trending</div>
<div id="trending" style="display:none;">
<?php

include "includes/trending.php";
?>
</div>
<?php
if ($showAdsSide == '1') {
	$sideA2 = $db->prepare ( "SELECT userId, adName, adExt, linkText, linkLocal FROM advertising WHERE slot = ? && activeUntil >= ? ORDER BY RAND() LIMIT 1" );
	$sideA2->execute ( array (
			'side2',
			$time
	) );
	$sideA2row = $sideA2->fetch ();
	if ($sideA2row) {
		$AuserId2 = $sideA2row ['userId'];
		$sideAd2Name = ($sideA2row ['adName'] != "") ? $sideA2row ['adName'] : "0";
		$sideAd2Ext = ($sideA2row ['adExt'] != "") ? $sideA2row ['adExt'] : "jpg";
		$linkAd2Text = $sideA2row ['linkText'];
		$linkAd2Local = $sideA2row ['linkLocal'];

		if (file_exists ( "userPics/" . $AuserId2 . "/" . $sideAd2Name . "." . $sideAd2Ext )) {
			$lt = ($linkAd2Local == '1') ? "href='index.php?page=BusinessDetail&business=$AuserId2'" : "href='$linkAd2Text' target='_blank'";
			echo "<div style='overflow:hidden; margin:0px; padding:0px; position:relative; width:200px; height:300px; margin-top:20px;'><a $lt><img src='userPics/" . $AuserId2 . "/" . $sideAd2Name . "." . $sideAd2Ext . "' alt='' style='max-width:200px; max-height:300px;' /></a></div>";
		}
	}
}

if ($showJobs == 1) {
	?>
    <div style="width:95%; padding:10px; border:5px double <?php

	echo $highlightColor;
	?>; text-align:center; cursor:pointer; box-shadow: 5px 5px 5px grey; margin-top:20px;" onclick="toggleview('jobopportunities')">Job Opportunities</div>
    <div id="jobopportunities" style="display:none; position:relative; top:0px; left:0px; width:100%;">
    <?php

	include "includes/jobs.php";
	?>
    </div>
    <?php
}
?>
<div style="width:95%; padding:10px; border:5px double <?php

echo $highlightColor;
?>; text-align:center; cursor:pointer; box-shadow: 5px 5px 5px grey; margin-top:20px;" onclick="toggleview('feedback')">Feedback</div>
<div id="feedback" style="width:95%; padding:10px; border:1px solid <?php

echo $highlightColor;
?>; text-align:center; display:none; box-shadow: 5px 5px 5px grey;">
<?php

include "includes/feedback.php";
?>
</div>
<?php
if ($showAdsSide == '1') {
	$sideA3 = $db->prepare ( "SELECT userId, adName, adExt, linkText, linkLocal FROM advertising WHERE slot = ? && activeUntil >= ? ORDER BY RAND() LIMIT 1" );
	$sideA3->execute ( array (
			'side3',
			$time
	) );
	$sideA3row = $sideA3->fetch ();
	if ($sideA3row) {
		$AuserId3 = $sideA3row ['userId'];
		$sideAd3Name = ($sideA3row ['adName'] != "") ? $sideA3row ['adName'] : "0";
		$sideAd3Ext = ($sideA3row ['adExt'] != "") ? $sideA3row ['adExt'] : "jpg";
		$linkAd3Text = $sideA3row ['linkText'];
		$linkAd3Local = $sideA3row ['linkLocal'];

		if (file_exists ( "userPics/" . $AuserId3 . "/" . $sideAd3Name . "." . $sideAd3Ext )) {
			$lt = ($linkAd3Local == '1') ? "href='index.php?page=BusinessDetail&business=$AuserId3'" : "href='$linkAd3Text' target='_blank'";
			echo "<div style='overflow:hidden; margin:0px; padding:0px; position:relative; width:200px; height:300px; margin-top:20px;'><a $lt><img src='userPics/" . $AuserId3 . "/" . $sideAd3Name . "." . $sideAd3Ext . "' alt='' style='max-width:200px; max-height:300px;' /></a></div>";
		}
	}
}
