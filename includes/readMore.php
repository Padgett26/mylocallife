<!-- Beginning of Read More -->
<header style="width:100%; font-weight:bold; font-size:1.25em; text-align:center; margin:40px 0px 20px 0px;">Other Articles You Might Be Interested In</header>
<article style="width:100%;">
  <?php
  $aLog = array();
  $stmt2 = $db->prepare("SELECT articleId FROM articleLog");
  $stmt2->execute();
  while ($row2 = $stmt2->fetch()) {
      $aLog[] = $row2['articleId'];
  }
  $aCount = array_count_values($aLog);
  arsort($aCount);
  $t = 0;
  foreach ($aCount as $k => $v) {
    displayArticle($k, $db, $highlightColor);
    $t++;
    if ($t == 5) {
      break;
    }
  }

  $pLog = array();
  $stmtp2 = $db->prepare("SELECT photoId FROM photoLog");
  $stmtp2->execute();
  while ($rowp2 = $stmtp2->fetch()) {
      $pLog[] = $rowp2['photoId'];
  }
  $pCount = array_count_values($pLog);
  arsort($pCount);
  $p = 0;
  foreach ($pCount as $k => $v) {
    displayPhoto($k, $db, $highlightColor);
    $p++;
    if ($p == 5) {
      break;
    }
  }

  $bLog = array();
  $stmtb2 = $db->prepare("SELECT blogUserId FROM blogLog");
  $stmtb2->execute();
  while ($rowb2 = $stmtb2->fetch()) {
      $bLog[] = $rowb2['blogUserId'];
  }
  $bCount = array_count_values($bLog);
  arsort($bCount);
  $b = 0;
  foreach ($bCount as $k => $v) {
    $stmtb3 = $db->prepare("SELECT id FROM blogDescriptions WHERE userId = ?");
    $stmtb3->execute(array($k));
    $rowb3 = $stmtb3->fetch();
    $getId = $rowb3['id'];
    displayBlog($getId, $db, $highlightColor);
    $b++;
    if ($b == 5) {
      break;
    }
  }
  ?>
</article>
<!-- End of Read More -->