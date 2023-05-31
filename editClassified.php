<?php
if (filter_input(INPUT_GET, 'clsId', FILTER_SANITIZE_STRING)) {
    $clsId = filter_input(INPUT_GET, 'clsId', FILTER_SANITIZE_STRING);
}

if (filter_input(INPUT_POST, 'startClassified', FILTER_SANITIZE_NUMBER_INT) == '1') {
    $clsTitle = trim(filter_input(INPUT_POST, 'classifiedTitle', FILTER_SANITIZE_STRING));
    $clsnew = $db->prepare("INSERT INTO classifieds VALUES(NULL, ?, ?, '', '0', '250', '0', '0', '0', '0', '0', '0', '0')");
    $clsnew->execute(array($myId, $clsTitle));
    $clsget = $db->prepare("SELECT id FROM classifieds WHERE userId=? && classifiedTitle=? ORDER BY id DESC LIMIT 1");
    $clsget->execute(array($myId, $clsTitle));
    $clsgetrow = $clsget->fetch();
    $clsId = $clsgetrow['id'];
}

if ($clsId == 'pp') {
    $getpp = $db->prepare("SELECT id FROM classifieds WHERE active='1' && userId=?");
    $getpp->execute(array($myId));
    $grow = $getpp->fetch();
    $clsId = $grow['id'];
}

$act1 = $db->prepare("UPDATE classifieds SET active='0' WHERE userId=?");
$act1->execute(array($myId));
$act2 = $db->prepare("UPDATE classifieds SET active='1' WHERE id=?");
$act2->execute(array($clsId));

$stmt = $db->prepare("SELECT * FROM classifieds WHERE id=? && userId=?");
$stmt->execute(array($clsId, $myId));
$row = $stmt->fetch();
$classifiedTitle = $row['classifiedTitle'];
$classifiedText = html_entity_decode($row['classifiedText'], ENT_QUOTES);
$chrlen = mb_strlen($classifiedText);
$displayUntil = $row['displayUntil'];
$classifiedTextLength = $row['classifiedTextLength'];
$catId = $row['catId'];
$userId = $row['userId'];
$picName = $row['picName'];
$picExt = $row['picExt'];

if ($myId != '0' && $clsId == "new") {
    echo "<form action='index.php?page=editClassified' method='post'>";
    echo "Classified title:<br /><input type='text' name='classifiedTitle' value='' size='50' max-length='80' /><br /><br />";
    echo "<input type='hidden' name='startClassified' value='1' /><input type='submit' value=' Create Classified ' /></form>";
} else {
    if ($userId == $myId) {
        ?>
        <div id='mainTableBox' style="padding:10px;">
            <?php
            if ($classifiedTextLength == '250') {
                ?>
                The first 250 characters in your classified is free.<br />If you need more space, it can be purchased below.<br />Also, with the purchase of more classified space, you are able to include a picture.<br /><br />
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="hosted_button_id" value="62TSCABCZ5LX4">
                    <table>
                        <tr>
                            <td><input type="hidden" name="on0" value="Character Length">Character Length</td>
                        </tr>
                        <tr>
                            <td>
                                <select name="os0">
                                    <option value="500 characters">500 characters $10.00 USD</option>
                                    <option value="1000 characters">1000 characters $20.00 USD</option>
                                    <option value="2000 characters">2000 characters $40.00 USD</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="hidden" name="custom" value="<?php echo $myId . "," . $clsId; ?>,0,0,0"/>
                    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                </form><br /><br />
            <?php } ?>
            If you have purchased extra character length for your classified, but it is not showing up yet, please give it a little time and check this classified again. The information from paypal may just be delayed.<br /><br />
            <form action="index.php?page=myAccount" method="post" enctype='multipart/form-data'>
                <?php
                if ($displayUntil > 1) {
                    echo "Previous 'visible until' date: " . date("M j, Y", $displayUntil) . "<br /><br />";
                }
                ?>
                Classified title:<br />
                <input type="text" name="classifiedTitle" value="<?php echo $classifiedTitle; ?>" size="50" max-length="80" /><br /><br />
                Category: <select name="catId" size="1">
                    <?php
                    $substmt = $db->prepare("SELECT id, category FROM classifiedCategories");
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
                </select><br /><br />
                <?php
                if ($classifiedTextLength >= 251) {
                    if (file_exists("userPics/$myId/$picName.$picExt")) {
                        echo "<img src='userPics/$myId/$picName.$picExt' alt='' style='max-width:250px; max-height:250px; border:1px solid $highlightColor; padding:2px;' /><br /><br />";
                    }
                    echo "Upload a pic for this classifed: <input type='file' name='image' /><br /><br />";
                }
                ?>
                Article text:<br />
                <textarea id='clsText' name="classifiedText" cols="50" rows="6" maxlength="<?php echo $classifiedTextLength; ?>" oninput="chrCount('<?php echo $classifiedTextLength; ?>')"><?php echo $classifiedText; ?></textarea><br />
                <div id='showclsCount' style='position:relative; left:50px; color:#222;'><?php echo $chrlen . "/" . $classifiedTextLength; ?></div><br />
                Do you want this classified visible on the site for the next two weeks?<br />
                YES, <input type="radio" name="activateClassified" value="1" checked /> I want to post this classified for the next two weeks.<br />
                NO, <input type="radio" name="activateClassified" value="0" /> I do not want this classified visible for now.<br /><br />
                Do you want to delete this classified?  This is permanent and cannot be undone.<br />
                YES, <input type="checkbox" name="delClassified" value="1" /> delete this classified.<br /><br />
                <input type="hidden" name="editedClassified" value="<?php echo $clsId; ?>" /><input type="submit" value=" Save changes " />
            </form>
        </div>
        <?php
    } else {
        echo "You do not have permission to edit this article.";
    }
}