<?php

$dbhost = 'localhost';
$dbname = 'mll_db';
$dbuser = 'mll_user';
$dbpass = 'mLl_pWd';

try {
    $db = new PDO("mysql:host=$dbhost; dbname=$dbname", "$dbuser", "$dbpass");
} catch (PDOException $e) {
    echo "";
}

if (filter_input(INPUT_POST, 'addPuzzles', FILTER_SANITIZE_NUMBER_INT) >= 1) {
    $add = filter_input(INPUT_POST, 'addPuzzles', FILTER_SANITIZE_NUMBER_INT);
    for ($i = 0; $i < $add; $i++) {
        $puzzle = newGrid();
        file_put_contents('sudokuPuzzles.txt', $puzzle, FILE_APPEND);
    }
}

if (filter_input(INPUT_POST, 'uploadPuzzles', FILTER_SANITIZE_NUMBER_INT) == 1) {
    $puzzles = file_get_contents('sudokuPuzzles.txt');
    $puzzleList = str_split($puzzles, 81);
    $x = count($puzzleList);
    $time = time();
    for ($i = 0; $i < $x; $i++) {
        $stmt3 = $db->prepare("INSERT INTO sudokuBoards VALUES(NULL, ?, ?, ?, ?)");
        $stmt3->execute(array("X", $puzzleList[$i], 0, $time));
    }
    file_put_contents('sudokuPuzzles.txt', "");
}

if (filter_input(INPUT_POST, 'numToDelete', FILTER_SANITIZE_NUMBER_INT) >= 1) {
    $del = filter_input(INPUT_POST, 'numToDelete', FILTER_SANITIZE_NUMBER_INT);
    $stmt = $db->prepare("SELECT id FROM sudokuBoards ORDER BY usedBoard DESC LIMIT ?");
    $stmt->execute(array($del));
    while ($row = $stmt->fetch()) {
        $id = $row['id'];
        $stmt2 = prepare("DELETE FROM sudokuBoards WHERE id = ?");
        $stmt2->execute(array($id));
    }
}
?>

<section>
    <header style="cursor:pointer; background-color:#dddddd; border:1px solid #aaaaaa; font-weight:bold; font-size:1.5em; padding:10px; margin:10px 0px;">
        Generate Sudoku Puzzles
    </header>
    <article id="sudokuGen" style="display:block; padding:10px; margin:10px 0px; border:1px solid #aaaaaa;">
        <?php
        echo "<form action='generatePuzzles.php' method='post'><table cellspacing='0' cellpadding='10'>";
        echo "<tr>";
        echo "<td style='border:1px solid black;'>Add<br />Puzzles</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td style='border:1px solid black;'><input type='text' name='addPuzzles' value='0' size='4' /></td>";
        echo "</tr>";
        echo "<tr><td style='border:1px solid black;' colspan='3'><input type='hidden' name='sudokuUpdate' value='1' /><input type='submit' value=' Update ' /></td></tr></table></form>";
        ?>
    </article>
    <article id="sudokuUp" style="display:block; padding:10px; margin:10px 0px; border:1px solid #aaaaaa;">
        <?php
        echo "<form action='generatePuzzles.php' method='post'><table cellspacing='0' cellpadding='10'>";
        echo "<tr>";
        echo "<td style='border:1px solid black;'>Available Puzzles<br />to upload</td>";
        echo "</tr>";
        echo "<tr>";
        $contents = file_get_contents("sudokuPuzzles.txt");
        $count = strlen($contents) / 81;
        echo "<td style='border:1px solid black;'>$count</td>";
        echo "</tr>";
        echo "<tr><td style='border:1px solid black;' colspan='3'><input type='hidden' name='uploadPuzzles' value='1' /><input type='submit' value=' Upload ' /></td></tr></table></form>";
        ?>
    </article>
    <article id="sudokuDel" style="display:block; padding:10px; margin:10px 0px; border:1px solid #aaaaaa;">
        <?php
        echo "<form action='generatePuzzles.php' method='post'><table cellspacing='0' cellpadding='10'>";
        echo "<tr>";
        echo "<td style='border:1px solid black;'>Delete oldest<br /># of puzzles</td>";
        echo "</tr>";
        echo "<tr>";
        $stmt = $db->prepare("SELECT COUNT(*) FROM sudokuBoards");
        $stmt->execute();
        $row = $stmt->fetch();
        $c = $row[0];
        echo "<td style='border:1px solid black;'>There are $c<br />available<br /><input type='text' name='numToDelete' value='0' size='4' /></td>";
        echo "</tr>";
        echo "<tr><td style='border:1px solid black;' colspan='3'><input type='hidden' name='delPuzzles' value='1' /><input type='submit' value=' Upload ' /></td></tr></table></form>";
        ?>
    </article>
</section>

<?php

// Gadmin Sudoku Puzzel generation

function newGrid() {
    $baseArray = array();
    for ($i = 0; $i < 9; $i++) {
        for ($j = 0; $j < 9; $j++) {
            $baseArray[$i][$j] = 0;
        }
    }

    $filledArray = fillGrid(0, 0, $baseArray);

    $shuffledArray = shuffleGrid($filledArray);

    $t = "";
    for ($k = 0; $k < 9; $k++) {
        for ($l = 0; $l < 9; $l++) {
            $t .= $shuffledArray[$k][$l];
        }
    }

    return $t;
}

