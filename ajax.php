<?php

include "cgi-bin/config.php";

if (filter_input(INPUT_GET, 'blogUserId', FILTER_SANITIZE_NUMBER_INT) && filter_input(INPUT_GET, 'myId', FILTER_SANITIZE_NUMBER_INT)) {

    $blogUserId = filter_input(INPUT_GET, 'blogUserId', FILTER_SANITIZE_NUMBER_INT);
    $myId = filter_input(INPUT_GET, 'myId', FILTER_SANITIZE_NUMBER_INT);

    $find = $db->prepare("SELECT COUNT(*) FROM blogFavorites WHERE myId=? && blogUserId=?");
    $find->execute(array($myId, $blogUserId));
    $findrow = $find->fetch();
    if ($findrow[0] >= 1) {
        $sub = $db->prepare("DELETE FROM blogFavorites WHERE myId=? && blogUserId=?");
        $sub->execute(array($myId, $blogUserId));
        echo "Save blog as a favorite";
    } else {
        $sub = $db->prepare("INSERT INTO blogFavorites VALUES(NULL,?,?,'0','0','0')");
        $sub->execute(array($myId, $blogUserId));
        echo "Remove blog from favorites";
    }
}

if (filter_input(INPUT_GET, 'getNew', FILTER_SANITIZE_NUMBER_INT) == '1' && filter_input(INPUT_GET, 'myId', FILTER_SANITIZE_NUMBER_INT)) {
    $myId = filter_input(INPUT_GET, 'myId', FILTER_SANITIZE_NUMBER_INT);
    echo "<div style='text-align:center; text-decoration:underline; font-weight:bold; color:$highlightColor;'>Favorite&nbsp;Blogs</div>";
    $t = 0;
    $getF = $db->prepare("SELECT blogUserId FROM blogFavorites WHERE myId=? ORDER BY RAND()");
    $getF->execute(array($myId));
    while ($getFrow = $getF->fetch()) {
        $buId = $getFrow['blogUserId'];
        $subF = $db->prepare("SELECT t1.blogTitle, t2.firstName, t2.lastName FROM blogDescriptions AS t1 INNER JOIN users AS t2 ON t1.userId = t2.id WHERE t2.id = ?");
        $subF->execute(array($buId));
        $subFrow = $subF->fetch();
        $bt = $subFrow['blogTitle'];
        $fn = $subFrow['firstName'];
        $ln = $subFrow['lastName'];
        if ($t != 0) {
            echo "<div style='height:3px; width:50px; background-color:#dddddd; border:1px solid $highlightColor; margin:5px auto;'></div>";
        }
        echo "<div style='padding:10px;'>";
        echo "<a href='index.php?page=Blog&blogUserId=$buId'><span style='font-weight:bold;'>$bt</span><br />by $fn $ln</a><br /><br />";
        echo "</div>";
        $t++;
    }
}

if (filter_input(INPUT_GET, 'getPrice', FILTER_SANITIZE_STRING)) {
    $getPrice = filter_input(INPUT_GET, 'getPrice', FILTER_SANITIZE_STRING);
    switch ($getPrice) {
        case "top":
            echo "$144 - 6 months<br />$240 - 1 year<br />$400 - 2 years";
            break;
        case "side1":
            echo "$108 - 6 months<br />$180 - 1 year<br />$300 - 2 years";
            break;
        case "side2":
            echo "$72 - 6 months<br />$120 - 1 year<br />$200 - 2 years";
            break;
        case "side3":
            echo "$36 - 6 months<br />$60 - 1 year<br />$100 - 2 years";
            break;
    }
}

if (filter_input(INPUT_GET, 'surveyAnswer', FILTER_SANITIZE_STRING)) {
    $surveyId = filter_input(INPUT_GET, 'surveyId', FILTER_SANITIZE_NUMBER_INT);
    $responderId = filter_input(INPUT_GET, 'responderId', FILTER_SANITIZE_NUMBER_INT);
    $questionNumber = filter_input(INPUT_GET, 'questionNumber', FILTER_SANITIZE_NUMBER_INT);
    $surveyAnswer = filter_input(INPUT_GET, 'surveyAnswer', FILTER_SANITIZE_STRING);
    $answer = htmlentities($surveyAnswer);
    
    $s = $db->prepare("SELECT COUNT(*) FROM surveyAnswers WHERE responderId = ? && surveyId = ? && answerNum = ?");
    $s->execute(array($responderId,$surveyId,$questionNumber));
    $sr = $s->fetch();
    if ($sr[0] == 1) {
        $s1 = $db->prepare("UPDATE surveyAnswers SET answer = ? WHERE responderId = ? && surveyId = ? && answerNum = ?");
        $s1->execute(array($answer,$responderId,$surveyId,$questionNumber));
    } else {
        $s1 = $db->prepare("INSERT INTO surveyAnswers VALUES(NULL,?,?,?,?,?,'0','0')");
        $s1->execute(array($surveyId,$responderId,$time,$answer,$questionNumber));
    }
    
    echo $surveyAnswer;
}