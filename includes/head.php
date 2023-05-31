<!-- Beginning of Head -->
<link rel="shortcut icon" href="images/icon.png" />
<meta http-equiv='Content-Type'     content='text/html; charset=UTF-8' />
<meta name="viewport"               content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1" />
<meta name="keywords"               content="<?php
echo $metaKeywords;
?>" />
<meta name="description"            content="<?php
echo $metaDesc;
?>" />
<meta property="fb:app_id"          content="539048382922093" />
<meta property="og:site_name"       content="My Local Life" />
<meta property="og:type"            content="article" />
<link href="includes/lightbox2/css/lightbox.css" rel="stylesheet" />
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TBZJ9FM');</script>
<!-- End Google Tag Manager -->
<?php
if ($page === "Articles" && filter_input ( INPUT_GET, 'articleDetail', FILTER_SANITIZE_NUMBER_INT ) >= 1) {
	$article = filter_input ( INPUT_GET, 'articleDetail', FILTER_SANITIZE_NUMBER_INT );
	$stmt = $db->prepare ( "SELECT articleTitle, articleText, pic1Name, pic1Ext, authorId, catId, youtube FROM articles WHERE id=?" );
	$stmt->execute ( array (
			$article
	) );
	$row = $stmt->fetch ();
	if ($row) {
		$arttitle = $row ['articleTitle'];
		$artcontent = nl2br ( html_entity_decode ( $row ['articleText'], ENT_QUOTES ), $highlightColor );
		$posContent = strripos ( $artcontent, "<br />" );
		$ac = substr ( $artcontent, 0, $posContent - 8 );
		$artpic1Name = $row ['pic1Name'];
		$artpic1Ext = $row ['pic1Ext'];
		$authorId = $row ['authorId'];
		$cat = $row ['catId'];
		$yout = $row ['youtube'];
	}
	$s = $db->prepare ( "SELECT category FROM articleCategories WHERE id=?" );
	$s->execute ( array (
			$cat
	) );
	$r = $s->fetch ();
	$artCat = $r ['category'];

	$headTitle = "<title>My Local Life || $artCat || $arttitle</title>";
	?>
    <meta property="og:url"         content="https://mylocal.life/index.php?page=Articles&articleDetail=<?php

	echo $article;
	?>">
    <meta property="og:title"       content="<?php

	echo $arttitle;
	?>">

    <?php
	if (strlen ( $ac ) >= 250) {
		$subContent1 = substr ( $ac, 0, 249 );
		$posContent1 = strripos ( $subContent1, " " );
		$acontent = substr ( $ac, 0, $posContent1 );
	} else {
		$acontent = $ac;
	}

	if (file_exists ( "userPics/$authorId/$artpic1Name.$artpic1Ext" )) {
		list ( $widthm, $heightm ) = (getimagesize ( "userPics/$authorId/$artpic1Name.$artpic1Ext" ) != null) ? getimagesize ( "userPics/$authorId/$artpic1Name.$artpic1Ext" ) : null;
		echo "<meta property='og:image'       content='https://mylocal.life/userPics/$authorId/$artpic1Name.$artpic1Ext'>\n";
		echo "<meta property='og:image:width'       content='$widthm'>\n";
		echo "<meta property='og:image:height'       content='$heightm'>\n";
	} elseif ($yout != '0') {
		echo "<meta property='og:video'       content='https://youtu.be/$yout'>\n";
		echo "<meta property='og:video:secure_url'       content='https://youtu.be/$yout'>\n";
		echo "<meta property='og:video:type'       content='application/x-shockwave-flash'>\n";
		echo "<meta property='og:video:width'       content='560'>\n";
		echo "<meta property='og:video:height'       content='315'>\n";
		echo "<meta property='og:image'       content='https://i.ytimg.com/vi/$yout/hqdefault.jpg'>\n";
	} else {
		echo "<meta property='og:image'       content='https://mylocal.life/images/logo.png'>\n";
		echo "<meta property='og:image:width'       content='256'>\n";
		echo "<meta property='og:image:height'       content='256'>\n";
	}
	?>
    <meta property="og:description" content="<?php

	echo $acontent;
	?>">

    <?php
} elseif ($page === "Photo" && filter_input ( INPUT_GET, 'photoShow', FILTER_SANITIZE_NUMBER_INT )) {
	$pId = filter_input ( INPUT_GET, 'photoShow', FILTER_SANITIZE_NUMBER_INT );
	$stmt = $db->prepare ( "SELECT photoTitle, photoText, authorId FROM photoJournalism WHERE id=?" );
	$stmt->execute ( array (
			$pId
	) );
	$row = $stmt->fetch ();
	if ($row) {
		$photoTitle = $row ['photoTitle'];
		$photoText = nl2br ( html_entity_decode ( $row ['photoText'], ENT_QUOTES ), $highlightColor );
		$posContent = strripos ( $photoText, "<br />" );
		$ac = substr ( $photoText, 0, $posContent - 8 );
		$authorId = $row ['authorId'];
	}
	$stmt2 = $db->prepare ( "SELECT photoName, photoExt FROM photoList WHERE photoId=? ORDER BY photoOrder LIMIT 1" );
	$stmt2->execute ( array (
			$pId
	) );
	$row2 = $stmt2->fetch ();
	if ($row2) {
		$photoName = $row2 ['photoName'];
		$photoExt = $row2 ['photoExt'];
	}

	$headTitle = "<title>My Local Life || PhotoShow || $photoTitle</title>";
	?>
    <meta property="og:url"         content="https://mylocal.life/index.php?page=Photo&photoShow=<?php

	echo $pId;
	?>">
    <meta property="og:title"       content="<?php

	echo $photoTitle;
	?>">
    <?php
	if (strlen ( $ac ) >= 250) {
		$subContent1 = substr ( $ac, 0, 249 );
		$posContent1 = strripos ( $subContent1, " " );
		$acontent = substr ( $ac, 0, $posContent1 );
	} else {
		$acontent = $ac;
	}

	if (file_exists ( "userPics/$authorId/$photoName.$photoExt" )) {
		list ( $widthm, $heightm ) = (getimagesize ( "userPics/$authorId/$photoName.$photoExt" ) != null) ? getimagesize ( "userPics/$authorId/$photoName.$photoExt" ) : null;
		echo "<meta property='og:image'       content='https://mylocal.life/userPics/$authorId/$photoName.$photoExt'>\n";
		echo "<meta property='og:image:width'       content='$widthm'>\n";
		echo "<meta property='og:image:height'       content='$heightm'>\n";
	} else {
		echo "<meta property='og:image'       content='https://mylocal.life/images/logo.png'>\n";
		echo "<meta property='og:image:width'       content='256'>\n";
		echo "<meta property='og:image:height'       content='256'>\n";
	}
	?>
    <meta property="og:description" content="<?php

	echo $acontent;
	?>">

    <?php
} elseif ($page === "Blog" && filter_input ( INPUT_GET, 'blogUserId', FILTER_SANITIZE_NUMBER_INT )) {
	$blogId = filter_input ( INPUT_GET, 'blogUserId', FILTER_SANITIZE_NUMBER_INT );
	$stmt = $db->prepare ( "SELECT blogTitle, blogDesc, blogPic, blogPicExt FROM blogDescriptions WHERE userId=?" );
	$stmt->execute ( array (
			$blogId
	) );
	$row = $stmt->fetch ();
	if ($row) {
		$blogtitle = $row ['blogTitle'];
		$blogcontent = nl2br ( html_entity_decode ( $row ['blogDesc'], ENT_QUOTES ), $highlightColor );
		$blogpic = $row ['blogPic'];
		$blogpicExt = $row ['blogPicExt'];
	}
	$headTitle = "<title>My Local Life || Blog || $blogtitle</title>";

	if (strlen ( $blogcontent ) >= 250) {
		$subContent1 = substr ( $blogcontent, 0, 249 );
		$posContent1 = strripos ( $subContent1, " " );
		$acontent = substr ( $blogcontent, 0, $posContent1 );
	} else {
		$acontent = $blogcontent;
	}
	if (file_exists ( "userPics/$blogId/$blogpic.$blogpicExt" )) {
		list ( $widthm, $heightm ) = (getimagesize ( "userPics/$blogId/$blogpic.$blogpicExt" ) != null) ? getimagesize ( "userPics/$blogId/$blogpic.$blogpicExt" ) : null;
		echo "<meta property='og:image'       content='https://mylocal.life/userPics/$blogId/$blogpic.$blogpicExt'>";
		echo "<meta property='og:image:width'       content='$widthm'>\n";
		echo "<meta property='og:image:height'       content='$heightm'>\n";
	} else {
		echo "<meta property='og:image'       content='https://mylocal.life/images/logo.png'>";
		echo "<meta property='og:image:width'       content='256'>\n";
		echo "<meta property='og:image:height'       content='256'>\n";
	}
	?>
    <meta property="og:url"         content="https://mylocal.life/index.php?page=Blog&blogUserId=<?php

	echo $blogId;
	?>">
    <meta property="og:title"       content="<?php

	echo $blogtitle;
	?>">
    <meta property="og:description" content="<?php

	echo $acontent;
	?>...">

    <?php
} elseif ($page === "calendar" && filter_input ( INPUT_GET, 'h', FILTER_SANITIZE_NUMBER_INT )) {
	$calId = filter_input ( INPUT_GET, 'h', FILTER_SANITIZE_NUMBER_INT );
	$stmtc = $db->prepare ( "SELECT startTime, title, writeUp, picture, userId FROM calendar WHERE id = ?" );
	$stmtc->execute ( array (
			$calId
	) );
	$rowc = $stmtc->fetch ();
	if ($rowc) {
		$cStartTime = $rowc ['startTime'];
		$cTitle = $rowc ['title'];
		$cW = nl2br ( $rowc ['writeUp'] );
		$cWriteUp = str_replace ( "<br />", " ", $cW );
		$cPic = $rowc ['picture'];
		$cUserId = $rowc ['userId'];
	}
	$headTitle = "<title>My Local Life || Upcoming Events || $cTitle</title>";

	if (strlen ( $cWriteUp ) >= 250) {
		$subContent1 = substr ( $cWriteUp, 0, 249 );
		$posContent1 = strripos ( $subContent1, " " );
		$acontent = substr ( $cWriteUp, 0, $posContent1 );
	} else {
		$acontent = $cWriteUp;
	}
	if (file_exists ( "userPics/$cUserId/$cPic" ) && getimagesize ( "userPics/$cUserId/$cPic" ) >= 1000) {
		list ( $widthm, $heightm ) = getimagesize ( "userPics/$cUserId/$cPic" );
		echo "<meta property='og:image'       content='https://mylocal.life/userPics/$cUserId/$cPic'>\n";
		echo "<meta property='og:image:width'       content='$widthm'>\n";
		echo "<meta property='og:image:height'       content='$heightm'>\n";
	} else {
		echo "<meta property='og:image'       content='https://mylocal.life/images/logo.png'>\n";
		echo "<meta property='og:image:width'       content='256'>\n";
		echo "<meta property='og:image:height'       content='256'>\n";
	}
	?>
    <meta property="og:url"         content="https://mylocal.life/index.php?page=calendar&h=<?php

	echo $calId;
	?>#c<?php

	echo $calId;
	?>">
    <meta property="og:title"       content="<?php

	echo $cTitle;
	?>">
    <meta property="og:description" content="<?php

	echo date ( 'M jS, g:ia', $cStartTime )?> - <?php

	echo $acontent;
	?>...">

    <?php
} elseif ($page === "Survey" && filter_input ( INPUT_GET, 'surveyId', FILTER_SANITIZE_NUMBER_INT )) {
	$surId = filter_input ( INPUT_GET, 'surveyId', FILTER_SANITIZE_NUMBER_INT );
	$stmt = $db->prepare ( "SELECT userId, surveyTitle, introText, picName FROM survey WHERE id=?" );
	$stmt->execute ( array (
			$surId
	) );
	$row = $stmt->fetch ();
	if ($row) {
		$uId = $row ['userId'];
		$surveyTitle = $row ['surveyTitle'];
		$introText = $row ['introText'];
		$picName = $row ['picName'];
	}
	$headTitle = "<title>My Local Life || Survey || $surveyTitle</title>";

	if (strlen ( $introText ) >= 250) {
		$subContent1 = substr ( $introText, 0, 249 );
		$posContent1 = strripos ( $subContent1, " " );
		$acontent = substr ( $introText, 0, $posContent1 );
	}
	if (file_exists ( "userPics/$uId/$picName" )) {
		list ( $widthm, $heightm ) = (getimagesize ( "userPics/$uId/$picName" ) != null) ? getimagesize ( "userPics/$uId/$picName" ) : null;
		echo "<meta property='og:image'       content='https://mylocal.life/userPics/$uId/$picName'>";
		echo "<meta property='og:image:width'       content='$widthm'>\n";
		echo "<meta property='og:image:height'       content='$heightm'>\n";
	} else {
		echo "<meta property='og:image'       content='https://mylocal.life/images/logo.png'>";
		echo "<meta property='og:image:width'       content='256'>\n";
		echo "<meta property='og:image:height'       content='256'>\n";
	}
	?>
    <meta property="og:url"         content="https://mylocal.life/index.php?page=Survey&surveyId=<?php

	echo $surId;
	?>">
    <meta property="og:title"       content="<?php

	echo $surveyTitle;
	?>">
    <meta property="og:description" content="<?php

	echo $acontent;
	?>...">

    <?php
} elseif ($page === "Writings" && filter_input ( INPUT_GET, 'authorId', FILTER_SANITIZE_NUMBER_INT ) && filter_input ( INPUT_GET, 'bookId', FILTER_SANITIZE_NUMBER_INT )) {
	$authorId = filter_input ( INPUT_GET, 'authorId', FILTER_SANITIZE_NUMBER_INT );
	$bookId = filter_input ( INPUT_GET, 'bookId', FILTER_SANITIZE_NUMBER_INT );
	$stmt = $db->prepare ( "SELECT t1.title, t2.firstName, t2.lastName, t1.chText FROM myWritings AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id WHERE authorId = ? AND bookId = ? AND part='1' AND chapter = '1'" );
	$stmt->execute ( array (
			$authorId,
			$bookId
	) );
	$row = $stmt->fetch ();
	if ($row) {
		$title = $row [0];
		$firstName = $row [1];
		$lastName = $row [2];
		$chText = $row [3];
	}
	$headTitle = "<title>My Local Life || Writings || $title</title>";

	if (strlen ( $chText ) >= 250) {
		$subContent1 = substr ( $chText, 0, 249 );
		$posContent1 = strripos ( $subContent1, " " );
		$acontent = substr ( $chText, 0, $posContent1 );
	}
	echo "<meta property='og:image'       content='https://mylocal.life/images/logo.png'>";
	echo "<meta property='og:image:width'       content='256'>\n";
	echo "<meta property='og:image:height'       content='256'>\n";
	?>
    <meta property="og:url"         content="https://mylocal.life/index.php?page=Writings&authorId=<?php
	echo $authorId;
	?>&bookId=<?php
	echo $bookId;
	?>&part=1&chapter=1">
    <meta property="og:title"       content="<?php

	echo $title . " -by " . $firstName . " " . $lastName;
	?>">
    <meta property="og:description" content="<?php

	echo $acontent;
	?>...">

    <?php
} else {
	$headTitle = "<title>My Local Life</title>";
	?>
    <meta property="og:image"       content="https://mylocal.life/images/logo.png">
    <meta property='og:image:width'       content='256'>
    <meta property='og:image:height'       content='256'>
    <meta property="og:url"         content="https://mylocal.life">
    <meta property="og:title"       content="My Local Life">
    <meta property="og:description" content="<?php

	echo $metaDesc;
	?>">
    <?php
}
echo "$headTitle\n";
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css'>
<script>
    $(document).ready(function () {
        $("#panel").slideUp();
    });
    $(document).ready(function () {
        $("#flip").click(function () {
            $("#panel").slideToggle("slow");
        });
    });
