<?php
session_start();

function db ()
{
    $dbhost = 'localhost';
    $dbname = 'mll_db';
    $dbuser = 'mll_user';
    $dbpass = 'mLl_pWd';

    $db = new PDO("mysql:host=$dbhost; dbname=$dbname", "$dbuser", "$dbpass");

    return $db;
}

function db_ccdc ()
{
    $ccdchost = 'localhost';
    $ccdcname = 'ccdcks_site';
    $ccdcuser = 'ccdcks_users';
    $ccdcpass = 'users321';

    $db_ccdc = new PDO("mysql:host=$ccdchost; dbname=$ccdcname", "$ccdcuser",
            "$ccdcpass");

    return $db_ccdc;
}

// *** logout ***
function logout ()
{
    setcookie("staySignedIn", '', $time - 1209600, "/", "mylocal.life", 0);
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"],
                $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
}

// *** Sign in ***
function login ($email, $pwd, $visitingIP)
{
    $time = time();
    $L1 = db()->prepare(
            "SELECT id,salt FROM users WHERE email = ? AND accessLevel >= '1'");
    $L1->execute(array(
            $email
    ));
    $L1R = $L1->fetch();
    if ($L1R) {
        $id = $L1R['id'];
        $salt = $L1R['salt'];
    } else {
        return false;
    }
    $checkId = (isset($id) && $id > 0) ? $id : '0';
    $L2 = db()->prepare(
            "SELECT COUNT(*) FROM logInFails WHERE (userId = ? || ipAddress = ?) && tryTime >= ($time - 300)");
    $L2->execute(array(
            $checkId,
            $visitingIP
    ));
    $L2R = $L2->fetch();
    if ($L2R[0] < 3) {
        $pwd = htmlspecialchars(trim($pwd));
        $hidepwd = hash('sha512', ($salt . $pwd), FALSE);
        $L3 = db()->prepare(
                "SELECT * FROM users WHERE email = ? AND password = ?");
        $L3->execute(array(
                $email,
                $hidepwd
        ));
        $L3R = $L3->fetch();
        if ($L3R) {
            $_SESSION['myId'] = $L3R['id'];
            $_SESSION['myTheme'] = $L3R['theme'];
            setcookie("myTheme", $_SESSION['myTheme'], $time + 1209600, "/",
                    "mylocal.life", 0);
            setcookie("staySignedIn", $_SESSION['myId'], $time + 1209600, "/",
                    "mylocal.life", 0); // set for 14 days
            $L4 = db()->prepare(
                    "DELETE FROM logInFails WHERE userId = ? || ipAddress = ?");
            $L4->execute(array(
                    $checkId,
                    $visitingIP
            ));
        } else {
            $L5 = db()->prepare(
                    "INSERT INTO logInFails VALUES(NULL,?,?,?,'0','0','0')");
            $L5->execute(array(
                    $checkId,
                    $visitingIP,
                    $time
            ));
        }
        return true;
    }
    return false;
}

function isAdmin ($myId)
{
    $mastmt = db()->prepare("SELECT accessLevel FROM users WHERE id=?");
    $mastmt->execute(array(
            $myId
    ));
    $marow = $mastmt->fetch();
    if ($marow && $marow[0] == 3)
        return true;
    return false;
}

function titleText ($myId, $highlightColor)
{
    if ($myId != '0') {
        $stmt = db()->prepare("SELECT firstName FROM users WHERE id=?");
        $stmt->execute(array(
                $myId
        ));
        $row = $stmt->fetch();
        if ($row) {
            $hName = $row['firstName'] . "'s";
        } else {
            $hName = "my";
        }
    } else {
        $hName = "my";
    }
    echo "<header style='width:100%;'><div id='titleBar' style='text-transform:lowercase; text-align:center; font-size:6em; font-weight:300; text-shadow: 20px 20px 50px black; padding:20px 0px 50px 0px;'><span style='color:$highlightColor;'>$hName</span><span style='color:black;'>local</span><span style='color:$highlightColor;'>life</span></div></header>";
}

