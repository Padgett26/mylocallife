<?php
if (filter_input ( INPUT_POST, 'candLogIn', FILTER_SANITIZE_NUMBER_INT ) == '1') {
	$pwd = filter_input ( INPUT_POST, 'pwd', FILTER_SANITIZE_STRING );
	$hidepwd = hash ( 'sha512', ("*!*" . $pwd), FALSE );
	$id = filter_input ( INPUT_POST, 'pwd', FILTER_SANITIZE_NUMBER_INT );
	$a = $db->prepare ( "SELECT COUNT(*) FROM candidates WHERE id = ? && pwd = ?" );
	$a->execute ( array (
			$id,
			$hidepwd
	) );
	$arow = $a->fetch ();
	if ($arow [0] == 1) {
		$_SESSION ['candidate'] = $id;
	} else {
		$_SESSION ['candidate'] = '0';
	}
}

$cand = ($_SESSION ['candidate']) ? $_SESSION ['candidate'] : '0';

if (filter_input ( INPUT_POST, 'candUp', FILTER_SANITIZE_NUMBER_INT )) {
	$candidate = filter_input ( INPUT_POST, 'candUp', FILTER_SANITIZE_NUMBER_INT );
	for($i = 1; $i <= 11; $i ++) {
		$a2 = htmlEntities ( trim ( $_POST ['answer' . $i] ), ENT_QUOTES );
		${"answer" . $i} = filter_var ( $a2, FILTER_SANITIZE_STRING );
	}

	$d = $db->prepare ( "UPDATE candidates SET answer1 = ?, answer2 = ?, answer3 = ?, answer4 = ?, answer5 = ?, answer6 = ?, answer7 = ?, answer8 = ?, answer9 = ?, answer10 = ?, answer11 = ? WHERE id = ?" );
	$d->execute ( array (
			$answer1,
			$answer2,
			$answer3,
			$answer4,
			$answer5,
			$answer6,
			$answer7,
			$answer8,
			$answer9,
			$answer10,
			$answer11,
			$candidate
	) );

	$image = $_FILES ["image"] ["tmp_name"];
	$imageName = $time;
	list ( $width, $height ) = (getimagesize ( $image ) != null) ? getimagesize ( $image ) : null;
	if ($width != null && $height != null) {
		$imageType = getPicType ( $_FILES ["image"] ['type'] );
		processPic ( "candidates2016", $imageName, 800, 800, $image, $imageType );
		$p1stmt = $db->prepare ( "UPDATE candidates SET picName=?, picExt=? WHERE id=?" );
		$p1stmt->execute ( array (
				$imageName,
				$imageType,
				$candidate
		) );
	}
	echo "<div style='color:$highlightColor; text-align:center; font-weight:bold; font-size:2em;'>Thank you for your answers.<br />They have been recorded and will appear on the website.</div>";
}

echo "<div id='mainTableBox' style='padding:10px;'>";
echo "<header style='text-align:center; font-weight:bold; font-size:3em; margin-bottom:30px;'>2016 Candidate Questionnaire</header>";

if ($cand == '0') {
	?>
    <div style='font-weight:bold; text-align:center;'>
        Welcome, and thank you for participating in this candidate questionnaire.<br /><br />
        <form action='index.php?page=candidateQuestionnaire' method='post'>
            Please enter the password included in your letter: <input type='password' name='pwd' /><input type='hidden' name="candLogIn" value="1" /><input type='submit' value=' Go ' />
        </form>
    </div>
    <?php
} else {
	$b = $db->prepare ( "SELECT * FROM candidates WHERE id = ?" );
	$b->execute ( array (
			$cand
	) );
	$brow = $b->fetch ();
	$candName = $brow ['candName'];
	$office = $brow ['office'];
	$questions = explode ( ",", $brow ['questions'] );
	$picName = $brow ['picName'];
	$picExt = $brow ['picExt'];
	$answer1 = $brow ['answer1'];
	$answer2 = $brow ['answer2'];
	$answer3 = $brow ['answer3'];
	$answer4 = $brow ['answer4'];
	$answer5 = $brow ['answer5'];
	$answer6 = $brow ['answer6'];
	$answer7 = $brow ['answer7'];
	$answer8 = $brow ['answer8'];
	$answer9 = $brow ['answer9'];
	$answer10 = $brow ['answer10'];
	$answer11 = $brow ['answer11'];

	echo "<form action='index.php?page=candidateQuestionnaire' method='post' enctype='multipart/form-data'>";
	echo "<table cellspacing='0' style='width:100%'>";
	echo "<tr><td style='width:100%; padding:20px;'><div style='text-align:center; font-weight:bold;'><span style='font-size:3em;'>$candName</span><br /><br /><span style='font-size:2em;'>$office</span></div></td></tr>\n";
	echo "<tr><td style='width:100%; padding:20px;'>";
	if (file_exists ( "userPics/candidates2016/$picName.$picExt" )) {
		echo "<img src='userPics/candidates2016/$picName.$picExt' alt='' style='border:1px solid $highlighColor; padding:2px; max-width:294px;' />";
	}
	echo "<br /><br />To upload a picture: <input type='file' name='image' />";
	echo "</td></tr>\n";
	foreach ( $questions as $k => $v ) {
		$c = $db->prepare ( "SELECT question FROM candidateQuestions WHERE id = ?" );
		$c->execute ( array (
				$v
		) );
		$crow = $c->fetch ();

		$q = str_split ( $office );
		if ($q [0] == "A" || $q [0] == "E" || $q [0] == "I" || $q [0] == "O" || $q [0] == "U" || $q [0] == "Y" || $q [0] == "H") {
			$x = "an $office";
		} else {
			$x = "a $office";
		}
		$ques = str_ireplace ( "&&&", $x, $crow ['question'] );
		$question = str_ireplace ( "***", $office, $ques );

		echo "<tr><td style='width:100%; padding:20px;'><div style='font-weight:bold;'>$question</div><br /><textarea name='answer$v' cols='100' rows='10'>" . ${"answer" . $v} . "</textarea></td></tr>\n";
	}
	echo "<tr><td style='width:100%; padding:20px;'><input type='hidden' name='candUp' value='$cand' /><input type='submit' value=' Update data ' /></td></tr></table></form>";
}