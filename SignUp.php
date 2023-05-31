<?php
$view = 0;
$errorSignUp = "";
if (filter_input ( INPUT_POST, 'signUpId', FILTER_SANITIZE_NUMBER_INT ) >= 1) {
	$uSignUpId = filter_input ( INPUT_POST, 'signUpId', FILTER_SANITIZE_NUMBER_INT );
	$uName = filter_input ( INPUT_POST, 'name', FILTER_SANITIZE_STRING );
	$uEmail = filter_input ( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL );
	$uPhone = filter_input ( INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT );
	$l = 0;
	$goUp = 1;
	$up1 = $db->prepare ( "SELECT limitNum FROM signUpTimes WHERE id = ?" );
	$up1->execute ( array (
			$uSignUpId
	) );
	$up1R = $up1->fetch ();
	if ($up1R) {
		$l = $up1R ['limitNum'];
	}
	if ($l >= 1) {
		$up2 = $db->prepare ( "SELECT COUNT(*) FROM signUpRegistry WHERE signUpId = ?" );
		$up2->execute ( array (
				$uSignUpId
		) );
		$up2R = $up2->fetch ();
		if ($up2R) {
			$lc = $up2R [0];
		}
		if ($lc >= $l) {
			$goUp = 0;
			$errorSignUp = "Unable to save sign up. The space has been filled by someone else.";
		}
	}
	if ($goUp == 1) {
		$up3 = $db->prepare ( "INSERT INTO signUpRegistry VALUES(NULL,?,?,?,?,?,'0','0','0')" );
		$up3->execute ( array (
				$uSignUpId,
				$uName,
				$uEmail,
				$uPhone,
				$time
		) );
		$errorSignUp = "Your sign up has been saved.";
	}
}
if (filter_input ( INPUT_GET, 'viewSignUp', FILTER_SANITIZE_NUMBER_INT )) {
	$v = filter_input ( INPUT_GET, 'viewSignUp', FILTER_SANITIZE_NUMBER_INT );
	$getV = $db->prepare ( "SELECT COUNT(*) FROM signUp WHERE id = ?" );
	$getV->execute ( array (
			$v
	) );
	$getVR = $getV->fetch ();
	if ($getVR && $getVR [0] == 1) {
		$view = $v;
	}
}

