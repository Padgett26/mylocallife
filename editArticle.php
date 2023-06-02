<?php
$inReplyTo = (filter_input(INPUT_GET, 'inReplyTo', FILTER_SANITIZE_NUMBER_INT)) ? filter_input(
        INPUT_GET, 'inReplyTo', FILTER_SANITIZE_NUMBER_INT) : '0';
if ($myAccess != 0) {
    $artId = filter_input(INPUT_GET, 'artId', FILTER_SANITIZE_STRING);
    $postedDate = date("Y-m-d", $time);
    if ($artId == 'new') {
        $authorId = $myId;
        $articleTitle = "";
        $articleText = "";
        $pic1Name = "x";
        $pic1Ext = "png";
        $pic1Caption = "";
        $pic2Name = "x";
        $pic2Ext = "png";
        $pic2Caption = "";
        $postedDate = date("Y-m-d", $time);
        $editedDate = "";
        $catId = 0;
        $inReplyTo = 0;
        $youtube = "";
        $pdf1 = "";
        $pdfText1 = "";
        $pdf2 = "";
        $pdfText2 = "";
        $embedCode1 = "";
    } else {
        $stmt = $db->prepare("SELECT * FROM articles WHERE id=? && authorId=?");
        $stmt->execute(array(
                $artId,
                $myId
        ));
        $row = $stmt->fetch();
        if ($row) {
            $authorId = $row['authorId'];
            $articleTitle = $row['articleTitle'];
            $articleText = html_entity_decode($row['articleText'], ENT_QUOTES);
            $pic1Name = $row['pic1Name'];
            $pic1Ext = $row['pic1Ext'];
            $pic1Caption = $row['pic1Caption'];
            $pic2Name = $row['pic2Name'];
            $pic2Ext = $row['pic2Ext'];
            $pic2Caption = $row['pic2Caption'];
            $postedDate = $row['postedDate'];
            $editedDate = $row['editedDate'];
            $catId = $row['catId'];
            $inReplyTo = $row['inReplyTo'];
            $youtube = $row['youtube'];
            $pdf1 = $row['pdf1'];
            $pdfText1 = $row['pdfText1'];
            $pdf2 = $row['pdf2'];
            $pdfText2 = $row['pdfText2'];
            $embedCode1 = html_entity_decode($row['embedCode1'], ENT_QUOTES);
        }
    }

    if ($authorId == $myId || ($myId != '0' && $artId == "new")) {
        ?>
		<div id='mainTableBox' style="padding:10px;">
			<form action="index.php?page=myAccount" method="post" enctype='multipart/form-data'>
				<?php
        if ($inReplyTo != '0') {
            $gr = $db->prepare(
                    "SELECT t1.articleTitle, t1.postedDate, t2.firstName, t2.lastName FROM articles AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id WHERE t1.id=?");
            $gr->execute(array(
                    $inReplyTo
            ));
            $grow = $gr->fetch();
            $artTitle = $grow['articleTitle'];
            $artPosted = $grow['postedDate'];
            $artFN = $grow['firstName'];
            $artLN = $grow['lastName'];
            echo "<div style='margin:20px 0px;'>This article is a reply to:<br />- $artTitle<br />\t<span style='font-size:.75em;'>" .
                    date("M j, Y", $artPosted) .
                    "\t by $artFN $artLN</span></div>";
        }
        if ($editedDate > '1') {
            echo "Previous edit date: " . date("M j, Y", $editedDate) .
                    "<br /><br />";
        }
        ?>
				Category: <select name="catId" size="1">
					<?php
        $substmt = $db->prepare("SELECT id, category FROM articleCategories");
        $substmt->execute();
        while ($subrow = $substmt->fetch()) {
            $cId = $subrow['id'];
            $cCategory = $subrow['category'];
            echo "<option value='$cId'";
            if ($cId == $catId) {
                echo " selected='selected'";
            }
            echo ">$cCategory</option>";
        }
        ?>
				</select><br><br>
				<span style="font-weight:bold;">Publish Article Date:</span><br>
				<?php
        echo ($artId == "new") ? "<input type='date' name='postedDate' value='$postedDate' min='$postedDate'>\n" : "$postedDate<input type='hidden' name='postedDate' value='$postedDate'>\n";
        ?>
				<br><br>
				<span style='font-weight:bold;'>Article title:</span><br>
				<input type="text" name="articleTitle" value="<?php

        echo $articleTitle;
        ?>" size="70" max-length="120" /><br /><br />
				<span style='font-weight:bold;'>Pic 1:</span>
				<?php
        if (file_exists("userPics/$myId/$pic1Name.$pic1Ext")) {
            echo "<img src='userPics/$myId/$pic1Name.$pic1Ext' alt='' style='max-width:400px; max-height:400px' /><br />";
        }
        ?>
				Upload pic 1: <input type="file" name="image1" style="background-color:#dddddd;" /><br />
				Pic 1 caption:<br />
				<input type="text" name="pic1Caption" value="<?php

        echo $pic1Caption;
        ?>" size="70" max-length="200" /><br /><br />
				<span style='font-weight:bold;'>Pic 2:</span>
				<?php
        if (file_exists("userPics/$myId/$pic2Name.$pic2Ext")) {
            echo "<img src='userPics/$myId/$pic2Name.$pic2Ext' alt='' style='max-width:400px; max-height:400px' /><br />";
        }
        ?>
				Upload pic 2: <input type="file" name="image2" style="background-color:#dddddd;" /><br />
				Pic 2 caption:<br />
				<input type="text" name="pic2Caption" value="<?php

        echo $pic2Caption;
        ?>" size="70" max-length="200" /><br /><br />
				<?php
        $pdf1Exists = (file_exists("userPics/$myId/" . $pdf1 . ".pdf")) ? "1" : "0";
        echo "<span style='font-weight:bold;'>PDF 1";
        echo ($pdf1Exists == '1') ? " is already set</span><br />Replace the existing pdf: " : " is available</span><br/>Upload a new pdf: ";
        echo "<input type='file' name='newPdf1' /><br />Text to use as a link to your pdf:<br /><input type='text' name='pdfText1' value='$pdfText1' size='70' /><br /><br />";

        $pdf2Exists = (file_exists("userPics/$myId/" . $pdf2 . ".pdf")) ? "1" : "0";
        echo "<span style='font-weight:bold;'>PDF 2";
        echo ($pdf1Exists == '1') ? " is already set</span><br />Replace the existing pdf: " : " is available</span><br/>Upload a new pdf: ";
        echo "<input type='file' name='newPdf2' /><br />Text to use as a link to your pdf:<br /><input type='text' name='pdfText2' value='$pdfText2' size='70' /><br /><br />";

        echo "<span style='font-weight:bold;'>Add a youtube video:</span> (on the youtube page, under the video you want to share, click the 'share' button.  There is an address that shows up under the social media icons, copy it and paste it here)<br />";
        if ($youtube != "0" && $artId != "new") :
            echo "<input type='text' name='youtube' value='https://youtu.be/$youtube' size='70' /><br /><br />";
        else :
            echo "<input type='text' name='youtube' value='' size='70' /><br /><br />";
        endif;
        echo "<span style='font-weight:bold;'>Add an embed code (1):</span> (in the article text put <*embedCode1*> where you want this code to go)<br />";
        echo "<input type='text' name='embedCode1' value='$embedCode1' size='70' /><br /><br />";
        ?>
				<span style='font-weight:bold;'>Article text:</span><br />
				To include a website link in your text, simply put the full address in your text (ex. <?php

        echo make_links_clickable('https://mylocal.life/index.php');
        ?> or <?php

        echo make_links_clickable('https://mylocal.life');
        ?>), and it will be converted to a link when displayed.<br />
				<textarea name="articleText" cols="70" rows="20" maxlength='65500'><?php

        echo $articleText;
        ?></textarea><br /><br />
				<?php
        if ($artId != "new") {
            ?>
					Do you want to delete this article? This will erase the article and delete any pictures associated with it. This is permanent and cannot be undone.<br />
					YES, <input type="checkbox" name="delArticle" value="1" /> delete this article.<br /><br />
				<?php
        } else {
            ?>
					<header style="font-weight:bold; text-align:center;">Terms and Conditions</header>
					<article style="text-align:justify;">
						Initially, your article will be reviewed by an admin of this website. The admin will decide to either approve the article for publication on mylocal.life, or decide to delete the article due to being inappropriate for mylocal.life.<br /><br />
						My Local Life claims no rights to any data you enter into the site, other than the right to display such data on the mylocal.life website, and delete the data if it is found to be inappropriate for the website during the initial review of the article, or if it is reported and found to be inappropriate for the website at a later time. Users of the site have the right to view such data, and with each article users have the right and ability to share your article on social media.<br /><br />
						If there is intent to republish, share, redistribute any content posted on this site, whether it text, any type of media, or any information in any form, that you did not create and submit yourself, then My Local Life and the specific web page the material originates from, must be cited in the republication or redistribution.<br /><br />
						Registered users have the ability to report your data for whatever reason. If an article is reported, it will be hidden from public display on mylocal.life. A notification of the reported article will go to one of the admins of this site, and the article will be reviewed. The admin has the right to, and will decide if the article will return to a visible state on mylocal.life, or if it will be deleted from the site. If your article is deleted, the assigned admin will attempt to notify you of the deletion, but mylocal.life is not accountable if the notification does not reach you for any reason.<br /><br />
						By putting any data on this website, you are stating that you have the right to publish that data to this site. You either own rights to any collection of words, or digital media, because you are the creator, or you have the permission from the creator of those collections of words or digital media. If you have a quote from another author, it must be cited. If a collection of words is found to originate from another author and is not cited, it will be considered plagiarism, and you as the submitter of the article will be responsible to fix the problem, or the article will be deleted.<br /><br />
						Check this box if you agree to these conditions? <input type="checkbox" onchange="agreeToTerms()" />
					</article><br /><br />
				<?php
        }
        $d = ($artId != "new") ? "" : " disabled";
        echo "<input type='hidden' name='inReplyTo' value='$inReplyTo' /><input type='hidden' name='imageWidth' value='800' /><input type='hidden' name='imageHeight' value='800' /><input type='hidden' name='editedArticle' value='$artId' /><input type='submit' id='articleSubmit'$d value=' Save changes ' />";
        ?>

			</form>
		</div>
<?php
    } else {
        echo "You do not have permission to edit this article.";
    }
}
