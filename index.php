<?php
include "cgi-bin/functions.php";
include "cgi-bin/config.php";
?>
<!DOCTYPE HTML>
<html manifest="includes/cache.appcache">
  <head>
    <?php
    include "includes/head.php";
    ?>
  </head>
  <body>
  <div class="flex-container" style="max-width: 1100px; margin: auto;">
      <header class="header">
        <?php
        titleText($myId, $highlightColor);
        ?>
      </header>
      <nav class="menu menu">
        <?php
        include "includes/header.php";
        ?>
      </nav>
      <article class="main" id="mainPanel">
        <?php
        include "pages/" . $page . ".php";
        ?>
      </article>
      <aside class="advert advert">
        <?php
        if ($showAdsTop == '1') {
            $topA = $db->prepare(
                    "SELECT userId, adName, adExt, linkText, linkLocal FROM advertising WHERE slot = ? && activeUntil >= ? ORDER BY RAND() LIMIT 1");
            $topA->execute(array(
                    'top',
                    $time
            ));
            $topArow = $topA->fetch();
            $AuserId = $topArow['userId'];
            $topAdName = ($topArow['adName'] != "") ? $topArow['adName'] : "0";
            $topAdExt = ($topArow['adExt'] != "") ? $topArow['adExt'] : "jpg";
            $linkText = $topArow['linkText'];
            $linkLocal = $topArow['linkLocal'];
            if (file_exists(
                    "userPics/" . $AuserId . "/" . $topAdName . "." . $topAdExt)) {
                $lt = ($linkLocal == '1') ? "href='BusinessDetail/$AuserId'" : "href='$linkText' target='_blank'";
                echo "<div style='margin-bottom:0px; width:100%; height:100px; padding:0px; position:relative; overflow:hidden;'><a $lt><img src='userPics/" .
                        $AuserId . "/" . $topAdName . "." . $topAdExt .
                        "' alt='' style='width:100%;' /></a></div>";
            }
        }
        ?>
      </aside>
      <aside class="aside aside">
        <?php
        include "includes/advertSideBar.php";
        ?>
      </aside>
      <footer class="footer">
        <?php
        include "includes/footer.php";
        include "../familyLinks.php";
        ?>
      </footer>
    </div>
    <script src="includes/lightbox2/js/lightbox.js"></script>
  </body>
</html>