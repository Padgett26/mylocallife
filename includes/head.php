<!-- Beginning of Head -->
<link rel="shortcut icon" href="images/icon.png" />
<meta http-equiv='Content-Type'     content='text/html; charset=UTF-8' />
<meta name="viewport"               content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1" />
<?php
echo "<meta name='keywords' content='$metaKeywords'>\n";
echo "<meta name='description' content='$metaDesc'>\n";
if ($slug != "" && $slug != " ") {
    echo "<title>My Local Life || $page || $slug</title>\n";
} else {
    echo "<title>My Local Life || $page</title>\n";
}
?>
<link href="includes/lightbox2/css/lightbox.css" rel="stylesheet" />
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
<?php
$textSize = ($myTheme == "Large Text") ? "font-size:1.25em;" : "font-size:1em;";
?>
    body {
        <?php
        echo $textSize . "\n";
        ?>
    }
    .menu {
        height:30px;
        float:left;
        padding:0px 20px;
    }
    .subMenu {
        display:none;
        text-align:center;
        background-color: #ffffff;
        color: <?php
        echo $highlightColor;
        ?>;
        width:100%;
        padding:20px 0px;
    }
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