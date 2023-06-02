<table id='mainTableBox' cellspacing="5px">
    <?php
    $office = filter_input(INPUT_GET, 'office', FILTER_SANITIZE_STRING);
    $mc1 = $db->prepare("SELECT COUNT(*) FROM candidates WHERE office = ?");
    $mc1->execute(array(
            $office
    ));
    $mc1row = $mc1->fetch();
    $cols = ($mc1row[0] >= 2) ? $mc1row[0] : 1;
    $colWidth = (100 / $cols) . "%";
    $fontSize = ($cols >= 4) ? "font-size:.75em;" : "font-size:1em;";
    ?>
    <tr><td colspan='<?php

echo $cols;
    ?>' style="border:1px solid <?php

echo $highlightColor;
    ?>;"><div style="text-align:center; font-size:3em; font-weight:bold;">My 2016 Candidates</div></td></tr>
    <tr><td colspan='<?php

echo $cols;
    ?>' style="border:1px solid <?php

echo $highlightColor;
    ?>;"><div style="text-align:center; font-size:2em; font-weight:bold;"><?php

echo $office;
    ?></div></td></tr>
    <tr><td colspan='<?php

echo $cols;
    ?>'><div style="height:20px;">&nbsp;</div></td></tr>
    <?php
    // Reference by name
    $c = 1;
    $mc2 = $db->prepare(
            "SELECT candName FROM candidates WHERE office = ? ORDER BY RAND()");
    $mc2->execute(array(
            $office
    ));
    while ($mc2row = $mc2->fetch()) {
        ${"cand" . $c} = $mc2row['candName'];
        $c ++;
    }

    // Display pictures
    echo "<tr>\n";
    for ($i = 1; $i <= $cols; $i ++) {
        $mc = $db->prepare(
                "SELECT picName, picExt FROM candidates WHERE candName = ?");
        $mc->execute(array(
                ${"cand" . $i}
        ));
        $mcrow = $mc->fetch();
        $picName = $mcrow['picName'];
        $picExt = $mcrow['picExt'];

        echo "<td style='border:1px solid $highlightColor; width:$colWidth;'>";
        if (file_exists("userPics/candidates2016/$picName.$picExt")) {
            echo "<img src='userPics/candidates2016/$picName.$picExt' alt='' style='max-width:100%; margin:auto;' />\n";
        }
        echo "</td>\n";
    }
    echo "</tr>\n";

    // Display Name
    echo "<tr>\n";
    for ($i = 1; $i <= $cols; $i ++) {
        echo "<td style='border:1px solid $highlightColor; width:$colWidth; text-align:center;'><span style='font-weight:bold;'>${"cand" . $i}</span></td>\n";
    }
    echo "</tr>\n";

    // Question and Answers
    $mc3 = $db->prepare("SELECT questions FROM candidates WHERE candName = ?");
    $mc3->execute(array(
            $cand1
    ));
    $mc3row = $mc3->fetch();
    $questions = explode(",", $mc3row['questions']);

    foreach ($questions as $k => $v) {
        $mc4 = $db->prepare(
                "SELECT question FROM candidateQuestions WHERE id = ?");
        $mc4->execute(array(
                $v
        ));
        $mc4row = $mc4->fetch();
        $q = str_split($office);
        if ($q[0] == "A" || $q[0] == "E" || $q[0] == "I" || $q[0] == "O" ||
                $q[0] == "U" || $q[0] == "Y" || $q[0] == "H") {
            $x = "an $office";
        } else {
            $x = "a $office";
        }
        $ques = str_ireplace("&&&", $x, $mc4row['question']);
        $question = str_ireplace("***", $office, $ques);
        echo "<tr><td colspan='$cols' style='border:1px solid $highlightColor;'><div style='text-align:center; font-size:1.25em; font-weight:bold; padding:20px;'>$question</div></td></tr>\n";
        echo "<tr>\n";
        for ($i = 1; $i <= $cols; $i ++) {
            $mc5 = $db->prepare(
                    "SELECT answer$v FROM candidates WHERE candName = ?");
            $mc5->execute(array(
                    ${"cand" . $i}
            ));
            $mc5row = $mc5->fetch();
            $answer = nl2br(
                    make_links_clickable(
                            html_entity_decode($mc5row[0], ENT_QUOTES)));
            echo "<td style='border:1px solid $highlightColor; width:$colWidth; padding:5px;'><span style='text-align:justify; $fontSize'>$answer</span></td>\n";
        }
        echo "</tr>\n";
    }
    ?>
</table>