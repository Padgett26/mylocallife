<?php
include "cgi-bin/config.php";
include "cgi-bin/functions.php";
?>
<!DOCTYPE HTML>
<html manifest="includes/cache.appcache">
  <head>
    <?php
				include "includes/head.php";
				?>
  </head>
  <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-62J1QP0V8K"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-62J1QP0V8K');
</script>
  <?php
		$bgImage = (file_exists ( "themePics/$backgroundImage" )) ? "background-image: url('themePics/$backgroundImage');" : "";
		if ($page == "ComicStrips" && filter_input ( INPUT_GET, 'getSeries', FILTER_SANITIZE_NUMBER_INT )) {
			$gs = filter_input ( INPUT_GET, 'getSeries', FILTER_SANITIZE_NUMBER_INT );
			$comicbg = $db->prepare ( "SELECT userId, stripTitle, backExt FROM strips WHERE id=?" );
			$comicbg->execute ( array (
					$gs
			) );
			$comicbgrow = $comicbg->fetch ();
			$bgUserId = $comicbgrow ['userId'];
			$bgBackExt = $comicbgrow ['backExt'];
			$bgStripTitle = str_replace ( " ", "", strtolower ( "back" . $comicbgrow ['stripTitle'] ) );
			$bgImage = (file_exists ( "userPics/$bgUserId/$bgStripTitle.$bgBackExt" )) ? "background-image: url('userPics/$bgUserId/$bgStripTitle.$bgBackExt');" : "";
		}
		?>
  <body onload="sizeBoxes()" style="<?php

		echo $bgImage;
		?> background-color:<?php

		echo $bgColor;
		?>; width:100%; margin:0px; padding:0px; position:relative; left:0px; top:0px; font-family:sans-serif; font-size:<?php

echo $fontSizePercent;
?>%; font-family: 'Open Sans', sans-serif; font-weight:400; color:<?php

echo $fontColor;
?>">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TBZJ9FM"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <!-- Facebook Script -->
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2&appId=539048382922093"></script>

    <div class="flex-container" style="max-width: 1100px; margin: auto;">
      <header class="header">
        <?php
								if ($myId != '0') {
									$stmt = $db->prepare ( "SELECT firstName FROM users WHERE id=?" );
									$stmt->execute ( array (
											$myId
									) );
									$row = $stmt->fetch ();
									$hName = $row ['firstName'] . "'s";
								} else {
									$hName = "my";
								}
								echo "<header style='width:100%;'><div id='titleBar' style='text-transform:lowercase; text-align:center; font-size:6em; font-weight:300; text-shadow: 20px 20px 50px black; padding:20px 0px 50px 0px;'><span style='color:$highlightColor;'>$hName</span><span style='color:black;'>local</span><span style='color:$highlightColor;'>life</span></div></header>";
								?>
      </header>
      <nav class="menu menu">
        <?php
								include "includes/header.php";
								?>
      </nav>
      <article class="main" id="mainPanel">
        <?php
								if (isset ( $page )) {
									include $page . ".php";
								} else {
									include "Home.php";
								}
								?>
      </article>
      <aside class="advert advert">
        <?php
								if ($showAdsTop == '1') {
									$topA = $db->prepare ( "SELECT userId, adName, adExt, linkText, linkLocal FROM advertising WHERE slot = ? && activeUntil >= ? ORDER BY RAND() LIMIT 1" );
									$topA->execute ( array (
											'top',
											$time
									) );
									$topArow = $topA->fetch ();
									$AuserId = $topArow ['userId'];
									$topAdName = ($topArow ['adName'] != "") ? $topArow ['adName'] : "0";
									$topAdExt = ($topArow ['adExt'] != "") ? $topArow ['adExt'] : "jpg";
									$linkText = $topArow ['linkText'];
									$linkLocal = $topArow ['linkLocal'];
									if (file_exists ( "userPics/" . $AuserId . "/" . $topAdName . "." . $topAdExt )) {
										$lt = ($linkLocal == '1') ? "href='index.php?page=BusinessDetail&business=$AuserId'" : "href='$linkText' target='_blank'";
										echo "<div style='margin-bottom:0px; width:100%; height:100px; padding:0px; position:relative; overflow:hidden;'><a $lt><img src='userPics/" . $AuserId . "/" . $topAdName . "." . $topAdExt . "' alt='' style='width:100%;' /></a></div>";
									}
								}
								?>
      </aside>
      <aside class="aside aside">
        <?php
								include "advertSideBar.php";
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