</script>
<script type="text/javascript">
    function toggleview(itm) {
        var itmx = document.getElementById(itm);
        if (itmx.style.display === "none") {
            itmx.style.display = "block";
        } else {
            itmx.style.display = "none";
        }
    }

    var height = window.innerHeight
            || document.documentElement.clientHeight
            || document.body.clientHeight;
    var width = window.innerWidth
            || document.documentElement.clientWidth
            || document.body.clientWidth;

    var moveleft = (width >= 1102) ? ((width - 1100) / 2) : 0;
    var mainSize = (width >= 1100) ? 1100 : width;
    var halfWidth = 370;
    var thirdWidth = 220;
    var titleFontSize = 6;
    var boxBorder = 85;
    var boxThirdBorder = boxBorder - 10;
    if (width >= 1100) {
        halfWidth = 370;
        thirdWidth = 220;
        titleFontSize = 6;
    }
    if (width >= 820 && width < 1100) {
        halfWidth = (width / 2) - boxBorder;
        thirdWidth = (width / 3) - boxThirdBorder;
        titleFontSize = 5;
    }
    if (width < 820) {
        halfWidth = width - boxBorder;
        thirdWidth = (width / 2) - boxThirdBorder;
        titleFontSize = 4;
    }

    function sizeBoxes() {
        var x = document.getElementById("mainPanel");
        var y2 = x.getElementsByClassName("pageBoxesHalf");
        var y3 = x.getElementsByClassName("pageBoxesThird");
        var i;
        for (i = 0; i < y2.length; i++) {
            y2[i].style.width = halfWidth + "px";
        }
        var j;
        for (j = 0; j < y3.length; j++) {
            y3[j].style.width = thirdWidth + "px";
        }
        var z = document.getElementById("titleBar");
        z.style.fontSize = titleFontSize + "em";

        var artImage1 = document.getElementById("articleImage1");
        var artImage2 = document.getElementById("articleImage2");
        if (width < 800) {
            artImage1.style.width = "93%";
            artImage2.style.width = "93%";
            artImage1.style.margin = "auto";
            artImage2.style.margin = "auto";
        }
    }

    function getBoxBottom(linkId) {
        var elmnt = document.getElementById(linkId);
        var rect = elmnt.getBoundingClientRect();
        return rect.bottom;
    }

    function getBoxLeft(linkId) {
        var elmnt = document.getElementById(linkId);
        var rect = elmnt.getBoundingClientRect();
        return (rect.left - moveleft);
    }

    function showMenu(boxId, linkId) {
        var ele = document.getElementById(boxId);
        var x = getBoxLeft(linkId);
        var y = getBoxBottom(linkId);
        ele.style.left = x + "px";
        ele.style.top = (y + 15) + "px";
        toggleview(boxId);
    }

