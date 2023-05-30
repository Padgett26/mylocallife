<?php
session_start();

$dbhost = 'localhost';
$dbname = 'mll_db';
$dbuser = 'mll_user';
$dbpass = 'mLl_pWd';

try {
  $db = new PDO("mysql:host=$dbhost; dbname=$dbname", "$dbuser", "$dbpass");
} catch (PDOException $e) {
  echo "";
}

if (filter_input(INPUT_GET, 'userId', FILTER_SANITIZE_STRING)) {
  $userId = filter_input(INPUT_GET, 'userId', FILTER_SANITIZE_STRING);
}

if ($_SESSION['myTheme']) {
  $myTheme = $_SESSION['myTheme'];
} elseif (filter_input(INPUT_COOKIE, 'myTheme', FILTER_SANITIZE_STRING)) {
  $myTheme = filter_input(INPUT_COOKIE, 'myTheme', FILTER_SANITIZE_STRING);
} else {
  $myTheme = "default";
}
$getTheme = $db->prepare("SELECT highlightColor FROM themes WHERE themeName=?");
$getTheme->execute(array($myTheme));
$rowTheme = $getTheme->fetch();
$highlightColor = $rowTheme['highlightColor'];

$time = time();

if (filter_input(INPUT_GET, 'gridSize', FILTER_SANITIZE_NUMBER_INT)) {
  $g = filter_input(INPUT_GET, 'gridSize', FILTER_SANITIZE_NUMBER_INT);
  $_SESSION['gridSize'] = $g;

  // The base, randomized board

  $_SESSION['solution'] = array();
  for ($i = 0; $i <= $g; $i++) {
    for ($j = 0; $j <= $g; $j++) {
      $base = RAND(0, 9);
      if ($base >= 4) {
        $_SESSION['solution'][$i][$j] = 1;
      } else {
        $_SESSION['solution'][$i][$j] = 0;
      }
    }
  }

  //the play area tile definitions

  $_SESSION['playArea'] = array();
  for ($i = 0; $i < $g; $i++) {
    for ($j = 0; $j < $g; $j++) {
      $_SESSION['playArea'][$i][$j][0] = $_SESSION['solution'][$i][$j];
      $_SESSION['playArea'][$i][$j][1] = $_SESSION['solution'][$i][($j + 1)];
      $_SESSION['playArea'][$i][$j][2] = $_SESSION['solution'][($i + 1)][($j + 1)];
      $_SESSION['playArea'][$i][$j][3] = $_SESSION['solution'][($i + 1)][$j];

      if ($j == 0) {
        $_SESSION['playarea'][$i][$j][0] = 0;
      }
      if ($i == 0) {
        $_SESSION['playarea'][$i][$j][3] = 0;
      }
      if ($j == $g) {
        $_SESSION['playarea'][$i][$j][1] = 0;
      }
      if ($i == $g) {
        $_SESSION['playarea'][$i][$j][2] = 0;
      }
    }
  }

  // Shuffle the tiles

  for ($i = 0; $i < $g; $i++) {
    for ($j = 0; $j < $g; $j++) {
      $shuffleTimes = RAND(1, 4);
      for ($k = 0; $k <= $shuffleTimes; $k++) {
        $Temp0 = $_SESSION['playarea'][$i][$j][0];
        $_SESSION['playarea'][$i][$j][0] = $_SESSION['playarea'][$i][$j][3];
        $_SESSION['playarea'][$i][$j][3] = $_SESSION['playarea'][$i][$j][2];
        $_SESSION['playarea'][$i][$j][2] = $_SESSION['playarea'][$i][$j][1];
        $_SESSION['playarea'][$i][$j][1] = $Temp0;
      }
    }
  }

  $_SESSION['startTime'] = $time;
}












$gridSize = ($_SESSION['gridSize'] && $_SESSION['gridSize'] != 0) ? $_SESSION['gridSize'] : 0;
$solution = array();
$playArea = array();

if (filter_input(INPUT_GET, 'i', FILTER_SANITIZE_NUMBER_INT) && filter_input(INPUT_GET, 'j', FILTER_SANITIZE_NUMBER_INT)) {
  $newi = filter_input(INPUT_GET, 'i', FILTER_SANITIZE_NUMBER_INT);
  $newj = filter_input(INPUT_GET, 'j', FILTER_SANITIZE_NUMBER_INT);
  $_SESSION['playArea'][$newi][$newj] = ($_SESSION['playArea'][$newi][$newj] == 1) ? 0 : 1;
  $complete = 1;
  for ($i = 1; $i <= $gridSize; $i++) {
    for ($j = 1; $j <= $gridSize; $j++) {
      $complete = ($_SESSION['solution'][$i][$j] != $_SESSION['playArea'][$i][$j]) ? 0 : $complete;
    }
  }
}

if (filter_input(INPUT_GET, 'reset', FILTER_SANITIZE_STRING) == "yes") {
  for ($i = 0; $i <= $gridSize; $i++) {
    for ($j = 0; $j <= $gridSize; $j++) {
      $_SESSION['playArea'][$i][$j] = 0;
    }
  }
}

