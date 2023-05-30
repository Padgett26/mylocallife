<?php
$del = (filter_input ( INPUT_POST, 'delSU', FILTER_SANITIZE_NUMBER_INT ) == 1) ? 1 : 0;
$dId = filter_input ( INPUT_POST, 'signUpDel', FILTER_SANITIZE_NUMBER_INT );

if ($del == 1 && $dId >= 1) {
	$d1 = $db->prepare ( "SELECT pic FROM signUp WHERE id = ?" );
	$d1->execute ( array (
			$dId
	) );
	$d1R = $d1->fetch ();
	if ($d1R) {
		$p = $d1R ['pic'];
		if (file_exists ( "userPics/$myId/$p" )) {
			unlink ( "userPics/$myId/$p" );
		}
		$d2 = $db->prepare ( "SELECT id FROM signUpTimes WHERE subOfId = ?" );
		$d2->execute ( array (
				$dId
		) );
		while ( $d2R = $d2->fetch () ) {
			$tId = $d2R ['id'];
			$d3 = $db->prepare ( "DELETE FROM signUpRegistry WHERE signUpId = ?" );
			$d3->execute ( array (
					$tId
			) );
		}
		$d4 = $db->prepare ( "DELETE FROM signUpTimes WHERE subOfId = ?" );
		$d4->execute ( array (
				$dId
		) );
	}
	$d5 = $db->prepare ( "DELETE FROM signUp WHERE id = ?" );
	$d5->execute ( array (
			$dId
	) );
}

$title = htmlentities ( filter_input ( INPUT_POST, 'title', FILTER_SANITIZE_STRING ), ENT_QUOTES );
$description = htmlentities ( filter_input ( INPUT_POST, 'description', FILTER_SANITIZE_STRING ), ENT_QUOTES );
$startSignUp = filter_input ( INPUT_POST, 'startSignUp', FILTER_SANITIZE_NUMBER_INT );
$endSignUp = filter_input ( INPUT_POST, 'endSignUp', FILTER_SANITIZE_NUMBER_INT );
$useTS1 = filter_input ( INPUT_POST, 'useTS1', FILTER_SANITIZE_NUMBER_INT );
$useTS2 = filter_input ( INPUT_POST, 'useTS2', FILTER_SANITIZE_NUMBER_INT );
$useTS3 = filter_input ( INPUT_POST, 'useTS3', FILTER_SANITIZE_NUMBER_INT );
$startDate1 = filter_input ( INPUT_POST, 'startDate1', FILTER_SANITIZE_NUMBER_INT );
$startHour1 = filter_input ( INPUT_POST, 'startHour1', FILTER_SANITIZE_NUMBER_INT );
$startMin1 = filter_input ( INPUT_POST, 'startMin1', FILTER_SANITIZE_NUMBER_INT );
$endDate1 = filter_input ( INPUT_POST, 'endDate1', FILTER_SANITIZE_NUMBER_INT );
$endHour1 = filter_input ( INPUT_POST, 'endHour1', FILTER_SANITIZE_NUMBER_INT );
$endMin1 = filter_input ( INPUT_POST, 'endMin1', FILTER_SANITIZE_NUMBER_INT );
$subMin1 = filter_input ( INPUT_POST, 'subMin1', FILTER_SANITIZE_NUMBER_INT );
$limit1 = filter_input ( INPUT_POST, 'limit1', FILTER_SANITIZE_NUMBER_INT );
$startDate2 = filter_input ( INPUT_POST, 'startDate2', FILTER_SANITIZE_NUMBER_INT );
$startHour2 = filter_input ( INPUT_POST, 'startHour2', FILTER_SANITIZE_NUMBER_INT );
$startMin2 = filter_input ( INPUT_POST, 'startMin2', FILTER_SANITIZE_NUMBER_INT );
$endDate2 = filter_input ( INPUT_POST, 'endDate2', FILTER_SANITIZE_NUMBER_INT );
$endHour2 = filter_input ( INPUT_POST, 'endHour2', FILTER_SANITIZE_NUMBER_INT );
$endMin2 = filter_input ( INPUT_POST, 'endMin2', FILTER_SANITIZE_NUMBER_INT );
$subMin2 = filter_input ( INPUT_POST, 'subMin2', FILTER_SANITIZE_NUMBER_INT );
$limit2 = filter_input ( INPUT_POST, 'limit2', FILTER_SANITIZE_NUMBER_INT );
$startDate3 = filter_input ( INPUT_POST, 'startDate3', FILTER_SANITIZE_NUMBER_INT );
$startHour3 = filter_input ( INPUT_POST, 'startHour3', FILTER_SANITIZE_NUMBER_INT );
$startMin3 = filter_input ( INPUT_POST, 'startMin3', FILTER_SANITIZE_NUMBER_INT );
$endDate3 = filter_input ( INPUT_POST, 'endDate3', FILTER_SANITIZE_NUMBER_INT );
$endHour3 = filter_input ( INPUT_POST, 'endHour3', FILTER_SANITIZE_NUMBER_INT );
$endMin3 = filter_input ( INPUT_POST, 'endMin3', FILTER_SANITIZE_NUMBER_INT );
$subMin3 = filter_input ( INPUT_POST, 'subMin3', FILTER_SANITIZE_NUMBER_INT );
$limit3 = filter_input ( INPUT_POST, 'limit3', FILTER_SANITIZE_NUMBER_INT );

