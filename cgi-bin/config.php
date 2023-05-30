<?php
$time = time();

$db = db();

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
$useLocalization = 0;
$showFactoids = 1;
$showWritings = 1;
$showSignUp = 1;

$visitingIP = htmlspecialchars(trim($_SERVER["REMOTE_ADDR"]));

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

// *** page settings ***
$url_data = explode("/", __FILE__);
$t = 0;
for ($i = 0; $i < count($url_data); $i ++) {
    if ($url_data[$i] == "index.php") {
        $t = $i;
        break;
    }
}
$page = (isset($url_data[$t + 1])) ? $url_data[$t + 1] : "Home";
$slug = (isset($url_data[$t + 2])) ? $url_data[$t + 2] : "";

if (! file_exists("pages/" . $page . ".php")) {
    $page = "Home";
}

// *** Log out ***
if ($slug == "logout") {
    logout();
}

// *** Sign in ***
if (filter_input(INPUT_POST, 'login', FILTER_SANITIZE_NUMBER_INT) == "1") {
    $email = (filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)) ? strtolower(
            filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)) : '0';
    $pwd = htmlspecialchars(trim($_POST["pwd"]));
    if (! login($email, $pwd, $visitingIP)) {
        $loginErr = "There was problem getting you logged in. If you have tried 3 times, please wait 5 minutes before trying again.";
    }
}

// *** User settings ***
$myId = (isset($_SESSION['myId']) && ($_SESSION['myId'] >= '1')) ? $_SESSION['myId'] : '0';
if ($myId == '0' && (empty($_GET["logout"]))) {
    $myId = (filter_input(INPUT_COOKIE, 'staySignedIn',
            FILTER_SANITIZE_NUMBER_INT) >= '1') ? filter_input(INPUT_COOKIE,
            'staySignedIn', FILTER_SANITIZE_NUMBER_INT) : '0';
}

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

// *** Delete a user ***
if (filter_input(INPUT_POST, 'deleteMyId', FILTER_SANITIZE_NUMBER_INT) == $myId &&
        $myId != '0') {
    deleteUser($myId);
}

// *** Time zone ***
date_default_timezone_set('America/Chicago');

// *** Set default location ***
$myZip = '67756';
if (filter_input(INPUT_GET, 'myZip', FILTER_SANITIZE_NUMBER_INT)) {
    $_SESSION['myZip'] = filter_input(INPUT_GET, 'myZip',
            FILTER_SANITIZE_NUMBER_INT);
}
if ($myId != '0') {
    $zipstmt = $db->prepare("SELECT zip FROM users WHERE id=?");
    $zipstmt->execute(array(
            $myId
    ));
    $ziprow = $zipstmt->fetch();
    if ($ziprow) {
        $myZip = $ziprow['zip'];
    }
}
if (isset($_SESSION['myZip'])) {
    $myZip = $_SESSION['myZip'];
}

if ($useLocalization == 0) {
    $myZip = 0;
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