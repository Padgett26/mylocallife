<?php
$myBusinessUp = filter_input ( INPUT_POST, 'myBusinessUp', FILTER_SANITIZE_NUMBER_INT );
$busiName = trim ( filter_input ( INPUT_POST, 'busiName', FILTER_SANITIZE_STRING ) );
$busiPhone = trim ( filter_input ( INPUT_POST, 'busiPhone', FILTER_SANITIZE_STRING ) );
$hoursOfOperation = trim ( filter_input ( INPUT_POST, 'hoursOfOperation', FILTER_SANITIZE_STRING ) );
$busiAddress1 = trim ( filter_input ( INPUT_POST, 'busiAddress1', FILTER_SANITIZE_STRING ) );
$busiAddress2 = trim ( filter_input ( INPUT_POST, 'busiAddress2', FILTER_SANITIZE_STRING ) );
$a2 = htmlEntities ( trim ( $_POST ['busiDescText'] ), ENT_QUOTES );
$busiDescText = filter_var ( $a2, FILTER_SANITIZE_STRING );
$busiEmail = trim ( filter_input ( INPUT_POST, 'busiEmail', FILTER_SANITIZE_EMAIL ) );
$imageWidth = filter_input ( INPUT_POST, 'imageWidth', FILTER_SANITIZE_NUMBER_INT );
$imageHeight = filter_input ( INPUT_POST, 'imageHeight', FILTER_SANITIZE_NUMBER_INT );
$busiCat = filter_input ( INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT );

$stmt = $db->prepare ( "UPDATE busiListing SET busiName=?, busiPhone=?, hoursOfOperation=?, busiAddress1=?, busiAddress2=?, busiDescText=?, busiEmail=?, category=? WHERE userId=?" );
$stmt->execute ( array (
		$busiName,
		$busiPhone,
		$hoursOfOperation,
		$busiAddress1,
		$busiAddress2,
		$busiDescText,
		$busiEmail,
		$busiCat,
		$myId
) );

$image1 = $_FILES ["image1"] ["tmp_name"];
$image1Name = ($time + 1);
list ( $width1, $height1 ) = (getimagesize ( $image1 ) != null) ? getimagesize ( $image1 ) : null;
if ($width1 != null && $height1 != null) {
	$image1Type = getPicType ( $_FILES ["image1"] ['type'] );
	processPic ( $myId, $image1Name, $imageWidth, $imageHeight, $image1, $image1Type );
	processThumbPic ( $myId, $image1Name, '100', '100', $image1, $image1Type );
	$p1stmt = $db->prepare ( "UPDATE busiListing SET busiPic1=?, busiPicExt1=? WHERE userId=?" );
	$p1stmt->execute ( array (
			$image1Name,
			$image1Type,
			$myId
	) );
}
$image2 = $_FILES ["image2"] ["tmp_name"];
$image2Name = ($time + 2);
list ( $width2, $height2 ) = (getimagesize ( $image2 ) != null) ? getimagesize ( $image2 ) : null;
if ($width2 != null && $height2 != null) {
	$image2Type = getPicType ( $_FILES ["image2"] ['type'] );
	processPic ( $myId, $image2Name, $imageWidth, $imageHeight, $image2, $image2Type );
	processThumbPic ( $myId, $image2Name, '100', '100', $image2, $image2Type );
	$p2stmt = $db->prepare ( "UPDATE busiListing SET busiPic2=?, busiPicExt2=? WHERE userId=?" );
	$p2stmt->execute ( array (
			$image2Name,
			$image2Type,
			$myId
	) );
}