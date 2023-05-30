
<div style="text-align:center; font-size:0.75em; margin-top:10px;">Q & A</div>
<?php
$a = $db->prepare("SELECT DISTINCT office FROM candidates ORDER BY showOrder");
$a->execute();
while ($arow = $a->fetch()) {
    $office = $arow['office'];
    if ($office != "Admin") {
        $b = $db->prepare("SELECT COUNT(*) FROM candidates WHERE office = ?");
        $b->execute(array($office));
        $brow = $b->fetch();
        $numCand = $brow[0];
        $numReplied = 0;
        $c = $db->prepare("SELECT * FROM candidates WHERE office = ?");
        $c->execute(array($office));
        while ($crow = $c->fetch()) {
            if ($crow['answer1'] != "" || $crow['answer2'] != "" || $crow['answer3'] != "" || $crow['answer4'] != "" || $crow['answer5'] != "" || $crow['answer6'] != "" || $crow['answer7'] != "" || $crow['answer8'] != "" || $crow['answer9'] != "" || $crow['answer10'] != "" || $crow['answer11'] != "") {
                $numReplied++;
            }
        }
        if ($numReplied >= 1) {
            echo "<div style='text-align:center; font-size:0.75em; margin-top:10px;'><a href='index.php?page=myCandidates&office=$office' title='$numCand Candidates, $numReplied Replied'  style='cursor:help;'> - $office</a></div>";
        }
    }
}
    