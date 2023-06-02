<?php
session_start();

include "../globalFunctions.php";

$db = db_mll();
$db_ccdc = db_ccdc();

$time = time();

$stmtc = $db->prepare(
        "SELECT COUNT(*) FROM classifieds WHERE displayUntil >= ?");
$stmtc->execute(array(
        $time
));
$rowc = $stmtc->fetch();
$showClassifieds1 = ($rowc[0] >= 1) ? 1 : 0;

// *** Misc settings: 1 = show, 0 = don't show. ***
$showClassifieds = 1;
$showSurvey = 0;
$showCalendar = 1;
$showHolidays = 1;
$showSubmitNews = 1;
$showJobs = 1;
$showAdsTop = 1;
$showAdsSide = 1;
$showGames = 1;
$showDirectory = 1;
$showBlogs = 1;
$showCandidates = 0;
$useLocalization = 0;
$showFactoids = 1;
$showHelpingHands = 0;
$showWritings = 1;
$showSignUp = 1;

$domain = "mylocal.life";
$docRoot = htmlspecialchars(trim($_SERVER["DOCUMENT_ROOT"]));
$testingSite = ($docRoot == "/home/jachpa/public_html/padgett-online.com") ? '1' : '0';

$visitingIP = htmlspecialchars(trim($_SERVER["REMOTE_ADDR"]));

$SIurl = ($testingSite == '1') ? "http://padgett-online.com" : "https://mylocal.life";
$CST = $time - (6 * 60 * 60); // Setting the time zone to central standard time
                              // for the sake of the holidays display
$weekdays = array(
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
        "Sunday"
);
$months = array(
        1 => "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
);

// *** Log out ***
if (! empty($GET['logout']) &&
        htmlspecialchars(trim($_GET["logout"]), ENT_QUOTES) == 'yep') {
    destroySession();
    setcookie("staySignedIn", '', $time - 1209600, "/", "mylocal.life", 0);
}

// *** Sign in ***
$loginErr = "x";
if (filter_input(INPUT_POST, 'login', FILTER_SANITIZE_NUMBER_INT) == "1") {
    $email = (filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)) ? strtolower(
            filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)) : '0';
    $login1stmt = $db->prepare("SELECT id,salt FROM users WHERE email = ?");
    $login1stmt->execute(array(
            $email
    ));
    $login1row = $login1stmt->fetch();
    $salt = $login1row['salt'];
    $checkId = (isset($login1row['id']) && $login1row['id'] > 0) ? $login1row['id'] : '0';
    $lstmt1 = $db->prepare(
            "SELECT COUNT(*) FROM logInFails WHERE (userId = ? || ipAddress = ?) && tryTime >= ($time - 300)");
    $lstmt1->execute(array(
            $checkId,
            $visitingIP
    ));
    $lrow1 = $lstmt1->fetch();
    if ($lrow1[0] < 3) {
        $pwd = htmlspecialchars(trim($_POST["pwd"]));
        $hidepwd = hash('sha512', ($salt . $pwd), FALSE);
        $login2stmt = $db->prepare(
                "SELECT * FROM users WHERE email = ? AND password = ?");
        $login2stmt->execute(array(
                $email,
                $hidepwd
        ));
        $login2row = $login2stmt->fetch();
        if ($login2row['id']) {
            $_SESSION['myId'] = $login2row['id'];
            $_SESSION['myZip'] = $login2row['zip'];
            $_SESSION['myTheme'] = $login2row['theme'];
            setcookie("myTheme", $_SESSION['myTheme'], $time + 1209600, "/",
                    "mylocal.life", 0);
            setcookie("staySignedIn", $_SESSION['myId'], $time + 1209600, "/",
                    "mylocal.life", 0); // set for 14 days
            $lstmt2 = $db->prepare(
                    "DELETE FROM logInFails WHERE userId = ? || ipAddress = ?");
            $lstmt2->execute(array(
                    $checkId,
                    $visitingIP
            ));
        } else {
            $loginErr = "Your email / password combination isn't correct.";
            $lstmt3 = $db->prepare(
                    "INSERT INTO logInFails VALUES(NULL,?,?,?,'0','0','0')");
            $lstmt3->execute(array(
                    $checkId,
                    $visitingIP,
                    $time
            ));
        }
    } else {
        $loginErr = "You have tried to log in with the wrong email/password combination 3 times in the last few minutes. Please wait at least 5 mins before trying again.";
    }
}

