<?php

$y = 0;
$stmt = $db_ccdc->prepare("SELECT * FROM jobs ORDER BY RAND()");
$stmt->execute();
while ($row = $stmt->fetch()) {
    $jobid = $row['id'];
    $title = $row['title'];
    $content = nl2br(make_links_clickable(html_entity_decode($row['content'], ENT_QUOTES), $highlightColor));
    if ($y != 0) {
        echo "<div style='width:50%; margin-left:25%; height:2px; background-color:$highlightColor'></div>\n";
    }
    echo "<div style='text-align:center; width:100%; font-weight:normal; font-size:1em; padding:10px 0px; text-decoration:none; cursor:pointer;' onclick='toggleview(\"job$jobid\")'>$title</div>\n";
    echo "<div style='text-align:justify; margin:0px 0px 10px 0px; padding:10px; border-width:0px 1px 1px 1px; border-style:solid; border-color:$highlightColor; background-color:#ffffff; width:96%; display:none;' id='job$jobid'>";
    echo "$content</div>";
    $y++;
}