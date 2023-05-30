<!DOCTYPE HTML>
<html>
    <head>
        <script>
            function allowDrop(ev) {
                ev.preventDefault();
            }

            function drag(ev) {
                ev.dataTransfer.setData("text", ev.target.id);
            }

            function drop(ev) {
                ev.preventDefault();
                var data = ev.dataTransfer.getData("text");
                ev.target.appendChild(document.getElementById(data));
            }
        </script>
    </head>
    <body>
        <?php
        $answer = array();
        $quiries = array();

        for ($i = 0; $i < count($_SESSION['answer']); $i++) {
            $answer[$i] = $_SESSION['answer'][$i];
        }

        for ($i = 0; $i < count($_SESSION['quiries']); $i++) {
            for ($j = 0; $j <= 10; $j++) {
                $quiries[$i][$j] = $_SESSION['quiries'][$i][$j];
            }
        }

        function checkAnswer($line) {
            $won = 1;
            for ($i = 0; $i <= count($answer); $i++) {
                if ($quiries[$line][$i] != $answer[$i]) {
                    $won = 0;
                }
            }
            return $won;
        }

        if (filter_input(INPUT_GET, 'userId', FILTER_SANITIZE_NUMBER_INT)) {
            $userId = filter_input(INPUT_GET, 'userId', FILTER_SANITIZE_NUMBER_INT);
        }

        if (filter_input(INPUT_GET, 'game', FILTER_SANITIZE_STRING) == "new") { // Creating a new puzzle
            $answer = array();
            $quiries = array();
            $_SESSION['startTime'] = time();

            $o = filter_input(INPUT_GET, 'gridSize', FILTER_SANITIZE_NUMBER_INT);
            $_SESSION['gridSize'] = $o;
            for ($i = 0; $i <= $o; $i++) {
                if ($i == 0) {
                    $answer[0] = "c";
                } else {
                    $answer[$i] = rand(1, $o) - 1;
                }
            }

            for ($i = 0; $i <= $o; $i++) {
                for ($j = 0; $j < 10; $j++) {
                    $quiries[$i][$j] = "x";
                }
            }
        }

        $gridSize = $_SESSION['gridSize'];

        if (filter_input(INPUT_GET, 'dot', FILTER_SANITIZE_NUMBER_INT)) {
            $dot = filter_input(INPUT_GET, 'dot', FILTER_SANITIZE_NUMBER_INT);
            $spot = filter_input(INPUT_GET, 'spot', FILTER_SANITIZE_NUMBER_INT);
            for ($i = 0; $i < 10; $i++) {
                if ($quiries[$i][0] != "c") {
                    $quiries[$i][$spot] = $dot;
                    $lineComplete = 1;
                    foreach ($quiries[$i] as $k => $v) {
                        if ($k != 0 && $v == "x") {
                            $lineComplete = 0;
                        }
                    }
                    if ($lineComplete == 1) {
                        $quiries[$i][0] = "c";
                        $won = checkAnswer($i);
                    }
                    break;
                }
            }
        }

        for ($i = 0; $i < count($quiries); $i++) {
            for ($j = 0; $j <= 10; $j++) {
                $_SESSION['quiries'][$i][$j] = $quiries[$i][$j];
            }
        }

        if ($won == 1) {
            $tot = (time() - $_SESSION['startTime']);
            $hours = (INT) ($tot / 3600);
            $mins = (INT) (($tot - ($hours * 3600)) / 60);
            $secs = (INT) (($tot - ($hours * 3600)) % 60);
            echo "<span style='font-weight:bold;'>You won!!! And it only took ";
            if ($hours != 0) {
                echo "$hours hours";
                $h = 1;
            }
            if ($mins != 0) {
                if ($h == 1) {
                    echo ", ";
                }
                echo "$mins mins";
                $m = 1;
            }
            if ($secs != 0) {
                if ($h == 1 || $m == 1) {
                    echo ", ";
                }
                echo "$secs secs</span><br /><br />";
            }
            ?>
            Select a size: <select onchange='puzzleStart(this.value, "<?php echo $userId; ?>")' />
            <?php
            for ($i = 4; $i <= 9; $i++) {
                echo "<option value='$i'>$i</option>\n";
            }
            ?>
        </select>
        <?php
        if ($userId != '0') {
            echo "<div style='font-weight:bold; font-size:1.25em;'>My best scores:</div>";
            $sb = $db->prepare("SELECT COUNT(*) FROM scoreboard WHERE userId=? && gameName=? && gameLevel=? && score=?");
            $sb->execute(array($userId, 'sequence', $gridSize, $tot));
            $sbrow = $sb->fetch();
            if ($sbrow[0] == 0) {
                $sb1 = $db->prepare("INSERT INTO scoreboard VALUES(NULL,?,?,?,?,'0','0','0')");
                $sb1->execute(array($userId, 'sequence', $gridSize, $tot));
            }
            $sb3 = $db->prepare("SELECT id FROM scoreboard WHERE userId=? && gameName=? && gameLevel=? ORDER BY score LIMIT 10,999");
            $sb3->execute(array($userId, 'sequence', $gridSize));
            while ($sb3row = $sb3->fetch()) {
                $DelId = $sb3row['id'];
                $sb4 = $db->prepare("DELETE FROM scoreboard WHERE id=?");
                $sb4->execute(array($DelId));
            }
            $sb2 = $db->prepare("SELECT score FROM scoreboard WHERE userId=? && gameName=? && gameLevel=? ORDER BY score");
            $sb2->execute(array($userId, 'sequence', $gridSize));
            $t = '1';
            while ($sb2row = $sb2->fetch()) {
                $score = $sb2row['score'];
                $s = "<span style='font-weight:bold;'>$t.</span> ";
                $s .= ($score == $tot) ? "<span style='font-weight:bold; font-size:1.25em;'>" : "<span style=''>";
                $hours = (INT) ($score / 3600);
                $mins = (INT) (($score - ($hours * 3600)) / 60);
                $secs = (INT) (($score - ($hours * 3600)) % 60);
                if ($hours != '0') {
                    $s .= "$hours hours";
                    $h = '1';
                }
                if ($mins != '0') {
                    if ($h == '1') {
                        $s .= ", ";
                    }
                    $s .= "$mins mins";
                    $m = '1';
                }
                if ($secs != '0') {
                    if ($h == '1' || $m == '1') {
                        $s .= ", ";
                    }
                    $s .= "$secs secs";
                }
                $s .= "</span><br />";
                echo $s;
                $t++;
            }
            echo "<br /><div style='font-weight:bold; font-size:1.25em;'>Global best scores:</div>";
            $sb2 = $db->prepare("SELECT t1.score, t2.firstName, t2.lastName FROM scoreboard AS t1 INNER JOIN users AS t2 ON t1.userId = t2.id WHERE t1.gameName=? && gameLevel=? ORDER BY t1.score LIMIT 10");
            $sb2->execute(array('sequence', $gridSize));
            $t = '1';
            while ($sb2row = $sb2->fetch()) {
                $score = $sb2row['score'];
                $fname = $sb2row['firstName'];
                $lname = str_split($sb2row['lastName']);
                $s = "<span style='font-weight:bold;'>$t.</span> ";
                $s .= ($score == $tot) ? "<span style='font-weight:bold; font-size:1.25em;'>" : "<span style=''>";
                $hours = (INT) ($score / 3600);
                $mins = (INT) (($score - ($hours * 3600)) / 60);
                $secs = (INT) (($score - ($hours * 3600)) % 60);
                if ($hours != '0') {
                    $s .= "$hours hours";
                    $h = '1';
                }
                if ($mins != '0') {
                    if ($h == '1') {
                        $s .= ", ";
                    }
                    $s .= "$mins mins";
                    $m = '1';
                }
                if ($secs != '0') {
                    if ($h == '1' || $m == '1') {
                        $s .= ", ";
                    }
                    $s .= "$secs secs";
                }
                $s .= " by $fname $lname[0]</span><br />";
                echo $s;
                $t++;
            }
        }
    } else {
        
    }
    ?>
    </body>
</html>