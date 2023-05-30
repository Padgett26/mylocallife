<?php
$stripId = filter_input ( INPUT_POST, 'editedStrip', FILTER_SANITIZE_STRING );
$stripTitle = filter_input ( INPUT_POST, 'stripTitle', FILTER_SANITIZE_STRING );
$imageWidth = filter_input ( INPUT_POST, 'imageWidth', FILTER_SANITIZE_NUMBER_INT );
$imageHeight = filter_input ( INPUT_POST, 'imageHeight', FILTER_SANITIZE_NUMBER_INT );
$startDay = filter_input ( INPUT_POST, 'startDay', FILTER_SANITIZE_NUMBER_INT );
$startMonth = filter_input ( INPUT_POST, 'startMonth', FILTER_SANITIZE_NUMBER_INT );
$startYear = filter_input ( INPUT_POST, 'startYear', FILTER_SANITIZE_NUMBER_INT );
$endDay = filter_input ( INPUT_POST, 'endDay', FILTER_SANITIZE_NUMBER_INT );
$endMonth = filter_input ( INPUT_POST, 'endMonth', FILTER_SANITIZE_NUMBER_INT );
$endYear = filter_input ( INPUT_POST, 'endYear', FILTER_SANITIZE_NUMBER_INT );
$displayDayStart = mktime ( 0, 0, 0, $startMonth, $startDay, $startYear );
$displayDayEnd = mktime ( 23, 59, 59, $startMonth, $startDay, $startYear );
$delStrip = (filter_input ( INPUT_POST, 'delStrip', FILTER_SANITIZE_NUMBER_INT ) == '1') ? "1" : "0";
$removeBackPic = (filter_input ( INPUT_POST, 'removeBackPic', FILTER_SANITIZE_NUMBER_INT ) == '1') ? "1" : "0";

if ($removeBackPic == '1') {
	$getId = $db->prepare ( "SELECT userId, backExt FROM strips WHERE id = ?" );
	$getId->execute ( array (
			$stripId
	) );
	$getRow = $getId->fetch ();
	$getUserId = $getRow ['userId'];
	$getBackExt = $getRow ['backExt'];
	$bgStripTitle = str_replace ( " ", "", strtolower ( "back" . $stripTitle ) );
	if (file_exists ( "userPics/$getUserId/$bgStripTitle.$getBackExt" )) {
		unlink ( "userPics/$getUserId/$bgStripTitle.$getBackExt" );
	}
}
if ($delStrip == "1") {
	$stmt = $db->prepare ( "SELECT picName, picExt FROM strips WHERE id=?" );
	$stmt->execute ( array (
			$stripId
	) );
	$row = $stmt->fetch ();
	$pn = $row ['picName'];
	$pe = $row ['picExt'];
	if (file_exists ( "userPics/$myId/$pn.$pe" )) {
		unlink ( "userPics/$myId/$pn.$pe" );
	}
	$substmt = $db->prepare ( "DELETE FROM strips WHERE id=?" );
	$substmt->execute ( array (
			$stripId
	) );
} else {
	if ($stripId == "new") {
		$newsstmt = $db->prepare ( "INSERT INTO strips VALUES" . "(NULL, ?, ?, 'jpg', ?, ?, ?', '0', '0', '0')" );
		$newsstmt->execute ( array (
				$myId,
				'0',
				$stripTitle,
				$displayDayStart,
				$displayDayEnd
		) );
		$getidstmt = $db->prepare ( "SELECT id FROM strips WHERE stripTitle=? && userId=? ORDER BY id DESC LIMIT 1" );
		$getidstmt->execute ( array (
				$stripTitle,
				$myId
		) );
		$getidrow = $getidstmt->fetch ();
		$stripId = $getidrow ['id'];
	}
	$image = $_FILES ["image"] ["tmp_name"];
	$imageName = $time;
	list ( $width, $height ) = (getimagesize ( $image ) != null) ? getimagesize ( $image ) : null;
	if ($width != null && $height != null) {
		$imageType = getPicType ( $_FILES ["image"] ['type'] );
		processPic ( $myId, $imageName, $imageWidth, $imageHeight, $image, $imageType );
		$p1stmt = $db->prepare ( "UPDATE strips SET picName=?, picExt=? WHERE id=?" );
		$p1stmt->execute ( array (
				$imageName,
				$imageType,
				$stripId
		) );
	}
	$imageBack = $_FILES ["imageBack"] ["tmp_name"];
	$imageBackName = str_replace ( " ", "", strtolower ( "back" . $stripTitle ) );
	list ( $widthBack, $heightBack ) = (getimagesize ( $imageBack ) != null) ? getimagesize ( $imageBack ) : null;
	if ($widthBack != null && $heightBack != null) {
		$imageBackType = getPicType ( $_FILES ["imageBack"] ['type'] );
		processPic ( $myId, $imageBackName, '1200', '1200', $imageBack, $imageBackType );
		$p1stmt = $db->prepare ( "UPDATE strips SET backExt=? WHERE userId=? && stripTitle=?" );
		$p1stmt->execute ( array (
				$imageBackType,
				$myId,
				$stripTitle
		) );
	}
	$stripstmt = $db->prepare ( "UPDATE strips SET stripTitle=?, displayDayStart=?, displayDayEnd=? WHERE id=?" );
	$stripstmt->execute ( array (
			$stripTitle,
			$displayDayStart,
			$displayDayEnd,
			$stripId
	) );
}