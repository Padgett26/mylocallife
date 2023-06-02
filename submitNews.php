<?php
$submitted = 0;

if (filter_input(INPUT_POST, 'artUpTime', FILTER_SANITIZE_NUMBER_INT)) {
    $artTime = filter_input(INPUT_POST, 'artUpTime', FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = (filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) ? filter_input(
            INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) : "x";
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $a2 = htmlEntities(trim($_POST['content']), ENT_QUOTES);
    $con = filter_var($a2, FILTER_SANITIZE_STRING);

    if ($email != "x") {
        $content = $name . "<br />" . $email . "<br /><br />" . $con;

        $newartstmt = $db->prepare(
                "INSERT INTO articles VALUES" .
                "(NULL, 'title', 'text', '0', 'jpg', NULL, '0', 'jpg', NULL, ?, 'local', '0', '0', NULL, '0', '0', '0', NULL, '0', NULL, '0', '', '0', '0', '0', '0')");
        $newartstmt->execute(array(
                $artTime
        ));
        $getidstmt = $db->prepare(
                "SELECT id FROM articles WHERE postedDate=? ORDER BY id DESC LIMIT 1");
        $getidstmt->execute(array(
                $artTime
        ));
        $getidrow = $getidstmt->fetch();
        $artId = $getidrow['id'];
        $newartstmt2 = $db->prepare(
                "INSERT INTO reported VALUES" .
                "(NULL, '0', ?, '0', ?, '0', '', '0', '0')");
        $newartstmt2->execute(array(
                $artId,
                $time
        ));
        $artstmt = $db->prepare(
                "UPDATE articles SET articleTitle=?, articleText=? WHERE id=?");
        $artstmt->execute(array(
                $title,
                $content,
                $artId
        ));

        $image1 = $_FILES["image1"]["tmp_name"];
        list ($width1, $height1) = (getimagesize($image1) != null) ? getimagesize(
                $image1) : null;
        if ($width1 != null && $height1 != null) {
            $image1Type = getPicType($_FILES["image1"]['type']);
            $image1Name = $artTime . "." . $image1Type;
            processPic("$domain/userPics/$myId", $image1Name, $image1, 800, 150);
            $p1stmt = $db->prepare(
                    "UPDATE articles SET pic1Name=?, pic1Ext=? WHERE id=?");
            $p1stmt->execute(array(
                    $artTime,
                    $image1Type,
                    $artId
            ));
        }
        $image2 = $_FILES["image2"]["tmp_name"];
        list ($width2, $height2) = (getimagesize($image2) != null) ? getimagesize(
                $image2) : null;
        if ($width2 != null && $height2 != null) {
            $image2Type = getPicType($_FILES["image2"]['type']);
            $image2Name = ($artTime + 1) . "." . $image2Type;
            processPic("$domain/userPics/$myId", $image2Name, $image2, 800, 150);
            $p2stmt = $db->prepare(
                    "UPDATE articles SET pic2Name=?, pic2Ext=? WHERE id=?");
            $p2stmt->execute(array(
                    ($artTime + 1),
                    $image2Type,
                    $artId
            ));
        }

        $submitted = 1;
    }
}
?>

<div id='mainTableBox' style='padding:40px 0px;'>
    <header style='text-align:center; font-weight:bold; font-size:2em;'>Submit a news story or idea</header>
    <?php
    if ($submitted == 1) {
        ?>
        <div style="text-align:center; margin:20px 0px; font-weight:bold; font-size:1.25em; color:<?php

        echo $highlightColor;
        ?>;">
            Your news submission has been received.<br /><span style="font-size:1.5em;">Thank you</span><br />If we have any questions, or if we need any facts verified, we will contact you through the email address you provided.
        </div>
        <?php
    }
    ?>
    <div style="text-align:center; margin:20px 0px;">
        Please complete this form with your article, or story idea. All fields marked with an (*), are required.<br />Thank you for your input
    </div>
    <?php
    $tdStyle1 = "width:300px; height:40px;";
    $tdStyle2 = "width:500px; height:40px;";
    ?>
    <form action="index.php?page=submitNews" method="post" enctype='multipart/form-data'>
        <table cellpadding="0px" cellspacing="0px" border="0px">
            <tr>
                <td style="<?php

                echo $tdStyle1;
                ?>">
                    Name (*)
                </td>
                <td style="<?php

                echo $tdStyle2;
                ?>">
                    <input type="text" name="name" value="" placeholder="Name" size="60" required />
                </td>
            </tr>
            <tr>
                <td style="<?php

                echo $tdStyle1;
                ?>">
                    Email (*)
                </td>
                <td style="<?php

                echo $tdStyle2;
                ?>">
                    <input type="email" name="email" value="" placeholder="Email" size="60" required />
                </td>
            </tr>
            <tr>
                <td style="<?php

                echo $tdStyle1;
                ?>">
                    Title / Subject
                </td>
                <td style="<?php

                echo $tdStyle2;
                ?>">
                    <input type="text" name="title" value="" placeholder="Either the title of your article, or the subject matter" size="60" />
                </td>
            </tr>
            <tr>
                <td style="<?php

                echo $tdStyle1;
                ?>">
                    Upload an image #1 (main)
                </td>
                <td style="<?php

                echo $tdStyle2;
                ?>">
                    <input type="file" name="image1" />
                </td>
            </tr>
            <tr>
                <td style="<?php

                echo $tdStyle1;
                ?>">
                    Upload an image #2 (secondary)
                </td>
                <td style="<?php

                echo $tdStyle2;
                ?>">
                    <input type="file" name="image2" />
                </td>
            </tr>
            <tr>
                <td style="<?php

                echo $tdStyle1;
                ?>">
                    Content (*)
                </td>
                <td style="<?php

                echo $tdStyle2;
                ?>">

                </td>
            </tr>
            <tr>
                <td style="" colspan="2">
                    <textarea name="content" cols="90" rows="15" required ></textarea>
                </td>
            </tr>
            <tr>
                <td style="" colspan="2">
                    <header style="font-weight:bold; text-align:center;">Terms and Conditions</header>
                    <article style="text-align:justify;">
                        Initially, your article will be reviewed by an admin of this website.  The admin will decide to either approve the article for publication on mylocal.life, or decide to delete the article due to being inappropriate for mylocal.life.<br /><br />
                        My Local Life claims no rights to any data you enter into the site, other than the right to display such data on the mylocal.life website, and delete the data if it is found to be inappropriate for the website during the initial review of the article, or if it is reported and found to be inappropriate for the website at a later time. Users of the site have the right to view such data, and with each article users have the right and ability to share your article on social media.<br /><br />
                        If there is intent to republish, share, redistribute any content posted on this site, whether it text, any type of media, or any information in any form, that you did not create and submit yourself, then My Local Life and the specific web page the material originates from, must be cited in the republication or redistribution.<br /><br />
                        Registered users have the ability to report your data for whatever reason.  If an article is reported, it will be hidden from public display on mylocal.life.  A notification of the reported article will go to one of the admins of this site, and the article will be reviewed.  The admin has the right to, and will decide if the article will return to a visible state on mylocal.life, or if it will be deleted from the site.  If your article is deleted, the assigned admin will attempt to notify you of the deletion, but mylocal.life is not accountable if the notification does not reach you for any reason.<br /><br />
                        By putting any data on this website, you are stating that you have the right to publish that data to this site.  You either own rights to any collection of words, or digital media, because you are the creator, or you have the permission from the creator of those collections of words or digital media.  If you have a quote from another author, it must be cited.  If a collection of words is found to originate from another author and is not cited, it will be considered plagiarism, and you as the submitter of the article will be responsible to fix the problem, or the article will be deleted.<br /><br />
                        Check this box if you agree to these conditions? <input type="checkbox" onchange="agreeToTerms()" />
                    </article><br /><br />
                </td>
            </tr>
            <tr>
                <td style="<?php

                echo $tdStyle1;
                ?>">
                    <input type="hidden" name="artUpTime" value="<?php

                    echo $time;
                    ?>" />
                    <input type='submit' id='articleSubmit' disabled value=' Save changes ' />
                </td>
                <td style="<?php

                echo $tdStyle2;
                ?>">

                </td>
            </tr>
        </table>
    </form>
</div>