if ($title != "" && $description != "") {
	$s = explode ( "-", $startSignUp );
	$start = mktime ( 0, 0, 0, $s [1], $s [2], $s [0] );
	$e = explode ( "-", $endSignUp );
	$end = mktime ( 23, 59, 59, $e [1], $e [2], $e [0] );
	$u1 = $db->prepare ( "INSERT INTO signUp VALUES(NULL,?,?,?,'x.jpg',?,?,'0','0','0')" );
	$u1->execute ( array (
			$myId,
			$title,
			$description,
			$start,
			$end
	) );
	$u2 = $db->prepare ( "SELECT id FROM signUp WHERE userId = ? AND title = ? AND description = ? AND startSignUp = ? AND endSignUp = ? ORDER BY id DESC LIMIT 1" );
	$u2->execute ( array (
			$myId,
			$title,
			$description,
			$start,
			$end
	) );
	$u2R = $u2->fetch ();
	if ($u2R) {
		$suId = $u2R ['id'];
	}
	if ($_FILES ['image'] ['size'] >= 1000) {
		$image = $_FILES ["image"] ["tmp_name"];
		$imageName = $time;
		list ( $width, $height ) = (getimagesize ( $image ) != null) ? getimagesize ( $image ) : null;
		if ($width != null && $height != null) {
			$imageType = getPicType ( $_FILES ["image"] ['type'] );
			processPic ( $myId, $imageName, '400', '400', $image, $imageType );
			$p1stmt = $db->prepare ( "UPDATE signUp SET pic=? WHERE id=?" );
			$p1stmt->execute ( array (
					$imageName . "." . $imageType,
					$suId
			) );
		}
	}
	for($i = 1; $i <= 3; ++ $i) {
		if (${"useTS" . $i} == 1) {
			$sd = explode ( "-", ${"startDate" . $i} );
			$st = mktime ( ${"startHour" . $i}, ${"startMin" . $i}, 0, $sd [1], $sd [2], $sd [0] );
			$ed = explode ( "-", ${"endDate" . $i} );
			$et = mktime ( ${"endHour" . $i}, ${"endMin" . $i}, 0, $ed [1], $ed [2], $ed [0] );
			if (${"subMin" . $i} >= 1) {
				$t = (${"subMin" . $i} * 60);
				for($j = $st; $j < $et; $j = $j + $t) {
					$u3 = $db->prepare ( "INSERT INTO signUpTimes VALUES(NULL,?,?,?,?,'0','0','0')" );
					$u3->execute ( array (
							$suId,
							$j,
							($j + $t - 1),
							${"limit" . $i}
					) );
				}
			} else {
				$u4 = $db->prepare ( "INSERT INTO signUpTimes VALUES(NULL,?,?,?,?,'0','0','0')" );
				$u4->execute ( array (
						$suId,
						$st,
						$et,
						${"limit" . $i}
				) );
			}
		}
	}
}