<?php
if ($page == "editArticle" || $page == "editPhoto" || $page == "submitNews") {
	?>
        function agreeToTerms() {
            var d = document.getElementById("articleSubmit");
            d.disabled = (d.disabled === true) ? false : true;
        }
    <?php
}

if ($page == "myAccount" || $page == "Gadmin") {
	?>
        function showAdPrices(price) {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("showPrice").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "ajax.php?getPrice=" + price, true);
            xmlhttp.send();
        }
    <?php
}

if ($page == "Survey") {
	?>
        function updateSurvey(sId,IP,questionNumber,answer) {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("answer").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "ajax.php?surveyId=" + sId + "&responderId=" + IP + "&questionNumber=" + questionNumber + "&surveyAnswer=" + answer, true);
            xmlhttp.send();
        }
    <?php
}

if ($page == "Blog") {
	?>
        function setBlogFav(blogId, me) {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("blogFavBtn").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "ajax.php?blogUserId=" + blogId + "&myId=" + me, true);
            xmlhttp.send();

            xmlhttp2 = new XMLHttpRequest();
            xmlhttp2.onreadystatechange = function ()
            {
                if (xmlhttp2.readyState === 4 && xmlhttp2.status === 200)
                {
                    document.getElementById("blogFav").innerHTML = xmlhttp2.responseText;
                }
            };
            xmlhttp2.open("GET", "ajax.php?getNew=1&myId=" + me, true);
            xmlhttp2.send();
        }
    <?php
}
if ($page == "editClassified") {
	?>
        function chrCount(max) {
            var x = document.getElementById("clsText").value;
            var l = x.length;
            document.getElementById("showclsCount").innerHTML = l + "/" + max;
        }
    <?php
}
if ($page == "editWriting") {
	?>
        function ModifySelection(tag, t) {
            var textarea = document.getElementById("textField" + t);
            if ('selectionStart' in textarea) {
                // check whether some text is selected in the textarea
                if (textarea.selectionStart !== textarea.selectionEnd) {
                    var newText = textarea.value.substring(0, textarea.selectionStart) +
                            "<" + tag + ">" + textarea.value.substring(textarea.selectionStart, textarea.selectionEnd) + "</" + tag + ">" +
                            textarea.value.substring(textarea.selectionEnd);
                    textarea.value = newText;
                } else {
                    var newText = textarea.value.substring(0, textarea.selectionStart) +
                            "<" + tag + ">" + "</" + tag + ">" + textarea.value.substring(textarea.selectionEnd);
                    textarea.value = newText;
                }
            }
        }

        function textAlignSelection(tag, t) {
            var textarea = document.getElementById("textField" + t);
            if ('selectionStart' in textarea) {
                // check whether some text is selected in the textarea
                if (textarea.selectionStart !== textarea.selectionEnd) {
                    var newText = textarea.value.substring(0, textarea.selectionStart) +
                            "<div style='text-align:" + tag + ";'>" + textarea.value.substring(textarea.selectionStart, textarea.selectionEnd) + "</div>" +
                            textarea.value.substring(textarea.selectionEnd);
                    textarea.value = newText;
                } else {
                    var newText = textarea.value.substring(0, textarea.selectionStart) +
                            "<div style='text-align:" + tag + ";'>" + "</div>" + textarea.value.substring(textarea.selectionEnd);
                    textarea.value = newText;
                }
            }
        }
<?php
}
?>
</script>
<?php
if ($page == "Games") {
	include "includes/gamesHead.php";
}
?>
<style type="text/css">
    a {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    a:hover {
        color: #444444;
        text-decoration: underline;
        cursor: pointer;
    }
    a.submenu {
        color:black;
    }
    a.menu {
        color: #000000;
        text-decoration: none;
        border: 1px solid #ffffff;
        border-radius: 4px;
        padding: 2px;
        margin: 0px 5px;
        font-size: 1em;
        cursor: pointer;
    }
    a.menu:hover {
        color: #444444;
        text-decoration: none;
        border: 1px solid #cccccc;
        border-radius: 4px;
        padding: 2px;
        margin: 0px 5px;
        font-size: 1em;
        cursor: pointer;
    }
    th, td {
        vertical-align: top;
    }

    .flex-container {
        display: -webkit-flex;
        display: flex;
        -webkit-flex-flow: row wrap;
        flex-flow: row wrap;
    }

    .flex-container > * {
        padding: 10px;
        flex: 1 100%;
    }

    @media all and (min-width: 1100px) {
        .main { flex: 4.5 900px; max-width: 860px; }
        .aside    { max-width: 200px; }
        .header { order: 1; }
        .advert { order: 2; }
        .menu { order: 3; }
        .main    { order: 4; }
        .aside { order: 5; }
        .footer  { order: 6; }
    }

    @media print {
        body * {
            visibility: hidden;
        }
        #printArea, #printArea * {
            visibility: visible;
        }
        #printArea {
            position: absolute;
            left: 0;
            top: 0;
        }
    }

    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }

    .main {
        width:100%;
    }

    .hebrewFont {
    	font-family: 'Noto Serif Hebrew', serif;
    	text-size: 1em;
    }

    #signuptd {
    padding:5px;
    border-bottom:1px;
    border-style:solid;
    border-color:black;
    }
</style>

<!-- End of Head -->