if ($view == 0) {
	echo "<div style='text-align:center; font-weight:bold; font-size:2em; margin:30px 0px;'>Available Sign Ups</div>\n";
	$getS1 = $db->prepare ( "SELECT * FROM signUp WHERE startSignUp <= ? AND endSignUp >= ? ORDER BY endSignUp" );
	$getS1->execute ( array (
			$time,
			$time
	) );
	while ( $getSR1 = $getS1->fetch () ) {
		$id = $getSR1 ['id'];
		$userId = $getSR1 ['userId'];
		$title = html_entity_decode ( $getSR1 ['title'], ENT_QUOTES );
		$pic = $getSR1 ['pic'];
		$endSignUp = $getSR1 ['endSignUp'];

		$s = 0;
		$e = 0;
		$tRange = $db->prepare ( "SELECT start, end FROM signUpTimes WHERE subOfId = ?" );
		$tRange->execute ( array (
				$id
		) );
		while ( $tr = $tRange->fetch () ) {
			$st = $tr ['start'];
			$en = $tr ['end'];
			if ($s == 0) {
				$s = $st;
			}
			$s = ($s > $st) ? $st : $s;
			if ($e == 0) {
				$e = $en;
			}
			$e = ($e < $en) ? $en : $e;
		}

		echo "<div class='clearfix'>";
		if (file_exists ( "userPics/$userId/$pic" )) {
			echo "<img src='userPics/$userId/$pic' style='max-width:100px; max-height:100px; float:left; margin:15px;'>\n";
		}
		echo "<div style='text-align:center; font-weight:bold; font-size:1.5em; margin-top:15px;'><a href='index.php?page=SignUp&viewSignUp=$id'>$title</a></div>\n";
		echo "<div style='text-align:center; font-weight:bold; font-size:1.25em; margin-top:15px;'>" . date ( 'H:i', $s ) . " to " . date ( 'H:i', $e ) . " " . date ( 'Y-m-d', $s ) . "</div>\n";
		echo "<div style='text-align:center; font-weight:normal; font-size:1em; margin:15px;'> Sign Up ends @ " . date ( 'H:i Y-m-d', $endSignUp ) . "</div></div>\n";
		echo "<hr style='width:75%; margin:10px auto;'>\n";
	}
} else {
	$getS2 = $db->prepare ( "SELECT * FROM signUp WHERE id = ?" );
	$getS2->execute ( array (
			$view
	) );
	$getSR2 = $getS2->fetch ();
	$userId = $getSR2 ['userId'];
	$title = html_entity_decode ( $getSR2 ['title'], ENT_QUOTES );
	$description = nl2br ( html_entity_decode ( $getSR2 ['description'], ENT_QUOTES ) );
	$pic = $getSR2 ['pic'];
	$startSignUp = $getSR2 ['startSignUp'];
	$endSignUp = $getSR2 ['endSignUp'];

	$s = 0;
	$e = 0;
	$tRange = $db->prepare ( "SELECT start, end FROM signUpTimes WHERE subOfId = ?" );
	$tRange->execute ( array (
			$view
	) );
	while ( $tr = $tRange->fetch () ) {
		$st = $tr ['start'];
		$en = $tr ['end'];
		if ($s == 0) {
			$s = $st;
		}
		$s = ($s > $st) ? $st : $s;
		if ($e == 0) {
			$e = $en;
		}
		$e = ($e < $en) ? $en : $e;
	}

	echo "<div class='clearfix'>";
	if (file_exists ( "userPics/$userId/$pic" )) {
		echo "<img src='userPics/$userId/$pic' style='max-width:200px; max-height:200px; float:left; margin:15px;'>\n";
	}
	echo "<div style='text-align:center; font-weight:bold; font-size:1.5em; margin-top:15px;'>$title</div>\n";
	echo "<div style='text-align:center; font-weight:bold; font-size:1.25em; margin-top:15px;'>" . date ( 'H:i', $s ) . " to " . date ( 'H:i', $e ) . " " . date ( 'Y-m-d', $s ) . "</div>\n";
	echo "<div style='text-align:center; font-weight:normal; font-size:1em; margin:15px;'> Sign Up closes @ " . date ( 'H:i Y-m-d', $endSignUp ) . "</div>\n";
	echo "<div style='text-align:justify; font-weight:normal; font-size:1.25em; margin:15px;'>$description</div></div>\n";
	echo "<div style='margin:15px;'>\n";
	if ($startSignUp <= $time && $endSignUp >= $time) {
		echo "<div style='text-align:center; font-weight:bold; font-size:1.25em; margin-bottom:15px;'>Available Sign Ups</div>\n";
		if ($errorSignUp != "") {
			echo "<div style='text-align:center; font-weight:bold; font-size:1.25em; margin-bottom:15px;'>$errorSignUp</div>\n";
		}
		$getT = $db->prepare ( "SELECT * FROM signUpTimes WHERE subOfId = ? ORDER BY start" );
		$getT->execute ( array (
				$view
		) );
		while ( $getTR = $getT->fetch () ) {
			$tId = $getTR ['id'];
			$tStart = $getTR ['start'];
			$tEnd = $getTR ['end'];
			$tLimit = $getTR ['limitNum'];
			$isFull = 0;
			if ($tLimit >= 1) {
				$getR = $db->prepare ( "SELECT COUNT(*) FROM signUpRegistry WHERE signUpId = ?" );
				$getR->execute ( array (
						$tId
				) );
				$getRR = $getR->fetch ();
				$c = ($getRR) ? $getRR [0] : 0;
				if ($c >= $tLimit) {
					$isFull = 1;
				}
			}
			echo "<div style='border:1px solid black; width:90%;'>\n";
			echo "<div class='clearfix'>\n";
			echo "<div style='float:left; padding:10px; width:75px;'>\n";
			if ($isFull == 0) {
				echo "<button onclick='toggleview(\"register$tId\")'>Register</button>";
			} else {
				echo "Full";
			}
			echo "</div>\n";
			echo "<div style='padding:10px;'>Starts @ " . date ( 'H:i', $tStart ) . " and ends @ " . date ( 'H:i', $tEnd ) . " " . date ( 'Y-m-d', $tStart ) . "</div></div>\n";
			if ($isFull == 0) {
				echo "<div id='register$tId' style='display:none; padding:10px;'>\n";
				echo "<form action='index.php?page=SignUp&viewSignUp=$view' method='post'>\n";
				echo "<table cellspacing='5px'>";
				echo "<tr><td>Name:</td><td><input type='text' name='name' value='' placeholder='Name' maxlength='200' required></td></tr>\n";
				echo "<tr><td>Email:</td><td><input type='email' name='email' value='' placeholder='Email' maxlength='200' required></td></tr>\n";
				echo "<tr><td>Phone:</td><td><input type='text' name='phone' value='' placeholder='Phone' maxlength='20' required></td></tr>\n";
				echo "<tr><td colspan='2'><input type='submit' value=' Register '><input type='hidden' name='signUpId' value='$tId'></td></tr></table></form></div>\n";
			}
			echo "</div>\n";
		}
	} else {
		echo "<div style='text-align:center; font-weight:bold; font-size:1.25em;'>Signing up for this event is not currently available</div>\n";
	}
	echo "</div>\n";
}