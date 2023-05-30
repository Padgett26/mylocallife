<?php
$pId = filter_input(INPUT_GET, 'photoShow', FILTER_SANITIZE_NUMBER_INT);
$stmt = $db->prepare("SELECT COUNT(*) FROM photoJournalism WHERE id = ?");
$stmt->execute(array($pId));
$row = $stmt->fetch();
$photoId = ($row[0] == 1) ? $pId : FALSE;

if ($photoId) {
  echo "<div id='mainTableBox' style='padding:10px;'>\n";
  echo "<div id='printArea'>";
  $logstmt = $db->prepare("INSERT INTO photoLog VALUES" . "(NULL,?,?,'0','0','0')");
  $logstmt->execute(array($photoId, $time));
  $logstmt2 = $db->prepare("UPDATE photoJournalism SET totalViews = totalViews + 1 WHERE id = ?");
  $logstmt2->execute(array($photoId));
  $stmt = $db->prepare("SELECT * FROM photoJournalism WHERE id=?");
  $stmt->execute(array($photoId));
  $row = $stmt->fetch();
  $photoTitle = $row['photoTitle'];
  $photoText = nl2br(make_links_clickable(html_entity_decode($row['photoText'], ENT_QUOTES), $highlightColor));
  $authorId = $row['authorId'];
  $postedDate = $row['postedDate'];
  $editedDate = $row['editedDate'];
  $substmt = $db->prepare("SELECT firstName, lastName FROM users WHERE id=?");
  $substmt->execute(array($authorId));
  $subrow = $substmt->fetch();
  $firstName = $subrow['firstName'];
  $lastName = $subrow['lastName'];

  echo "<header style='text-align:center; margin:20px; font-weight:bold; font-size:1.5em;'>$photoTitle</header>\n";
  ?>
  <div style='float:right; margin:3px 10px 0px 0px;' class="fb-share-button" data-href="https://mylocal.life/index.php?page=Photo&photoShow=<?php echo $photoId; ?>" data-layout="button" data-size="small"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fmylocal.life%2Findex.php%3Fpage%3DPhoto%26photoShow%3D<?php echo $photoId; ?>&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a></div>
  <div style='float:right; margin:3px 10px 0px 0px;'><a class="twitter-share-button" href="https://twitter.com/intent/tweet?original_referer=https%3A%2F%2Fmylocal.life%2Findex.php?page=Photo%26photoShow=<?php echo $photoId; ?>&ref_src=twsrc%5Etfw&related=twitterapi%2Ctwitter&tw_p=tweetbutton&url=https%3A%2F%2Fmylocal.life%2Findex.php?page=Photo%26photoShow=<?php echo $photoId; ?>&via=MyLocalLife" data-size="large">Tweet</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script></div>
  <?php
  echo "<button onclick='print()' style='float:right; margin:0px 5px;'>Print</button>";
  echo "Posted date: " . date("M j, Y", $postedDate) . "<br />";
  if ($editedDate != '0' && date("M j, Y", $postedDate) != date("M j, Y", $editedDate)) {
    echo "Last edit date: " . date("M j, Y", $editedDate) . "<br /><br />";
  }
  echo "<br />by: $firstName $lastName<br /><br />";
  echo "<article style='text-align:justify;'>$photoText</article><br /><br /><br /><span style='font-size:.75em;'>Click on photos to view slide show</span><br />";
  $pst3 = $db->prepare("SELECT COUNT(*) FROM photoList WHERE photoId = ?");
  $pst3->execute(array($photoId));
  $prow3 = $pst3->fetch();
  $pCount = $prow3[0];
  $s = ceil($pCount / 4) * 162;
  echo "<div style='width:100%; height:" . $s . "px;'>";
  $pst2 = $db->prepare("SELECT * FROM photoList WHERE photoId = ? ORDER BY photoOrder");
  $pst2->execute(array($photoId));
  while ($prow2 = $pst2->fetch()) {
    $photoName = $prow2['photoName'];
    $photoExt = $prow2['photoExt'];
    $photoCaption = nl2br(make_links_clickable($prow2['photoCaption'], $highlightColor));

    if (file_exists("userPics/$authorId/$photoName.$photoExt")) {
      echo "<div style='width:25%; height:162px; float:left;'>";
      echo "<a href='userPics/$authorId/$photoName.$photoExt' data-lightbox='images' data-title='$photoCaption'>";
      if (file_exists("userPics/$authorId/thumb/$photoName.$photoExt")) {
        echo "<img src='userPics/$authorId/thumb/$photoName.$photoExt' alt='' style='border:1px solid $highlightColor; padding:5px;' />";
      } else {
        echo "<img src='userPics/$authorId/$photoName.$photoExt' alt='' style='border:1px solid $highlightColor; padding:5px; max-width:150px; max-height:150px;' />";
      }
      echo "</a>";
      echo "</div>\n";
    }
  }
  echo "</div></div></div>\n";
  include "includes/readMore.php";
} else {

  $x = 3;
  echo "<table id='mainTableBox' cellspacing='5'><tr>";
  $stmt18 = $db->prepare("SELECT id FROM photoJournalism WHERE approved = ? ORDER BY postedDate DESC LIMIT 36");
  $stmt18->execute(array('1'));
  while ($row18 = $stmt18->fetch()) {
    $id = $row18['id'];
    displayPhoto($id, $db, $highlightColor);
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