function delTree ($dir)
{
    $files = array_diff(scandir($dir), array(
            '.',
            '..'
    ));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

function deleteUser ($myId)
{
    $stmt1 = db()->prepare("DELETE FROM classifieds WHERE userId = ?");
    $stmt1->execute(array(
            $myId
    ));
    $stmt2 = db()->prepare("DELETE FROM articles WHERE authorId = ?");
    $stmt2->execute(array(
            $myId
    ));
    $stmt3 = db()->prepare("DELETE FROM directory WHERE userId = ?");
    $stmt3->execute(array(
            $myId
    ));
    $stmt4 = db()->prepare("DELETE FROM users WHERE id = ?");
    $stmt4->execute(array(
            $myId
    ));
    $stmt5 = db()->prepare("DELETE FROM blog WHERE userId = ?");
    $stmt5->execute(array(
            $myId
    ));
    $stmt6 = db()->prepare("DELETE FROM blogDescriptions WHERE userId = ?");
    $stmt6->execute(array(
            $myId
    ));
    $stmt7 = db()->prepare("DELETE FROM blogFavorites WHERE myId = ?");
    $stmt7->execute(array(
            $myId
    ));
    $stmt8 = db()->prepare("DELETE FROM busiListing WHERE userId = ?");
    $stmt8->execute(array(
            $myId
    ));
    $stmt9 = db()->prepare("DELETE FROM scoreboard WHERE userId = ?");
    $stmt9->execute(array(
            $myId
    ));
    $stmt10 = db()->prepare("DELETE FROM strips WHERE userId = ?");
    $stmt10->execute(array(
            $myId
    ));
    $stmt11 = db()->prepare("SELECT id FROM surveyQuestions WHERE userId = ?");
    $stmt11->execute(array(
            $myId
    ));
    while ($row11 = $stmt11->fetch()) {
        $x = $row11['id'];
        $stmt13 = db()->prepare("DELETE FROM surveyAnswers WHERE surveyId = ?");
        $stmt13->execute(array(
                $x
        ));
    }
    $stmt12 = db()->prepare("DELETE FROM surveyQuestions WHERE userId = ?");
    $stmt12->execute(array(
            $myId
    ));
    delTree("userPics/$myId");
    logout();
}

function getPicType ($imageType)
{
    switch ($imageType) {
        case "image/gif":
            $picExt = "gif";
            break;
        case "image/jpeg":
            $picExt = "jpg";
            break;
        case "image/pjpeg":
            $picExt = "jpg";
            break;
        case "image/png":
            $picExt = "png";
            break;
        default:
            $picExt = "xxx";
            break;
    }
    return $picExt;
}

function processPic ($userId, $imageName, $imageWidth, $imageHeight, $tmpFile,
        $picExt)
{
    $folder = "userPics/$userId";
    if (! is_dir("$folder")) {
        mkdir("$folder", 0777, true);
    }

    $saveto = "$folder/$imageName.$picExt";

    list ($width, $height) = (getimagesize($tmpFile) != null) ? getimagesize(
            $tmpFile) : null;
    if ($width != null && $height != null) {
        $image = new Imagick($tmpFile);
        $image->thumbnailImage($imageWidth, $imageHeight, true);
        $image->writeImage($saveto);
    }
}

function processThumbPic ($userId, $imageName, $imageWidth, $imageHeight,
        $tmpFile, $picExt)
{
    $folder = "userPics/$userId/thumb";
    if (! is_dir("$folder")) {
        mkdir("$folder", 0777, true);
    }

    $saveto = "$folder/$imageName.$picExt";

    list ($width, $height) = (getimagesize($tmpFile) != null) ? getimagesize(
            $tmpFile) : null;
    if ($width != null && $height != null) {
        $image = new Imagick($tmpFile);
        $image->thumbnailImage(150, 150, true);
        $image->writeImage($saveto);
    }
}

function processPdf ($userId, $time, $pdf1or2, $file, $artId, $db)
{
    $pdfName = $time + $pdf1or2;
    $saveto = "userPics/$userId/$pdfName.pdf";
    move_uploaded_file($file, $saveto);
    if (filesize("../userPics/$userId/$pdfName.pdf") <= 1000) {
        unlink("../userPics/$userId/$pdfName.pdf");
    }
    if (file_exists("userPics/$userId/$pdfName.pdf")) {
        $pdfstmt = $db->prepare(
                "UPDATE articles SET pdf" . $pdf1or2 . "=? WHERE id=?");
        $pdfstmt->execute(array(
                $pdfName,
                $artId
        ));
    }
}

function deletePdf ($userId, $pdf1or2, $artId, $db)
{
    $stmt = $db->prepare("SELECT pdf" . $pdf1or2 . " FROM articles WHERE id=?");
    $stmt->execute(array(
            $artId
    ));
    $row = $stmt->fetch();
    if (file_exists("userPics/$userId/" . $row[0] . ".pdf")) {
        unlink("userPics/$userId/" . $row[0] . ".pdf");
    }
    $stmt2 = $db->prepare(
            "UPDATE articles SET pdf" . $pdf1or2 . "='0' WHERE id=?");
    $stmt2->execute(array(
            $artId
    ));
}

class functions
{

    var $lat;

    var $lng;

    function LatLong ($lat, $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    function distance ($to)
    {
        $er = 6366.707;

        $latFrom = deg2rad($this->lat);
        $latTo = deg2rad($to->lat);
        $lngFrom = deg2rad($this->lng);
        $lngTo = deg2rad($to->lng);

        $x1 = $er * cos($lngFrom) * sin($latFrom);
        $y1 = $er * sin($lngFrom) * sin($latFrom);
        $z1 = $er * cos($latFrom);

        $x2 = $er * cos($lngTo) * sin($latTo);
        $y2 = $er * sin($lngTo) * sin($latTo);
        $z2 = $er * cos($latTo);

        $d = acos(
                sin($latFrom) * sin($latTo) +
                cos($latFrom) * cos($latTo) * cos($lngTo - $lngFrom)) * $er;
        return $d;
    }
}

function getZipAreas ($myZip)
{
    $zipcode1 = db()->prepare(
            "SELECT longitude,latitude FROM zipCodes WHERE zipCode=?");
    $zipcode1->execute(array(
            $myZip
    ));
    $ziprow1 = $zipcode1->fetch();
    $lat1 = $ziprow1['latitude'];
    $lon1 = $ziprow1['longitude'];

    $lld1 = new functions($lat1, $lon1);

    $getZipCodes1 = array();
    $getZipCodes2 = array();
    $getZipCodes3 = array();

    $zipcode2 = db()->prepare("SELECT zipCode,longitude,latitude FROM zipCodes");
    $zipcode2->execute(array(
            $myZip
    ));
    while ($ziprow2 = $zipcode2->fetch()) {
        $zipC = $ziprow2['zipCode'];
        $lat2 = $ziprow2['latitude'];
        $lon2 = $ziprow2['longitude'];
        $lld2 = new functions($lat2, $lon2);
        $d = $lld1->distance($lld2);
        if ($d >= 0 && $d <= 161) { // 0 to 100 miles
            $getZipCodes1[] = $zipC;
        } elseif ($d > 161 && $d <= 403) { // 100 to 250 miles
            $getZipCodes2[] = $zipC;
        } elseif ($d > 403) {
            $getZipCodes3[] = $zipC; // over 250 miles
        }
    }
    return array(
            $getZipCodes1,
            $getZipCodes2,
            $getZipCodes3
    );
}

function make_links_clickable ($text, $highlightColor)
{
    return preg_replace(
            '!(((f|ht)tp(s)?://)[-a-zA-ZÐ°-Ñ�Ð�-Ð¯()0-9@:%_+.~#?&;//=]+)!i',
            "<a href='$1' target='_blank' style='color:$highlightColor; text-decoration:underline;'>$1</a>",
            $text);
}

function money ($amt)
{
    settype($amt, "float");
    $fmt = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    return $fmt->formatCurrency($amt, "USD");
}

function slugify ($urlString)
{
    $url = html_entity_decode($urlString, ENT_QUOTES);
    $search = [
            'Ș',
            'Ț',
            'ş',
            'ţ',
            'Ş',
            'Ţ',
            'ș',
            'ț',
            'î',
            'â',
            'ă',
            'Î',
            ' ',
            'Ă',
            'ë',
            'Ë'
    ];
    $replace = [
            's',
            't',
            's',
            't',
            's',
            't',
            's',
            't',
            'i',
            'a',
            'a',
            'i',
            ' ',
            'a',
            'e',
            'e'
    ];
    $str = str_ireplace($search, $replace, strtolower(trim($url)));
    $str = preg_replace('/[^\w\d\-\ ]/', '', $str);
    $str = str_replace(' ', '-', $str);
    return preg_replace('/\-{2,}/', '-', $str);
}

function displayArticle ($getId, $highlightColor)
{
    $stmt = $db->prepare(
            "SELECT articleTitle,articleText,pic1Name,pic1Ext,authorId,youtube,slug FROM articles WHERE id = ?");
    $stmt->execute(array(
            $getId
    ));
    $row = $stmt->fetch();
    $articleTitle = $row['articleTitle'];
    $a1 = html_entity_decode($row['articleText'], ENT_QUOTES);
    $a2 = htmlspecialchars(trim($a1));
    $articleText = nl2br(substr($a2, 0, 500));
    $pic1Name = $row['pic1Name'];
    $pic1Ext = $row['pic1Ext'];
    $authorId = $row['authorId'];
    $yt = $row['youtube'];
    $slug = $row['slug'];
    // echo "<article class='pageBoxesHalf' style='float:left; overflow:hidden;
    // margin:10px; padding:20px; font-size:0.75em; text-align:center;
    // border:0px solid $highlightColor; width:370px; height:150px; box-shadow:
    // 5px 5px 5px grey;'>\n<a style='color:black;' href='/Articles::$slug'>";
    echo "<article class='pageBoxesHalf' style='float:left; overflow:hidden; margin:10px; padding:20px; font-size:0.75em; text-align:center; border:0px solid $highlightColor; width:370px; height:150px; box-shadow: 5px 5px 5px grey;'>\n<a style='color:black;' href='index.php?page=Articles&artId=$getId'>";
    echo "<header style='font-weight:bold; text-align:center; margin-bottom:10px; font-size:1.25em;'>$articleTitle</header>\n";
    if ($yt != '0') {
        echo "<div style='margin:auto; width:150px; float:left;'><img src='images/video.png' alt='' style='width:150px;' /></div>";
    }
    if (file_exists("userPics/$authorId/thumb/$pic1Name.$pic1Ext")) {
        echo "<img src='userPics/$authorId/thumb/$pic1Name.$pic1Ext' alt='' style='margin:0px 10px 10px 0px; float:left;' />";
    }
    echo "<article style='text-align:justify;'>$articleText</article></a></article>\n";
}