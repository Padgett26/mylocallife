<?php

$sId = filter_input(INPUT_GET, 'surveyId', FILTER_SANITIZE_STRING);

if ($myId >= 1) {
    $s = $db->prepare("SELECT * FROM survey WHERE id=? && userId = ?");
    $s->execute(array($sId, $myId));
    $sr = $s->fetch();
    $startDate = $sr['startDate'];
    $endDate = $sr['endDate'];
    $surveyTitle = $sr['surveyTitle'];
    $introText = html_entity_decode($sr['introText'], ENT_QUOTES);
    $exitText = html_entity_decode($sr['exitText'], ENT_QUOTES);
    $picName = $sr['picName'];

    if ($sId == 'new') {
        $thisYear = date("Y");
        $thisDay = date("j");
        $thisMonth = date("n");
        $timePlus = (time() + (60 * 60 * 24 * 14)); // two weeks
        $laterYear = date("Y", $timePlus);
        $laterDay = date("j", $timePlus);
        $laterMonth = date("n", $timePlus);
    } else {
        $thisYear = date("Y", $startDate);
        $thisDay = date("j", $startDate);
        $thisMonth = date("n", $startDate);
        $laterYear = date("Y", $endDate);
        $laterDay = date("j", $endDate);
        $laterMonth = date("n", $endDate);
    }

    echo "<form method='post' action='index.php?page=myAccount' enctype='multipart/form-data'>\n";

    echo "Title&nbsp;&nbsp;&nbsp;<input type='text' name='surveyTitle' value='$surveyTitle' size='60' /><br /><br />\n";

    echo "Survey start date:&nbsp;&nbsp;&nbsp;<select name='startMonth'>";
    for ($sm = 1; $sm <= 12; $sm++) {
        echo "<option value='$sm'";
        if ($sm == $thisMonth) {
            echo " selected";
        }
        echo ">$sm</option>\n";
    }
    echo "</select>&nbsp;&nbsp;&nbsp;<select name='startDay'>";
    for ($sd = 1; $sd <= 31; $sd++) {
        echo "<option value='$sd'";
        if ($sd == $thisDay) {
            echo " selected";
        }
        echo ">$sd</option>\n";
    }
    echo "</select>&nbsp;&nbsp;&nbsp;<select name='startYear'>";
    for ($sy = $thisYear; $sy <= $thisYear + 1; $sy++) {
        echo "<option value='$sy'";
        if ($sy == $thisYear) {
            echo " selected";
        }
        echo ">$sy</option>\n";
    }
    echo "</select><br /><br />\n";
    echo "Survey end date:&nbsp;&nbsp;&nbsp;<select name='endMonth'>";
    for ($em = 1; $em <= 12; $em++) {
        echo "<option value='$em'";
        if ($em == $laterMonth) {
            echo " selected";
        }
        echo ">$em</option>\n";
    }
    echo "</select>&nbsp;&nbsp;&nbsp;<select name='endDay'>";
    for ($ed = 1; $ed <= 31; $ed++) {
        echo "<option value='$ed'";
        if ($ed == $laterDay) {
            echo " selected";
        }
        echo ">$ed</option>\n";
    }
    echo "</select>&nbsp;&nbsp;&nbsp;<select name='endYear'>";
    for ($ey = $thisYear - 1; $ey <= $thisYear + 1; $ey++) {
        echo "<option value='$ey'";
        if ($ey == $laterYear) {
            echo " selected";
        }
        echo ">$ey</option>\n";
    }
    echo "</select><br /><br />\n";
    echo "Introductory text:<br /><textarea name='introText' rows='10' cols='70'>$introText</textarea><br /><br />";
    echo "Exit/Thank you text:<br /><textarea name='introText' rows='10' cols='70'>$exitText</textarea><br /><br />";

    if (file_exists("userPics/$userId/$picName")) {
        echo "<img src='userPics/$userId/$picName' alt='' style='max-width:400px;' /><br />\n";
    }
    echo "Upload a new pic:&nbsp;&nbsp;&nbsp;<input type='file' name='imageIntro' /><br /><br />\n";
    
    $quesquestionNum = 0;
    echo "<table cellspacing='0px' style='border:1px solid black;'>";
    $ques = $db->prepare("SELECT * FROM surveyQuestions WHERE surveyId = ? ORDER BY questionNum");
    $ques->execute(array($sId));
    while ($quesRow = $ques->fetch()) {
        $quesid = $quesRow['id'];
        $quespicName = $quesRow['picName'];
        $quesquestion = html_entity_decode($quesRow['question'], ENT_QUOTES);
        $quesquestionNum = $quesRow['questionNum'];
        echo "<tr><td style='padding:10px;'>Question<br />number<br /><input type='text' name='questionNum$quesid' value='$quesquestionNum' /></td>\n";
        echo "<td style='padding:10px;'>Question:<br /><textarea name='question$quesid' rows='10' cols='60'>$quesquestion</textarea></td>\n";
        echo "<td style='padding:10px;'>Picture:<br />";
        if (file_exists("userPics/$userId/$quespicName")) {
            echo "<img src='userPics/$userId/$quespicName' style='max-width:150px;' /><br />";
        }
        echo "Upload a new pic for this question:<br />";
        echo "<input type='file' name='questionImage$quesid' /></td>\n";
        echo "<td style='padding:10px;'>Delete<br />question<br /><input type='checkbox' name='delQuestion$quesid' value='1' /></td></tr>\n";
    }
    for ($i = 1; $i <= 10; $i++) {
        echo "<tr><td style='padding:10px;'>Question<br />number<br /><input type='text' name='questionNumNew$i' value='" . $quesquestionNum + $i . "' /></td>\n";
        echo "<td style='padding:10px;'>Question:<br /><textarea name='questionNew$i' rows='10' cols='60'></textarea></td>\n";
        echo "<td style='padding:10px;'>Picture:<br />";
        echo "Upload a new pic for this question:<br />";
        echo "<input type='file' name='questionImageNew$i' />";
        echo "</td><td>&nbsp;</td></tr>\n";
    }
    echo "</table>";
    if ($sId != 'new') {
        echo "Delete this survey: <input type='checkbox' name='delSurvey' value='1' /><br /><br />";
    }
    echo "<input type='hidden' name='surveyId' value='$sId' /><input type='submit' value=' Submit ' />";
    echo "</form>";
}