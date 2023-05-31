<?php
if ($myId >= 1) {
    $writeId = filter_input(INPUT_GET, 'writeId', FILTER_SANITIZE_STRING);
    $wCats = array();
    $getC = $db->prepare("SELECT * FROM writingCategories");
    $getC->execute();
    while ($getCR = $getC->fetch()) {
        $wCats[$getCR['id']] = $getCR['category'];
    }

    // FORM PROCESSING
    if (filter_input(INPUT_POST, 'upWriting', FILTER_SANITIZE_NUMBER_INT) >= 1) {
        $upStart = filter_input(INPUT_POST, 'upWriting', FILTER_SANITIZE_NUMBER_INT);
        $upCategory = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
        $upTitle = htmlEntities(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING), ENT_QUOTES);
        $upShowParts = (filter_input(INPUT_POST, 'showParts', FILTER_SANITIZE_NUMBER_INT) == 1) ? 1 : 0;
        $upPartNew = filter_input(INPUT_POST, 'partNew', FILTER_SANITIZE_NUMBER_INT);
        $upChapterNew = filter_input(INPUT_POST, 'chapterNew', FILTER_SANITIZE_NUMBER_INT);
        $upChTitleNew = htmlEntities(filter_input(INPUT_POST, 'chTitleNew', FILTER_SANITIZE_STRING), ENT_QUOTES);
        $upChTextNew = htmlEntities(filter_input(INPUT_POST, 'chTextNew', FILTER_SANITIZE_STRING), ENT_QUOTES);
        $upDelB = (filter_input(INPUT_POST, 'delB', FILTER_SANITIZE_NUMBER_INT) == 1) ? 1 : 0;

        if ($upDelB == 1) {
            $delB = $db->prepare("DELETE FROM myWritings WHERE authorId = ? AND bookId = ?");
            $delB->execute(array(
                $myId,
                $writeId
            ));
            $writeId = 'new';
        } else {
            if ($upStart >= 1 && filter_var($writeId, FILTER_SANITIZE_NUMBER_INT) >= 1) {
                foreach ($_POST as $key => $v) {
                    $val = htmlEntities($v, ENT_QUOTES);
                    if (preg_match("/^ptTitle([0-9][0-9]*)$/", $key, $match)) {
                        $t = filter_var($val, FILTER_SANITIZE_STRING);
                        $p = $match[1];
                        $up1 = $db->prepare("UPDATE myWritings SET ptTitle = ? WHERE authorId = ? AND bookId = ? AND part = ?");
                        $up1->execute(array(
                            $t,
                            $myId,
                            $writeId,
                            $p
                        ));
                    }
                    if (preg_match("/^chTitleP([0-9][0-9]*)C([1-9][0-9]*)$/", $key, $match)) {
                        $t = filter_var($val, FILTER_SANITIZE_STRING);
                        $p = $match[1];
                        $c = $match[2];
                        $up2 = $db->prepare("UPDATE myWritings SET chTitle = ? WHERE authorId = ? AND bookId = ? AND part = ? AND chapter = ?");
                        $up2->execute(array(
                            $t,
                            $myId,
                            $writeId,
                            $p,
                            $c
                        ));
                    }
                    if (preg_match("/^chTextP([0-9][0-9]*)C([1-9][0-9]*)$/", $key, $match)) {
                        $t = filter_var($val, FILTER_SANITIZE_STRING);
                        $p = $match[1];
                        $c = $match[2];
                        $up3 = $db->prepare("UPDATE myWritings SET chText = ? WHERE authorId = ? AND bookId = ? AND part = ? AND chapter = ?");
                        $up3->execute(array(
                            $t,
                            $myId,
                            $writeId,
                            $p,
                            $c
                        ));
                    }
                    if (preg_match("/^delP([0-9][0-9]*)C([1-9][0-9]*)$/", $key, $match)) {
                        $t = filter_var($val, FILTER_SANITIZE_NUMBER_INT);
                        $p = $match[1];
                        $c = $match[2];
                        if ($t == 1) {
                            $up9 = $db->prepare("DELETE FROM  myWritings WHERE authorId = ? AND bookId = ? AND part = ? AND chapter = ?");
                            $up9->execute(array(
                                $myId,
                                $writeId,
                                $p,
                                $c
                            ));
                            $up10 = $db->prepare("SELECT id FROM myWritings WHERE authorId = ? AND bookId = ? AND part = ? AND chapter > ? ORDER BY chapter");
                            $up10->execute(array(
                                $myId,
                                $writeId,
                                $p,
                                $c
                            ));
                            while ($up10R = $up10->fetch()) {
                                $id = $up10R['id'];
                                $up11 = $db->prepare("UPDATE myWritings SET chapter = ? WHERE id = ?");
                                $up11->execute(array(
                                    $c,
                                    $id
                                ));
                                $c ++;
                            }
                        }
                    }
                }
                if (($upChTitleNew != "" && $upChTitleNew != " ") || ($upChTextNew != "" && $upChTextNew != " ")) {
                    $up4 = $db->prepare("SELECT COUNT(*) FROM myWritings WHERE authorId = ? AND bookId = ? AND part = ? AND chapter = ?");
                    $up4->execute(array(
                        $myId,
                        $writeId,
                        $upPartNew,
                        $upChapterNew
                    ));
                    $up4R = $up4->fetch();
                    $exists = $up4R[0];
                    if ($exists >= 1) {
                        $up5 = $db->prepare("SELECT id FROM myWritings WHERE authorId = ? AND bookId = ? AND part = ? AND chapter >= ? ORDER BY chapter");
                        $up5->execute(array(
                            $myId,
                            $writeId,
                            $upPartNew,
                            $upChapterNew
                        ));
                        $tic = ($upChapterNew + 1);
                        while ($up5R = $up5->fetch()) {
                            $id = $up5R['id'];
                            $up6 = $db->prepare("UPDATE myWritings SET chapter = ? WHERE id = ?");
                            $up6->execute(array(
                                $tic,
                                $id
                            ));
                            $tic ++;
                        }
                    }
                    $up7 = $db->prepare("INSERT INTO myWritings VALUES(NULL,?,?,?,?,?,?,?,?,?,?,?,?,'0','0')");
                    $up7->execute(array(
                        $upStart,
                        $time,
                        $myId,
                        $writeId,
                        $upCategory,
                        $upShowParts,
                        $upTitle,
                        $upPartNew,
                        '',
                        $upChapterNew,
                        $upChTitleNew,
                        $upChTextNew
                    ));
                }
                $up8 = $db->prepare("UPDATE myWritings SET editDate = ?, category = ?, showParts = ?, title = ? WHERE authorId = ? AND bookId = ?");
                $up8->execute(array(
                    $time,
                    $upCategory,
                    $upShowParts,
                    $upTitle,
                    $myId,
                    $writeId
                ));
            }

            if ($writeId = 'new') {
                $up1 = $db->prepare("SELECT bookId FROM myWritings WHERE authorId = ? ORDER BY bookId DESC LIMIT 1");
                $up1->execute(array(
                    $myId
                ));
                $up1R = $up1->fetch();
                $lastBook = $up1R['bookId'];
                $nextBook = ($lastBook >= 1) ? $lastBook ++ : 1;

                $up2 = $db->prepare("INSERT INTO myWritings VALUES(NULL,?,?,?,?,?,?,?,?,?,?,?,?,'0','0')");
                $up2->execute(array(
                    $time,
                    $time,
                    $myId,
                    $nextBook,
                    $upCategory,
                    $upShowParts,
                    $upTitle,
                    $upPartNew,
                    '',
                    $upChapterNew,
                    $upChTitleNew,
                    $upChTextNew
                ));
                $writeId = $nextBook;
            }
        }
    }

    // FORM
    echo "<form action='index.php?page=editWriting&writeId=$writeId' method='post'>\n";
    echo "<input type='submit' value=' Submit Changes '><br><br>\n";

    if (filter_var($writeId, FILTER_SANITIZE_NUMBER_INT) >= 1) {
        $getB = $db->prepare("SELECT title, startDate, editDate, showParts, category FROM myWritings WHERE authorId = ? AND bookId = ?");
        $getB->execute(array(
            $myId,
            $writeId
        ));
        $getBR = $getB->fetch();
        $title = $getBR['title'];
        $start = $getBR['startDate'];
        $startDate = date("Y-m-d H:i:s", $start);
        $editDate = date("Y-m-d H:i:s", $getBR['editDate']);
        $showParts = $getBR['showParts'];
        $category = $getBR['category'];

        echo "<input type='hidden' name='upWriting' value='$start'>\n";
        echo "<div style='cursor:pointer;' onclick='toggleview(\"deleteBookDiv\")'>--> Delete this whole book <--</div>";
        echo "<div id='deleteBookDiv' style='display:none; border:1px solid black; padding:5px;'>";
        echo "Are you sure?<br>It will not be restorable after deletion.<br><br>";
        echo "<input type='radio' name='delB' value='0' selected> NO<br>";
        echo "<input type='radio' name='delB' value='1'> Delete the whole book<br><br><input type='submit' value=' Do it '>";
        echo "</div>";
        echo "Category: <select name='category' size='1'>\n";
        foreach ($wCats as $k => $v) {
            echo "<option value='$k'";
            echo ($k == $category) ? " selected" : "";
            echo ">$v</option>\n";
        }
        echo "</select><br>\n";
        echo "Title:<br><input type='text' name='title' value='$title'><br><br>\n";
        echo "Start Date: $startDate<br>\n";
        echo "Last Edit: $editDate<br><br>\n";
        echo "Show parts seperation in public display: <input type='checkbox' name='showParts' value='1'";
        echo ($showParts == 1) ? " checked" : "";
        echo "><br><br>\n";

        echo "New chapter:<br><span style='font-size: .75em;'>Please use part 0 for you introduction / acknowledgements / contents and the like. Part 1 should be the start of your story. This will just make everything display better.</span><br>\n";
        $getP = $db->prepare("SELECT part FROM myWritings WHERE authorId = ? AND bookId = ? ORDER BY part DESC LIMIT 1");
        $getP->execute(array(
            $myId,
            $writeId
        ));
        $getPR = $getP->fetch();
        $topPart = $getPR['part'];
        echo "Part <select name='partNew' size='1'>\n";
        for ($i = 0; $i <= ($topPart + 5); ++ $i) {
            echo "<option value='$i'";
            echo ($i == $topPart) ? " selected" : "";
            echo ">$i</option>\n";
        }
        echo "</select><br>\n";
        $getC = $db->prepare("SELECT chapter FROM myWritings WHERE authorId = ? AND bookId = ? AND part = ? ORDER BY chapter DESC LIMIT 1");
        $getC->execute(array(
            $myId,
            $writeId,
            $topPart
        ));
        $getCR = $getC->fetch();
        $topCh = $getCR['chapter'] ++;
        echo "Chapter <input type='number' name='chapterNew' step='1' min='1' value='$topCh'><br>\n";
        echo "Chapter Title <input type='text' name='chTitleNew' value=''><br><br>\n";
        echo "Chapter Text - <span style='cursor:pointer;' onclick='toggleview(\"chTextNew\")'>OPEN</span><br>\n";
        echo "<div id='chTextNew' style='display:none;'>";
        ?>
		Text align:&nbsp;&nbsp;
        <span style="cursor:pointer;" onclick="textAlignSelection('left', 1)">left</span>&nbsp;&nbsp;
        <span style="cursor:pointer;" onclick="textAlignSelection('center', 1)">center</span>&nbsp;&nbsp;
        <span style="cursor:pointer;" onclick="textAlignSelection('right', 1)">right</span>&nbsp;&nbsp;
        <span style="cursor:pointer; font-size:2em; font-weight:bold;" onclick="ModifySelection('h1', 1)">h1</span>&nbsp;&nbsp;
        <span style="cursor:pointer; font-size:1.5em; font-weight:bold;" onclick="ModifySelection('h2', 1)">h2</span>&nbsp;&nbsp;
        <span style="cursor:pointer; font-size:1.17em; font-weight:bold;" onclick="ModifySelection('h3', 1)">h3</span>&nbsp;&nbsp;
        <span style="cursor:pointer;" onclick="ModifySelection('b', 1)"><b>bold</b></span>&nbsp;&nbsp;
        <span style="cursor:pointer;" onclick="ModifySelection('i', 1)"><i>italics</i></span>&nbsp;&nbsp;
        <span style="cursor:pointer;" onclick="ModifySelection('blockquote', 1)">blockquote</span>&nbsp;&nbsp;
        <span style="cursor:pointer;" onclick="ModifySelection('del', 1)"><del>deleted text</del></span>&nbsp;&nbsp;
        <span style="cursor:pointer;" onclick="ModifySelection('ins', 1)"><ins>replacement text</ins></span><br><br>
		<?php
        echo "<textarea name='chTextNew' id='textField1' style='width:100%;' rows='25'></textarea>";
        echo "</div><br><br>\n";

        $getB1 = $db->prepare("SELECT DISTINCT part FROM myWritings WHERE authorId = ? AND bookId = ? ORDER BY part");
        $getB1->execute(array(
            $myId,
            $writeId
        ));
        while ($getB1R = $getB1->fetch()) {
            $part = $getB1R['part'];

            $getB2 = $db->prepare("SELECT ptTitle FROM myWritings WHERE authorId = ? AND bookId = ? AND part = ? LIMIT 1");
            $getB2->execute(array(
                $myId,
                $writeId,
                $part
            ));
            $getB2R = $getB2->fetch();
            $ptTitle = html_entity_decode($getB2R['ptTitle'], ENT_QUOTES);

            echo "Part $part<br>\n";
            echo "Part title<br><input type='text' name='ptTitle$part' value='$ptTitle'><br><br>\n";
            echo "<blockquote>\n";

            $getB3 = $db->prepare("SELECT chapter, chTitle, chText FROM myWritings WHERE authorId = ? AND bookId = ? AND part = ? ORDER BY chapter");
            $getB3->execute(array(
                $myId,
                $writeId,
                $part
            ));
            while ($getB3R = $getB3->fetch()) {
                $chapter = $getB3R['chapter'];
                $chTitle = html_entity_decode($getB3R['chTitle'], ENT_QUOTES);
                $chText = html_entity_decode($getB3R['chText'], ENT_QUOTES);

                echo "Chapter $chapter - <input type='text' name='chTitleP" . $part . "C" . $chapter . "' value='$chTitle'> - <span style='cursor:pointer;' onclick='toggleview(\"pt" . $part . "ch" . $chapter . "\")'>OPEN</span><br>\n";
                echo "<div id='pt" . $part . "ch" . $chapter . "' style='display:none;'>";
                echo "<div style='cursor:pointer;' onclick='toggleview(\"deleteP" . $part . "C" . $chapter . "\")'>--> Delete this chapter <--</div>";
                echo "<div id='deleteP" . $part . "C" . $chapter . "' style='display:none; padding:5px; border:1px solid black;'>";
                echo "Are you sure?<br>It will not be restorable after deletion.<br><br>";
                echo "<input type='radio' name='delP" . $part . "C" . $chapter . "' value='0' selected> NO<br>";
                echo "<input type='radio' name='delP" . $part . "C" . $chapter . "' value='1'> Delete the chapter<br><br><input type='submit' value=' Do it '>";
                echo "</div>";
                ?>
				Text align:&nbsp;&nbsp;
        		<span style="cursor:pointer;" onclick="textAlignSelection('left', 2)">left</span>&nbsp;&nbsp;
        		<span style="cursor:pointer;" onclick="textAlignSelection('center', 2)">center</span>&nbsp;&nbsp;
        		<span style="cursor:pointer;" onclick="textAlignSelection('right', 2)">right</span>&nbsp;&nbsp;
       			<span style="cursor:pointer; font-size:2em; font-weight:bold;" onclick="ModifySelection('h1', 2)">h1</span>&nbsp;&nbsp;
        		<span style="cursor:pointer; font-size:1.5em; font-weight:bold;" onclick="ModifySelection('h2', 2)">h2</span>&nbsp;&nbsp;
        		<span style="cursor:pointer; font-size:1.17em; font-weight:bold;" onclick="ModifySelection('h3', 2)">h3</span>&nbsp;&nbsp;
        		<span style="cursor:pointer;" onclick="ModifySelection('b', 2)"><b>bold</b></span>&nbsp;&nbsp;
        		<span style="cursor:pointer;" onclick="ModifySelection('i', 2)"><i>italics</i></span>&nbsp;&nbsp;
        		<span style="cursor:pointer;" onclick="ModifySelection('blockquote', 2)">blockquote</span>&nbsp;&nbsp;
        		<span style="cursor:pointer;" onclick="ModifySelection('del', 2)"><del>deleted text</del></span>&nbsp;&nbsp;
        		<span style="cursor:pointer;" onclick="ModifySelection('ins', 2)"><ins>replacement text</ins></span><br><br>
				<?php
                echo "<textarea id='textField2' name='chTextP" . $part . "C" . $chapter . "' style='width:100%;' rows='25'>$chText</textarea>";
                echo "</div>\n";
            }
            echo "</blockquote>\n";
        }
    } else {
        echo "<input type='hidden' name='upWriting' value='$time'>\n";
        echo "Category: <select name='category' size='1'>\n";
        foreach ($wCats as $k => $v) {
            echo "<option value='$k'>$v</option>\n";
        }
        echo "</select><br>\n";
        echo "Title:<br><input type='text' name='title' value=''><br><br>\n";
        echo "Show parts seperation in public display: <input type='checkbox' name='showParts' value='1'><br>\n";

        echo "New chapter:<br><span style='font-size: .75em;'>Please use part 0 for you introduction / acknowledgements / contents and the like. Part 1 should be the start of your story. This will just make everything display better.</span><br><br>\n";
        echo "Part <select name='partNew' size='1'>\n";
        for ($i = 0; $i <= 5; ++ $i) {
            echo "<option value='$i'";
            echo ($i == 1) ? " selected" : "";
            echo ">$i</option>\n";
        }
        echo "</select><br>\n";
        echo "Chapter <input type='number' name='chapterNew' step='1' min='1' value='1'><br>\n";
        echo "Chapter Title <input type='text' name='chTitleNew' value=''><br><br>\n";
        echo "Chapter Text - <span style='cursor:pointer;' onclick='toggleview(\"chTextNew\")'>OPEN</span><br>\n";
        echo "<div id='chTextNew' style='display:none;'>";
        ?>
		Text align:&nbsp;&nbsp;
        <span style="cursor:pointer;" onclick="textAlignSelection('left', 3)">left</span>&nbsp;&nbsp;
        <span style="cursor:pointer;" onclick="textAlignSelection('center', 3)">center</span>&nbsp;&nbsp;
        <span style="cursor:pointer;" onclick="textAlignSelection('right', 3)">right</span>&nbsp;&nbsp;
        <span style="cursor:pointer; font-size:2em; font-weight:bold;" onclick="ModifySelection('h1', 3)">h1</span>&nbsp;&nbsp;
        <span style="cursor:pointer; font-size:1.5em; font-weight:bold;" onclick="ModifySelection('h2', 3)">h2</span>&nbsp;&nbsp;
        <span style="cursor:pointer; font-size:1.17em; font-weight:bold;" onclick="ModifySelection('h3', 3)">h3</span>&nbsp;&nbsp;
        <span style="cursor:pointer;" onclick="ModifySelection('b', 3)"><b>bold</b></span>&nbsp;&nbsp;
        <span style="cursor:pointer;" onclick="ModifySelection('i', 3)"><i>italics</i></span>&nbsp;&nbsp;
        <span style="cursor:pointer;" onclick="ModifySelection('blockquote', 3)">blockquote</span>&nbsp;&nbsp;
        <span style="cursor:pointer;" onclick="ModifySelection('del', 3)"><del>deleted text</del></span>&nbsp;&nbsp;
        <span style="cursor:pointer;" onclick="ModifySelection('ins', 3)"><ins>replacement text</ins></span><br><br>
		<?php
        echo "<textarea name='chTextNew' id='textField3' style='width:100%;' rows='25'></textarea>";
        echo "</div><br><br>\n";
    }
    echo "</form>\n";
} else {
    echo "You do not have access to this page.";
}