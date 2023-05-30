<?php
if ($myId != '0') {
	if (filter_input ( INPUT_POST, 'title', FILTER_SANITIZE_STRING )) {
		$upTitle = filter_input ( INPUT_POST, 'title', FILTER_SANITIZE_STRING );
		$upStartHour = filter_input ( INPUT_POST, 'startHour', FILTER_SANITIZE_NUMBER_INT );
		$upStartMinute = filter_input ( INPUT_POST, 'startMinute', FILTER_SANITIZE_NUMBER_INT );
		$upStartMonth = filter_input ( INPUT_POST, 'startMonth', FILTER_SANITIZE_NUMBER_INT );
		$upStartDay = filter_input ( INPUT_POST, 'startDay', FILTER_SANITIZE_NUMBER_INT );
		$upStartYear = filter_input ( INPUT_POST, 'startYear', FILTER_SANITIZE_NUMBER_INT );
		$upStartTime = mktime ( $upStartHour, $upStartMinute, 00, $upStartMonth, $upStartDay, $upStartYear );
		$a2 = htmlEntities ( trim ( $_POST ['writeUp'] ), ENT_QUOTES );
		$upWriteUp = filter_var ( $a2, FILTER_SANITIZE_STRING );

		$stmt = $db->prepare ( "INSERT INTO calendar VALUES(NULL,?,?,?,'0.jpg','0','0',?,'0','0','0')" );
		$stmt->execute ( array (
				$upStartTime,
				$upTitle,
				$upWriteUp,
				$myId
		) );

		$getId = $db->prepare ( "SELECT id FROM calendar WHERE startTime=? && title=? && writeUp=? && userId=? ORDER BY id DESC LIMIT 1" );
		$getId->execute ( array (
				$upStartTime,
				$upTitle,
				$upWriteUp,
				$myId
		) );
		$row = $getId->fetch ();
		$eventId = $row ['id'];

		$image = $_FILES ["image"] ["tmp_name"];
		$imageName = $time;
		list ( $width, $height ) = (getimagesize ( $image ) != null) ? getimagesize ( $image ) : null;
		if ($width != null && $height != null) {
			$imageType = getPicType ( $_FILES ["image"] ['type'] );
			processPic ( $myId, $imageName, '800', '800', $image, $imageType );
			processThumbPic ( $myId, $imageName, '100', '100', $image, $imageType );
			$upPicture = $imageName . "." . $imageType;
			$p1stmt = $db->prepare ( "UPDATE calendar SET picture=? WHERE id=?" );
			$p1stmt->execute ( array (
					$upPicture,
					$eventId
			) );
		}
		echo "Your event has been uploaded and needs to be approved before it is visible.";
	}
	?>
    <div style="text-decoration:underline; font-size:1em; font-weight:bold; cursor:pointer;" onclick="toggleview('eventUp')">Add your event to the calendar</div>
    <div id="eventUp" style="margin:20px; padding:20px; display:none;">
        Lets get your event on the calendar:<br /><br />
        <form action="index.php?page=calendar" method="post" enctype='multipart/form-data'>
            <table style="width:100%;" cellspacing="0px">
                <tr>
                    <td>
                        Event Title
                    </td>
                    <td>
                        <input type="text" name="title" size="70" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div style="margin:15px 50px; background-color:#dddddd; border:1px solid <?php

echo $highlightColor;
	?>; height:5px; width:300px;"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        Start time / date
                    </td>
                    <td>
                        <table cellspacing="0">
                            <tr>
                                <td>
                                    <div style="text-align:center;">h</div>
                                </td>
                                <td>
                                    <div style="text-align:center;">m</div>
                                </td>
                                <td>
                                    <div style="text-align:center;">M</div>
                                </td>
                                <td>
                                    <div style="text-align:center;">D</div>
                                </td>
                                <td>
                                    <div style="text-align:center;">Y</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select size="1" name="startHour">
                                        <?php
	for($a = 0; $a <= 23; $a ++) {
		echo "<option value='$a'>$a</option>\n";
	}
	?>
                                    </select>
                                </td>
                                <td>
                                    <select size="1" name="startMinute">
                                        <?php
	for($b = 00; $b <= 45; $b = $b + 15) {
		echo "<option value='$b'>$b</option>\n";
	}
	?>
                                    </select>
                                </td>
                                <td>
                                    <select size="1" name="startMonth">
                                        <?php
	for($c = 1; $c <= 12; $c ++) {
		echo "<option value='$c'>$c</option>\n";
	}
	?>
                                    </select>
                                </td>
                                <td>
                                    <select size="1" name="startDay">
                                        <?php
	for($d = 1; $d <= 31; $d ++) {
		echo "<option value='$d'>$d</option>\n";
	}
	?>
                                    </select>
                                </td>
                                <td>
                                    <select size="1" name="startYear">
                                        <?php
	$thisY = date ( "Y" );
	echo "<option value='$thisY'>$thisY</option>\n";
	echo "<option value='" . ($thisY + 1) . "'>" . ($thisY + 1) . "</option>\n";
	?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div style="margin:15px 50px; background-color:#dddddd; border:1px solid <?php

echo $highlightColor;
	?>; height:5px; width:300px;"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        Upload an image
                    </td>
                    <td>
                        <input type="file" name="image" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div style="margin:15px 50px; background-color:#dddddd; border:1px solid <?php

echo $highlightColor;
	?>; height:5px; width:300px;"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        Event write up
                    </td>
                    <td>
                        <textarea name="writeUp" cols="60" rows="10"></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div style="margin:15px 50px; background-color:#dddddd; border:1px solid <?php

echo $highlightColor;
	?>; height:5px; width:300px;"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" value=" Add event " />
                    </td>
                    <td>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <br /><br />
    <?php
} else {
	?>
    <div style="text-decoration:underline; font-size:1em; font-weight:bold; cursor:pointer;" onclick="showMenu('signInBox', 'signInLink')">Sign in or register to add your event to the calendar</div><br /><br />
    <?php
}
$_SESSION ['preview'] = (filter_input ( INPUT_GET, 'preview', FILTER_SANITIZE_NUMBER_INT )) ? filter_input ( INPUT_GET, 'preview', FILTER_SANITIZE_NUMBER_INT ) : 3;
$p = $_SESSION ['preview'];
$h = (filter_input ( INPUT_GET, 'h', FILTER_SANITIZE_NUMBER_INT )) ? filter_input ( INPUT_GET, 'h', FILTER_SANITIZE_NUMBER_INT ) : 0;
$m = date ( 'n' ) + $p;
$y = date ( 'Y' );
echo "<form action='index.php' method='get'><input type='hidden' name='page' value='calendar' />View events happening during the next <select name='preview' size='1'>";
echo "<option value='1'";
if ($p == '1') {
	echo " selected";
}
echo ">this month</option>\n";
echo "<option value='2'";
if ($p == '2') {
	echo " selected";
}
echo ">2 months</option>\n";
echo "<option value='3'";
if ($p == '3') {
	echo " selected";
}
echo ">3 months</option>\n";
echo "<option value='6'";
if ($p == '6') {
	echo " selected";
}
echo ">6 months</option>\n";
echo "<option value='12'";
if ($p == '12') {
	echo " selected";
}
echo ">12 months</option>\n";
echo "</select> <input type='submit' value=' Go ' /></form><br /><br />";
echo "<div id='printArea'>";
$llm = date ( 'n', $time );
$lld = date ( 'j', $time );
$lly = date ( 'Y', $time );
$lowerLimit = mktime ( 0, 0, 0, $llm, $lld, $lly );
$limit = mktime ( 23, 59, 59, $m, - 1, $y );
$stmt = $db->prepare ( "SELECT * FROM calendar WHERE startTime >= ? && startTime <= ? && approved = '1' ORDER BY startTime" );
$stmt->execute ( array (
		$lowerLimit,
		$limit
) );
while ( $row = $stmt->fetch () ) {
	$cId = $row ['id'];
	$cStartTime = $row ['startTime'];
	$cTitle = $row ['title'];
	$cWriteUp = nl2br ( make_links_clickable ( html_entity_decode ( $row ['writeUp'], ENT_QUOTES ), $highlightColor ) );
	$cPic = $row ['picture'];
	$cUserId = $row ['userId'];

	echo "<a href='index.php?page=calendar&h=" . $cId . "#c" . $cId . "'><div id='c$cId'></div><div style='text-align:center; width:98%; border:2px solid $highlightColor; cursor:pointer; padding:5px;' ><span style='font-size:1.5em; font-weight:bold; text-decoration:none;'>$cTitle</span><br />";
	echo "<div style='font-size:1em; text-align:center;'>" . date ( 'M jS, g:ia', $cStartTime ) . "</div></div></a>";
	$dis = ($h == $cId) ? "block" : "none";
	if (file_exists ( "userPics/$cUserId/$cPic" )) {
		list ( $wi, $hi ) = (getimagesize ( "userPics/$cUserId/$cPic" ) != null) ? getimagesize ( "userPics/$cUserId/$cPic" ) : null;
		$maxW = 300;
		$maxH = 400;
		if (($wi / $hi) > ($maxW / $maxH) && $maxW < $wi) {
			$th = ($maxW / $wi * $hi) + 10;
		} elseif (($wi / $hi) < ($maxW / $maxH) && $maxH < $hi) {
			$th = $maxH + 10;
		} elseif ($max < $wi) {
			$th = $maxW + 10;
		}
		echo "<div id='e$cId' style='margin:0px 20px; border:1px solid $highlightColor; display:$dis; padding:10px; min-height:" . $th . "px;'>";
		echo "<a href='userPics/$cUserId/$cPic' data-lightbox='images'><img src='userPics/$cUserId/$cPic' style='float:right; margin:0px 0px 5px 5px; padding:3px; border:1px solid $highlightColor; max-width:" . $maxW . "px; max-height:" . $maxH . "px;' /></a>";
		echo "";
	} else {
		echo "<div id='e$cId' style='margin:0px 20px; border:1px solid $highlightColor; display:$dis; padding:10px'>";
	}
	?>
    <div style="float:right;" class="fb-like" data-share="true" data-width="100" data-layout="button" data-show-faces="false"></div>
    <?php
	echo "<div style='float:right; margin:3px 10px 0px 10px;'><a href='https://twitter.com/share' class='twitter-share-button' data-via='MyLocalLife'>Tweet</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></div>";
	echo "<article style='text-align:justify;'>$cWriteUp</article></div>";
	echo "<div style='width:100%; height:15px;'>&nbsp;</div>";
}
echo "</div>";