for ($i = 0; $i <= $gridSize; $i++) {
  for ($j = 0; $j <= $gridSize; $j++) {
    $solution[$i][$j] = $_SESSION['solution'][$i][$j];
    $playArea[$i][$j] = $_SESSION['playArea'][$i][$j];
  }
}

if ($gridSize == 0) {
  echo "Select a grid size: <input size='2' onchange='puzzleStart(this.value,\"$userId\")' max-length='2' /> (4-20) <button type='button'> Create puzzle </button>\n";
} elseif ($complete == 1) {
  $tot = ($time - $_SESSION['startTime']);
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
  echo "<br />Select a grid size: <input size='2' onchange='puzzleStart(this.value,\"$userId\")' max-length='2' /> (4-20) <button type='button'> Play Again </button>\n<br /><br />";
  if ($userId != '0') {
    echo "<div style='font-weight:bold; font-size:1.25em;'>My best scores:</div>";
    $sb = $db->prepare("SELECT COUNT(*) FROM scoreboard WHERE userId=? && gameName=? && gameLevel=? && score=?");
    $sb->execute(array($userId, 'gridPuzzle', $gridSize, $tot));
    $sbrow = $sb->fetch();
    if ($sbrow[0] == 0) {
      $sb1 = $db->prepare("INSERT INTO scoreboard VALUES(NULL,?,?,?,?,'0','0','0')");
      $sb1->execute(array($userId, 'gridPuzzle', $gridSize, $tot));
    }
    $sb3 = $db->prepare("SELECT id FROM scoreboard WHERE userId=? && gameName=? && gameLevel=? ORDER BY score LIMIT 10,999");
    $sb3->execute(array($userId, 'gridPuzzle', $gridSize));
    while ($sb3row = $sb3->fetch()) {
      $DelId = $sb3row['id'];
      $sb4 = $db->prepare("DELETE FROM scoreboard WHERE id=?");
      $sb4->execute(array($DelId));
    }
    $sb2 = $db->prepare("SELECT score FROM scoreboard WHERE userId=? && gameName=? && gameLevel=? ORDER BY score");
    $sb2->execute(array($userId, 'gridPuzzle', $gridSize));
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
    $sb2->execute(array('gridPuzzle', $gridSize));
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
  ?>
  <table cellpadding="0" cellspacing="0">
    <?php
    for ($i = 0; $i <= $gridSize; $i++) {
      for ($j = 0; $j <= $gridSize; $j++) {
        if ($i == 0 && $j == 0) {
          echo "<tr><td><div style='text-align:center;'>$gridSize X $gridSize</div></td>\n";
        } elseif ($i != 0 && $j == 0) {
          $clue = "";
          $t = 0;
          $rowCorrect = 1;
          for ($k = 1; $k <= $gridSize; $k++) {
            if ($solution[$i][$k] == 0) {
              if ($t != 0) {
                if ($clue != "") {
                  $clue .= ", ";
                }
                $clue .= $t;
                $t = 0;
              }
            } else {
              $t++;
              if ($k == $gridSize) {
                if ($clue != "") {
                  $clue .= ", ";
                }
                $clue .= $t;
              }
            }
            $rowCorrect = ($_SESSION['solution'][$i][$k] != $_SESSION['playArea'][$i][$k]) ? 0 : $rowCorrect;
          }
          echo "<td style='text-align:right; border:1px solid $highlightColor;";
          if ($rowCorrect == 1) {
            echo " background-color:#cccccc;";
          }
          echo "'>$clue</td>\n";
        } elseif ($i == 0 && $j != 0) {
          $clue = "";
          $t = 0;
          $colCorrect = 1;
          for ($k = 1; $k <= $gridSize; $k++) {
            if ($solution[$k][$j] == 0) {
              if ($t != 0) {
                if ($clue != "") {
                  $clue .= ",<br />";
                }
                $clue .= $t;
                $t = 0;
              }
            } else {
              $t++;
              if ($k == ($gridSize)) {
                if ($clue != "") {
                  $clue .= ",<br />";
                }
                $clue .= $t;
              }
            }
            $colCorrect = ($_SESSION['solution'][$k][$j] != $_SESSION['playArea'][$k][$j]) ? 0 : $colCorrect;
          }
          echo "<td style='vertical-align:bottom; text-align:center; border:1px solid $highlightColor;";
          if ($colCorrect == 1) {
            echo " background-color:#cccccc;";
          }
          echo "'>$clue</td>\n";
          if ($j == $gridSize) {
            echo "</tr>";
          }
        } else {
          $bgcolor = ($playArea[$i][$j] == 0) ? "white" : "black";
          echo "<td><button type='button' onclick='puzzleUpdate(\"$i\",\"$j\",\"$userId\")' style='background-color:$bgcolor; width:25px; height:25px;'>&nbsp;</button></td>\n";
          if ($j == $gridSize) {
            echo "</tr>";
          }
        }
      }
    }
    ?>
  </table>
  <?php
  echo "<br /><br /><button type='button' onclick='puzzleReset(\"yes\",\"$userId\")'> Reset game? </button><br /><br /><br />\n";
}
?>
