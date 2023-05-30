<?php
$photoId = filter_input ( INPUT_POST, 'photoUp', FILTER_SANITIZE_NUMBER_INT );
$photoTitle = filter_input ( INPUT_POST, 'photoTitle', FILTER_SANITIZE_STRING );
$a2 = htmlEntities ( trim ( $_POST ['photoText'] ), ENT_QUOTES );
$photoText = filter_var ( $a2, FILTER_SANITIZE_STRING );
$delPhoto = (filter_input ( INPUT_POST, 'delPhoto', FILTER_SANITIZE_NUMBER_INT ) == '1') ? '1' : '0';

if ($delPhoto == '1') {
	$p1 = $db->prepare ( "DELETE FROM photoJournalism WHERE id=?" );
	$p1->execute ( array (
			$photoId
	) );
	$p5 = $db->prepare ( "SELECT photoName, photoExt FROM photoList WHERE photoId=?" );
	$p5->execute ( array (
			$photoId
	) );
	while ( $pr5 = $p5->fetch () ) {
		if (file_exists ( "userPics/$myId/" . $pr5 ['photoName'] . "." . $pr5 ['photoExt'] )) {
			unlink ( "userPics/$myId/" . $pr5 ['photoName'] . "." . $pr5 ['photoExt'] );
		}
		if (file_exists ( "userPics/$myId/thumb/" . $pr5 ['photoName'] . "." . $pr5 ['photoExt'] )) {
			unlink ( "userPics/$myId/thumb/" . $pr5 ['photoName'] . "." . $pr5 ['photoExt'] );
		}
	}
	$p2 = $db->prepare ( "DELETE FROM photoList WHERE photoId=?" );
	$p2->execute ( array (
			$photoId
	) );
	$p4 = $db->prepare ( "DELETE FROM photoLog WHERE photoId=?" );
	$p4->execute ( array (
			$photoId
	) );
} else {
	$p3 = $db->prepare ( "UPDATE photoJournalism SET photoTitle=?, photoText=? WHERE id=?" );
	$p3->execute ( array (
			$photoTitle,
			$photoText,
			$photoId
	) );

	foreach ( $_FILES as $key => $val ) {
		if (preg_match ( "/^image([1-9][0-9]*)$/", $key, $match )) {
			$iId = $match [1];
			$image = $_FILES ["$key"] ["tmp_name"];
			$imageName = ($time + $iId);
			list ( $width, $height ) = (getimagesize ( $image ) != null) ? getimagesize ( $image ) : null;
			if ($width != null && $height != null) {
				$imageType = getPicType ( $_FILES ["$key"] ['type'] );
				processPic ( $myId, $imageName, '1000', '1000', $image, $imageType );
				processThumbPic ( $myId, $imageName, '100', '100', $image, $imageType );
				$pstmt1 = $db->prepare ( "SELECT COUNT(*) FROM photoList WHERE photoId=?" );
				$pstmt1->execute ( array (
						$photoId
				) );
				$prow1 = $pstmt1->fetch ();
				$pCount = $prow1 [0] + 1;
				if ($iId < $pCount) {
					$pstmt3 = $db->prepare ( "UPDATE photoList SET photoName = ?, photoExt = ? WHERE photoId = ? && photoOrder = ?" );
					$pstmt3->execute ( array (
							$imageName,
							$imageType,
							$photoId,
							$iId
					) );
				} else {
					$pstmt2 = $db->prepare ( "INSERT INTO photoList VALUES(NULL,?,?,?,?,?,'0','0','0')" );
					$pstmt2->execute ( array (
							$photoId,
							$imageName,
							$imageType,
							' ',
							$pCount
					) );
				}
			}
		}
	}
	foreach ( $_POST as $key => $val ) {
		if (preg_match ( "/^photoCaption([1-9][0-9]*)$/", $key, $match )) {
			$cId = $match [1];
			$photoCaption = filter_var ( $val, FILTER_SANITIZE_STRING );
			$pstmt = $db->prepare ( "UPDATE photoList SET photoCaption=? WHERE photoId=? && photoOrder=?" );
			$pstmt->execute ( array (
					$photoCaption,
					$photoId,
					$cId
			) );
		}
		if (preg_match ( "/^photoOrder([1-9][0-9]*)$/", $key, $match )) {
			$oId = $match [1];
			$photoOrder = filter_var ( $val, FILTER_SANITIZE_NUMBER_INT );
			// Grab the id of the picture whos order is changing
			$p10 = $db->prepare ( "SELECT id FROM photoList WHERE photoId = ? && photoOrder = ?" );
			$p10->execute ( array (
					$photoId,
					$oId
			) );
			$pr10 = $p10->fetch ();
			$oldOrderId = $pr10 ['id'];
			// Move all of the pictures who are equal to or later in the order by +1
			$p7 = $db->prepare ( "UPDATE photoList SET photoOrder = photoOrder + 1 WHERE photoOrder >= ? && photoId = ?" );
			$p7->execute ( array (
					$photoOrder,
					$photoId
			) );
			// Move the picture in to the hole that is opened up
			$p11 = $db->prepare ( "UPDATE photoList SET photoOrder = ? WHERE id = ?" );
			$p11->execute ( array (
					$photoOrder,
					$oldOrderId
			) );
			// renumber the order to clean up any gaps
			$t = 1;
			$p12 = $db->prepare ( "SELECT id FROM photoList WHERE photoId = ? ORDER BY photoOrder" );
			$p12->execute ( array (
					$photoId
			) );
			while ( $pr12 = $p12->fetch () ) {
				$pid = $pr12 ['id'];
				$p13 = $db->prepare ( "UPDATE photoList SET photoOrder = ? WHERE id = ?" );
				$p13->execute ( array (
						$t,
						$pid
				) );
				$t ++;
			}
		}
	}
}