// *** User settings ***
$myId = (isset($_SESSION['myId']) && ($_SESSION['myId'] >= '1')) ? $_SESSION['myId'] : '0'; // are
                                                                                            // they
                                                                                            // logged
                                                                                            // in
if ($myId == '0' && (empty($_GET["logout"]))) {
    $myId = (filter_input(INPUT_COOKIE, 'staySignedIn',
            FILTER_SANITIZE_NUMBER_INT) >= '1') ? filter_input(INPUT_COOKIE,
            'staySignedIn', FILTER_SANITIZE_NUMBER_INT) : '0'; // are they
                                                               // logged in
}

$theme = (! empty($_POST["theme"])) ? htmlspecialchars(trim($_POST["theme"])) : "";
$_SESSION['myTheme'] = $theme;
setcookie("myTheme", $_SESSION['myTheme'], $time + 1209600, "/", "mylocal.life",
        0);
if ($_SESSION['myTheme']) {
    $myTheme = $_SESSION['myTheme'];
} elseif (! empty($_COOKIE["myTheme"])) {
    $myTheme = htmlspecialchars(trim($_COOKIE["myTheme"]));
} else {
    $myTheme = "Default";
}

$getTheme = $db->prepare("SELECT * FROM themes WHERE themeName=?");
$getTheme->execute(array(
        $myTheme
));
$rowTheme = $getTheme->fetch();
if ($rowTheme) {
    $highlightColor = $rowTheme['highlightColor'];
    $fontColor = $rowTheme['fontColor'];
    $backgroundImage = $rowTheme['backgroundImage'];
    $bgColor = $rowTheme['bgColor'];
    $fontSizePercent = $rowTheme['fontSizePercent'];
} else {
    $highlightColor = "#bd4a11";
    $fontColor = "black";
    $backgroundImage = "";
    $bgColor = "#ffffff";
    $fontSizePercent = 100;
}

$myAccess = '0';
if ($myId != '0') {
    $mastmt = $db->prepare("SELECT accessLevel FROM users WHERE id=?");
    $mastmt->execute(array(
            $myId
    ));
    $marow = $mastmt->fetch();
    $myAccess = $marow['accessLevel'];
}

// *** Delete a user ***
if (filter_input(INPUT_POST, 'deleteMyId', FILTER_SANITIZE_NUMBER_INT) == $myId &&
        $myId != '0') {
    $stmt1 = $db->prepare("DELETE FROM classifieds WHERE userId = ?");
    $stmt1->execute(array(
            $myId
    ));
    $stmt2 = $db->prepare("DELETE FROM articles WHERE authorId = ?");
    $stmt2->execute(array(
            $myId
    ));
    $stmt3 = $db->prepare("DELETE FROM directory WHERE userId = ?");
    $stmt3->execute(array(
            $myId
    ));
    $stmt4 = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt4->execute(array(
            $myId
    ));
    $stmt5 = $db->prepare("DELETE FROM blog WHERE userId = ?");
    $stmt5->execute(array(
            $myId
    ));
    $stmt6 = $db->prepare("DELETE FROM blogDescriptions WHERE userId = ?");
    $stmt6->execute(array(
            $myId
    ));
    $stmt7 = $db->prepare("DELETE FROM blogFavorites WHERE myId = ?");
    $stmt7->execute(array(
            $myId
    ));
    $stmt8 = $db->prepare("DELETE FROM busiListing WHERE userId = ?");
    $stmt8->execute(array(
            $myId
    ));
    $stmt9 = $db->prepare("DELETE FROM scoreboard WHERE userId = ?");
    $stmt9->execute(array(
            $myId
    ));
    $stmt10 = $db->prepare("DELETE FROM strips WHERE userId = ?");
    $stmt10->execute(array(
            $myId
    ));
    $stmt11 = $db->prepare("SELECT id FROM surveyQuestions WHERE userId = ?");
    $stmt11->execute(array(
            $myId
    ));
    while ($row11 = $stmt11->fetch()) {
        $x = $row11['id'];
        $stmt13 = $db->prepare("DELETE FROM surveyAnswers WHERE surveyId = ?");
        $stmt13->execute(array(
                $x
        ));
    }
    $stmt12 = $db->prepare("DELETE FROM surveyQuestions WHERE userId = ?");
    $stmt12->execute(array(
            $myId
    ));
    delTree("userPics/$myId");
    $_SESSION = array();
    destroySession();
    $myId = '0';
    $myAccess = '0';
    $_SESSION['myId'] = '0';
    $_SESSION['myAccess'] = '0';
}

