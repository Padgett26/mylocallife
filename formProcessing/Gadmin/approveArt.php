<?php
$approveArt = filter_input ( INPUT_POST, 'approveArt', FILTER_SANITIZE_NUMBER_INT );
$approve = filter_input ( INPUT_POST, 'approve', FILTER_SANITIZE_NUMBER_INT );
$msgToAuthor = filter_input ( INPUT_POST, 'msgToAuthor', FILTER_SANITIZE_STRING );

if ($approve == "1") {
	$appr1 = $db->prepare ( "UPDATE reported SET clearedTime=? WHERE reportedBy=? && articleId=?" );
	$appr1->execute ( array (
			$time,
			'0',
			$approveArt
	) );
} else {
	$appr2 = $db->prepare ( "SELECT authorId, articleTitle FROM articles WHERE id=?" );
	$appr2->execute ( array (
			$approveArt
	) );
	$approw2 = $appr2->fetch ();
	$apprId = $approw2 ['authorId'];
	$subject = $approw2 ['articleTitle'];
	$appr3 = $db->prepare ( "SELECT firstName, email FROM users WHERE id=?" );
	$appr3->execute ( array (
			$apprId
	) );
	$approw3 = $appr3->fetch ();
	$apprFirstName = $approw3 ['firstName'];
	$apprEmail = $approw3 ['email'];
	sendArticleEmail ( $msgToAuthor, $apprFirstName, $apprEmail, $subject );

	if ($approve == '2') {
		$appr4 = $db->prepare ( "DELETE FROM reported WHERE reportedBy=? && articleId=?" );
		$appr4->execute ( array (
				'0',
				$approveArt
		) );

		$appr5 = $db->prepare ( "SELECT pic1Name, pic1Ext, pic2Name, pic2Ext FROM articles WHERE id=?" );
		$appr5->execute ( array (
				$approveArt
		) );
		$approw5 = $appr5->fetch ();
		$apprpic1 = $approw5 ['pic1Name'];
		$apprpic1e = $approw5 ['pic1Ext'];
		$apprpic2 = $approw5 ['pic2Name'];
		$apprpic2e = $approw5 ['pic2Ext'];
		if (file_exists ( "userPics/$apprId/$apprpic1.$apprpic1e" )) {
			unlink ( "userPics/$apprId/$apprpic1.$apprpic1e" );
		}
		if (file_exists ( "userPics/$apprId/$apprpic2.$apprpic2e" )) {
			unlink ( "userPics/$apprId/$apprpic2.$apprpic2e" );
		}

		$appr6 = $db->prepare ( "DELETE FROM articles WHERE id=?" );
		$appr6->execute ( array (
				$approveArt
		) );
	} elseif ($approve == '0') {
		$appr7 = $db->prepare ( "UPDATE reported SET whyReported=? WHERE reportedBy=? && articleId=?" );
		$appr7->execute ( array (
				$msgToAuthor,
				'0',
				$approveArt
		) );
	}
}