function fillGrid($a, $b, &$array) {
    if ($a == 9 && $b == 0) {
        return $array;
    } else {
        $numbers = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
        shuffle($numbers);
        for ($r = 0; $r < 9; $r++) {
            $array[$a][$b] = $numbers[$r];
            if (checkCRB($a, $b, $array) == 1) {
                $i = ($b == 8) ? $a + 1 : $a;
                $j = ($b == 8) ? 0 : $b + 1;
                if (!fillGrid($i, $j, $array)) {
                    if ($r == 8) {
                        $array[$a][$b] = 0;
                        return false;
                    } else {
                        continue;
                    }
                } else {
                    return $array;
                }
            } else {
                if ($r == 8) {
                    $array[$a][$b] = 0;
                    return false;
                } else {
                    continue;
                }
            }
        }
    }
}

function shuffleGrid($array) {
    for ($i = 0; $i < 10; $i++) {
        $t = rand(0, 3);
        switch ($t) {
            case 0:
                $array = shuffle1($array);
                break;
            case 1:
                $array = shuffle2($array);
                break;
            case 2:
                $array = shuffle3($array);
                break;
            case 3:
                $array = shuffle4($array);
                break;
        }
    }
    return $array;
}

// mutual exchange of two digits
function shuffle1($array) {
    $t1 = rand(1, 9);
    $t2 = rand(1, 9);
    if ($t1 == $t2) {
        $t2 = ($t1 < 5) ? $t1 + 1 : $t1 - 1;
    }

    for ($i = 0; $i < 9; $i++) {
        for ($j = 0; $j < 9; $j++) {
            if ($array[$i][$j] == $t1) {
                $array[$i][$j] = $t2;
            } elseif ($array[$i][$j] == $t2) {
                $array[$i][$j] = $t1;
            }
        }
    }
    return $array;
}

// mutual exchange of two columns in the same column of blocks
function shuffle2($array) {
    $array2 = array();
    for ($i = 0; $i < 9; $i++) {
        for ($j = 0; $j < 9; $j++) {
            $array2[$i][$j] = $array[$i][$j];
        }
    }
    for ($k = 0; $k < 9; $k = $k + 3) {
        $t1 = rand(0, 2);
        $x = rand(1, 2);
        if ($t1 == 0) {
            $t2 = $t1 + $x;
        } elseif ($t1 == 1) {
            $t2 = ($x == 1) ? $t1 - 1 : $t1 + 1;
        } elseif ($t1 == 2) {
            $t2 = $t1 - $x;
        }
        for ($i = 0; $i < 9; $i++) {
            $array[$i][$t2 + $k] = $array2[$i][$t1 + $k];
        }
        for ($i = 0; $i < 9; $i++) {
            $array[$i][$t1 + $k] = $array2[$i][$t2 + $k];
        }
    }
    return $array;
}

// mutual exchange of two columns of blocks
function shuffle3($array) {
    $array2 = array();
    $t1 = rand(0, 2);
    $x = rand(1, 2);
    if ($t1 == 0) {
        $t2 = $t1 + $x;
    } elseif ($t1 == 1) {
        $t2 = ($x == 1) ? $t1 - 1 : $t1 + 1;
    } elseif ($t1 == 2) {
        $t2 = $t1 - $x;
    }
    for ($i = 0; $i < 9; $i++) {
        for ($j = 0; $j < 9; $j++) {
            $array2[$i][$j] = $array[$i][$j];
        }
    }
    for ($i = 0; $i < 9; $i++) {
        $array[$i][$t2 * 3] = $array2[$i][$t1 * 3];
        $array[$i][$t2 * 3 + 1] = $array2[$i][$t1 * 3 + 1];
        $array[$i][$t2 * 3 + 2] = $array2[$i][$t1 * 3 + 2];
    }
    for ($i = 0; $i < 9; $i++) {
        $array[$i][$t1 * 3] = $array2[$i][$t2 * 3];
        $array[$i][$t1 * 3 + 1] = $array2[$i][$t2 * 3 + 1];
        $array[$i][$t1 * 3 + 2] = $array2[$i][$t2 * 3 + 2];
    }
    return $array;
}

// grid rolling
function shuffle4($array) {
    $array2 = array();
    for ($i = 0; $i < 9; $i++) {
        for ($j = 0; $j < 9; $j++) {
            $array2[$i][$j] = $array[$i][$j];
        }
    }
    for ($i = 0; $i < 9; $i++) {
        for ($j = 0; $j < 9; $j++) {
            $a = 0 + $j;
            $b = 8 - $i;
            $array[$a][$b] = $array2[$i][$j];
        }
    }
    return $array;
}

function checkCRB($i, $j, $array) {
    $x = column($i, $j, $array);
    $y = row($i, $j, $array);
    $z = block($i, $j, $array);
    if ($x == 1 && $y == 1 && $z == 1) {
        return 1;
    } else {
        return 0;
    }
}

function column($i, $j, $array) {
    $num = $array[$i][$j];
    if ($num != 0) {
        for ($a = 0; $a < 9; $a++) {
            if ($array[$a][$j] == $num && $a != $i) {
                return 0;
            }
        }
    }
    return 1;
}

function row($i, $j, $array) {
    $num = $array[$i][$j];
    if ($num != 0) {
        for ($a = 0; $a < 9; $a++) {
            if ($array[$i][$a] == $num && $a != $j) {
                return 0;
            }
        }
    }
    return 1;
}

function block($i, $j, $array) {
    $num = $array[$i][$j];
    if ($num != 0) {
        $a = floor($i / 3) * 3;
        $b = floor($j / 3) * 3;
        for ($c = $a; $c < $a + 3; $c++) {
            for ($d = $b; $d < $b + 3; $d++) {
                if ($c == $i && $d == $j) {
                    continue;
                }
                if ($array[$c][$d] == $num) {
                    return 0;
                }
            }
        }
    }
    return 1;
}