// *** Time zone ***
date_default_timezone_set('America/Chicago');

// *** Set default location ***
$defaultZip = '67756';
if (filter_input(INPUT_GET, 'myZip', FILTER_SANITIZE_NUMBER_INT)) {
    setcookie("myZip",
            filter_input(INPUT_GET, 'myZip', FILTER_SANITIZE_NUMBER_INT),
            $time + 5184000, "/", "mylocal.life", 0); // set for 60 days
} else {
    if ($myId != '0') {
        $stmt = $db->prepare("SELECT zip FROM users WHERE id=?");
        $stmt->execute(array(
                $myId
        ));
        $row = $stmt->fetch();
        setcookie("myZip", $row['zip'], $time + 5184000, "/", "mylocal.life", 0); // set
                                                                                  // for
                                                                                  // 60
                                                                                  // days
    }
}

$myZip = (filter_input(INPUT_COOKIE, 'myZip', FILTER_SANITIZE_NUMBER_INT)) ? filter_input(
        INPUT_COOKIE, 'myZip', FILTER_SANITIZE_NUMBER_INT) : $defaultZip;

if ($useLocalization == 0) {
    $myZip = 0;
}

// *** page settings ***
// $url_data = explode("::", $_GET['slug']);
// if (empty($url_data[0])) {
$page = (null !== $_GET["page"]) ? htmlspecialchars(trim($_GET["page"])) : "Home";
// } else {
// $page = $url_data[0];
// $slug = $url_data[1];
// }

if (! file_exists($page . ".php")) {
    $page = "Home";
}

if (! empty($_GET["articleCategory"])) {
    $articleCategory = htmlspecialchars(trim($_GET["articleCategory"]));
}
if (! empty($_GET["classifiedsCategory"])) {
    $classifiedsCategory = htmlspecialchars(trim($_GET["classifiedsCategory"]));
}

$getCompany = $db->prepare("SELECT * FROM company WHERE id='1'");
$getCompany->execute();
$getrow = $getCompany->fetch();
$contactEmail = $getrow['contactEmail'];
$myPaypalEmail = $getrow['myPaypalEmail'];
$metaKeywords = $getrow['metaKeywords'];
$metaDesc = $getrow['metaDesc'];
$version = $getrow['version'];

if (filter_input(INPUT_POST, 'feedback', FILTER_SANITIZE_NUMBER_INT) &&
        filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_INT) != NULL) {
    $feedback = filter_input(INPUT_POST, 'feedback', FILTER_SANITIZE_NUMBER_INT);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_INT);
    $feedText = htmlspecialchars(trim($_POST["feedText"]));
    $feed = $db->prepare(
            "INSERT INTO feedback VALUES(NULL, ?, ?, ?, '0',?,'0','0')");
    $feed->execute(array(
            $feedback,
            $rating,
            $feedText,
            $time
    ));
}

$errorMsg = "";

$tableCols = 3;
$directoryCols = 4;
$clsCols = 3;
