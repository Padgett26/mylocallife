<?php
$sId = filter_input(INPUT_POST, 'surveyId', FILTER_SANITIZE_STRING);
$delSurvey = (filter_input(INPUT_POST, 'delSurvey', FILTER_SANITIZE_NUMBER_INT) ==
        '1') ? '1' : '0';

if ($delSurvey == '1') {
    $test = $db->prepare("SELECT userId, picName FROM survey WHERE id = ?");
    $test->execute(array(
            $sId
    ));
    $testRow = $test->fetch();
    if ($myId == $testRow['userId']) {
        $pn = $testRow['picName'];
        if (file_exists("userPics/$myId/$pn")) {
            unlink("userPics/$myId/$pn");
        }
        $delpic = $db->prepare(
                "SELECT picName FROM surveyQuestions WHERE surveyId = ?");
        $delpic->execute(array(
                $sId
        ));
        while ($delpicRow = $delpic->fetch()) {
            $pn = $delpicRow['picName'];
            if (file_exists("userPics/$myId/$pn")) {
                unlink("userPics/$myId/$pn");
            }
        }
        $del = $db->prepare("DELETE FROM survey WHERE id = ?");
        $del->execute(array(
                $sId
        ));
        $del1 = $db->prepare("DELETE FROM surveyQuestions WHERE surveyId = ?");
        $del1->execute(array(
                $sId
        ));
        $del2 = $db->prepare("DELETE FROM surveyAnswers WHERE surveyId = ?");
        $del2->execute(array(
                $sId
        ));
    }
} else {
    $startMonth = filter_input(INPUT_POST, 'startMonth',
            FILTER_SANITIZE_NUMBER_INT);
    $startDay = filter_input(INPUT_POST, 'startDay', FILTER_SANITIZE_NUMBER_INT);
    $startYear = filter_input(INPUT_POST, 'startYear',
            FILTER_SANITIZE_NUMBER_INT);
    $endMonth = filter_input(INPUT_POST, 'endMonth', FILTER_SANITIZE_NUMBER_INT);
    $endDay = filter_input(INPUT_POST, 'endDay', FILTER_SANITIZE_NUMBER_INT);
    $endYear = filter_input(INPUT_POST, 'endYear', FILTER_SANITIZE_NUMBER_INT);
    $startDate = mktime(0, 0, 0, $startMonth, $startDay, $startYear);
    $endDate = mktime(23, 59, 59, $endMonth, $endDay, $endYear);
    $surveyTitle = filter_input(INPUT_POST, 'surveyTitle',
            FILTER_SANITIZE_STRING);
    $introText = htmlEntities(
            trim(
                    filter_input(INPUT_POST, 'introText', FILTER_SANITIZE_STRING)),
            ENT_QUOTES);
    $exitText = htmlEntities(
            trim(
                    filter_input(INPUT_POST, 'exitText', FILTER_SANITIZE_STRING)),
            ENT_QUOTES);
    if ($sId == 'new') {
        $ins = $db->prepare(
                "INSERT INTO survey VALUES(NULL,?,?,?,?,?,?,'0.jpg','0','0','0')");
        $ins->execute(
                array(
                        $myId,
                        $startDate,
                        $endDate,
                        $surveyTitle,
                        $introText,
                        $exitText
                ));
        $getId = $db->prepare(
                "SELECT id FROM survey WHERE userId = ? ORDER BY id DESC LIMIT 1");
        $getId->execute(array(
                $myId
        ));
        $getR = $getId->fetch();
        $sId = $getR['id'];
    } else {
        $s = $db->prepare(
                "UPDATE survey SET startDate = ?, endDate = ?, surveyTitle = ?, introText = ?, exitText = ? WHERE id = ?");
        $s->execute(
                array(
                        $startDate,
                        $endDate,
                        $surveyTitle,
                        $introText,
                        $exitText,
                        $sId
                ));
    }

    foreach ($_POST as $k => $v) {
        if (preg_match("/^questionNum([1-9][0-9]*)$/", $k, $match)) {
            $qId = $match[1];
            $val = filter_var($v, FILTER_SANITIZE_NUMBER_INT);
            if ($val >= 1) {
                $s1 = $db->prepare(
                        "SELECT questionNum FROM surveyQuestions WHERE id = ?");
                $s1->execute(array(
                        $qId
                ));
                $s1row = $s1->fetch();
                $oldNum = $s1row['questionNum'];

                // Update Question table
                $s2 = $db->prepare(
                        "SELECT id, questionNum FROM surveyQuestions WHERE surveyId = ?");
                $s2->execute(array(
                        $sId
                ));
                while ($s2row = $s2->fetch()) {
                    $xId = $s2row['id'];
                    $xQN = $s2row['questionNum'];
                    if ($xQN > $oldNum) {
                        $s3 = $db->prepare(
                                "UPDATE surveyQuestions SET questionNum = questionNum - 1 WHERE id = ?");
                        $s3->execute(array(
                                $xId
                        ));
                    }
                    if ($xQN >= $val) {
                        $s3 = $db->prepare(
                                "UPDATE surveyQuestions SET questionNum = questionNum + 1 WHERE id = ?");
                        $s3->execute(array(
                                $xId
                        ));
                    }
                }
                $s4 = $db->prepare(
                        "UPDATE surveyQuestions SET questionNum = ? WHERE id = ?");
                $s4->execute(array(
                        $val,
                        $qId
                ));

                // Update Answer table
                $s5 = $db->prepare(
                        "SELECT id, answerNum FROM surveyAnswers WHERE surveyId = ?");
                $s5->execute(array(
                        $sId
                ));
                while ($s5row = $s5->fetch()) {
                    $xId = $s5row['id'];
                    $xQN = $s5row['answerNum'];
                    if ($xQN > $oldNum) {
                        $s6 = $db->prepare(
                                "UPDATE surveyAnswers SET answerNum = answerNum - 1 WHERE id = ?");
                        $s6->execute(array(
                                $xId
                        ));
                    }
                    if ($xQN >= $val) {
                        $s7 = $db->prepare(
                                "UPDATE surveyAnswers SET answerNum = answerNum + 1 WHERE id = ?");
                        $s7->execute(array(
                                $xId
                        ));
                    }
                }
                $s8 = $db->prepare(
                        "UPDATE surveyAnswers SET answerNum = ? WHERE id = ?");
                $s8->execute(array(
                        $val,
                        $qId
                ));
            } else {
                $s5 = $db->prepare(
                        "SELECT COUNT(*) FROM surveyQuestions WHERE surveyId = ?");
                $s5->execute(array(
                        $sId
                ));
                $s5row = $s5->fetch();
                $c = ($s5row[0] + 1);
                $s6 = $db->prepare(
                        "UPDATE surveyQuestions SET questionNum = ? WHERE id = ?");
                $s6->execute(array(
                        $c,
                        $qId
                ));
            }
            continue;
        }
        if (preg_match("/^question([1-9][0-9]*)$/", $k, $match)) {
            $qId = $match[1];
            $val = filter_var($v, FILTER_SANITIZE_STRING);
            $s1 = $db->prepare(
                    "UPDATE surveyQuestions SET question = ? WHERE id = ?");
            $s1->execute(array(
                    $val,
                    $qId
            ));
            continue;
        }
        if (preg_match("/^questionNumNew([1-9][0-9]*)$/", $k, $match)) {
            $qId = $match[1];
            $val = filter_var($v, FILTER_SANITIZE_NUMBER_INT);
            if ($val >= 1) {
                // Update question table
                $s2 = $db->prepare(
                        "SELECT id, questionNum FROM surveyQuestions WHERE surveyId = ?");
                $s2->execute(array(
                        $sId
                ));
                while ($s2row = $s2->fetch()) {
                    $xId = $s2row['id'];
                    $xQN = $s2row['questionNum'];
                    if ($xQN >= $val) {
                        $s3 = $db->prepare(
                                "UPDATE surveyQuestions SET questionNum = questionNum + 1 WHERE id = ?");
                        $s3->execute(array(
                                $xId
                        ));
                    }
                }

                // Update answer table
                $s2 = $db->prepare(
                        "SELECT id, answerNum FROM surveyAnswers WHERE surveyId = ?");
                $s2->execute(array(
                        $sId
                ));
                while ($s2row = $s2->fetch()) {
                    $xId = $s2row['id'];
                    $xQN = $s2row['answerNum'];
                    if ($xQN >= $val) {
                        $s3 = $db->prepare(
                                "UPDATE surveyAnswers SET answerNum = answerNum + 1 WHERE id = ?");
                        $s3->execute(array(
                                $xId
                        ));
                    }
                }

                $s4 = $db->prepare(
                        "SELECT id FROM surveyQuestions WHERE surveyId = ? && ticNew = ?");
                $s4->execute(array(
                        $sId,
                        $qId
                ));
                $s4row = $s4->fetch();
                $workingId = $s4row['id'];
                if ($workingId >= 1) {
                    $s5 = $db->prepare(
                            "UPDATE surveyQuestions SET questionNum = ? WHERE id = ?");
                    $s5->execute(array(
                            $val,
                            $workingId
                    ));
                } else {
                    $s6 = $db->prepare(
                            "INSERT INTO surveyQuestions VALUES(NULL,'0.jpg','',?,?,?,'0','0')");
                    $s6->execute(array(
                            $val,
                            $sId,
                            $qId
                    ));
                }
            } else {
                $s7 = $db->prepare(
                        "SELECT COUNT(*) FROM surveyQuestions WHERE surveyId = ?");
                $s7->execute(array(
                        $sId
                ));
                $s7row = $s7->fetch();
                $c = ($s7row[0] + 1);
                $s8 = $db->prepare(
                        "SELECT id FROM surveyQuestions WHERE surveyId = ? && ticNew = ?");
                $s8->execute(array(
                        $sId,
                        $qId
                ));
                $s8row = $s8->fetch();
                $workingId = $s8row['id'];
                if ($workingId >= 1) {
                    $s9 = $db->prepare(
                            "UPDATE surveyQuestions SET questionNum = ? WHERE id = ?");
                    $s9->execute(array(
                            $c,
                            $workingId
                    ));
                } else {
                    $s10 = $db->prepare(
                            "INSERT INTO surveyQuestions VALUES(NULL,'0.jpg','',?,?,?,'0','0')");
                    $s10->execute(array(
                            $c,
                            $sId,
                            $qId
                    ));
                }
            }
            continue;
        }
        if (preg_match("/^questionNew([1-9][0-9]*)$/", $k, $match)) {
            $qId = $match[1];
            $val = filter_var($v, FILTER_SANITIZE_STRING);
            $s1 = $db->prepare(
                    "SELECT id FROM surveyQuestions WHERE surveyId = ? && ticNew = ?");
            $s1->execute(array(
                    $sId,
                    $qId
            ));
            $s1row = $s1->fetch();
            $workingId = $s1row['id'];
            if ($workingId >= 1) {
                $s2 = $db->prepare(
                        "UPDATE surveyQuestions SET question = ? WHERE id = ?");
                $s2->execute(array(
                        $val,
                        $workingId
                ));
            } else {
                $s3 = $db->prepare(
                        "INSERT INTO surveyQuestions VALUES(NULL,'0.jpg',?,'',?,?,'0','0')");
                $s3->execute(array(
                        $val,
                        $sId,
                        $qId
                ));
            }
            continue;
        }
    }

    $tmpFile = $_FILES["imageIntro"]["tmp_name"];
    list ($width1, $height1) = (getimagesize($tmpFile) != null) ? getimagesize(
            $tmpFile) : null;
    if ($width1 != null && $height1 != null) {
        $image1Type = getPicType($_FILES["imageIntro"]['type']);
        $image1Name = $time . "." . $image1Type;
        processPic("$domain/userPics/$myId", $image1Name, $tmpFile, 1000, 150);
        $p1stmt = $db->prepare("UPDATE survey SET picName=? WHERE id=?");
        $p1stmt->execute(array(
                $image1Name,
                $sId
        ));
    }

    foreach ($_FILES as $key => $val) {
        if (preg_match("/^questionImage([1-9][0-9]*)$/", $key, $match)) {
            $iId = $match[1];
            $tmpFile = $_FILES["$key"]["tmp_name"];
            list ($width, $height) = (getimagesize($tmpFile) != null) ? getimagesize(
                    $tmpFile) : null;
            if ($width != null && $height != null) {
                $imageType = getPicType($_FILES["$key"]['type']);
                $imageName = ($time + $iId) . "." . $imageType;
                processPic("$domain/userPics/$myId", $imageName, $tmpFile, 1000,
                        150);
                $pstmt3 = $db->prepare(
                        "UPDATE surveyQuestions SET picName = ? WHERE id = ?");
                $pstmt3->execute(array(
                        $imageName,
                        $iId
                ));
            }
            continue;
        }
        if (preg_match("/^questionImageNew([1-9][0-9]*)$/", $key, $match)) {
            $iId = $match[1];
            $tmpFile = $_FILES["$key"]["tmp_name"];
            list ($width, $height) = (getimagesize($tmpFile) != null) ? getimagesize(
                    $tmpFile) : null;
            if ($width != null && $height != null) {
                $imageType = getPicType($_FILES["$key"]['type']);
                $imageName = ($time + $iId) . "." . $imageType;
                processPic("$domain/userPics/$myId", $imageName, $tmpFile, 1000,
                        150);
                $s1 = $db->prepare(
                        "SELECT id FROM surveyQuestions WHERE surveyId = ? && ticNew = ?");
                $s1->execute(array(
                        $sId,
                        $iId
                ));
                $s1row = $s1->fetch();
                $workingId = $s1row['id'];
                if ($workingId >= 1) {
                    $s2 = $db->prepare(
                            "UPDATE surveyQuestions SET picName = ? WHERE id = ?");
                    $s2->execute(array(
                            $imageName,
                            $workingId
                    ));
                } else {
                    $s3 = $db->prepare(
                            "INSERT INTO surveyQuestions VALUES(NULL,?,'','',?,?,'0','0')");
                    $s3->execute(array(
                            $imageName,
                            $sId,
                            $iId
                    ));
                }
                continue;
            }
        }
    }
}
