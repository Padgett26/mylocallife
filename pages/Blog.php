<?php
$blogUserId = filter_input(INPUT_GET, 'blogUserId', FILTER_SANITIZE_NUMBER_INT);

if ($blogUserId) {
  $bdstmt = $db->prepare("SELECT COUNT(*) FROM blogDescriptions WHERE userId=?");
  $bdstmt->execute(array($blogUserId));
  $bdrow = $bdstmt->fetch();
  if ($bdrow[0] == 0) {
    $blogUserId = "x";
  }
}

if ($blogUserId && $blogUserId != "x") {
  $showM = (filter_input(INPUT_GET, 'showM', FILTER_SANITIZE_NUMBER_INT)) ? filter_input(INPUT_GET, 'showM', FILTER_SANITIZE_NUMBER_INT) : date("n");
  $showY = (filter_input(INPUT_GET, 'showY', FILTER_SANITIZE_NUMBER_INT)) ? filter_input(INPUT_GET, 'showY', FILTER_SANITIZE_NUMBER_INT) : date("Y");
  $check = $db->prepare("SELECT COUNT(*) FROM blog WHERE userId=? && postedMonth=? && postedYear=?");
  $check->execute(array($blogUserId, $showM, $showY));
  $checkrow = $check->fetch();
  if ($checkrow[0] == 0) {
    $check2 = $db->prepare("SELECT postedMonth, postedYear FROM blog WHERE userId=? ORDER BY postedDate DESC LIMIT 1");
    $check2->execute(array($blogUserId));
    $checkrow2 = $check2->fetch();
    $showM = $checkrow2['postedMonth'];
    $showY = $checkrow2['postedYear'];
  }
  echo "<div id='mainTableBox' style='padding:10px;'>";
  $logstmt = $db->prepare("INSERT INTO blogLog VALUES" . "(NULL,?,?,'0','0','0')");
  $logstmt->execute(array($blogUserId, $time));
  $logstmt2 = $db->prepare("UPDATE blogDescriptions SET totalViews = totalViews + 1 WHERE userId = ?");
  $logstmt2->execute(array($blogUserId));
  $bdstmt = $db->prepare("SELECT * FROM blogDescriptions WHERE userId=?");
  $bdstmt->execute(array($blogUserId));
  $bdrow = $bdstmt->fetch();
  $blogTitle = $bdrow['blogTitle'];
  $blogDesc = nl2br(make_links_clickable(html_entity_decode($bdrow['blogDesc'], ENT_QUOTES), $highlightColor));
  $blogPic = $bdrow['blogPic'];
  $blogPicExt = $bdrow['blogPicExt'];
  echo "<table cellspacing='0' style='width:100%'><tr><td colspan='2'>";
  echo "<header style='font-weight:bold; font-size:1.5em; text-align:center;'>$blogTitle</header><br />";
  if (file_exists("userPics/$blogUserId/$blogPic.$blogPicExt")) {
    echo "<img src='userPics/$blogUserId/$blogPic.$blogPicExt' alt='' style='float:right; margin: 10px 0px 10px 10px; border:1px solid $highlightColor; padding:2px; max-width:200px; max-height:200px;' />";
  }
  echo "<article style='text-align:justify;'>$blogDesc</article>";
  echo "</td></tr><tr><td style='width:140px;'><nav style='margin-top:20px;'><div style='text-align:center; text-decoration:underline; font-weight:bold; color:$highlightColor;'>Blog&nbsp;Entries</div>";
  $dateY = $db->prepare("SELECT DISTINCT postedYear FROM blog WHERE userId=? ORDER BY postedDate");
  $dateY->execute(array($blogUserId));
  while ($dateYrow = $dateY->fetch()) {
    $getY = $dateYrow['postedYear'];
    $dateM = $db->prepare("SELECT DISTINCT postedMonth FROM blog WHERE userId=? && postedYear=? ORDER BY postedDate");
    $dateM->execute(array($blogUserId, $getY));
    while ($dateMrow = $dateM->fetch()) {
      $getM = $dateMrow['postedMonth'];
      echo "<div style='padding:10px;'>";
      if ($getM == $showM && $getY == $showY) {
        echo "<span style='font-weight:bold;'>" . $months[$getM] . " " . $getY . "</span>";
      } else {
        echo "<a href='index.php?page=Blog&blogUserId=$blogUserId&showM=$getM&showY=$getY'>" . $months[$getM] . " " . $getY . "</a>";
      }
      echo "</div>";
    }
  }
  echo "</nav>";
  if ($myId != '0') {
    echo "<div id='blogFav' style='margin-top:20px;'><div style='margin-bottom:10px; text-align:center; text-decoration:underline; font-weight:bold; color:$highlightColor;'>Favorite&nbsp;Blogs</div>";
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
        echo "<div style='height:3px; width:50px; background-color:#dddddd; border:1px solid $highlightColor; margin:10px auto;'></div>";
      }
      echo "<div style=''>";
      echo "<a href='index.php?page=Blog&blogUserId=$buId'><div style='font-weight:bold; text-align:center;'>$bt</div><span style='font-size:.75em;'>by $fn $ln</span></a>";
      echo "</div>";
      $t++;
    }
    echo "</div>";
  }
  echo "</td><td>";
  echo "<div style='padding:10px;'>";
  if ($myId != '0') {
    $find = $db->prepare("SELECT COUNT(*) FROM blogFavorites WHERE myId=? && blogUserId=?");
    $find->execute(array($myId, $blogUserId));
    $findrow = $find->fetch();
    $x = ($findrow[0] >= 1) ? "Remove blog from favorites" : "Save blog as a favorite";
    echo "<div id='blogFavBtn' style='margin-bottom:20px; padding:5px 10px; background-color:#f5f5f5; text-decoration:none; cursor:pointer; border:1px solid $highlightColor;' onclick='setBlogFav(\"$blogUserId\", \"$myId\")'>$x</div>";
  }
  ?>
  <div style='float:right; margin:3px 10px 0px 0px;' class="fb-share-button" data-href="https://mylocal.life/index.php?page=Blog&blogUserId=<?php echo $blogUserId; ?>" data-layout="button" data-size="small"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fmylocal.life%2Findex.php%3Fpage%3DBlog%26blogUserId%3D<?php echo $blogUserId; ?>&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a></div>
  <div style='float:right; margin:3px 10px 0px 0px;'><a class="twitter-share-button" href="https://twitter.com/intent/tweet?original_referer=https%3A%2F%2Fmylocal.life%2Findex.php?page=Blog%26blogUserId=<?php echo $blogUserId; ?>&ref_src=twsrc%5Etfw&related=twitterapi%2Ctwitter&tw_p=tweetbutton&url=https%3A%2F%2Fmylocal.life%2Findex.php?page=Blog%26blogUserId=<?php echo $blogUserId; ?>&via=MyLocalLife" data-size="large">Tweet</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script></div>
  <?php
  echo "<button onclick='print()' style='float:right; margin:0px 5px;'>Print</button>";
  echo "<div id='printArea'>";
  $blog = $db->prepare("SELECT * FROM blog WHERE userId=? && postedMonth=? && postedYear=? ORDER BY postedDate");
  $blog->execute(array($blogUserId, $showM, $showY));
  while ($blogrow = $blog->fetch()) {
    $blogEntryId = $blogrow['id'];
    $blogEntryTitle = $blogrow['blogEntryTitle'];
    $blogEntryText = nl2br(make_links_clickable(html_entity_decode($blogrow['blogEntryText'], ENT_QUOTES), $highlightColor));
    $postedDate = $blogrow['postedDate'];
    $picName1 = $blogrow['picName1'];
    $picExt1 = $blogrow['picExt1'];
    $picCaption1 = make_links_clickable($blogrow['picCaption1'], $highlightColor);
    $picName2 = $blogrow['picName2'];
    $picExt2 = $blogrow['picExt2'];
    $picCaption2 = make_links_clickable($blogrow['picCaption2'], $highlightColor);
    $picName3 = $blogrow['picName3'];
    $picExt3 = $blogrow['picExt3'];
    $picCaption3 = make_links_clickable($blogrow['picCaption3'], $highlightColor);
    $picName4 = $blogrow['picName4'];
    $picExt4 = $blogrow['picExt4'];
    $picCaption4 = make_links_clickable($blogrow['picCaption4'], $highlightColor);

    echo "<div style='text-align:left; margin:10px 0px;'>Posted on: " . date("j M Y", $postedDate) . "</div>";
    echo "<header style='font-weight:bold; font-size:2em;'>$blogEntryTitle</header><br />";
    echo "<article style='text-align:justify;'>";
    $t = 0;
    for ($i = 1; $i <= 4; $i++) {
      if (file_exists("userPics/$blogUserId/" . ${'picName' . $i} . "." . ${'picExt' . $i})) {
        $t++;
      }
    }
    if ($t != 0) {
      $textLen = strlen($blogEntryText);
      $pos0 = 0;
      for ($i = 1; $i <= $t; $i++) {
        $offset = (($textLen / ($t + 1)) * $i);
        $pos1 = strpos($blogEntryText, "<br>", $offset) + 6;
        echo ($i <= $t) ? substr($blogEntryText, $pos0, ($pos1 - $pos0)) : substr($blogEntryText, $pos0);
        $pos0 = $pos1;
        if (file_exists("userPics/$blogUserId/" . ${'picName' . $i} . "." . ${'picExt' . $i})) {
          echo "<a href='userPics/$blogUserId/" . ${'picName' . $i} . "." . ${'picExt' . $i} . "' data-lightbox='images'><img src='userPics/$blogUserId/" . ${'picName' . $i} . "." . ${'picExt' . $i} . "' alt='' style=' style='border:1px solid #aaaaaa; padding:10px; width:390px; float:";
          echo ($i % 2 == 1) ? "right; margin:10px 0px 10px 10px;" : "left; margin:10px 10px 10px 0px;";
          echo " width:390px; margin:auto;' /><figcaption style='text-align:center;'>${'picCaption' . $i}</figcaption></a>";
        }
      }
    } else {
      echo $blogEntryText;
    }
    $b2 = $db->prepare("SELECT COUNT(*) FROM blogVideos WHERE blogEntryId=?");
    $b2->execute(array($blogEntryId));
    $b2row = $b2->fetch();
    if ($b2row[0] >= 1) {
      $b3 = $db->prepare("SELECT videoTitle, videoAddress FROM blogVideos WHERE blogEntryId=? ORDER BY videoOrder");
      $b3->execute(array($blogEntryId));
      while ($b3row = $b3->fetch()) {
        $vidTitle = $b3row['videoTitle'];
        $vidaddy = $b3row['videoAddress'];
        echo "<div style='margin:30px auto; height:3px; width:20%; background-color:$highlightColor;'></div>";
        echo "<header style='font-weight:bold; font-size:1.5em;'>$vidTitle</header><br />";
        echo "<iframe width='650' height='365' src='https://www.youtube.com/embed/$vidaddy' frameborder='0' allowfullscreen></iframe>";
      }
    }
    echo "</article><div style='margin:40px auto 60px auto; height:5px; width:80%; background-color:$highlightColor;'></div>";
  }
  echo "</div></div>";
  echo "</td></tr></table>\n";
  include "includes/readMore.php";
} else {
  $x = 0;
  echo "<table id='mainTableBox' cellspacing='5'><tr>";
  $blogList = $db->prepare("SELECT id FROM blogDescriptions ORDER BY RAND() LIMIT 39");
  $blogList->execute();
  while ($blogLrow = $blogList->fetch()) {
    $bid = $blogLrow['id'];
    displayBlog($bid, $db, $highlightColor);
    $x++;
    if ($x % $tableCols == 0) {
      echo "</tr><tr>";
    }
  }
  if ($x % $tableCols == 1) {
    echo "<td></td><td></td>";
  } elseif ($x % $tableCols == 2) {
    echo "<td></td>";
  }
  echo "</tr></table>";
}
