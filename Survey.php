<?php
$surveyId = filter_input(INPUT_GET, 'surveyId', FILTER_SANITIZE_NUMBER_INT);
$questionNumber = (filter_input(INPUT_GET, 'questionNumber',
        FILTER_SANITIZE_NUMBER_INT)) ? filter_input(INPUT_GET, 'questionNumber',
        FILTER_SANITIZE_NUMBER_INT) : 0;
if ($questionNumber == 0) {
    $questionNumber = (filter_input(INPUT_POST, 'questionNumber',
            FILTER_SANITIZE_NUMBER_INT)) ? filter_input(INPUT_POST,
            'questionNumber', FILTER_SANITIZE_NUMBER_INT) : 0;
}
$stmt = $db->prepare(
        "SELECT COUNT(*) FROM survey WHERE id = ? && startDate <= ? && endDate >= ?");
$stmt->execute(array(
        $surveyId,
        $time,
        $time
));
$row = $stmt->fetch();
$sId = ($row[0] == 1) ? $surveyId : FALSE;

if ($sId) {
    if (filter_input(INPUT_COOKIE, 'responderId', FILTER_SANITIZE_NUMBER_INT) < 1 ||
            ! filter_input(INPUT_COOKIE, 'responderId',
                    FILTER_SANITIZE_NUMBER_INT)) {
        setcookie("responderId",
                filter_var($visitingIP, FILTER_SANITIZE_NUMBER_INT),
                $time + 1209600, "/", "mylocal.life", 0);
    }
    $IP = filter_input(INPUT_COOKIE, 'responderId', FILTER_SANITIZE_NUMBER_INT);

    // Process answers
    foreach ($_POST as $k => $v) {
        if (preg_match("/^answer([1-9][0-9]*)$/", $k, $match)) {
            $aNum = $match[1];
            $val = htmlentities(filter_var($v, FILTER_SANITIZE_STRING));
            $acheck = $db->prepare(
                    "SELECT COUNT(*) FROM surveyAnswers WHERE responderId = ? && answerNum = ?");
            $acheck->execute(array(
                    $IP,
                    $aNum
            ));
            $achrow = $acheck->fetch();
            if ($achrow[0] == 1) {
                $aUp = $db->prepare(
                        "UPDATE surveyAnswers SET answer = ? WHERE responderId = ? && answerNum = ?");
                $aUp->execute(array(
                        $val,
                        $IP,
                        $aNum
                ));
            } else {
                $aUp = $db->prepare(
                        "INSERT INTO surveyAnswers VALUES(NULL,?,?,?,?,'0','0')");
                $aUp->execute(array(
                        $sId,
                        $IP,
                        $time,
                        $val,
                        $aNum
                ));
            }
        }
    }

    $answered = array();
    $s = $db->prepare(
            "SELECT answer,answerNum FROM surveyAnswers WHERE surveyId=? && responderId = ?");
    $s->execute(array(
            $sId,
            $IP
    ));
    while ($sr = $s->fetch()) {
        $a = $sr['answer'];
        $an = $sr['answerNum'];
        if ($a != "" && $a != " ") {
            $answered[] = $an;
        }
    }

    $s1 = $db->prepare("SELECT * FROM survey WHERE surveyId=?");
    $s1->execute(array(
            $sId
    ));
    $s1r = $s1->fetch();
    $userId = $s1r['userId'];
    $surveyTitle = $s1r['surveyTitle'];
    $introText = nl2br(
            make_links_clickable(
                    html_entity_decode($s1r['introText'], ENT_QUOTES)));
    $exitText = nl2br(
            make_links_clickable(
                    html_entity_decode($s1r['exitText'], ENT_QUOTES)));
    $intropicName = $s1r['picName'];

    $qNum = array();
    $s2 = $db->prepare(
            "SELECT questionNum FROM surveyQuestions WHERE surveyId=? ORDER BY questionNum");
    $s2->execute(array(
            $sId
    ));
    while ($s2r = $s2->fetch()) {
        $qn = $s2r['questionNum'];
        $qNum[] = $qn;
    }

    echo "<div style='text-align:center; font-weight:bold; font-size:2em; margin:20px 0px;'>$surveyTitle</div>";
    if ($questionNumber == 0) {
        ?>
        <div style="float:right" class="fb-like" data-share="true" data-width="100" data-layout="button" data-show-faces="false"></div>
        <?php
        echo "<div style='float:right; margin:3px 10px 0px 0px;'><a href='https://twitter.com/share' class='twitter-share-button' data-via='MyLocalLife'>Tweet</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></div>";
        if (file_exists("userPics/$userId/$intropicName")) {
            echo "<img src='userPics/$userId/$intropicName' alt='' style='float:right; margin:0px 0px 10px 10px; border:1px solid $highlightColor; padding:5px; max-width:400px;' />";
        }
        echo "<div style='text-align:justify; margin:0px 10px;'>$introText</div>";
    } else {
        echo "<form method='post' action='index.php?page=Survey&surveyId=$sId'>";
        $a = $db->prepare(
                "SELECT answer FROM surveyAnswers WHERE responderId=? && surveyId = ? && answerNum = ?");
        $a->execute(array(
                $IP,
                $sId,
                $questionNumber
        ));
        $ar = $a->fetch();
        $answer = $ar['answer'];
        $a1 = $db->prepare(
                "SELECT question,picName FROM surveyQuestions WHERE surveyId = ? && questionNum = ?");
        $a1->execute(array(
                $sId,
                $questionNumber
        ));
        $a1r = $a1->fetch();
        $question = $a1r['question'];
        $picName = $a1r['picName'];
        if (file_exists("userPics/$userId/$picName")) {
            echo "<img src='userPics/$userId/$picName' alt='' style='float:right; margin:0px 0px 10px 10px; border:1px solid $highlightColor; padding:5px; max-width:400px;' />";
        }
        echo "<div style='text-align:justify; margin:0px 10px 20px 10px;'>$question</div>";
        echo "<textarea id='answer' name='answer$questionNumber' rows='15' cols='70' onchange='updateSurvey(\"$sId\",\"$IP\",\"$questionNumber\",this.value)'>$answer</textarea><br />";

        echo "<div style='text-align:center;'>";
        foreach ($qNum as $num) {
            if ($num == $questionNumber + 1) {
                echo "<a href='index.php?page=Survey&surveyId=$sId&questionNumber=$num' style='font-size:2em; color:$highlightColor; margin:0px 5px;'><div style='padding:5px; background-color:$highlightColor; color:#ffffff; margin-top:-5px; border:1px solid $highlightColor; border-radius:5px;'>Next</div></a>";
            } else {
                if (in_array($num, $answered)) {
                    echo "<a href='index.php?page=Survey&surveyId=$sId&questionNumber=$num' style='font-size:1.5em; color:$highlightColor; margin:0px 5px;'><div style='padding:5px; color:$highlightColor; margin-top:-5px; border:1px solid #ffffff; border-radius:5px;'>$num</div></a>";
                } else {
                    echo "<a href='index.php?page=Survey&surveyId=$sId&questionNumber=$num' style='font-size:2em; font-weight:bold; color:$highlightColor; margin:0px 5px;'><div style='padding:5px; background-color:$highlightColor; color:#ffffff; margin-top:-5px; border:1px solid $highlightColor; border-radius:5px;'>$num</div></a>";
                }
            }
        }
        echo "<a href='index.php?page=Survey&surveyId=0&questionNumber=9999' style='font-size:2em; color:$highlightColor; margin:0px 5px;'><div style='padding:5px; background-color:$highlightColor; color:#ffffff; margin-top:-5px; border:1px solid $highlightColor; border-radius:5px;'>Finish</div></a>";
        echo "</div>";
        echo "</form>";
    }
} else {
    if ($questionNumber == 9999) {
        echo "<div style='text-align:center; font-weight:bold; font-size:2em; margin:30px 0px;'>Thank you for your input.<br /><br />$exitText</div>";
    }
    echo "<div id='surveyBox' style='text-align:center; background-color: #ffffff; color: $highlightColor; width:100%; padding:20px 0px;'>\n";
    $s = $db->prepare(
            "SELECT id,surveyTitle FROM survey WHERE startDate <= ? && endDate >= ? ORDER BY RAND()");
    $s->execute(array(
            $time,
            $time
    ));
    $b = 0;
    while ($srow = $s->fetch()) {
        $surveyId = $srow['id'];
        $surveyTitle = $srow['surveyTitle'];
        echo ($b != 0) ? "&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;\n" : "";
        echo "<a href='index.php?page=Survey&surveyId=$surveyId'>$surveyTitle</a>\n";
        $b ++;
    }
    echo "</div>\n";
}